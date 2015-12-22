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

class Ideasa_Data_Form_Div extends Varien_Data_Form_Element_Abstract {

    private $cssClass;
    private $input;

    public function __construct($cssClass, $input, $attributes=array()) {
        $this->cssClass = $cssClass;
        $this->input = $input;
        parent::__construct($attributes);
    }

    public function getHtml() {
        return parent::getHtml();
    }

    public function getDefaultHtml() {
        $html = $this->getData('default_html');
        if (is_null($html)) {
            $html = '<div class="fields ' . $this->cssClass . '">' . "\n";
            $html .= $this->input->getHtml();
            $html.= '</div>' . "\n";
        }

        return $html;
    }

}