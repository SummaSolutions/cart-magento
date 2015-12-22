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

abstract class Ideasa_IdeCheckoutvm_Block_Onepage_Abstract extends Mage_Checkout_Block_Onepage_Abstract {
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

    protected $sortedFields = array();

    /**
     *
     * @var type Mage_Customer_Block_Widget_Name
     */
    protected $nameBlock = null;

    /**
     *
     * @var type Ideasa_IdeCheckoutvm_Block_Widget_Pessoa
     */
    protected $pessoaBlock;

    /**
     *
     * @var type Mage_Customer_Block_Widget_Dob
     */
    protected $dobBlock = null;

    /**
     *
     * @var type Mage_Customer_Block_Widget_Gender
     */
    protected $genderBlock = null;

    /**
     *
     * @var type Mage_Customer_Block_Widget_Taxvat
     */
    protected $taxvatBlock = null;

    /**
     *
     * @var type Ideasa_IdeCheckoutvm_Block_Widget_Address_Address
     */
    protected $addressBlock = null;

    /**
     *
     * @var type 
     */
    protected $streetLines = 0;

    /**
     *
     * @var type Varien_Data_Form
     */
    protected $form = null;

    /**
     *
     * @var type
     */
    protected $customerLoggedIn = null;

    /**
     *
     * @var type
     */
    protected $showTipoPessoa = false;

    public function _construct() {
        parent::_construct();
        $this->form = new Varien_Data_Form();
        $this->form->setId('ide-checkout-form');
    }
    
    abstract public function getAddress();
    
    abstract public function printEmail();

    abstract public function printDob();

    abstract public function printGender();

    abstract public function printTaxvat();

    abstract public function printExtraField($id);

    abstract public function printCompany();

    abstract public function printTipoPessoa();

    abstract public function printRg();

    abstract public function printInscEst();

    abstract public function printRazaoSocial();

    abstract public function printNomeFantasia();

    public function printOrderingFields() {
        foreach ($this->sortedFields as $key => $value) {
            if(!Ideasa_Data_Config::isMappedField($value)) {
                continue;
            }
            
            $suffixMethod = null;
            $pos = strrpos($value, "_");
            if ($pos === false) {
                $suffixMethod = ucfirst($value);
            } else {
                $exploded = explode("_", $value);
                foreach ($exploded as $part) {
                    $suffixMethod = $suffixMethod . ucfirst($part);
                }
            }
            $printMethod = "print{$suffixMethod}";
            if (method_exists($this, $printMethod)) {
                $this->{$printMethod}(); // campos padrão do magento
            } else {
                //$this->printExtraField($value);
            }
        }
    }

    protected function loadDependencies() {
        // linhas de endereço
        $this->streetLines = $this->helper('customer/address')->getStreetLines();

        $this->customerLoggedIn = $this->isCustomerLoggedIn();
    }

    protected function showPrefix() {
        return $this->nameBlock->showPrefix();
    }

    protected function showSuffix() {
        return $this->nameBlock->showSuffix();
    }

    protected function showDob() {
        if ($this->customerLoggedIn) {
            return false;
        }
        return $this->dobBlock->isEnabled();
    }

    protected function showGender() {
        if ($this->customerLoggedIn) {
            return false;
        }
        return $this->genderBlock->isEnabled();
    }

    protected function showTaxvat() {
        if ($this->customerLoggedIn) {
            return false;
        }
        return $this->taxvatBlock->isEnabled();
    }

    protected function showFirstname() {
        return true;
    }

    protected function showLastname() {
        return true;
    }

    protected function showMiddlename() {
        return $this->nameBlock->showMiddlename();
    }

    protected function showEmail() {
        if ($this->customerLoggedIn) {
            return false;
        }
        return true;
    }

    protected function showStreet1() {
        return $this->streetLines > 0;
    }

    protected function showStreet2() {
        return $this->streetLines > 1;
    }

