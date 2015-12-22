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

class Ideasa_IdeCheckoutvm_Block_Widget_Dob extends Mage_Customer_Block_Widget_Dob {

    public function _construct() {
        parent::_construct();
        $this->setTemplate('ideasa/idecheckoutvm/widget/dob.phtml');
    }

    public function printDob() {
        echo $this->toHtml();
    }
}