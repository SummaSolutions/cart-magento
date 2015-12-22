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

class Ideasa_IdeCheckoutvm_Block_Widget_Pessoa extends Mage_Customer_Block_Widget_Abstract {
    /**
     * 
     */
    const REQ = 'req';

    /**
     * 
     */
    const OPT = 'opt';

    /**
     * 
     */
    const NO = '';

    public function _construct() {
        parent::_construct();
    }

    public function printHiddenTipoPessoa() {
        $map = Ideasa_Data_Config::getCustomerField('tipo_pessoa');
        $map['id'] = $this->getFieldId($map['id']);
        $map['name'] = $this->getFieldName($map['name']);
        
        $input = new Varien_Data_Form_Element_Hidden(array('name' => $map['name'], 'value' => 'F'));
        $input->setId($map['id']);
        $input->setForm($this->form);

        echo $input->toHtml();
    }
    
    public function printTipoPessoa() {
        $map = Ideasa_Data_Config::getCustomerField('tipo_pessoa');
        $map['id'] = $this->getFieldId($map['id']);
        $map['name'] = $this->getFieldName($map['name']);

        $options = array(array('value' => 'F', 'label' => Mage::helper('idecheckoutvm')->__('Pessoa Fisica')),
                         array('value' => 'J', 'label' => Mage::helper('idecheckoutvm')->__('Pessoa Juridica')));
        $input = new Ideasa_Data_Form_Radios($map);
        $input->setId($map['id']);
        $input->setValue($this->getTipoPessoa());
        $input->setValues($options);
        $input->setForm($this->getForm());

        $line = new Ideasa_Data_Form_Div('tipo_pessoa', $input, null);
        $line->setId($map['id']);
        echo $line->toHtml();
    }

    public function printRg() {
        $map = Ideasa_Data_Config::getCustomerField('rg');
        $input = new Ideasa_Data_Form_Text(array('name' => $this->getFieldName('rg'),
                    'required' => $this->isRgRequired(),
                    'title' => $map['title'],
                    'label' => $map['label'],
                    'value' => $this->htmlEscape($this->getObject()->getRg()),
                ));
        $input->setId($this->getFieldId('rg'));
        $input->setValue($this->htmlEscape($this->getObject()->getRg()));
        $input->setForm($this->form);

        $div = new Ideasa_Data_Form_Div('rg', $input, array('id' => $input->getId()));
        echo $div->toHtml();
    }
    
    public function printInscEst() {
        $map = Ideasa_Data_Config::getCustomerField('insc_est');
        $input = new Ideasa_Data_Form_Text(array('name' => $this->getFieldName('insc_est'),
                    'required' => $this->isInscEstRequired(),
                    'title' => $map['title'],
                    'label' => $map['label'],
                    'value' => $this->htmlEscape($this->getObject()->getInscEst()),
                ));
        $input->setId($this->getFieldId('insc_est'));
        $input->setValue($this->htmlEscape($this->getObject()->getInscEst()));
        $input->setForm($this->form);

        $div = new Ideasa_Data_Form_Div('insc_est', $input, array('id' => $input->getId()));
        echo $div->toHtml();
    }
    
    public function printRazaoSocial() {
        $map = Ideasa_Data_Config::getCustomerField('razao_social');
        $input = new Ideasa_Data_Form_Text(array('name' => $this->getFieldName('razao_social'),
                    'required' => $this->isRazaoSocialRequired(),
                    'title' => $map['title'],
                    'label' => $map['label'],
                    'value' => $this->htmlEscape($this->getObject()->getRazaoSocial()),
                ));
        $input->setId($this->getFieldId('razao_social'));
        $input->setValue($this->htmlEscape($this->getObject()->getRazaoSocial()));
        $input->setForm($this->form);

        $div = new Ideasa_Data_Form_Div('razao_social', $input, array('id' => $input->getId()));
        echo $div->toHtml();
    }
    
    public function printNomeFantasia() {
        $map = Ideasa_Data_Config::getCustomerField('nome_fantasia');
        $input = new Ideasa_Data_Form_Text(array('name' => $this->getFieldName('nome_fantasia'),
                    'required' => $this->isNomeFantasiaRequired(),
                    'title' => $map['title'],
                    'label' => $map['label'],
                    'value' => $this->htmlEscape($this->getObject()->getNomeFantasia()),
                ));
        $input->setId($this->getFieldId('nome_fantasia'));
        $input->setValue($this->htmlEscape($this->getObject()->getNomeFantasia()));
        $input->setForm($this->form);

        $div = new Ideasa_Data_Form_Div('nome_fantasia', $input, array('id' => $input->getId()));
        echo $div->toHtml();
    }

    /**
     *
     * @return type boolean
     */
    protected function isRgRequired() {
        return (bool) (Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::RG) == self::REQ);
    }

    /**
     *
     * @return type boolean
     */
    protected function isInscEstRequired() {
        return (bool) (Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::INSC_EST) == self::REQ);
    }

    /**
     *
     * @return type boolean
     */
    protected function isRazaoSocialRequired() {
        return (bool) (Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::RAZAO_SOCIAL) == self::REQ);
    }

    /**
     *
     * @return type boolean
     */
    protected function isNomeFantasiaRequired() {
        return (bool) (Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::NOME_FANTASIA) == self::REQ);
    }

}