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

class Ideasa_IdeCheckoutvm_Helper_Url extends Mage_Checkout_Helper_Url {

    public function getCheckoutUrl() {
        return $this->_getUrl('idecheckoutvm', array('_secure' => true));
    }

}