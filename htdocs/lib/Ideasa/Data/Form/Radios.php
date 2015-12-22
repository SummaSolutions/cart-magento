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

class Ideasa_Data_Form_Radios extends Varien_Data_Form_Element_Abstract {

    public function __construct($attributes=array()) {
        if ($attributes['title']) {
            $attributes['title'] = Mage::helper('core')->__($attributes['title']);
        }
        parent::__construct($attributes);
        $this->setType('radios');
    }

    public function getDefaultHtml() {
        $html = $this->getData('default_html');
        if (is_null($html)) {
            $html = $this->getLabelHtml();
            //$html.= '<div class="input-box">' . "\n";
            $html.= $this->getElementHtml();
            //$html.= '</div>' . "\n";
        }
        return $html;
    }

    public function getSeparator() {
        $separator = $this->getData('separator');
        if (is_null($separator)) {
            $separator = '&nbsp;';
        }
        return $separator;
    }

    public function getElementHtml() {
        $html = '';
        $value = $this->getValue();
        if ($values = $this->getValues()) {
            foreach ($values as $option) {
                $html.= $this->_optionToHtml($option, $value);
            }
        }
        $html.= $this->getAfterElementHtml();
        return $html;
    }

    protected function _optionToHtml($option, $selected) {
        $html = '<div class="'. $option['value'] .'">' . "\n";
        $html .= '<input type="radio"' . $this->serialize(array('name', 'class', 'style'));
        if (is_array($option)) {
            $html.= 'value="' . $this->_escape($option['value']) . '"  id="' . $this->getHtmlId() . $option['value'] . '"';
            if ($option['value'] == $selected) {
                $html.= ' checked="checked"';
            }
            $html.= ' />';
            $html.= '<label class="inline" for="' . $this->getHtmlId() . $option['value'] . '">' . $option['label'] . '</label>';
        } elseif ($option instanceof Varien_Object) {
            $html.= 'id="' . $this->getHtmlId() . $option->getValue() . '"' . $option->serialize(array('label', 'title', 'value', 'class', 'style'));
            if (in_array($option->getValue(), $selected)) {
                $html.= ' checked="checked"';
            }
            $html.= ' />';
            $html.= '<label class="inline" for="' . $this->getHtmlId() . $option->getValue() . '">' . $option->getLabel() . '</label>';
        }
        $html.= $this->getSeparator() . "\n";
        $html.= '</div>' . "\n";
        return $html;
    }

}