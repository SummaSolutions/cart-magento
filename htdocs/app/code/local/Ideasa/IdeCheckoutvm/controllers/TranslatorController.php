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

class Ideasa_IdeCheckoutvm_TranslatorController extends Mage_Checkout_Controller_Action {

    public function indexAction() {
        $helper = Mage::helper('idecheckoutvm');
        
        $result['street_address_2'] = $helper->__('Street Address_2');
        $result['street_address_3'] = $helper->__('Street Address_3');
        $result['street_address_4'] = $helper->__('Street Address_4');
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
}