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

class Ideasa_IdeCheckoutvm_Block_Onepage_Link extends Mage_Core_Block_Template {

    public function isPossibleCheckoutvm() {
        return $this->helper('idecheckoutvm')->isCheckoutEnabled();
    }

    public function checkEnable() {
        return Mage::getSingleton('checkout/session')->getQuote()->validateMinimumAmount();
    }

    public function getCheckoutvmUrl() {
        return $this->getUrl('idecheckoutvm', array('_secure' => true));
    }

}