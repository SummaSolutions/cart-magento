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

class Ideasa_IdeCheckoutvm_Block_Onepage_Ssl extends Mage_Core_Block_Template {

  protected function _construct() {
    parent::_construct();
  }

  public function isShowSslImage() {
    return (Mage::getStoreConfigFlag(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::SHOW_GOOGLE_SAFE) || Mage::getStoreConfigFlag(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::SHOW_SSL));
  }

  public function isShowGoogleSafe() {
    return Mage::getStoreConfigFlag(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::SHOW_GOOGLE_SAFE);
  }

  public function isShowSsl() {
    return Mage::getStoreConfigFlag(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::SHOW_SSL);
  }

}
