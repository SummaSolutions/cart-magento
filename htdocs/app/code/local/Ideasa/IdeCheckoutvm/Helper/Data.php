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

class Ideasa_IdeCheckoutvm_Helper_Data extends Mage_Core_Helper_Abstract {

    public function isCheckoutEnabled() {
        return (bool) Mage::getStoreConfig('idecheckoutvm/geral/active');
    }

    public function getAgreeIds() {
        if (!Mage::helper('idecheckoutvm/checkout')->isAgreementsEnable()) {
            return null;
        }
        $agree = Mage::getModel('checkout/agreement')->getCollection()
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addFieldToFilter('is_active', 1)
                ->getAllIds();
        return $agree;
    }

    public function getVersion() {
        return (string) Mage::getConfig()->getNode()->modules->Ideasa_IdeCheckoutvm->version;
    }

    // FIXME: verificar se há o módulo
    public function isAutocompleteCepEnable() {
        return Mage::getStoreConfig(Ideasa_IdeAddons_ConfiguracoesSystem::AUTOCOMPLETE_CEP);
    }

    // FIXME: verificar se há o módulo
    public function isLinkCepEnable() {
        return Mage::getStoreConfig(Ideasa_IdeAddons_ConfiguracoesSystem::SHOW_LINK_CEP);
    }

    public function mascararCep($cep) {
        if (!is_null($cep)) {
            $cep = str_replace('-', '', $cep);
            $number1 = substr($cep, 0, 5);
            $number2 = substr($cep, 5, 3);
            
            return $number1 . '-' . $number2;
        }
        return null;
    }

}