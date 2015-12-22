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

class Ideasa_IdeCheckoutvm_Model_System_Config_Source_Nooptreq {

    public function toOptionArray() {
        $helper = Mage::helper('idecheckoutvm');
        return array(
            array('value' => 'opt', 'label' => $helper->__('Optional')),
            array('value' => 'req', 'label' => $helper->__('Required')),
        );
    }

}