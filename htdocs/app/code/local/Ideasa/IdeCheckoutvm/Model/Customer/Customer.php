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

class Ideasa_IdeCheckoutvm_Model_Customer_Customer extends Mage_Customer_Model_Customer {

    /**
     * Validate customer attribute values.
     * For existing customer password + confirmation will be validated only when password is set (i.e. its change is requested)
     *
     * @return bool
     */
    public function validate() {
        $errors = array();
        $helper = Mage::helper('customer');

        if (!Zend_Validate::is(trim($this->getFirstname()), 'NotEmpty')) {
            $errors[] = Ideasa_IdeCheckoutvm_ErrorField::getInstance('billing:firstname', $helper->__('The first name cannot be empty.'));
        }
        if (!Zend_Validate::is(trim($this->getLastname()), 'NotEmpty')) {
            $errors[] = Ideasa_IdeCheckoutvm_ErrorField::getInstance('billing:lastname', $helper->__('The last name cannot be empty.'));
        }
        if (!Zend_Validate::is($this->getEmail(), 'EmailAddress')) {
            $errors[] = Ideasa_IdeCheckoutvm_ErrorField::getInstance('billing:email', $helper->__('Invalid email address "%s".', $this->getEmail()));
        }

        $password = $this->getPassword();
        if (!$this->getId() && !Zend_Validate::is($password, 'NotEmpty')) {
            $errors[] = Ideasa_IdeCheckoutvm_ErrorField::getInstance('billing:customer_password', $helper->__('The password cannot be empty.'));
        }
        if (strlen($password) && !Zend_Validate::is($password, 'StringLength', array(6))) {
            $errors[] = Ideasa_IdeCheckoutvm_ErrorField::getInstance('billing:customer_password', $helper->__('The minimum password length is %s', 6));
        }
        $confirmation = $this->getConfirmation();
        if ($password != $confirmation) {
            $errors[] = Ideasa_IdeCheckoutvm_ErrorField::getInstance('billing:confirm_password', $helper->__('Please make sure your passwords match.'));
        }

        $tp = $this->getTipoPessoa();
        $ignoreByType = Mage::getModel('idecheckoutvm/customer_form')->getIgnoreByType();

        $entityType = Mage::getSingleton('eav/config')->getEntityType('customer');
        $attribute = Mage::getModel('customer/attribute')->loadByCode($entityType, 'taxvat');
        if ($attribute->getIsRequired() && '' == trim($this->getTaxvat())) {
            $errors[] = Ideasa_IdeCheckoutvm_ErrorField::getInstance('billing:taxvat', $helper->__('The TAX/VAT number is required.'));
        }
        // só valida se for Pessoa Física
        if (!in_array('dob', $ignoreByType[$tp])) {
            $attribute = Mage::getModel('customer/attribute')->loadByCode($entityType, 'dob');
            if ($attribute->getIsRequired() && '' == trim($this->getDob())) {
                $errors[] = Ideasa_IdeCheckoutvm_ErrorField::getInstance('billing:dob', $helper->__('The Date of Birth is required.'));
            }
        }
        if (!in_array('gender', $ignoreByType[$tp])) {
            $attribute = Mage::getModel('customer/attribute')->loadByCode($entityType, 'gender');
            if ($attribute->getIsRequired() && '' == trim($this->getGender())) {
                $errors[] = Ideasa_IdeCheckoutvm_ErrorField::getInstance('billing:gender', $helper->__('Gender is required.'));
            }
        }


        if (empty($errors)) {
            return true;
        }

        return $errors;
    }

}