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

class Ideasa_IdeCheckoutvm_LoginController extends Mage_Checkout_Controller_Action {

  private $logger;

  /**
   * Initialize
   */
  protected function _construct() {
    $this->logger = Ideasa_IdeCheckoutvm_Logger::getLogger(__CLASS__);
  }

  public function preDispatch() {
    parent::preDispatch();
    $this->_preDispatchValidateCustomer();
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

  public function getOnepage() {
    return Mage::getSingleton('idecheckoutvm/type_onepage');
  }

  public function forgotpasswordAction() {
    $session = Mage::getSingleton('customer/session');
    if ($this->_expireAjax() || $session->isLoggedIn()) {
      return;
    }
    $result = Ideasa_IdeCheckoutvm_CheckoutResult::getInstance();
    $result->setUpdateSection(Ideasa_IdeCheckoutvm_CheckoutUpdateSection::getInstance());
    $result->setSuccess(false);

    try {
      if ($this->getRequest()->isPost()) {
        $loginData = $this->getRequest()->getPost('forgotpassword');
        $email = $loginData['username'];

        $errors = array();
        if (!Zend_Validate::is($email, 'NotEmpty')) {
          $errors[] = Ideasa_IdeCheckoutvm_ErrorField::getInstance('forgotpassword:username', Mage::helper('customer')->__('Please enter your email.'));
        }
        if (!Zend_Validate::is($email, 'EmailAddress')) {
          $session->setForgottenEmail($email);
          $errors[] = Ideasa_IdeCheckoutvm_ErrorField::getInstance('forgotpassword:username', Mage::helper('customer')->__('Invalid email address.'));
        }
        if (count($errors) > 0) {
          Ideasa_IdeCheckoutvm_ValidatorException::throwException(Mage::helper('idecheckoutvm')->__('Please check login information.'), $errors);
        } else {
          $customer = Mage::getModel('customer/customer')->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($email);
          if (!$customer->getId()) {
            $session->setForgottenEmail($email);
            $message = Mage::helper('customer')->__('This email address was not found in our records.');
            $result->setErrorMessage(Ideasa_IdeCheckoutvm_ErrorMessage::getInstance($message));
          } else {
            try {
              $newPass = $customer->generatePassword();
              $customer->changePassword($newPass, false);
              $customer->sendPasswordReminderEmail();
              $result->setSuccess(true);
              $result->setMessage(Mage::helper('customer')->__('A new password has been sent.'));
            } catch (Exception $e) {
              $result->setErrorMessage(Ideasa_IdeCheckoutvm_ErrorMessage::getInstance($e->getMessage()));
            }
          }
        }
      }
    } catch (Ideasa_IdeCheckoutvm_Exception $e) {
      $this->logger->warn($e->prepareQuoteForLog($this->getOnepage()->getQuote()));
      $result->setError(true);
      if ($e instanceof Ideasa_IdeCheckoutvm_ValidatorException) {
        $result->setErrorMessage(Ideasa_IdeCheckoutvm_ErrorMessage::getInstance($e->getMessage(), $e->getErrors()));
      } else if ($e instanceof Ideasa_IdeCheckoutvm_BusinessException) {
        $result->setErrorMessage(Ideasa_IdeCheckoutvm_ErrorMessage::getInstance($e->getMessage()));
      }
    }

    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
  }

  protected function validateLogin($username, $password) {
    $errors = array();
    if (!Zend_Validate::is($username, 'NotEmpty')) {
      $errors[] = Ideasa_IdeCheckoutvm_ErrorField::getInstance('login:username', Mage::helper('customer')->__('Login is required.'));
    }
    if (!Zend_Validate::is($password, 'NotEmpty')) {
      $errors[] = Ideasa_IdeCheckoutvm_ErrorField::getInstance('login:password', Mage::helper('customer')->__('Password are required.'));
    }

    return $errors;
  }

  public function indexAction() {
    $session = Mage::getSingleton('customer/session');
    if ($this->_expireAjax() || $session->isLoggedIn()) {
      return;
    }
    $this->logger->info(Mage::helper('idecheckoutvm/account')->logAccountInformation('Início da autenticação do cliente.'));
    $result = Ideasa_IdeCheckoutvm_CheckoutResult::getInstance();
    $result->setUpdateSection(Ideasa_IdeCheckoutvm_CheckoutUpdateSection::getInstance());
    $result->setSuccess(false);

    try {
      if ($this->getRequest()->isPost()) {
        $loginData = $this->getRequest()->getPost('login');
        $validateRes = $this->validateLogin($loginData['username'], $loginData['password']);
        if (count($validateRes) > 0) {
          Ideasa_IdeCheckoutvm_ValidatorException::throwException(Mage::helper('idecheckoutvm')->__('Please check login information.'), $validateRes);
        } else {
          try {
            $session->login($loginData['username'], $loginData['password']);
            $result->setSuccess(true);
          } catch (Mage_Core_Exception $e) {
            switch ($e->getCode()) {
              case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                $message = Mage::helper('customer')->__('Email is not confirmed. <a href="%s">Resend confirmation email.</a>', Mage::helper('customer')->getEmailConfirmationUrl($loginData['username']));
                break;
              default:
                $message = $e->getMessage();
            }
            $result->setErrorMessage(Ideasa_IdeCheckoutvm_ErrorMessage::getInstance($message));
            $session->setUsername($loginData['username']);
          }
        }
      }
    } catch (Ideasa_IdeCheckoutvm_Exception $e) {
      $this->logger->warn($e->prepareQuoteForLog($this->getOnepage()->getQuote()));
      $result->setError(true);
      if ($e instanceof Ideasa_IdeCheckoutvm_ValidatorException) {
        $result->setErrorMessage(Ideasa_IdeCheckoutvm_ErrorMessage::getInstance($e->getMessage(), $e->getErrors()));
      } else if ($e instanceof Ideasa_IdeCheckoutvm_BusinessException) {
        $result->setErrorMessage(Ideasa_IdeCheckoutvm_ErrorMessage::getInstance($e->getMessage()));
      }
    }
    $this->logger->info(Mage::helper('idecheckoutvm/account')->logAccountInformation('Final da autenticação do cliente.'));

    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
  }

  public function checkEmailAction() {
    $result = Ideasa_IdeCheckoutvm_CheckoutResult::getInstance();
    $result->setUpdateSection(Ideasa_IdeCheckoutvm_CheckoutUpdateSection::getInstance());
    $result->setSuccess(false);

    try {
      if ($this->getRequest()->isPost()) {
        $validator = new Zend_Validate_EmailAddress();
        $email = $this->getRequest()->getPost('email', false);

        if ($email && $email != '') {
          if (!$validator->isValid($email)) {
            $result->setMessage('invalid');
          } else {
            if ($this->_isEmailRegistered($email)) {
              $result->setMessage('exists');
              $result->setErrorMessage(Mage::helper('checkout')->__('There is already a customer registered using this email address. Please login using this email address or enter a different email address to register your account.'));
            } else {
              $result->setSuccess(true);
            }
          }
        }
      }
    } catch (Ideasa_IdeCheckoutvm_Exception $e) {
      $this->logger->warn($e->prepareQuoteForLog($this->getOnepage()->getQuote()));
      $result->setError(true);
      if ($e instanceof Ideasa_IdeCheckoutvm_ValidatorException) {
        $result->setErrorMessage(Ideasa_IdeCheckoutvm_ErrorMessage::getInstance($e->getMessage(), $e->getErrors()));
      } else if ($e instanceof Ideasa_IdeCheckoutvm_BusinessException) {
        $result->setErrorMessage(Ideasa_IdeCheckoutvm_ErrorMessage::getInstance($e->getMessage()));
      }
    }

    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
  }

  protected function _isEmailRegistered($email) {
    $model = Mage::getModel('customer/customer');
    $model->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($email);

    if ($model->getId() == null) {
      return false;
    }

    return true;
  }

}
