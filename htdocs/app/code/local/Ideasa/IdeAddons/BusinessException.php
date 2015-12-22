<?php

/**
* 
* Checkout Venda Mais para Magento
* 
* @category     Checkout Venda Mais
* @packages     IdeAddons
* @copyright    Copyright (c) 2013 Checkout Venda Mais (http://www.checkoutvendamais.com.br)
* @version      1.2.0
* @license      http://www.checkoutvendamais.com.br/magento/licenca
*
*/

class Ideasa_IdeAddons_BusinessException extends Ideasa_IdeAddons_Exception {

    public function __construct($message) {
        parent::__construct($message, null, null);
    }

    /**
     * Lança exceção com o erro encontrado.
     * 
     * @param type $message
     */
    public static function throwException($message) {
        throw new Ideasa_IdeAddons_BusinessException($message);
    }

}