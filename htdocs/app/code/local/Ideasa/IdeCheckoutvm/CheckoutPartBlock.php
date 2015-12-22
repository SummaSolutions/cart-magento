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

class Ideasa_IdeCheckoutvm_CheckoutPartBlock {

    public $name;
    public $html;

    public function __construct($name, $html) {
        $this->name = $name;
        $this->html = $html;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getHtml() {
        return $this->html;
    }

    public function setHtml($html) {
        $this->html = $html;
    }

}