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

class Ideasa_IdeCheckoutvm_Helper_Account extends Mage_Core_Helper_Abstract {

  /**
   *
   * @return type
   */
  public function isRequiredRegistration() {
    return (bool) (Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::REGISTRATION_MODE) == Ideasa_IdeCheckoutvm_Model_System_Config_Source_Registration::REQUIRE_REGISTRATION);
  }

  /**
   *
   * @return type
   */
  public function isDisableRegistration() {
    return (bool) (Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::REGISTRATION_MODE) == Ideasa_IdeCheckoutvm_Model_System_Config_Source_Registration::DISABLE_REGISTRATION);
  }

  /**
   *
   * @return type
   */
  public function isAllowGuest() {
    return (bool) (Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::REGISTRATION_MODE) == Ideasa_IdeCheckoutvm_Model_System_Config_Source_Registration::ALLOW_GUEST);
  }

  /**
   *
   * @return type
   */
  public function isRegistrationSuccess() {
    return (bool) (Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::REGISTRATION_MODE) == Ideasa_IdeCheckoutvm_Model_System_Config_Source_Registration::REGISTRATION_SUCCESS);
  }

  /**
   *
   * @return type
   */
  public function isAutoGenerateAccount() {
    return (bool) (Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::REGISTRATION_MODE) == Ideasa_IdeCheckoutvm_Model_System_Config_Source_Registration::AUTO_GENERATE_ACCOUNT);
  }

  public function isLoginLinkEnable() {
    return (bool) Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::LOGIN_LINK);
  }

  public function isShowLoginLink() {
    $helper = Mage::helper('idecheckoutvm/account');
    if ($helper->isDisableRegistration()) {
      return false;
    }
    if ($this->isLoginLinkEnable() && !Mage::getSingleton('customer/session')->isLoggedIn()) {
      return true;
    }

    return false;
  }

  public function logAccountInformation($local) {
    $customerSession = Mage::getSingleton('customer/session');
    $headers = $this->getHeaders();

    $log = array(
        'Local' => $local,
        'Client' => ($customerSession->isLoggedIn() ? $customerSession->getCustomer()->getEmail() : 'Não identificado'),
        'Session[frontend]' => Mage::getSingleton("core/session", array('name' => 'frontend'))->getSessionId(),
        'User-Agent' => $headers['User-Agent'],
    );
    $text = null;
    foreach ($log as $key => $value) {
      $text .= "\t$key => $value\n";
    };

    return "\n" . $text;
  }

  public function getHeaders() {
    if (!function_exists('getallheaders')) {
      $headers = '';
      foreach ($_SERVER as $name => $value) {
        if (substr($name, 0, 5) == 'HTTP_') {
          $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
        }
      }
      return $headers;
    } else {
      return getallheaders();
    }
  }

}
