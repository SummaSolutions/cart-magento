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

class Ideasa_Data_Form_Link extends Varien_Data_Form_Element_Abstract {

    public function __construct($attributes=array()) {
        parent::__construct($attributes);
        $this->setType('link');
    }

    /**
     * Generates element html
     *
     * @return string
     */
    public function getElementHtml() {
        $html = $this->getBeforeElementHtml();
        $html .= '<a id="' . $this->getHtmlId() . '" ' . $this->serialize($this->getHtmlAttributes()) . '>' . $this->getEscapedValue() . "</a>\n";
        $html .= $this->getAfterElementHtml();
        return $html;
    }

    /**
     * Prepare array of anchor attributes
     *
     * @return array
     */
    public function getHtmlAttributes() {
        return array('charset', 'coords', 'href', 'hreflang', 'rel', 'rev', 'name',
            'shape', 'target', 'accesskey', 'class', 'dir', 'lang', 'style',
            'tabindex', 'title', 'xml:lang', 'onblur', 'onclick', 'ondblclick',
            'onfocus', 'onmousedown', 'onmousemove', 'onmouseout', 'onmouseover',
            'onmouseup', 'onkeydown', 'onkeypress', 'onkeyup');
    }

    public function getDefaultHtml() {
        $html = $this->getData('default_html');
        if (is_null($html)) {
            $html.= '<div class="input-box">' . "\n";
            $html.= $this->getElementHtml();
            $html.= '</div>' . "\n";
        }
        return $html;
    }
}