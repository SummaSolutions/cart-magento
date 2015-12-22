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

class Ideasa_IdeCheckoutvm_Model_System_Config_Source_Registration {
    
    const REQUIRE_REGISTRATION  = 'require_registration';
    const DISABLE_REGISTRATION  = 'disable_registration';
    const ALLOW_GUEST           = 'allow_guest';
    const REGISTRATION_SUCCESS  = 'registration_success';
    const AUTO_GENERATE_ACCOUNT = 'auto_generate_account';
    
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        $helper = Mage::helper('idecheckoutvm');
        return array(
            array('value' => self::REQUIRE_REGISTRATION,  'label' => $helper->__('Require registration/login')),
            array('value' => self::DISABLE_REGISTRATION,  'label' => $helper->__('Disable registration/login')),
            array('value' => self::ALLOW_GUEST,           'label' => $helper->__('Allow guests and logged in users')),
            //array('value' => self::REGISTRATION_SUCCESS,  'label' => $helper->__('Enable registration on success page')),
            array('value' => self::AUTO_GENERATE_ACCOUNT, 'label' => $helper->__('Auto-generate account for new emails'))
        );
    }

}