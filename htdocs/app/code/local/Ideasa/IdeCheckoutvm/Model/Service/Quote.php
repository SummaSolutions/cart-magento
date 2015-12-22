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

class Ideasa_IdeCheckoutvm_Model_Service_Quote extends Mage_Sales_Model_Service_Quote {

    /**
     * 
     * 
     * @return Ideasa_IdeCheckoutvm_Model_Service_Quote 
     */
    protected function _validate() {
        $helper = Mage::helper('sales');
        if (!$this->getQuote()->isVirtual()) {
            $address = $this->getQuote()->getShippingAddress();
            $addrValidator = Mage::getSingleton('idecheckoutvm/type_onepage')->validateAddress($address);
            if ($addrValidator !== true) {
                Ideasa_IdeCheckoutvm_ValidatorException::throwException($helper->__('Please check shipping address information.'), $addrValidator);
            }

            $shipMethod = $address->getShippingMethod();
            $rate = $address->getShippingRateByCode($shipMethod);
            if (!$this->getQuote()->isVirtual() && (!$shipMethod || !$rate)) {
                Ideasa_IdeCheckoutvm_BusinessException::throwException($helper->__('Please specify a shipping method.'));
            }

            $addrValidator = Mage::getSingleton('idecheckoutvm/type_onepage')->validateAddress($this->getQuote()->getBillingAddress());
            if ($addrValidator !== true) {
                Ideasa_IdeCheckoutvm_ValidatorException::throwException($helper->__('Please check billing address information.'), $addrValidator);
            }
            if (!($this->getQuote()->getPayment()->getMethod())) {
                Ideasa_IdeCheckoutvm_BusinessException::throwException($helper->__('Please select a valid payment method.'));
            }

            return $this;
        }
    }

}