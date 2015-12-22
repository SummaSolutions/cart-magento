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

class Ideasa_IdeCheckoutvm_UpdateController extends Mage_Checkout_Controller_Action {

  /**
   *
   * @var type
   */
  private $currentLayout = null;

  /**
   *
   * @var type
   */
  private $logger;

  /**
   * Initialize
   */
  protected function _construct() {
    $this->logger = Ideasa_IdeCheckoutvm_Logger::getLogger(__CLASS__);
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
    if ($this->currentLayout === null) {
      $layout = $this->getLayout();
      $update = $layout->getUpdate();
      $update->load('idecheckoutvm_update_index');

      $layout->generateXml();
      $layout->generateBlocks();
      $this->currentLayout = $layout;
    }

    return $this->currentLayout;
  }

  protected function _getShippingMethodsHtml() {
    $layout = $this->_getUpdatedLayout();
    return $layout->getBlock('checkout.onepage.shipping_method.available')->toHtml();
  }

  protected function _getPaymentMethodsHtml() {
    $layout = $this->_getUpdatedLayout();
    return $layout->getBlock('checkout.payment.methods')->toHtml();
  }

  protected function _getReviewHtml() {
    $layout = $this->_getUpdatedLayout();
    return $layout->getBlock('review.info')->toHtml();
  }

  public function getOnepage() {
    return Mage::getSingleton('idecheckoutvm/type_onepage');
  }

  /**
   * 
   * 
   * @return type
   */
  public function billingAction() {
    if ($this->_expireAjax() || !$this->getRequest()->isPost()) {
      return;
    }
    $checkoutUpdateSession = Ideasa_IdeCheckoutvm_CheckoutUpdateSection::getInstance();
    $result = Ideasa_IdeCheckoutvm_CheckoutResult::getInstance();
    $result->setUpdateSection($checkoutUpdateSession);

    $request = $this->getRequest();
    $quote = $this->getOnepage()->getQuote();
    // ----------------------- billing address -----------------------
    try {
      $data = $request->getPost('billing', array());
      if ($data) {
        $customerAddressId = $request->getPost('billing_address_id', false);

        if (isset($data['email'])) {
          $data['email'] = trim($data['email']);
        }
        $res = $this->getOnepage()->updateBilling($data, $customerAddressId);
        if (!isset($res['error'])) {
          if ($quote->isVirtual()) {
            
          } elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
            $result->setDuplicateBillingInfo(true);
          }
        }
      }
    } catch (Exception $e) {
      Mage::logException($e);
    }
    // *********************** billing address ***********************
    // ----------------------- shipping address ----------------------
    try {
      if (!$quote->isVirtual()) {
        if ((!$data['use_for_shipping'] || !isset($data['use_for_shipping']))) {
          $data = $request->getPost('shipping', array());
          if ($data) {
            $customerAddressId = $request->getPost('shipping_address_id', false);
            $res = $this->getOnepage()->saveShipping($data, $customerAddressId);
          }
        }
      }
    } catch (Exception $e) {
      Mage::logException($e);
    }
    // *********************** shipping address ***********************
    // ----------------------- shipping method -----------------------
    try {
      $shipping_method = $request->getPost('shipping_method', false);
      if ($shipping_method) {
        $res = $this->getOnepage()->saveShippingMethod($shipping_method);
        if (!$res) {
          Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method', array('request' => $this->getRequest(), 'quote' => $this->getOnepage()->getQuote()));
        }
      }
    } catch (Exception $e) {
      Mage::logException($e);
    }
    // *********************** shipping method **********************
    // ----------------------- payment method -----------------------
    try {
      $data = $request->getPost('payment', array());
      if ($data && isset($data['method'])) {
        $res = $this->getOnepage()->savePayment($data);
      }
    } catch (Exception $e) {
      Mage::logException($e);
    }
    $quote->getBillingAddress();
    $quote->getShippingAddress()->setCollectShippingRates(true);
    $quote->collectTotals();
    $quote->save();

