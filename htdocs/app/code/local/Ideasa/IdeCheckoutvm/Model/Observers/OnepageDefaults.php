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

class Ideasa_IdeCheckoutvm_Model_Observers_OnepageDefaults extends Mage_Core_Model_Abstract {

    /**
     *
     * @var type
     */
    private $logger;

    /**
     *
     * @var type
     */
    protected $paymentMethods = array();

    /**
     * Class constructor
     * Set customer already exists message
     */
    public function __construct() {
        $this->logger = Ideasa_IdeCheckoutvm_Logger::getLogger(__CLASS__);
    }

    /**
     * Informa os valores default para o checkout.
     *
     * @param Varien_Event_Observer $observer
     * @return Ideasa_IdeCheckoutvm_Model_Observers_OnepageDefaults
     */
    public function defaultValues(Varien_Event_Observer $observer) {
        $this->setShippingDefaults($observer);
        $this->setPaymentDefaults($observer);

        return $this;
    }

    /**
     * 
     *
     * @param Varien_Event_Observer $observer
     * @return Ideasa_IdeCheckoutvm_Model_Observers_OnepageDefaults
     */
    public function setShippingDefaults(Varien_Event_Observer $observer) {
        $quote = $observer->getEvent()->getQuote();
        if (is_null($quote) || $quote->isVirtual()) {
            return $this;
        }
        $shippingMethod = $quote->getShippingAddress()->getShippingMethod();
        if (!empty($shippingMethod)) {
            return $this;
        }

        $defaultShippingMethod = Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::GERAL_SHIPPING_METHOD, $quote->getStore());
        if (empty($defaultShippingMethod)) {
            $this->logger->info('Método de Entrega default não configurado.');
        }
        
        $defaultCountry = Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::GERAL_COUNTRY, $quote->getStore());
        $defaultCountry = ($defaultCountry != '' ? $defaultCountry : 'BR');
        $address = $quote->getShippingAddress();
        $address->setCountryId($defaultCountry)->setCollectShippingrates(true);

        $shippingRates = $quote->getShippingAddress()->getGroupedAllShippingRates();
        if (empty($shippingRates)) {
            return $this;
        }
        $codes = array();
        foreach ($shippingRates as $rates) {
            foreach ($rates as $rate) {
                $codes[] = $rate->getCode();
            }
        }
        if (empty($codes)) {
            $this->logger->warn('Nenhum códigos para Método de Entrega encontrado.');
            return $this;
        }
        $totalCodes = (int) count($codes);
        if ($totalCodes === 1) {
            $quote->getShippingAddress()->setShippingMethod($defaultShippingMethod);
            return $this;
        }
        if (empty($shippingMethod) || !in_array($shippingMethod, $codes)) {
            if (in_array($defaultShippingMethod, $codes)) {
                $quote->getShippingAddress()->setShippingMethod($defaultShippingMethod);
            }
        }
        return $this;
    }

    /**
     * 
     *
     * @param Varien_Event_Observer $observer
     * @return Ideasa_IdeCheckoutvm_Model_Observers_OnepageDefaults
     */
    public function setPaymentDefaults(Varien_Event_Observer $observer) {
        return;

        $quote = $observer->getEvent()->getQuote();
        if (is_null($quote) || is_null($quote->getId())) {
            return $this;
        }

        $paymentMethod = $quote->getPayment()->getMethod();
        if (!empty($paymentMethod)) {
            return $this;
        }
        $defaultPaymentMethod = Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::GERAL_PAYMENT_METHOD, $quote->getStore());
        if (empty($defaultPaymentMethod)) {
            $this->logger->info('Método de Pagamento default não configurado.');
            return $this;
        }

        $codes = Mage::helper('idecheckoutvm/quote')->getPaymentMethods($quote);
        if (empty($codes) || !is_object($quote) || !$quote->getGrandTotal()) {
            return $this;
        }
        $totalPaymentMethod = (int) count($codes);
        if ($totalPaymentMethod === 1) {
            $defaultPaymentMethod = current($codes);
        }

        if (!empty($codes) && (empty($paymentMethod) || !in_array($paymentMethod, $codes))) {
            if (in_array($defaultPaymentMethod, $codes)) {
                if (Mage::getStoreConfig('payment/' . $defaultPaymentMethod . '/active', $quote->getStore())) {
                    if ($quote->isVirtual()) {
                        $quote->getBillingAddress()->setPaymentMethod($defaultPaymentMethod);
                    } else {
                        $quote->getShippingAddress()->setPaymentMethod($defaultPaymentMethod);
                    }
                    try {
                        $quote->getPayment()->setMethod($defaultPaymentMethod)->getMethodInstance();
                    } catch (Exception $e) {
                        $this->logger->error(Ideasa_IdeCheckoutvm_Exception::prepareQuoteForLog($e));
                    }
                }
            }
        }

        return $this;
    }

}