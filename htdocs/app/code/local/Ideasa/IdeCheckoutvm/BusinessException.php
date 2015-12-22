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

class Ideasa_IdeCheckoutvm_BusinessException extends Ideasa_IdeCheckoutvm_Exception {

    public function __construct($message) {
        parent::__construct($message, null, null);
    }

    /**
     * Lança exceção com o erro encontrado.
     * 
     * @param type $message
     */
    public static function throwException($message) {
        throw new Ideasa_IdeCheckoutvm_BusinessException($message);
    }

}