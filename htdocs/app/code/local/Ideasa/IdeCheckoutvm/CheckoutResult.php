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

class Ideasa_IdeCheckoutvm_CheckoutResult {

    /**
     *
     * @var type Ideasa_IdeCheckoutvm_CheckoutUpdateSection
     */
    public $updateSection;

    /**
     *
     * @var type 
     */
    public $duplicateBillingInfo = false;

    /**
     *
     * @var type 
     */
    public $reloadTotals = false;

    /**
     *
     * @var type 
     */
    public $error = false;

    /**
     *
     * @var type 
     */
    public $success = false;

    /**
     *
     * @var type 
     */
    public $orderCreated = false;

    /**
     *
     * @var type 
     */
    public $redirectUrl = null;

    /**
     * Erros encontrados
     * 
     * @return Ideasa_IdeCheckoutvm_ErrorMessage
     */
    public $errorMessage;

    /**
     * Mensagem genérica
     * 
     * @var type 
     */
    public $message;

    public static function getInstance() {
        return new Ideasa_IdeCheckoutvm_CheckoutResult();
    }

    public function getUpdateSection() {
        return $this->updateSection;
    }

    public function setUpdateSection($updateSection) {
        $this->updateSection = $updateSection;
    }

    public function getDuplicateBillingInfo() {
        return $this->duplicateBillingInfo;
    }

    public function setDuplicateBillingInfo($duplicateBillingInfo) {
        $this->duplicateBillingInfo = $duplicateBillingInfo;
    }

    public function getReloadTotals() {
        return $this->reloadTotals;
    }

    public function setReloadTotals($reloadTotals) {
        $this->reloadTotals = $reloadTotals;
    }

    public function isError() {
        return $this->error;
    }

    public function setError($error) {
        $this->error = $error;
        $this->success = !$error;
    }

    public function isSuccess() {
        return $this->success;
    }

    public function setSuccess($success) {
        $this->success = $success;
        $this->error = !$success;
    }

    public function isOrderCreated() {
        return $this->orderCreated;
    }

    public function setOrderCreated($orderCreated) {
        $this->orderCreated = $orderCreated;
    }

    public function getRedirectUrl() {
        return $this->redirectUrl;
    }

    public function setRedirectUrl($redirectUrl) {
        $this->redirectUrl = $redirectUrl;
    }

    public function getErrorMessage() {
        return $this->errorMessage;
    }

    public function setErrorMessage(Ideasa_IdeCheckoutvm_ErrorMessage $errorMessage) {
        $this->errorMessage = $errorMessage;
    }

    public function getMessage() {
        return $this->message;
    }

    public function setMessage($message) {
        $this->message = $message;
    }

}