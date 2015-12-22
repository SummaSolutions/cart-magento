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

class Ideasa_IdeCheckoutvm_CheckoutUpdateSection {

    /**
     *
     * @var type Ideasa_IdeCheckoutvm_CheckoutPartBlock
     */
    public $shippingMethod;

    /**
     *
     * @var type Ideasa_IdeCheckoutvm_CheckoutPartBlock
     */
    public $paymentMethod;

    /**
     *
     * @var type Ideasa_IdeCheckoutvm_CheckoutPartBlock
     */
    public $review;

    /**
     *
     * @var type Ideasa_IdeCheckoutvm_CheckoutPartBlock
     */
    public $couponDiscount;

    public static function getInstance() {
        return new Ideasa_IdeCheckoutvm_CheckoutUpdateSection();
    }

    public function getShippingMethod() {
        return $this->shippingMethod;
    }

    /**
     * Código HTML do bloco.
     * 
     * @param type $html String
     */
    public function setShippingMethod($html) {
        $this->shippingMethod = new Ideasa_IdeCheckoutvm_CheckoutPartBlock('shipping-method', $html);
    }

    public function getPaymentMethod() {
        return $this->paymentMethod;
    }

    /**
     * Código HTML do bloco.
     * 
     * @param type $html String
     */
    public function setPaymentMethod($html) {
        $this->paymentMethod = new Ideasa_IdeCheckoutvm_CheckoutPartBlock('payment-method', $html);
    }

    public function getReview() {
        return $this->review;
    }

    /**
     * Código HTML do bloco.
     * 
     * @param type $html String
     */
    public function setReview($html) {
        $this->review = new Ideasa_IdeCheckoutvm_CheckoutPartBlock('review', $html);
    }

    public function getCouponDiscount() {
        return $this->couponDiscount;
    }

    public function setCouponDiscount($html) {
        $this->couponDiscount = new Ideasa_IdeCheckoutvm_CheckoutPartBlock('coupon-discount', $html);
    }
}