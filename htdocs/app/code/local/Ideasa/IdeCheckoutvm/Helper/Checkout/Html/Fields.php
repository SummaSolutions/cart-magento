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

class Ideasa_IdeCheckoutvm_Helper_Checkout_Html_Fields extends Mage_Core_Helper_Abstract {

    /**
     * 
     * @var type
     */
    private $fields = array();

    /**
     * Indica se os campos já foram ordenados.
     * 
     * @var type
     */
    private $sorted = false;

    public function _construct() {
        parent::_construct();
    }

    public function getFields() {
        $this->_sort();
        return $this->fields;
    }

    /**
     * Ordena os campos pela chave.
     */
    protected function _sort() {
        $this->fields = Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::ORDERING_FIELDS);
        $extraFields = Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::EXTRA_FILEDS);
        if ($extraFields != null) {
            $extraFields = unserialize($extraFields);
        }

        if ($extraFields != null) {
            foreach ($extraFields as $key => $value) {
                $this->fields[$value['id']] = $value['order'];
            }
        }
        $this->fields = array_flip(array_unique($this->fields));
        ksort($this->fields);
        $this->sorted = true;
    }

}