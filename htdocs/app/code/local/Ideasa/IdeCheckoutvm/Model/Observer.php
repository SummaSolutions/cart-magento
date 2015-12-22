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

class Ideasa_IdeCheckoutvm_Model_Observer {

    public function addHistoryComment($data) {
        $comment = Mage::getSingleton('customer/session')->getOrderCustomerComment();
        $comment = trim($comment);
        if (!empty($comment))
            $data['order']->addStatusHistoryComment($comment)->setIsVisibleOnFront(true)->setIsCustomerNotified(false);
    }

    public function removeHistoryComment() {
        Mage::getSingleton('customer/session')->setOrderCustomerComment(null);
    }

}