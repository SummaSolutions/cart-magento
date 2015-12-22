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

class Ideasa_IdeCheckoutvm_Block_Onepage_Shipping extends Ideasa_IdeCheckoutvm_Block_Onepage_Abstract {

    private $shippingBlock = null;

    public function _construct() {
        parent::_construct();
    }

    protected function loadDependencies() {
        parent::loadDependencies();

        // ordenação dos campos
        $this->sortedFields = Mage::helper('idecheckoutvm/checkout_html_fields')->getFields();

        /**
         * blocos
         */
        $layout = Mage::getSingleton('core/layout');
        $this->shippingBlock = $layout->createBlock('checkout/onepage_shipping');

        $this->nameBlock = $layout->createBlock('customer/widget_name');

        $this->addressBlock = $layout->createBlock('idecheckoutvm/widget_address_address');
        $this->addressBlock->setForm($this->form);
        $this->addressBlock->setAddressType('shipping');
        $this->addressBlock->setFieldIdFormat('shipping:%s')->setFieldNameFormat('shipping[%s]');
        $this->addressBlock->setObject($this->getShippingAddress());
        $this->addressBlock->setCountryOptions($this->getCountryOptions());
    }

    public function getShippingAddress() {
        return $this->getQuote()->getShippingAddress();
    }

    public function printEmail() {
        
    }

    public function printDob() {
        
    }

    public function printGender() {
        
    }

    public function printTaxvat() {
        
    }

    public function printExtraField($id) {
        
    }

    public function printCompany() {
        
    }

    public function printTipoPessoa() {
        
    }

    public function printRg() {
        
    }

    public function printInscEst() {
        
    }

    public function printRazaoSocial() {
        
    }

    public function printNomeFantasia() {
        
    }

    public function getAddress() {
        return $this->shippingBlock->getAddress();
    }

}