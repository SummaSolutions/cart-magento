<?php

/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL).
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category       Payment Gateway
 * @package        MercadoPago
 * @author         Gabriel Matsuoka (gabriel.matsuoka@gmail.com)
 * @copyright      Copyright (c) MercadoPago [http://www.mercadopago.com]
 * @license        http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MercadoPago_Core_Model_Core
    extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'mercadopago';

    protected $_isGateway = true;
    protected $_canOrder = true;
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canCapturePartial = true;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid = true;
    protected $_canUseInternal = true;
    protected $_canUseCheckout = true;
    protected $_canUseForMultishipping = true;
    protected $_canFetchTransactionInfo = true;
    protected $_canCreateBillingAgreement = true;
    protected $_canReviewPayment = true;


    /**
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get admin checkout session namespace
     *
     * @return Mage_Adminhtml_Model_Session_Quote
     */
    protected function _getAdminCheckout()
    {
        return Mage::getSingleton('adminhtml/session_quote');
    }

    /**
     * Retrieves Quote
     *
     * @param integer $quoteId
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote($quoteId = null)
    {
        if (!empty($quoteId)) {
            return Mage::getModel('sales/quote')->load($quoteId);
        } else {
            if (Mage::app()->getStore()->isAdmin()) {
                return $this->_getAdminCheckout()->getQuote();
            } else {
                return $this->_getCheckout()->getQuote();
            }
        }
    }

    /**
     * Retrieves Order
     *
     * @param integer $incrementId
     *
     * @return Mage_Sales_Model_Order
     */
    protected function _getOrder($incrementId)
    {
        return Mage::getModel('sales/order')->loadByIncrementId($incrementId);
    }

    public function getInfoPaymentByOrder($order_id)
    {
        $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        $payment = $order->getPayment();
        $info_payments = array();
        $fields = array(
            array("field" => "cardholderName", "title" => "Card Holder Name: %s"),
            array("field" => "trunc_card", "title" => "Card Number: %s"),
            array("field" => "payment_method", "title" => "Payment Method: %s"),
            array("field" => "expiration_date", "title" => "Expiration Date: %s"),
            array("field" => "installments", "title" => "Installments: %s"),
            array("field" => "statement_descriptor", "title" => "Statement Descriptor: %s"),
            array("field" => "payment_id", "title" => "Payment id (MercadoPago): %s"),
            array("field" => "status", "title" => "Payment Status: %s"),
            array("field" => "status_detail", "title" => "Payment Detail: %s"),
            array("field" => "activation_uri", "title" => "Generate Ticket")
        );

        foreach ($fields as $field):
            if ($payment->getAdditionalInformation($field['field']) != ""):
                $text = Mage::helper('mercadopago')->__($field['title'], $payment->getAdditionalInformation($field['field']));
                $info_payments[$field['field']] = array(
                    "text"  => $text,
                    "value" => $payment->getAdditionalInformation($field['field'])
                );
            endif;
        endforeach;

        return $info_payments;
    }

    protected function validStatusTwoPayments($status)
    {
        $array_status = explode(" | ", $status);
        $status_verif = true;
        $status_final = "";
        foreach ($array_status as $status):

            if ($status_final == "") {
                $status_final = $status;
            } else {
                if ($status_final != $status) {
                    $status_verif = false;
                }
            }
        endforeach;

        if ($status_verif === false) {
            $status_final = "other";
        }

        return $status_final;
    }

    public function getMessageByStatus($status, $status_detail, $payment_method, $installment, $amount)
    {
        $status = $this->validStatusTwoPayments($status);
        $status_detail = $this->validStatusTwoPayments($status_detail);

        $message = array(
            "title"   => "",
            "message" => ""
        );

        $rawMessage = Mage::helper('mercadopago/statusMessage')->getMessage($status);
        $message['title'] = Mage::helper('mercadopago')->__($rawMessage['title']);

        if ($status == 'rejected'){
            if ($status_detail=='cc_rejected_invalid_installments') {
                $message['message'] = Mage::helper('mercadopago')
                    ->__(Mage::helper('mercadopago/statusDetailMessage')->getMessage($status_detail),strtoupper($payment_method),$installment);
            } elseif ($status_detail == 'cc_rejected_call_for_authorize'){
                $message['message'] = Mage::helper('mercadopago')
                    ->__(Mage::helper('mercadopago/statusDetailMessage')->getMessage($status_detail), strtoupper($payment_method), $amount);
            } else {
                $message['message'] = Mage::helper('mercadopago')
                ->__(Mage::helper('mercadopago/statusDetailMessage')->getMessage($status_detail), strtoupper($payment_method));
            }
        } else {
            $message['message'] = Mage::helper('mercadopago')->__($rawMessage['message']);
        }

        return $message;
    }

    public function makeDefaultPreferencePaymentV1($payment_info = array())
    {
        $core = Mage::getModel('mercadopago/core');
        $quote = $this->_getQuote();
        $order_id = $quote->getReservedOrderId();
        $order = $this->_getOrder($order_id);

        $customer = Mage::getSingleton('customer/session')->getCustomer();

        $billing_address = $quote->getBillingAddress();
        $billing_address = $billing_address->getData();

        //pega valor total da compra
        $total_cart = $order->getBaseGrandTotal();
        if (!$total_cart) {
            $total_cart = $order->getBasePrice() + $order->getBaseShippingAmount();
        }

        /* Pega o valor total do carrinho, incluindo o frete */
        $total_cart = number_format($total_cart, 2, '.', '');

        /* check info payer */
        $email = htmlentities($customer->getEmail());
        if ($email == "") {
            $email = $order['customer_email'];
        }

        $first_name = htmlentities($customer->getFirstname());
        if ($first_name == "") {
            $first_name = $order->getBillingAddress()->getFirstname();
        }

        $last_name = htmlentities($customer->getLastname());
        if ($last_name == "") {
            $last_name = $order->getBillingAddress()->getLastname();
        }

        /* INIT PREFERENCE */
        $preference = array();

        //define a url de notificacao
        $preference['notification_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) . "mercadopago/notifications/custom";

        /* informações obrigatório do pagamento */
        $preference['transaction_amount'] = (float)$total_cart;
        $preference['external_reference'] = $order_id;

        /* informações sobre o comprador */
        $preference['payer']['email'] = $email;

        if (isset($payment_info['identification_type']) && $payment_info['identification_type'] != "") {
            $preference['payer']['identification']['type'] = $payment_info['identification_type'];
            $preference['payer']['identification']['number'] = $payment_info['identification_number'];
        }

        /* informações sobre os items do carrinho */
        $preference['additional_info']['items'] = array();

        foreach ($order->getAllVisibleItems() as $item) {
            $produto = $item->getProduct();

            //get image
            try {
                $imagem = $produto->getImageUrl();
            } catch (Exception $e) {
                $imagem = "";
            }

            $preference['additional_info']['items'][] = array(
                "id"          => $item->getSku(),
                "title"       => $produto->getName(),
                "description" => $produto->getName(),
                "picture_url" => $imagem,
                "category_id" => Mage::getStoreConfig('payment/mercadopago/category_id'),
                "quantity"    => (int)number_format($item->getQtyOrdered(), 0, '.', ''),
                "unit_price"  => (float)number_format($produto->getPrice(), 2, '.', '')
            );
        }

        /* verifica se existe desconto, caso exista adiciona como um item */
        $discount = $this->getDiscount();
        if ($discount != 0) {
            $preference['additional_info']['items'][] = array(
                "title"       => "Discount by the Store",
                "description" => "Discount by the Store",
                "quantity"    => (int)1,
                "unit_price"  => (float)number_format($discount, 2, '.', '')
            );
        }

        /* informações sobre o comprador */
        $preference['additional_info']['payer']['first_name'] = $first_name;
        $preference['additional_info']['payer']['last_name'] = $last_name;

        $preference['additional_info']['payer']['address'] = array(
            "zip_code"      => $billing_address['postcode'],
            "street_name"   => $billing_address['street'] . " - " . $billing_address['city'] . " - " . $billing_address['country_id'],
            "street_number" => 0
        );

        $preference['additional_info']['payer']['registration_date'] = date('Y-m-d', $customer->getCreatedAtTimestamp()) . "T" . date('H:i:s', $customer->getCreatedAtTimestamp());

        /* informações sobre o envio e contato do comprador */
        if (method_exists($order->getShippingAddress(), "getData")) {
            $shipping = $order->getShippingAddress()->getData();

            $preference['additional_info']['shipments']['receiver_address'] = array(
                "zip_code"      => $shipping['postcode'],
                "street_name"   => $shipping['street'] . " - " . $shipping['city'] . " - " . $shipping['country_id'],
                "street_number" => 0,
                "floor"         => "-",
                "apartment"     => "-",

            );

            $preference['additional_info']['payer']['phone'] = array(
                "area_code" => "0",
                "number"    => $shipping['telephone']
            );
        }

        /* Verifica se existe coupon e adiciona na preferencia */
        if (isset($payment_info['coupon_code']) && $payment_info['coupon_code'] != "") {
            $coupon_code = $payment_info['coupon_code'];
            Mage::helper('mercadopago')->log("Validating coupon_code: " . $coupon_code, 'mercadopago-custom.log');

            $coupon = $core->validCoupon($coupon_code);
            Mage::helper('mercadopago')->log("Response API Coupon: ", 'mercadopago-custom.log', $coupon);

            if ($coupon['status'] != 200) {
                if ($coupon['response']['error'] != "campaign-code-doesnt-match" &&
                    $coupon['response']['error'] != "amount-doesnt-match" &&
                    $coupon['response']['error'] != "transaction_amount_invalid"
                ) {

                    // caso não seja os erros mapeados acima (todos são informandos no formulario no momento que aplica os desconto)
                    // o coupon code é inserido no array para o post de pagamento
                    // caso de erro significa que o coupon não é mais valido para utilização
                    // pode ter ocorrido do usuario ja ter utilizado o coupon e mesmo assim prosseguir com o pagamento

                    //adiciona o coupon amount, caso o usuario esteja passando pela v1
                    $preference['coupon_amount'] = (float)$coupon['response']['coupon_amount'];

                    //adiciona coupon_code
                    $preference['coupon_code'] = $coupon_code;

                    Mage::helper('mercadopago')->log("Coupon applied. API response 400, error not mapped", 'mercadopago-custom.log');
                } else {
                    Mage::helper('mercadopago')->log("Coupon invalid, not applied.", 'mercadopago-custom.log');
                }
            } else {

                //adiciona o coupon amount, caso o usuario esteja passando pela v1
                $preference['coupon_amount'] = (float)$coupon['response']['coupon_amount'];

                //adiciona coupon_code
                $preference['coupon_code'] = $coupon_code;

                Mage::helper('mercadopago')->log("Coupon applied. API response 200.", 'mercadopago-custom.log');
            }
        }

        /* adiciona sponsor_id */
        $sponsor_id = Mage::getStoreConfig('payment/mercadopago/sponsor_id');
        Mage::helper('mercadopago')->log("Sponsor_id", 'mercadopago-standard.log', $sponsor_id);
        if ($sponsor_id != null && $sponsor_id != "") {
            Mage::helper('mercadopago')->log("Sponsor_id identificado", 'mercadopago-custom.log', $sponsor_id);
            $preference['sponsor_id'] = (int)$sponsor_id;
        }

        return $preference;
    }


    public function postPaymentV1($preference)
    {

        //obtem access_token
        $access_token = Mage::getStoreConfig('payment/mercadopago/access_token');
        Mage::helper('mercadopago')->log("Access Token for Post", 'mercadopago-custom.log', $access_token);

        //seta sdk php mercadopago
        $mp = Mage::helper('mercadopago')->getApiInstance($access_token);

        $response = $mp->post("/v1/payments", $preference);
        Mage::helper('mercadopago')->log("POST /v1/payments", 'mercadopago-custom.log', $response);

        if ($response['status'] == 200 || $response['status'] == 201) {
            return $response;
        } else {
            $e = "";
            $exception = new MercadoPago_Core_Model_Api_V1_Exception();
            foreach ($response['response']['cause'] as $error) {
                $e .= $exception->getUserMessage($error) . " ";
            }

            Mage::helper('mercadopago')->log("erro post pago: " . $e, 'mercadopago-custom.log');
            Mage::helper('mercadopago')->log("response post pago: ", 'mercadopago-custom.log', $response);

            $exception->setMessage($e);
            throw $exception;

            return false;
        }
    }

    public function getPayment($payment_id)
    {
        $this->access_token = Mage::getStoreConfig('payment/mercadopago/access_token');
        $mp = Mage::helper('mercadopago')->getApiInstance($this->access_token);

        return $mp->get_payment($payment_id);
    }

    public function getPaymentV1($payment_id)
    {
        $this->access_token = Mage::getStoreConfig('payment/mercadopago/access_token');
        $mp = Mage::helper('mercadopago')->getApiInstance($this->access_token);

        return $mp->get("/v1/payments/" . $payment_id);
    }

    public function getMerchantOrder($merchant_order_id)
    {
        $this->access_token = Mage::getStoreConfig('payment/mercadopago/access_token');
        $mp = Mage::helper('mercadopago')->getApiInstance($this->access_token);

        return $mp->get("/merchant_orders/" . $merchant_order_id);
    }

    public function getPaymentMethods()
    {
        $this->access_token = Mage::getStoreConfig('payment/mercadopago/access_token');

        $mp = Mage::helper('mercadopago')->getApiInstance($this->access_token);

        $payment_methods = $mp->get("/v1/payment_methods");

        return $payment_methods;
    }

    public function getEmailCustomer()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $email = $customer->getEmail();

        if ($email == "") {
            $order = $this->_getOrder();
            $email = $order['customer_email'];
        }

        return $email;
    }


    public function getAmount()
    {
        $quote = $this->_getQuote();
        $total = $quote->getBaseGrandTotal();

        //caso o valor seja null setta um valor 0
        if (is_null($total)) {
            $total = 0;
        }

        return (float)$total;
    }

    public function validCoupon($id)
    {
        $this->access_token = Mage::getStoreConfig('payment/mercadopago/access_token');

        $mp = Mage::helper('mercadopago')->getApiInstance($this->access_token);

        $params = array(
            "transaction_amount" => $this->getAmount(),
            "payer_email"        => $this->getEmailCustomer(),
            "coupon_code"        => $id
        );

        $details_discount = $mp->get("/discount_campaigns", $params);

        //add value on return api discount
        $details_discount['response']['transaction_amount'] = $params['transaction_amount'];
        $details_discount['response']['params'] = $params;


        return $details_discount;
    }
}
