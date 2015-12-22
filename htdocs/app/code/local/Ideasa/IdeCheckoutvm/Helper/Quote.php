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

class Ideasa_IdeCheckoutvm_Helper_Quote extends Mage_Core_Helper_Abstract {

    /**
     * 
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return array
     */
    public function getPaymentMethods(Mage_Sales_Model_Quote $quote) {
        $methods = array();
        $store = $quote ? $quote->getStoreId() : null;
        $methodInstances = Mage::helper('payment')->getStoreMethods($store, $quote);
        $total = $quote->getGrandTotal();
        foreach ($methodInstances as $key => $method) {
            if ($this->canUsePaymentMethod($method, $quote)
                    && ($total != 0
                    || $method->getCode() == 'free'
                    || ($quote->hasRecurringItems() && $method->canManageRecurringProfiles()))) {
                $methods[] = $method->getCode();
            } else {
                unset($methods[$key]);
            }
        }

        return $methods;
    }

    /**
     * Check if method can be used
     *
     * @param string $method
     * @param object $quote
     * @return boolean
     */
    public function canUsePaymentMethod($method, $quote) {
        if (!$method->canUseForCountry($quote->getBillingAddress()->getCountry())) {
            return false;
        }
        if (method_exists($method, 'canUseForCurrency') && !$method->canUseForCurrency(Mage::app()->getStore()->getBaseCurrencyCode())) {
            return false;
        }

        /**
         * Checking for min/max order total for assigned payment method
         */
        $total = $quote->getBaseGrandTotal();
        $minTotal = $method->getConfigData('min_order_total');
        $maxTotal = $method->getConfigData('max_order_total');

        if ((!empty($minTotal) && ($total < $minTotal)) || (!empty($maxTotal) && ($total > $maxTotal))) {
            return false;
        }
        return true;
    }

}