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

class Ideasa_IdeCheckoutvm_Block_Onepage_News extends Mage_Core_Block_Template {

    protected function _construct() {
        parent::_construct();
    }

    public function isNewsletterLinkEnable() {
        return (bool) Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::NEWSLETTER_LINK);
    }

    public function isSubscribeNewAllowed() {
        if (!$this->isNewsletterLinkEnable()) {
            return false;
        }
        $customerSession = Mage::getSingleton('customer/session');
        if (!$customerSession->isLoggedIn() && !Mage::getStoreConfig('newsletter/subscription/allow_guest_subscribe')) {
            return false;
        }
        $subscribed = $this->getIsSubscribed();
        if ($subscribed) {
            return false;
        }

        return true;
    }

    public function getIsSubscribed() {
        $customerSession = Mage::getSingleton('customer/session');
        if (!$customerSession->isLoggedIn()) {
            return false;
        }
        $subscriber = Mage::getModel('newsletter/subscriber');
        return $subscriber->getCollection()->useOnlySubscribed()->addStoreFilter(Mage::app()->getStore()->getId())
                        ->addFieldToFilter('subscriber_email', $customerSession->getCustomer()->getEmail())
                        ->getAllIds();
    }

}