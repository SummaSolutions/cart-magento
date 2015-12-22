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

class Ideasa_Data_Form_Select extends Varien_Data_Form_Element_Abstract {

    public function __construct($attributes=array()) {
        if($attributes['title']) {
           $attributes['title'] = Mage::helper('core')->__($attributes['title']);
        }
        parent::__construct($attributes);
        $this->setType('select');
        $this->setExtType('combobox');
        $this->_prepareOptions();
    }

    public function getElementHtml() {
        $this->addClass('select');
        $html = '<select id="' . $this->getHtmlId() . '" name="' . $this->getName() . '" ' . $this->serialize($this->getHtmlAttributes()) . '>' . "\n";

        $value = $this->getValue();
        if (!is_array($value)) {
            $value = array($value);
        }

        if ($values = $this->getValues()) {
            foreach ($values as $key => $option) {
                if (!is_array($option)) {
                    $html.= $this->_optionToHtml(array(
                        'value' => $key,
                        'label' => $option), $value
                    );
                } elseif (is_array($option['value'])) {
                    $html.='<optgroup label="' . $option['label'] . '">' . "\n";
                    foreach ($option['value'] as $groupItem) {
                        $html.= $this->_optionToHtml($groupItem, $value);
                    }
                    $html.='</optgroup>' . "\n";
                } else {
                    $html.= $this->_optionToHtml($option, $value);
                }
            }
        }

        $html.= '</select>' . "\n";
        $html.= $this->getAfterElementHtml();
        return $html;
    }

    protected function _optionToHtml($option, $selected) {
        if (is_array($option['value'])) {
            $html = '<optgroup label="' . $option['label'] . '">' . "\n";
            foreach ($option['value'] as $groupItem) {
                $html .= $this->_optionToHtml($groupItem, $selected);
            }
            $html .='</optgroup>' . "\n";
        } else {
            $html = '<option value="' . $this->_escape($option['value']) . '"';
            $html.= isset($option['title']) ? 'title="' . $this->_escape($option['title']) . '"' : '';
            $html.= isset($option['style']) ? 'style="' . $option['style'] . '"' : '';
            if (in_array($option['value'], $selected)) {
                $html.= ' selected="selected"';
            }
            $html.= '>' . $this->_escape($option['label']) . '</option>' . "\n";
        }
        return $html;
    }

    protected function _prepareOptions() {
        $values = $this->getValues();
        if (empty($values)) {
            $options = $this->getOptions();
            if (is_array($options)) {
                $values = array();
                foreach ($options as $value => $label) {
                    $values[] = array('value' => $value, 'label' => $label);
                }
            } elseif (is_string($options)) {
                $values = array(array('value' => $options, 'label' => $options));
            }
            $this->setValues($values);
        }
    }

    public function getHtmlAttributes() {
        return array('title', 'class', 'style', 'onclick', 'onchange', 'disabled', 'readonly', 'tabindex');
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