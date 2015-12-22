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

class Ideasa_IdeCheckoutvm_Model_Customer_Attribute_Source_Pessoa_View extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions() {
        if (is_null($this->_options)) {
            $this->_options = array(
                array(
                    'label' => Mage::helper('idecheckoutvm')->__('Pessoa Física'),
                    'value' => 'F'
                ),
                array(
                    'label' => Mage::helper('idecheckoutvm')->__('Pessoa Jurídica'),
                    'value' => 'J'
                ),
            );
        }
        return $this->_options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray() {
        $_options = array();
        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }

    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string
     */
    public function getOptionText($value) {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }

}