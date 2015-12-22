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

class Ideasa_IdeCheckoutvm_ErrorMessage {

    /**
     * Mensagem de erro.
     * 
     * @var type
     */
    public $message = null;

    /**
     *
     * @var type array
     */
    public $errorFields = array();

    public function __construct($message, $errorFields = null) {
        $this->message = $message;
        $this->errorFields = $errorFields;
    }

    /**
     *
     * @param type $message
     * @param type 
     * @return Ideasa_IdeCheckoutvm_ErrorMessage 
     */
    public static function getInstance($message, $errorFields = null) {
        return new Ideasa_IdeCheckoutvm_ErrorMessage($message, $errorFields);
    }

    public function getMessage() {
        return $this->message;
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function getErrorFields() {
        return $this->errorFields;
    }

    public function setErrorFields($errorFields) {
        $this->errorFields = $errorFields;
    }

    /**
     *
     * @param Ideasa_IdeCheckoutvm_ErrorField $errorField
     */
    public function addErrorField(Ideasa_IdeCheckoutvm_ErrorField $errorField) {
        $this->errorFields[] = $$errorField;
    }

}