    /**
     * Verifica se é para mostrar o campo "Complemento". <br>
     * 
     * Consulte menu "Sistema > Configuração", menu lateral "Clientes > Configurações", seção "Opções de Nome e Endereço".
     * 
     * @return type boolean
     */
    protected function showStreet3() {
        if ($this->streetLines > 2) {
            $opt = Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::ADDRESS_3);
            if ($opt != self::NO) {
                return true;
            }
        }
        return false;
    }

    protected function showStreet4() {
        return $this->streetLines > 3;
    }

    /**
     *
     * @return type boolean
     */
    protected function showCity() {
        return Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::ADDRESS_CITY) != self::NO;
    }

    /**
     *
     * @return type boolean
     */
    protected function showRegion() {
        return Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::ADDRESS_STATE) != self::NO;
    }

    /**
     *
     * @return type boolean
     */
    protected function showPostcode() {
        return Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::ADDRESS_ZIP) != self::NO;
    }

    /**
     *
     * @return type boolean
     */
    protected function showTelephone() {
        return (bool) (Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::ADDRESS_PHONE) != self::NO);
    }

    /**
     *
     * @return type boolean
     */
    protected function showCountry() {
        return (bool) (Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::ADDRESS_COUNTRY) != self::NO);
    }

    /**
     *
     * @return type boolean
     */
    protected function showFax() {
        return (bool) (Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::ADDRESS_FAX) != self::NO);
    }

    /**
     *
     * @return type boolean
     */
    protected function showCompany() {
        return (bool) (Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::COMPANY) != self::NO);
    }

    /**
     *
     * @return type boolean
     */
    protected function showTipoPessoa() {
        return (bool) (Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::TIPO_PESSOA));
    }

    /**
     *
     * @return type boolean
     */
    protected function showRg() {
        if ($this->customerLoggedIn) {
            return false;
        }
        return (bool) (Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::RG) != self::NO);
    }

    /**
     *
     * @return type boolean
     */
    protected function showInscEst() {
        if ($this->customerLoggedIn) {
            return false;
        }
        return (bool) (Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::INSC_EST) != self::NO);
    }

    /**
     *
     * @return type boolean
     */
    protected function showRazaoSocial() {
        if ($this->customerLoggedIn) {
            return false;
        }
        return (bool) (Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::RAZAO_SOCIAL) != self::NO);
    }

    /**
     *
     * @return type boolean
     */
    protected function showNomeFantasia() {
        if ($this->customerLoggedIn) {
            return false;
        }
        return (bool) (Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::NOME_FANTASIA) != self::NO);
    }

    /**
     *
     * @return type boolean
     */
    protected function showMobile() {
        return (bool) (Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::MOBILE) != self::NO);
    }

    public function printPrefix() {
        if (!$this->showPrefix()) {
            return false;
        }
        $this->addressBlock->printPrefix();
    }

    public function printMiddlename() {
        if (!$this->showMiddlename()) {
            return false;
        }
        $this->addressBlock->printMiddlename();
    }

    public function printFirstname() {
        if (!$this->showFirstname()) {
            return false;
        }
        $this->addressBlock->printFirstname();
    }

    public function printLastname() {
        if (!$this->showLastname()) {
            return false;
        }
        $this->addressBlock->printLastname();
    }

    public function printSuffix() {
        if (!$this->showSuffix()) {
            return false;
        }
        $this->addressBlock->printSuffix();
    }

    public function printStreet1() {
        if (!$this->showStreet1()) {
            return false;
        }
        $this->addressBlock->printStreet1();
    }

    public function printStreet2() {
        if (!$this->showStreet2()) {
            return false;
        }
        $this->addressBlock->printStreet2();
    }

    public function printStreet3() {
        if (!$this->showStreet3()) {
            return false;
        }
        $this->addressBlock->printStreet3();
    }

    public function printStreet4() {
        if (!$this->showStreet4()) {
            return false;
        }
        $this->addressBlock->printStreet4();
    }

    public function printCity() {
        if (!$this->showCity()) {
            return false;
        }
        $this->addressBlock->printCity();
    }

    public function printCountry() {
        if (!$this->showCountry()) { // gera input hidden
            $this->addressBlock->printHiddenCountry();
        } else {// gera combo
            $this->addressBlock->printSelectCountry();
        }
    }

    public function printRegion() {
        if (!$this->showRegion()) {
            return false;
        }
        $this->addressBlock->printRegion();
    }

    public function printPostcode() {
        if (!$this->showPostcode()) {
            return false;
        }
        $this->addressBlock->printPostcode();
    }

    public function printTelephone() {
        if (!$this->showTelephone()) {
            return false;
        }
        $this->addressBlock->printTelephone();
    }

    public function printMobile() {
        if (!$this->showMobile()) {
            return false;
        }
        $this->addressBlock->printMobile();
    }

    public function printFax() {
        if (!$this->showFax()) {
            return false;
        }
        $this->addressBlock->printFax();
    }

    public function getForm() {
        return $this->form;
    }

    public function setForm($form) {
        $this->form = $form;
    }

    public function getOnepage() {
        return Mage::getSingleton('idecheckoutvm/type_onepage');
    }

}