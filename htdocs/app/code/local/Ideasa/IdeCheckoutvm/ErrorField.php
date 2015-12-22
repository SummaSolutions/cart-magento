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

class Ideasa_IdeCheckoutvm_ErrorField {

    /**
     * Identificador do campo com erro.
     * 
     * @var type
     */
    public $inputId = null;

    /**
     * Mensagem de erro.
     * 
     * @var type
     */
    public $message = null;

    public function __construct($inputId, $message) {
        $this->inputId = $inputId;
        $this->message = $message;
    }

    /**
     *
     * @param type $inputId
     * @param type $message
     * @return Ideasa_IdeCheckoutvm_ErrorField 
     */
    public static function getInstance($inputId, $message) {
        return new Ideasa_IdeCheckoutvm_ErrorField($inputId, $message);
    }

    public function getInputId() {
        return $this->inputId;
    }

    public function setInputId($inputId) {
        $this->inputId = $inputId;
    }

    public function getMessage() {
        return $this->message;
    }

    public function setMessage($message) {
        $this->message = $message;
    }

}