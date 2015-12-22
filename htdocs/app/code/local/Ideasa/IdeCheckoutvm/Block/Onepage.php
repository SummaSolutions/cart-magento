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

class Ideasa_IdeCheckoutvm_Block_Onepage extends Mage_Checkout_Block_Onepage {

    public function _construct() {
        parent::_construct();
    }

    public function getTitle() {
        return Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::GERAL_TITLE);
    }

    public function hasTitle() {
        return strlen($this->getTitle());
    }

}