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

class Ideasa_IdeAddons_Helper_LogUtils {

    public static function varDump($variable) {
        $logFile = Mage::getBaseDir('var') . DS . 'log' . DS . Mage::getStoreConfig('dev/log/file');
        ob_start();
        // write content
        var_dump($variable);
        $content = ob_get_contents();
        ob_end_clean();
        file_put_contents($logFile, $content, FILE_APPEND);
    }

}