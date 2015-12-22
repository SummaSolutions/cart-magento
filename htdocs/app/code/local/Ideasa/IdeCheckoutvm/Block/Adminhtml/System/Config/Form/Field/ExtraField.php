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

class Ideasa_IdeCheckoutvm_Block_Adminhtml_System_Config_Form_Field_ExtraField extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract {

    public function __construct() {
        parent::__construct();
    }

    protected function _prepareToRender() {
        $this->addColumn('label', array(
            'label' => Mage::helper('idecheckoutvm')->__('Field Label'),
            'style' => 'width:130px',
            'class' => 'required-entry'
        ));
        $this->addColumn('id', array(
            'label' => Mage::helper('idecheckoutvm')->__('Field Id'),
            'style' => 'width:130px',
            'class' => 'required-entry validate-code'
        ));
        $this->addColumn('option', array(
            'label' => Mage::helper('idecheckoutvm')->__('Field Option'),
            'style' => 'width:35px',
            'class' => 'required-entry validate-alpha idecheckoutvm-custom-field-option'
        ));
        $this->addColumn('order', array(
            'label' => Mage::helper('idecheckoutvm')->__('Field Order'),
            'style' => 'width:35px',
            'class' => 'required-entry validate-number'
        ));
        $this->_addAfter = false;
    }

}