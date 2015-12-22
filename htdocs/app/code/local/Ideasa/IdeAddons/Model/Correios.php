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

class Ideasa_IdeAddons_Model_Correios extends Mage_Core_Model_Abstract {
    const TIME_OUT = 60;

    /**
     * 
     * 
     * @var type
     */
    private $logger;

    protected function _construct() {
        $this->logger = Ideasa_IdeCheckoutvm_Logger::getLogger(__CLASS__);
    }

    public function getAddress($cep) {
        $url = 'http://m.correios.com.br/movel/buscaCepConfirma.do';

        $data = array('cepEntrada' => $cep, 'tipoCep' => '', 'cepTemp' => '', 'metodo' => 'buscarCep');
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::TIME_OUT);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($this->logger->isDebugEnabled()) {
            $this->logger->debug('$response: ' . $response);
            $this->logger->debug('$httpCode: ' . $httpCode);
        }

        $httpResponse = Mage::getModel('ideaddons/httpResponse', array('httpCode' => $httpCode, 'message' => $response));

        if ($httpResponse->isError()) {
            $this->$logger->error('Erro na chamada post: ' . curl_error($ch));
        }
        curl_close($ch);

        Ideasa_IdeAddons_Helper_LogUtils::varDump($httpResponse);
        
        return $httpResponse;
    }

}