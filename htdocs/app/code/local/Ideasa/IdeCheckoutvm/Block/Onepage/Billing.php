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

class Ideasa_IdeCheckoutvm_Block_Onepage_Billing extends Ideasa_IdeCheckoutvm_Block_Onepage_Abstract {

    private $billingBlock = null;

    public function _construct() {
        parent::_construct();
        $this->showTipoPessoa = $this->showTipoPessoa();
    }

    protected function loadDependencies() {
        parent::loadDependencies();

        // ordenação dos campos
        $this->sortedFields = Mage::helper('idecheckoutvm/checkout_html_fields')->getFields();

        /**
         * blocos
         */
        $layout = Mage::getSingleton('core/layout');

        $this->billingBlock = $layout->createBlock('checkout/onepage_billing');
        $this->nameBlock = $layout->createBlock('customer/widget_name');
        $this->dobBlock = $layout->createBlock('idecheckoutvm/widget_dob');
        $this->genderBlock = $layout->createBlock('idecheckoutvm/widget_gender');
        $this->taxvatBlock = $layout->createBlock('idecheckoutvm/widget_taxvat');

        $this->addressBlock = $layout->createBlock('idecheckoutvm/widget_address_address');
        $this->addressBlock->setForm($this->form);
        $this->addressBlock->setAddressType('billing');
        $this->addressBlock->setFieldIdFormat('billing:%s')->setFieldNameFormat('billing[%s]');
        $this->addressBlock->setObject($this->getBillingAddress());
        $this->addressBlock->setCountryOptions($this->getCountryOptions());

        $this->pessoaBlock = $layout->createBlock('idecheckoutvm/widget_pessoa');
        $this->pessoaBlock->setForm($this->form);
        $this->pessoaBlock->setFieldIdFormat('billing:%s')->setFieldNameFormat('billing[%s]');
        $this->pessoaBlock->setObject($this->getBillingAddress());
    }

    public function getBillingAddress() {
        return $this->getQuote()->getBillingAddress();
    }

    public function printExtraField($id) {
        $extraFields = Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::EXTRA_FILEDS);
        $extraFields = unserialize($extraFields);

        foreach ($extraFields as $key => $value) {
            if ($value['id'] != $id) {
                continue;
            }

            $input = new Ideasa_Data_Form_Text(array('name' => "billing:{$id}",
                        'required' => ($value['option'] == Ideasa_IdeCheckoutvm_Block_Onepage_Abstract::REQ ? true : false),
                        'title' => $value['label'],
                        'label' => $value['label'],
                        'class' => $id,
                        'value' => null,
                    ));
            $input->setId("billing:{$id}");
            $input->setForm($this->form);

            $div = new Ideasa_Data_Form_Div($id, $input, array('id' => $input->getId()));
            echo $div->toHtml();

            break;
        }
    }

    public function printEmail() {
        if (!$this->showEmail()) {
            return false;
        }
        $this->addressBlock->printEmail();
    }

    public function printDob() {
        if (!$this->showDob()) {
            return false;
        }
        $this->dobBlock->setForm($this->form);
        $this->dobBlock->setFieldIdFormat('billing:%s')->setFieldNameFormat('billing[%s]');
        $this->dobBlock->setDate($this->getQuote()->getCustomerDob());
        $this->dobBlock->printDob();
    }

    public function printGender() {
        if (!$this->showGender()) {
            return false;
        }
        $this->genderBlock->setForm($this->form);
        $this->genderBlock->setFieldIdFormat('billing:%s')->setFieldNameFormat('billing[%s]');
        $this->genderBlock->setGender($this->getQuote()->getGender());
        $this->genderBlock->printGender();
    }

    public function printTaxvat() {
        if (!$this->showTaxvat()) {
            return false;
        }
        $this->taxvatBlock->setForm($this->form);
        $this->taxvatBlock->setFieldIdFormat('billing:%s')->setFieldNameFormat('billing[%s]');
        $this->taxvatBlock->setTaxvat($this->getQuote()->getCustomerTaxvat());
        $this->taxvatBlock->printTaxvat();
    }

    public function printCompany() {
        if (!$this->showCompany()) {
            return false;
        }
        $layout = Mage::getSingleton('core/layout');
        $block = $layout->createBlock('idecheckoutvm/widget_company');
        $block->setForm($this->form);
        $block->setFieldIdFormat('billing:%s')->setFieldNameFormat('billing[%s]');
        $block->setCompany($this->getQuote()->getCompany());
        $block->printCompany();
    }

    public function printTipoPessoa() {
        if ($this->customerLoggedIn) {
            return false;
        }
        if (!$this->showTipoPessoa) {
            $this->pessoaBlock->printHiddenTipoPessoa();
        } else {
            $this->pessoaBlock->setTipoPessoa($this->getQuote()->getTipoPessoa());
            $this->pessoaBlock->printTipoPessoa();
        }
    }

    public function printRg() {
        if (!$this->showRg()) {
            return false;
        }
        $this->pessoaBlock->setRg($this->getQuote()->getRg());
        $this->pessoaBlock->printRg();
    }

    public function printInscEst() {
        if (!$this->showTipoPessoa) {
            return false;
        }
        if (!$this->showInscEst()) {
            return false;
        }
        $this->pessoaBlock->setInscEst($this->getQuote()->getInscEst());
        $this->pessoaBlock->printInscEst();
    }

    public function printRazaoSocial() {
        if (!$this->showTipoPessoa) {
            return false;
        }
        if (!$this->showRazaoSocial()) {
            return false;
        }
        $this->pessoaBlock->setRazaoSocial($this->getQuote()->getRazaoSocial());
        $this->pessoaBlock->printRazaoSocial();
    }

    public function printNomeFantasia() {
        if (!$this->showTipoPessoa) {
            return false;
        }
        if (!$this->showNomeFantasia()) {
            return false;
        }
        $this->pessoaBlock->setNomeFantasia($this->getQuote()->getNomeFantasia());
        $this->pessoaBlock->printNomeFantasia();
    }

    /**
     * Check is Quote items can ship to
     *
     * @return boolean
     */
    public function canShip() {
        return $this->billingBlock->canShip();
    }

    public function isUseBillingAddressForShipping() {
        return $this->billingBlock->isUseBillingAddressForShipping();
    }

    public function getAddress() {
        return $this->billingBlock->getAddress();
    }

}