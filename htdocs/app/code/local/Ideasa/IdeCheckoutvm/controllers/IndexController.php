<?php

/**
* 
* Checkout Venda Mais para Magento
* 
* @category     Idea S/A.
* @packages     IdeCheckoutvm
* @copyright    Copyright (c) 2012 IDEA S/A. (http://www.checkoutvendamais.com.br)
* @version      1.7.0
* @license      http://www.checkoutvendamais.com.br/magento/licenca
*
*/

class Ideasa_IdeCheckoutvm_IndexController extends Mage_Checkout_Controller_Action {

  /**
   *
   * @var type
   */
  private $_currentLayout = null;

  /**
   *
   * @var type
   */
  private $logger;

  /**
   *
   * @var type
   */
  protected $_sectionUpdateFunctions = array(
      'payment-method' => '_getPaymentMethodsHtml',
      'shipping-method' => '_getShippingMethodsHtml',
      'review' => '_getReviewHtml',
  );

  /** @var Mage_Sales_Model_Order */
  protected $_order;

  /**
   * Initialize
   */
  protected function _construct() {
    $this->logger = Ideasa_IdeCheckoutvm_Logger::getLogger(__CLASS__);
  }

  /**
   * @return Mage_Checkout_OnepageController
   */
  public function preDispatch() {
    parent::preDispatch();
    $this->_preDispatchValidateCustomer();

    $quote = Mage::getSingleton('checkout/session')->getQuote();
    if ($quote->getIsMultiShipping()) {
      $quote->setIsMultiShipping(false);
      $quote->removeAllAddresses();
    }

    $helper = Mage::helper('idecheckoutvm/mageversion');
    if ($helper->isCommunityGreaterOrEqual17()) {
      if (!$this->_canShowForUnregisteredUsers()) {
        $this->logger->fatal('Esta instalação não permite criação de pedidos para clientes não registrados.');
        $this->norouteAction();
        $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        return;
      }
    }

    return $this;
  }

  protected function _ajaxRedirectResponse() {
    $this->getResponse()
        ->setHeader('HTTP/1.1', '403 Session Expired')
        ->setHeader('Login-Required', 'true')
        ->sendResponse();
    return $this;
  }

  protected function _expireAjax() {
    if (!$this->getOnepage()->getQuote()->hasItems() || $this->getOnepage()->getQuote()->getHasError() || $this->getOnepage()->getQuote()->getIsMultiShipping()) {
      $this->_ajaxRedirectResponse();
      return true;
    }
    $action = $this->getRequest()->getActionName();
    if (Mage::getSingleton('checkout/session')->getCartWasUpdated(true) && !in_array($action, array('index', 'progress'))) {
      $this->_ajaxRedirectResponse();
      return true;
    }

    return false;
  }

  protected function _getUpdatedLayout() {
    $this->_initLayoutMessages('checkout/session');
    if ($this->_currentLayout === null) {
      $layout = $this->getLayout();
      $update = $layout->getUpdate();
      $update->load('idecheckoutvm_index_update');

      $layout->generateXml();
      $layout->generateBlocks();
      $this->_currentLayout = $layout;
    }

    return $this->_currentLayout;
  }

  public function getOnepage() {
    return Mage::getSingleton('idecheckoutvm/type_onepage');
  }

  private function validateMinimumItens($quote) {
    $minimumItemsQty = Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::MINIMUM_ITENS_ITENS, $quote->getStore());

