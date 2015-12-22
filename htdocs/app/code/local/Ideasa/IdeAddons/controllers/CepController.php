<?php

/**
* 
* Checkout Venda Mais para Magento
* 
* @category     Checkout Venda Mais
* @packages     IdeAddons
* @copyright    Copyright (c) 2013 Checkout Venda Mais (http://www.checkoutvendamais.com.br)
* @version      1.2.0
* @license      http://www.checkoutvendamais.com.br/magento/licenca
*
*/

class Ideasa_IdeAddons_CepController extends Mage_Core_Controller_Front_Action {

    private $logger;

    /**
     * Initialize
     */
    protected function _construct() {
        $this->logger = Ideasa_IdeCheckoutvm_Logger::getLogger(__CLASS__);
    }

    public function indexAction() {
        if (!$this->getRequest()->isPost()) {
            return;
        }
        $cep = $this->getRequest()->getParam('cep');

        $toolsweb = Mage::getModel('ideaddons/toolsweb');
        $endereco = $toolsweb->getAddress($cep);
        if (!($endereco instanceof Ideasa_IdeAddons_Endereco)) {
            $repVirtual = Mage::getModel('ideaddons/repvirtual');
            $endereco = $repVirtual->getAddress($cep);
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($endereco));
    }

}