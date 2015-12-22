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

class Ideasa_IdeCheckoutvm_Model_ExtraField extends Mage_Core_Model_Abstract {

    /**
     * 
     * 
     * @var type
     */
    private $logger;

    protected function _construct() {
        $this->logger = Ideasa_IdeCheckoutvm_Logger::getLogger(__CLASS__);

        $this->_init('idecheckoutvm/checkout_extrafield');
    }

}