    if ($minimumItemsQty) {
      $ItemsQty = $quote->getItemsQty();
      if ($ItemsQty < $minimumItemsQty) {
        $warning = Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::MINIMUM_ITENS_MESSAGE, $quote->getStore());
        $warning = sprintf($warning, $minimumItemsQty);
        Mage::getSingleton('checkout/session')->addNotice($warning);
        $this->_redirect('checkout/cart');
        return;
      }
    }
  }

  public function indexAction() {
    $this->logger->info(Mage::helper('idecheckoutvm/account')->logAccountInformation('Página inicial do checkout.'));

    if (!Mage::helper('idecheckoutvm')->isCheckoutEnabled()) {
      Mage::getSingleton('checkout/session')->addError($this->__('The Checkout Venda Mais is disabled.'));
      $this->_redirect('checkout/cart');
      return;
    }
    $quote = $this->getOnepage()->getQuote();
    if (!$quote->getAllItems() || $quote->getHasError()) {
      $this->_redirect('checkout/cart');
      return;
    }
    if (!$quote->validateMinimumAmount()) {
      $error = Mage::getStoreConfig('sales/minimum_order/error_message');
      Mage::getSingleton('checkout/session')->addError($error);
      $this->_redirect('checkout/cart');
      return;
    }
    $this->validateMinimumItens($quote);

    Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
    Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_secure' => true)));

    $this->getOnepage()->initCheckout();
    $this->loadLayout();
    $this->_initLayoutMessages('customer/session');
    $title = Mage::getStoreConfig('idecheckoutvm/geral/title');
    $this->getLayout()->getBlock('head')->setTitle($title);

    $this->renderLayout();
  }

  public function successAction() {
    $session = $this->getOnepage()->getCheckout();
    if (!$session->getLastSuccessQuoteId()) {
      $this->_redirect('checkout/cart');
      return;
    }

    $lastQuoteId = $session->getLastQuoteId();
    $lastOrderId = $session->getLastOrderId();
    $lastRecurringProfiles = $session->getLastRecurringProfileIds();
    if (!$lastQuoteId || (!$lastOrderId && empty($lastRecurringProfiles))) {
      $this->_redirect('checkout/cart');
      return;
    }
    $this->logger->info(Mage::helper('idecheckoutvm/account')->logAccountInformation('Página de sucesso do pedido.'));

    /**
     * set the quote as inactive after back from paypal
     */
    $session->getQuote()->setIsActive(false)->save();
    $session->clear();

    $this->loadLayout();
    $this->_initLayoutMessages('checkout/session');
    Mage::dispatchEvent('checkout_onepage_controller_success_action', array('order_ids' => array($lastOrderId)));
    $this->renderLayout();
  }

  public function getAddressAction() {
    if ($this->_expireAjax()) {
      return;
    }
    $addressId = $this->getRequest()->getParam('address', false);
    if ($addressId) {
      $address = $this->getOnepage()->getAddress($addressId);
      if (Mage::getSingleton('customer/session')->getCustomer()->getId() == $address->getCustomerId()) {
        $this->getResponse()->setHeader('Content-type', 'application/x-json');
        $this->getResponse()->setBody($address->toJson());
      } else {
        $this->getResponse()->setHeader('HTTP/1.1', '403 Forbidden');
      }
    }
  }

  public function saveOrderAction() {
    if ($this->_expireAjax()) {
      return;
    }
    $checkoutUpdateSession = Ideasa_IdeCheckoutvm_CheckoutUpdateSection::getInstance();
    $result = Ideasa_IdeCheckoutvm_CheckoutResult::getInstance();
    $result->setUpdateSection($checkoutUpdateSession);

    $res = array();
    $redirectUrl = null;
    try {
      $billingData = $this->getRequest()->getPost('billing', array());
      $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
      $res = $this->getOnepage()->saveBilling($billingData, $customerAddressId);

      // shipping address
      if (!$this->getOnepage()->getQuote()->isVirtual()) {
        if ((!$billingData['use_for_shipping'] || !isset($billingData['use_for_shipping']))) {
          $shippingData = $this->getRequest()->getPost('shipping', array());
          $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
          $res = $this->getOnepage()->saveShipping($shippingData, $customerAddressId);
        }
      }

      $agreements = Mage::helper('idecheckoutvm')->getAgreeIds();
      if ($agreements) {
        $postAgree = array_keys($this->getRequest()->getPost('agreement', array()));
        $agreementsErrors = $this->getOnepage()->validateAgreements($agreements, $postAgree);
        if ($agreementsErrors !== true) {
          Ideasa_IdeCheckoutvm_ValidatorException::throwException(Mage::helper('idecheckoutvm')->__('Please check agreements information.'), $agreementsErrors);
        }
      }

      try {
        $paymentData = $this->getRequest()->getPost('payment', array());
        $this->getOnepage()->savePayment($paymentData);
        $redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
      } catch (Mage_Payment_Exception $e) {
        Ideasa_IdeCheckoutvm_BusinessException::throwException($e->getMessage());
      } catch (Mage_Core_Exception $e) {
        Ideasa_IdeCheckoutvm_BusinessException::throwException($e->getMessage());
      } catch (Exception $e) {
        Ideasa_IdeCheckoutvm_BusinessException::throwException(Mage::helper('checkout')->__('Unable to set Payment Method.'));
      }

      $this->_subscribeNews();

      // insere informações adicionais
      $aditionalFields = $this->getRequest()->getPost('aditional', array());
      if (is_array($aditionalFields)) {
        foreach ($aditionalFields as $key => $value) {
          $this->getOnepage()->getQuote()->setData($key, $value);
        }
      }
      $this->logger->info(Mage::helper('idecheckoutvm/account')->logAccountInformation('Antes de salvar o pedido.'));

      $this->getOnepage()->saveOrder();

      $this->logger->info(Mage::helper('idecheckoutvm/account')->logAccountInformation('Após salvar o pedido.'));
      /**
       * Se vier "payment_same_screen" é para realizar pagamento na mesma tela.
       */
      if ($redirectUrl != 'payment_same_screen') {
        $redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();
      }

      $result->setSuccess(true);
      $result->setOrderCreated(true);

      if ($redirectUrl == null) {
        $redirectUrl = Mage::getUrl(Mage::getStoreConfig('idecheckoutvm/url/success-page'), array('_secure' => true));
      }

      $customerSession = Mage::getSingleton('customer/session');

      $this->logger->info('Pedido ' . Mage::getSingleton('checkout/session')->getLastRealOrderId() . '. Redirecionando cliente[' . $customerSession->getCustomer()->getEmail() . '] para ' . $redirectUrl);
    } catch (Mage_Core_Exception $e) {
      $this->logger->warn(Ideasa_IdeCheckoutvm_Exception::prepareQuoteForLog2($e, $this->getOnepage()->getQuote()));
      Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());

      $result->setError(true);
      $result->setErrorMessage(Ideasa_IdeCheckoutvm_ErrorMessage::getInstance($e->getMessage(), null));

      $updateSection = $this->getOnepage()->getCheckout()->getUpdateSection();
      if ($updateSection) {
        if (isset($this->_sectionUpdateFunctions[$updateSection])) {
          $layout = $this->_getUpdatedLayout();
          $updateSectionFunction = $this->_sectionUpdateFunctions[$updateSection];
        }
        $this->getOnepage()->getCheckout()->setUpdateSection(null);
      }

      $this->getOnepage()->getQuote()->save();
    } catch (Ideasa_IdeCheckoutvm_Exception $e) {
      $this->logger->warn($e->prepareQuoteForLog($this->getOnepage()->getQuote()));
      $result->setError(true);
      if ($e instanceof Ideasa_IdeCheckoutvm_ValidatorException) {
        $result->setErrorMessage(Ideasa_IdeCheckoutvm_ErrorMessage::getInstance($e->getMessage(), $e->getErrors()));
      } else if ($e instanceof Ideasa_IdeCheckoutvm_BusinessException) {
        $result->setErrorMessage(Ideasa_IdeCheckoutvm_ErrorMessage::getInstance($e->getMessage()));
      }
    } catch (Exception $e) {
      $this->logger->error(Ideasa_IdeCheckoutvm_Exception::prepareQuoteForLog2($e, $this->getOnepage()->getQuote()));
      $result->setError(true);
      Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());

      $errorMessage = Mage::helper('checkout')->__('There was an error processing your order. Please contact support or try again later.');
      $result->setErrorMessage(Ideasa_IdeCheckoutvm_ErrorMessage::getInstance($errorMessage));
      $this->getOnepage()->getQuote()->save();
    }
    $result->setRedirectUrl($redirectUrl);

    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
  }

  protected function _subscribeNews() {
    if ($this->getRequest()->isPost() && $this->getRequest()->getPost('newsletter')) {
      $customerSession = Mage::getSingleton('customer/session');

      if ($customerSession->isLoggedIn()) {
        $email = $customerSession->getCustomer()->getEmail();
      } else {
        $data = $this->getRequest()->getPost('billing');
        $email = $data['email'];
      }

      try {
        if (!$customerSession->isLoggedIn() && Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1) {
          Mage::throwException(Mage::helper('newsletter')->__('Sorry, subscription for guests is not allowed. Please <a href="%s">register</a>.', Mage::getUrl('customer/account/create/'), array('_secure' => true)));
        }
        $ownerId = Mage::getModel('customer/customer')->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($email)->getId();

        if ($ownerId !== null && $ownerId != $customerSession->getId()) {
          Mage::throwException(Mage::helper('newsletter')->__('Sorry, you are trying to subscribe email assigned to another user.'));
        }

        $status = Mage::getModel('newsletter/subscriber')->subscribe($email);
      } catch (Mage_Core_Exception $e) {
        Mage::logException($e);
      } catch (Exception $e) {
        Mage::logException($e);
      }
    }
  }

  /**
   * Check can page show for unregistered users
   *
   * @return boolean
   */
  protected function _canShowForUnregisteredUsers() {
    $isShow = Mage::getSingleton('customer/session')->isLoggedIn() || $this->getRequest()->getActionName() == 'index' || Mage::helper('checkout')->isAllowedGuestCheckout($this->getOnepage()->getQuote());
    $isShow = ($isShow || !Mage::helper('checkout')->isCustomerMustBeLogged());

    return $isShow;
  }

}
