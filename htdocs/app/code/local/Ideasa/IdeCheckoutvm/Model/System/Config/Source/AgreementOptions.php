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

class Ideasa_IdeCheckoutvm_Model_System_Config_Source_AgreementOptions {

    public function toOptionArray() {
        $helper = Mage::helper('checkout');
        $options = array(
            array('value' => 'screen', 'label' => $helper->__('Visualizar na tela')),
            array('value' => 'lightbox', 'label' => $helper->__('Visualizar em lightbox'))
        );
        return $options;
    }

}