    // *********************** payment method ***********************
    if (!$this->getOnepage()->getQuote()->isVirtual()) {
      $result->getUpdateSection()->setShippingMethod($this->_getShippingMethodsHtml());
    }
    $result->getUpdateSection()->setPaymentMethod($this->_getPaymentMethodsHtml());
    $result->getUpdateSection()->setReview($this->_getReviewHtml());

    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
  }

  /**
   * 
   * 
   * @return type
   */
  public function shippingMethodAction() {
    if ($this->_expireAjax() || !$this->getRequest()->isPost()) {
      return;
    }
    $checkoutUpdateSession = Ideasa_IdeCheckoutvm_CheckoutUpdateSection::getInstance();
    $result = Ideasa_IdeCheckoutvm_CheckoutResult::getInstance();
    $result->setUpdateSection($checkoutUpdateSession);

    $request = $this->getRequest();
    // ----------------------- shipping method -----------------------
    try {
      $data = $request->getPost('shipping_method', false);
      $res = $this->getOnepage()->saveShippingMethod($data);
      if (!$res) {
        Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method', array('request' => $this->getRequest(),
            'quote' => $this->getOnepage()->getQuote()));
        $this->getOnepage()->getQuote()->collectTotals()->save();
      }
      $result->getUpdateSection()->setShippingMethod($this->_getShippingMethodsHtml());
      $result->getUpdateSection()->setPaymentMethod($this->_getPaymentMethodsHtml());
    } catch (Exception $e) {
      Mage::logException($e);
    }
    // *********************** shipping method **********************
    // ----------------------- payment method -----------------------
    try {
      $data = $request->getPost('payment', array());
      if (isset($data['method'])) {
        $res = $this->getOnepage()->savePayment($data);
      }
    } catch (Exception $e) {
      Mage::logException($e);
    }
    $result->getUpdateSection()->setReview($this->_getReviewHtml());
    // *********************** payment method ***********************

    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
  }

  /**
   * 
   * 
   * @return type
   */
  public function paymentMethodAction() {
    if ($this->_expireAjax() || !$this->getRequest()->isPost()) {
      return;
    }
    $checkoutUpdateSession = Ideasa_IdeCheckoutvm_CheckoutUpdateSection::getInstance();
    $result = Ideasa_IdeCheckoutvm_CheckoutResult::getInstance();
    $result->setUpdateSection($checkoutUpdateSession);

    $request = $this->getRequest();
    // ----------------------- payment method -----------------------
    try {
      $payment_method = $request->getPost('payment_method');
      if (isset($payment_method)) {
        $quote = $this->getOnepage()->getQuote();
        $quote->getPayment()->setMethod($payment_method)->getMethodInstance();
        if ($quote->isVirtual()) {
          $quote->getBillingAddress()->setPaymentMethod(isset($payment_method) ? $payment_method : null);
        } else {
          $quote->getShippingAddress()->setPaymentMethod(isset($payment_method) ? $payment_method : null);
        }

        // shipping totals may be affected by payment method
        if (!$quote->isVirtual() && $quote->getShippingAddress()) {
          $quote->getShippingAddress()->setCollectShippingRates(true);
        }
        try {
          $quote->getPayment()->importData(array('method' => $payment_method));
        } catch (Exception $e) {
          
        }
        $quote->save();
      }
    } catch (Exception $e) {
      Mage::logException($e);
    }
    $result->getUpdateSection()->setReview($this->_getReviewHtml());
    // *********************** payment method ***********************

    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
  }

  /**
   * 
   * 
   * @return type
   */
  public function paymentAction() {
    if ($this->_expireAjax() || !$this->getRequest()->isPost()) {
      return;
    }
    $request = $this->getRequest();
    // ----------------------- payment method -----------------------
    try {
      $data = $request->getPost('payment', array());
      if (isset($data['method'])) {
        $quote = $this->getOnepage()->getQuote();
        $quote->getPayment()->setMethod($data['method'])->getMethodInstance();
        if ($quote->isVirtual()) {
          $quote->getBillingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
        } else {
          $quote->getShippingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
        }

        // shipping totals may be affected by payment method
        if (!$quote->isVirtual() && $quote->getShippingAddress()) {
          $quote->getShippingAddress()->setCollectShippingRates(true);
        }
        try {
          $quote->getPayment()->importData($data);
        } catch (Exception $e) {
          
        }
        $quote->save();
        sleep(5);
      }
    } catch (Exception $e) {
      
    }
  }

  /**
   * 
   * 
   * @return type
   */
  public function paymentAjaxAction() {
    if ($this->_expireAjax() || !$this->getRequest()->isPost()) {
      return;
    }
    $checkoutUpdateSession = Ideasa_IdeCheckoutvm_CheckoutUpdateSection::getInstance();
    $result = Ideasa_IdeCheckoutvm_CheckoutResult::getInstance();
    $result->setUpdateSection($checkoutUpdateSession);

    $request = $this->getRequest();
    // ----------------------- payment method -----------------------
    try {
      $data = $request->getPost('payment', array());
      if (isset($data['method'])) {
        $quote = $this->getOnepage()->getQuote();
        $quote->getPayment()->setMethod($data['method'])->getMethodInstance();
        if ($quote->isVirtual()) {
          $quote->getBillingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
        } else {
          $quote->getShippingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
        }

        // shipping totals may be affected by payment method
        if (!$quote->isVirtual() && $quote->getShippingAddress()) {
          $quote->getShippingAddress()->setCollectShippingRates(true);
        }
        try {
          $quote->getPayment()->importData($data);
        } catch (Exception $e) {
          
        }
        $quote->save();
      }
    } catch (Exception $e) {
      
    }
    $result->getUpdateSection()->setReview($this->_getReviewHtml());
    // *********************** payment method ***********************

    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
  }

  /**
   * Initialize coupon
   */
  public function couponAction() {
    $quote = $this->getOnepage()->getQuote();

    $checkoutUpdateSession = Ideasa_IdeCheckoutvm_CheckoutUpdateSection::getInstance();
    $result = Ideasa_IdeCheckoutvm_CheckoutResult::getInstance();
    $result->setUpdateSection($checkoutUpdateSession);
    $result->setError(true);

    if (!$quote->getItemsCount() || !$this->getRequest()->isPost()) {
      $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    $couponCode = (string) $this->getRequest()->getParam('coupon_code');
    if ($this->getRequest()->getParam('remove') == 1) {
      $couponCode = '';
    }
    $oldCouponCode = $quote->getCouponCode();

    if (!strlen($couponCode) && !strlen($oldCouponCode)) {
      return;
    }

    try {
      $quote->getShippingAddress()->setCollectShippingRates(true);
      $quote->setCouponCode(strlen($couponCode) ? $couponCode : '')->collectTotals()->save();

      if (strlen($couponCode)) {
        if ($couponCode == $quote->getCouponCode()) {
          $result->setSuccess(true);
          $result->setMessage($this->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode)));
        } else {
          $result->setError(true);
          $message = $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode));
          $result->setErrorMessage(Ideasa_IdeCheckoutvm_ErrorMessage::getInstance($message));
        }
      } else {
        $result->setSuccess(true);
        $result->setMessage($this->__('Coupon code was canceled.'));
      }
    } catch (Mage_Core_Exception $e) {
      $this->logger->error(Ideasa_IdeCheckoutvm_Exception::prepareQuoteForLog2($e, $this->getOnepage()->getQuote()));
      $result->setError(true);
      $result->setErrorMessage(Ideasa_IdeCheckoutvm_ErrorMessage::getInstance($e->getMessage()));
    } catch (Exception $e) {
      $this->logger->error(Ideasa_IdeCheckoutvm_Exception::prepareQuoteForLog2($e, $this->getOnepage()->getQuote()));
      $result->setError(true);
      $result->setErrorMessage(Ideasa_IdeCheckoutvm_ErrorMessage::getInstance($this->__('Cannot apply the coupon code.')));
    }
    if ($result->isSuccess()) {
      $result->getUpdateSection()->setShippingMethod($this->_getShippingMethodsHtml());
      $result->getUpdateSection()->setPaymentMethod($this->_getPaymentMethodsHtml());
      $result->getUpdateSection()->setCouponDiscount($quote->getCouponCode());
      $result->getUpdateSection()->setReview($this->_getReviewHtml());
    }

    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
  }

}
