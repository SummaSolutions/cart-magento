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

class Ideasa_IdeCheckoutvm_Block_Widget_Taxvat extends Mage_Customer_Block_Widget_Taxvat {

    public function _construct() {
        parent::_construct();
    }

    public function printTaxvat() {
        $map = Ideasa_Data_Config::getCustomerField('taxvat');
        $input = new Ideasa_Data_Form_Text(array('name' => $this->getFieldName('taxvat'),
                    'required' => $this->isRequired(),
                    'title' => $map['title'],
                    'label' => $map['label'],
                    'value' => $this->htmlEscape($this->getTaxvat()),
                ));
        $input->setId($this->getFieldId('taxvat'));
        $input->setValue($this->htmlEscape($this->getTaxvat()));
        $input->setForm($this->getForm());

        $div = new Ideasa_Data_Form_Div('taxvat', $input, array('id' => $input->getId()));
        echo $div->toHtml();
    }

}