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

class Ideasa_IdeCheckoutvm_Block_Onepage_Account extends Mage_Checkout_Block_Onepage_Abstract {

    /**
     *
     * @var type Varien_Data_Form
     */
    protected $form = null;

    public function _construct() {
        parent::_construct();
        $this->form = new Varien_Data_Form();
        $this->form->setId('ide-checkout-form');
    }

    public function getRegisterAccount() {
        return $this->getQuote()->getBillingAddress()->getRegisterAccount();
    }

    public function printPassword() {
        $map = Ideasa_Data_Config::getCustomerField('customer_password');
        $map['id'] = 'billing:customer_password';
        $map['name'] = 'billing[customer_password]';

        $input = new Ideasa_Data_Form_Password($map);
        $input->setClass('validate-password');
        $input->setId($map['id']);
        $input->setForm($this->form);

        $line = new Ideasa_Data_Form_Div('customer_password', $input, null);
        $line->setId($map['id']);
        echo $line->toHtml();

        // confirm_password
        $map = Ideasa_Data_Config::getCustomerField('confirm_password');
        $map['id'] = 'billing:confirm_password';
        $map['name'] = 'billing[confirm_password]';

        $input = new Ideasa_Data_Form_Password($map);
        $input->setClass('validate-password-confirm');
        $input->setId($map['id']);
        $input->setForm($this->form);

        $line = new Ideasa_Data_Form_Div('confirm_password', $input, null);
        $line->setId($map['id']);
        echo $line->toHtml();
    }

    /**
     * Verifica se é para validar e-mail do cliente.<br>
     * 
     * Inicialmente a validação é realizada para REQUIRE_REGISTRATION e AUTO_GENERATE_ACCOUNT, mas<br>
     * se o checkout for ALLOW_GUEST e o cliente escolher criar a conta, também deve validar.
     * 
     * @return type
     */
    public function isToValidateEmail() {
        $helper = Mage::helper('idecheckoutvm/account');
        return (bool) ($helper->isRequiredRegistration() || $helper->isAllowGuest() || $helper->isAutoGenerateAccount());
    }

    /**
     *
     * @return type
     */
    public function isRequiredRegistration() {
        return (bool) Mage::helper('idecheckoutvm/account')->isRequiredRegistration();
    }

    /**
     *
     * @return type
     */
    public function isAllowGuest() {
        return (bool) Mage::helper('idecheckoutvm/account')->isAllowGuest();
    }

    /**
     *
     * @return type
     */
    public function isShowLoginInputs() {
        $helper = Mage::helper('idecheckoutvm/account');
        return (bool) ($helper->isRequiredRegistration() || $helper->isAllowGuest());
    }

}