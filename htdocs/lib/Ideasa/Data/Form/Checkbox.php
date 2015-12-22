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

class Ideasa_Data_Form_Checkbox extends Varien_Data_Form_Element_Abstract {

    public function __construct($attributes=array()) {
        if($attributes['title']) {
           $attributes['title'] = Mage::helper('core')->__($attributes['title']);
        }
        parent::__construct($attributes);
        $this->setType('checkbox');
        $this->setExtType('checkbox');
    }

    /**
     * Render HTML for element's label
     *
     * @param string $idSuffix
     * @return string
     */
    public function getLabelHtml($idSuffix = '') {
        if (!is_null($this->getLabel())) {
            $html = '<label' . ( $this->getRequired() ? ' class="required"' : '' ) . ' for="' . $this->getHtmlId() . $idSuffix . '">' 
                    . ( $this->getRequired() ? ' <em>*</em>' : '' ) . $this->_escape(Mage::helper('core')->__($this->getLabel())) . 
                    '</label>' . "\n";
        } else {
            $html = '';
        }
        return $html;
    }

    public function getDefaultHtml() {
        $html = $this->getData('default_html');
        if (is_null($html)) {
            $html = $this->getLabelHtml();
            $html.= '<div class="input-box">' . "\n";
            $html.= $this->getElementHtml();
            $html.= '</div>' . "\n";
        }
        return $html;
    }

}