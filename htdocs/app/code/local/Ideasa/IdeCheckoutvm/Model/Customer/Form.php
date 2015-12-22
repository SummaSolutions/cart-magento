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

class Ideasa_IdeCheckoutvm_Model_Customer_Form extends Mage_Customer_Model_Form {

    /**
     * Contem mapeamento de campos a serem ignorados na validação, dependendo do tipo de pessoa.
     * 
     * @var type
     */
    private $ignoreByType = array('J' => array('dob', 'gender', 'rg'),
        'F' => array('razao_social', 'nome_fantasia', 'insc_est'));

    public function getIgnoreByType() {
        return $this->ignoreByType;
    }

    /**
     * Validate data array and return true or array of errors.
     *
     * @param array $data
     * @return boolean|array
     */
    public function validateData(array $data) {
        $errors = array();
        foreach ($this->getAttributes() as $attribute) {
            if (!Ideasa_Data_Config::isMappedField($attribute->getAttributeCode())) {
                continue;
            }
            if ($this->_isAttributeOmitted($attribute)) {
                continue;
            }
            /**
             * valida os campos por tipo de pessoa (Física, Jurídica) escolhido
             */
            if (isset($data['tipo_pessoa'])) {
                if (in_array($attribute->getAttributeCode(), $this->ignoreByType[$data['tipo_pessoa']])) {
                    continue;
                }
            }

            $dataModel = $this->_getAttributeDataModel($attribute);
            $dataModel->setExtractedData($data);
            if (!isset($data[$attribute->getAttributeCode()])) {
                $data[$attribute->getAttributeCode()] = null;
            }
            $result = $dataModel->validateValue($data[$attribute->getAttributeCode()]);

            if ($result !== true) {
                $result = array($result);
                $errors[] = Ideasa_IdeCheckoutvm_ErrorField::getInstance($data['addressType'] . ':' . $attribute->getAttributeCode(), $result[0]);
            }
        }

        if (count($errors) == 0) {
            return true;
        }

        return $errors;
    }

}