<?php

/**
* 
* Checkout Venda Mais para Magento
* 
* @category     Checkout Venda Mais
* @packages     IdeAddons
* @copyright    Copyright (c) 2013 Checkout Venda Mais (http://www.checkoutvendamais.com.br)
* @version      1.2.0
* @license      http://www.checkoutvendamais.com.br/magento/licenca
*
*/

class Ideasa_IdeAddons_Exception extends Exception {

    public function __construct($message) {
        parent::__construct($message, null, null);
    }

    /**
     * Prepara informações do Quote para serem logadas.
     * 
     * @param Mage_Sales_Model_Quote $quote
     * @return type String
     */
    public function prepareQuoteForLog(Mage_Sales_Model_Quote $quote) {
        $shippingMethod = '';
        if ($shippingInfo = $quote->getShippingAddress()->getShippingMethod()) {
            $data = explode('_', $shippingInfo);
            $shippingMethod = $data[0];
        }

        $paymentMethod = '';
        if ($paymentInfo = $quote->getPayment()) {
            $paymentMethod = $paymentInfo->getMethod();
        }

        $items = '';
        foreach ($quote->getAllVisibleItems() as $_item) {
            $items .= $_item->getProduct()->getName() . '  x ' . $_item->getQty() . '  '
                    . $quote->getStoreCurrencyCode() . ' '
                    . $_item->getProduct()->getFinalPrice($_item->getQty()) . "\n";
        }
        $total = $quote->getStoreCurrencyCode() . ' ' . $quote->getGrandTotal();

        $log = array(
            'Erro' => $this->__toString(),
            'Mensagem       ' => $this->getMessage(),
            'Store          ' => $quote->getStoreId(),
            'SateAndTime    ' => Mage::app()->getLocale()->date(),
            'Customer       ' => $quote->getCustomerFirstname() . ' ' . $quote->getCustomerLastname(),
            'CustomerEmail  ' => $quote->getCustomerEmail(),
            'BillingAddress ' => $quote->getBillingAddress(),
            'ShippingAddress' => $quote->getShippingAddress(),
            'ShippingMethod ' => Mage::getStoreConfig('carriers/' . $shippingMethod . '/title'),
            'PaymentMethod  ' => Mage::getStoreConfig('payment/' . $paymentMethod . '/title'),
            'Total          ' => $total,
            'Items          ' => $items
        );
        $line = null;
        foreach ($log as $key => $value) {
            if ($value instanceof Mage_Sales_Model_Quote_Address) {
                $value = $value->getName();
            }
            $line .= "$key => $value\n";
        };

        return $line;
    }

    /**
     * Prepara informações do Quote para serem logadas.
     * 
     * @param Exception $exception
     * @param Mage_Sales_Model_Quote $quote
     * @return String
     */
    public static function prepareQuoteForLog2(Exception $exception, Mage_Sales_Model_Quote $quote) {
        $exception = new Ideasa_IdeAddons_Exception($exception->getMessage());
        return $exception->prepareQuoteForLog($quote);
    }

}