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

class Ideasa_IdeCheckoutvm_Block_Widget_Address_Suffix extends Mage_Core_Block_Template {

    /**
     *
     * @var type Ideasa_IdeCheckoutvm_Block_Widget_Customer_Address
     */
    private $addressBlock;

    public function _construct() {
        parent::_construct();
    }

    public function setAddressBlock($block) {
        $this->addressBlock = $block;
    }

    public function getAddressBlock() {
        return $this->addressBlock;
    }

    protected function _toHtml() {
    }

    public function makeInput() {
        $map = Ideasa_Data_Config::getCustomerField('suffix');
        $map['required'] = $this->addressBlock->isSuffixRequired();
        $map['id'] = $this->addressBlock->getFieldId('suffix');
        $map['name'] = $this->addressBlock->getFieldName('suffix');

        if ($this->addressBlock->getSuffixOptions() === false) {
            $input = new Ideasa_Data_Form_Text($map);
        } else {
            $map['values'] = $this->prepareOptions();
            $input = new Ideasa_Data_Form_Select($map);
        }
        $input->setId($map['id']);
        $input->setForm($this->addressBlock->getForm());

        $line = new Ideasa_Data_Form_Div('suffix', $input, null);
        $line->setId($map['id']);

        return $line;
    }

    protected function prepareOptions() {
        $options = array();
        foreach ($this->addressBlock->getSuffixOptions() as $value) {
            $options[] = array('value' => $value, 'label' => $value);
        }

        return $options;
    }

}