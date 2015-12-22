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

class Ideasa_IdeCheckoutvm_Block_Widget_Address_Address extends Mage_Customer_Block_Widget_Name {

  /**
   * 
   */
  const BILLING = 'billing';

  /**
   * 
   */
  const SHIPPING = 'shipping';

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

  /**
   *
   * @var type Mage_Checkout_Block_Onepage
   */
  protected $onepageBlock;

  /**
   *
   * @var type Mage_Checkout_Block_Onepage_Billing
   */
  protected $billingBlock;

  /**
   *
   * @var type Varien_Dataform
   */
  protected $form;

  /**
   *
   * @var type String
   */
  protected $addressType;

  /**
   *
   * @var type Mage_Core_Model_Layout
   */
  private $layout = null;

  /**
   *
   * @var type 
   */
  private $mappedFields = array();

  /**
   * 
   */
  private $countryOptions = array();

  public function _construct() {
    parent::_construct();
    $this->layout = Mage::getSingleton('core/layout');

    $this->mappedFields = Ideasa_Data_Config::getCustomerFields();
  }

  final public function isCustomerLoggedIn() {
    return Mage::getSingleton('customer/session')->isLoggedIn();
  }

  public function setForm($form) {
    $this->form = $form;
  }

  public function getForm() {
    return $this->form;
  }

  public function setAddressType($addressType) {
    $this->addressType = $addressType;
  }

  public function getAddressType() {
    return $this->addressType;
  }

  public function setOnepageBlock($onepageBlock) {
    $this->onepageBlock = $onepageBlock;
  }

  public function getOnepageBlock() {
    return $this->onepageBlock;
  }

  public function setBillingBlock($billingBlock) {
    $this->billingBlock = $billingBlock;
  }

  public function getBillingBlock() {
    return $this->billingBlock;
  }

  public function getCountryOptions() {
    return $this->countryOptions;
  }

  public function setCountryOptions($countryOptions) {
    $this->countryOptions = $countryOptions;
  }

  /**
   *
   * @return type boolean
   */
  protected function isAddressOneRequired() {
    return (bool) (Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::ADDRESS_1) == self::REQ);
  }

  /**
   *
   * @return type boolean
   */
  protected function isAddressTwoRequired() {
    return (bool) (Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::ADDRESS_2) == self::REQ);
  }

  /**
   *
   * @return type boolean
   */
  protected function isAddressThreeRequired() {
    return (bool) (Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::ADDRESS_3) == self::REQ);
  }

  /**
   *
   * @return type boolean
   */
  protected function isAddressFourRequired() {
    return (bool) (Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::ADDRESS_4) == self::REQ);
  }

  /**
   *
   * @return type boolean
   */
  protected function isCityRequired() {
    return (bool) (Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::ADDRESS_CITY) == self::REQ);
  }

  /**
   *
   * @return type boolean
   */
  protected function isRegionRequired() {
    return (bool) (Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::ADDRESS_STATE) == self::REQ);
  }

  /**
   *
   * @return type boolean
   */
  protected function isPostcodeRequired() {
    return (bool) (Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::ADDRESS_ZIP) == self::REQ);
  }

  /**
   *
   * @return type boolean
   */
  protected function isPhoneRequired() {
    return (bool) (Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::ADDRESS_PHONE) == self::REQ);
  }

  /**
   *
   * @return type boolean
   */
  protected function isMobileRequired() {
    return (bool) (Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::MOBILE) == self::REQ);
  }

  /**
   *
   * @return type boolean
   */
  protected function isCountryRequired() {
    return (bool) (Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::ADDRESS_COUNTRY) == self::REQ);
  }

  /**
   *
   * @return type boolean
   */
  protected function isFaxRequired() {
    return (bool) (Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::ADDRESS_FAX) == self::REQ);
  }

  /**
   * 
   */
  public function printPrefix() {
    $prefixBlock = $this->layout->createBlock('idecheckoutvm/widget_address_prefix');
    $prefixBlock->setAddressBlock($this);
    $line = $prefixBlock->setObject($this->getObject())->makeInput();

    echo $line->toHtml();
  }

  public function printFirstname() {
    $map = $this->mappedFields['firstname'];
    $map['id'] = $this->getFieldId('firstname');
    $map['name'] = $this->getFieldName('firstname');
    $input = new Ideasa_Data_Form_Text($map);
    $input->setId($map['id']);

    $firstName = $this->getObject()->getFirstname();
    if (is_null($firstName)) {
      $customer = Mage::getSingleton('customer/session')->getCustomer();
      if (!is_null($customer)) {
        $firstName = $customer->getFirstname();
      }
    }
    $input->setValue($this->htmlEscape($firstName));
    $input->setForm($this->form);

    $line = new Ideasa_Data_Form_Div('firstname', $input, null);
    $line->setId($map['id']);

    echo $line->toHtml();
  }

  public function printMiddlename() {
    $map = $this->mappedFields['middlename'];
    $map['id'] = $this->getFieldId('middlename');
    $map['name'] = $this->getFieldName('middlename');

    $input = new Ideasa_Data_Form_Text($map);
    $input->setId($map['id']);

    $middleName = $this->getObject()->getMiddlename();
    if (is_null($middleName)) {
      $customer = Mage::getSingleton('customer/session')->getCustomer();
      if (!is_null($customer)) {
        $middleName = $customer->getMiddlename();
      }
    }
    $input->setValue($this->htmlEscape($middleName));
    $input->setForm($this->form);

    $line = new Ideasa_Data_Form_Div('middlename', $input, null);
    $line->setId($map['id']);

    echo $line->toHtml();
  }

  public function printLastname() {
    $map = $this->mappedFields['lastname'];
    $map['id'] = $this->getFieldId('lastname');
    $map['name'] = $this->getFieldName('lastname');

    $input = new Ideasa_Data_Form_Text($map);
    $input->setId($map['id']);

    $lastName = $this->getObject()->getLastname();
    if (is_null($lastName)) {
      $customer = Mage::getSingleton('customer/session')->getCustomer();
      if (!is_null($customer)) {
        $lastName = $customer->getLastname();
      }
    }
    $input->setValue($this->htmlEscape($lastName));
    $input->setForm($this->form);

    $line = new Ideasa_Data_Form_Div('lastname', $input, null);
    $line->setId($map['id']);

    echo $line->toHtml();
  }

  public function printSuffix() {
    $suffixBlock = $this->layout->createBlock('idecheckoutvm/widget_address_suffix');
    $suffixBlock->setAddressBlock($this);
    $line = $suffixBlock->setObject($this->getObject())->makeInput();

    echo $line->toHtml();
  }

  public function printEmail() {
    $map = $this->mappedFields['email'];
    $map['id'] = $this->getFieldId('email');
    $map['name'] = $this->getFieldName('email');

    $input = new Ideasa_Data_Form_Text($map);
    $input->setId($map['id']);
    $input->setValue($this->htmlEscape($this->getObject()->getEmail()));
    $input->setForm($this->form);

    $line = new Ideasa_Data_Form_Div('email', $input, null);
    $line->setId($map['id']);

    echo $line->toHtml();
  }

  public function printStreet1() {
    $map = $this->mappedFields['street1'];
    $map['id'] = $this->getFieldId('street1');
    $map['name'] = $this->getFieldName('street') . '[]';
    $required = $this->isAddressOneRequired();
    $map['required'] = $required;

    $input = new Ideasa_Data_Form_Text($map);
    $input->setId($map['id']);
    $input->setValue($this->htmlEscape($this->getObject()->getStreet(1)));
    $input->setForm($this->form);

    $line = new Ideasa_Data_Form_Div('street1', $input, null);
    $line->setId($map['id']);

    echo $line->toHtml();
  }

  public function printStreet2() {
    $map = $this->mappedFields['street2'];
    $map['id'] = $this->getFieldId('street2');
    $map['name'] = $this->getFieldName('street') . '[]';
    $required = $this->isAddressTwoRequired();
    $map['required'] = $required;

    $input = new Ideasa_Data_Form_Text($map);
    $input->setId($map['id']);
    $input->setValue($this->htmlEscape($this->getObject()->getStreet(2)));
    $input->setForm($this->form);

    $line = new Ideasa_Data_Form_Div('street2', $input, null);
    $line->setId($map['id']);

    echo $line->toHtml();
  }

  public function printStreet3() {
    $map = $this->mappedFields['street3'];
    $map['id'] = $this->getFieldId('street3');
    $map['name'] = $this->getFieldName('street') . '[]';
    $required = $this->isAddressThreeRequired();
    $map['required'] = $required;

    $input = new Ideasa_Data_Form_Text($map);
    $input->setId($map['id']);
    $input->setValue($this->htmlEscape($this->getObject()->getStreet(3)));
    $input->setForm($this->form);

    $line = new Ideasa_Data_Form_Div('street3', $input, null);
    $line->setId($map['id']);

    echo $line->toHtml();
  }

  public function printStreet4() {
    $map = $this->mappedFields['street4'];
    $map['id'] = $this->getFieldId('street4');
    $map['name'] = $this->getFieldName('street') . '[]';
    $required = $this->isAddressFourRequired();
    $map['required'] = $required;

    $input = new Ideasa_Data_Form_Text($map);
    $input->setId($map['id']);
    $input->setValue($this->htmlEscape($this->getObject()->getStreet(4)));
    $input->setForm($this->form);

    $line = new Ideasa_Data_Form_Div('street4', $input, null);
    $line->setId($map['id']);

    echo $line->toHtml();
  }

  public function printCity() {
    $map = $this->mappedFields['city'];
    $map['id'] = $this->getFieldId('city');
    $map['name'] = $this->getFieldName('city');
    $map['required'] = $this->isCityRequired();

    $input = new Ideasa_Data_Form_Text($map);
    $input->setId($map['id']);
    $input->setValue($this->htmlEscape($this->getObject()->getCity()));
    $input->setForm($this->form);

    $line = new Ideasa_Data_Form_Div('city', $input, null);
    $line->setId($map['id']);
    echo $line->toHtml();
  }

  public function printSelectCountry() {
    $map = $this->mappedFields['country'];
    $map['id'] = "{$this->getAddressType()}:country_id";
    $map['name'] = "{$this->getAddressType()}[country_id]";
    $map['required'] = $this->isCountryRequired();

    $input = $this->makeCountriesSelectBox($map);
    $input->setForm($this->form);
    $line = new Ideasa_Data_Form_Div('country', $input, null);
    $line->setId($map['id']);
    echo $line->toHtml();
  }

  public function printHiddenCountry() {
    $defaultCountry = Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::GERAL_COUNTRY);
    $input = new Varien_Data_Form_Element_Hidden(array('name' => "{$this->getAddressType()}[country_id]",
        'value' => $this->htmlEscape($defaultCountry),
    ));
    $input->setId("{$this->getAddressType()}:country_id");
    $input->setForm($this->form);

    echo $input->toHtml();
  }

  public function printRegion() {
    $map = $this->mappedFields['region_id'];
    $map['id'] = $this->getFieldId('region_id');
    $map['name'] = $this->getFieldName('region_id');

    $input = new Ideasa_Data_Form_Select($map);
    $input->setId($map['id']);
    $input->setValue($this->htmlEscape($this->getObject()->getRegionId()));
    $input->setForm($this->form);
    $regionId = $input->toHtml();

    // region
    $map = $this->mappedFields['region'];
    $map['id'] = $this->getFieldId('region');
    $map['name'] = $this->getFieldName('region');

    $input = new Ideasa_Data_Form_Text($map);
    $input->setId($map['id']);
    $input->setValue($this->htmlEscape($this->getObject()->getRegion()));
    $input->setForm($this->form);
    $region = $input->toHtml();

    echo '<div class="fields region"><label' . ($this->isRegionRequired() ? ' class="required"><em>*</em>' : '>') . '' . $this->__('State/Province') . '</label>' . $regionId . $region . '</div>';
  }

  public function printPostcode() {
    $map = $this->mappedFields['postcode'];

    $value = $this->getObject()->getPostcode();
    if (is_null($value)) {
      if ($this->isBillingAddress()) {
        $quote = Mage::getSingleton('idecheckoutvm/type_onepage')->getQuote();
        $value = $quote->getShippingAddress()->getPostcode();
        if (is_null($value)) {
          $quote = Mage::getSingleton('checkout/type_onepage')->getQuote();
          $value = $quote->getShippingAddress()->getPostcode();
        }
      }
    }
    if (!is_null($value)) {
      $value = Mage::helper('idecheckoutvm')->mascararCep($value);
    }

    $input = new Ideasa_Data_Form_Text(array('name' => $this->getFieldName('postcode'),
        'required' => $this->isPostcodeRequired(),
        'title' => $map['title'],
        'label' => $map['label']
    ));
    $input->setId($this->getFieldId('postcode'));
    $input->setValue($this->htmlEscape($value));
    $input->setForm($this->form);

    $div = new Ideasa_Data_Form_Div('postcode', $input, array('id' => $input->getId()));
    echo $div->toHtml();

    if (Mage::helper('idecheckoutvm')->isLinkCepEnable()) {
      $link = new Ideasa_Data_Form_Link(array('value' => Mage::helper('core')->__('Nao lembra seu CEP?'),
          'href' => Mage::getStoreConfig('idecheckoutvm/url/busca-cep'),
          'target' => '_blank',
      ));
      $link->setId('postcode:help');
      $link->setForm($this->form);

      $div = new Ideasa_Data_Form_Div('postcode-help', $link, array('id' => $link->getId()));
      echo $div->toHtml();
    }
  }

  public function printTelephone() {
    $map = $this->mappedFields['telephone'];
    $map['id'] = $this->getFieldId('telephone');
    $map['name'] = $this->getFieldName('telephone');
    $map['required'] = $this->isPhoneRequired();

    $input = new Ideasa_Data_Form_Text($map);
    $input->setId($map['id']);
    $input->setValue($this->htmlEscape($this->getObject()->getTelephone()));
    $input->setForm($this->form);
    
    $div = new Ideasa_Data_Form_Div('telephone', $input, null);
    $div->setId($map['id']);
    echo $div->toHtml();
    
    /*
    $options = array(array('value' => 'S', 'label' => Mage::helper('idecheckoutvm')->__('Tenho 9 digitos')));

    $parameters = array('onchange' => ($this->isShippingAddress() ? 'shipping.bindTelephone(this);' : 'billing.bindTelephone(this);'));
    $input = new Ideasa_Data_Form_Checkboxes($parameters);
    $input->setId($this->getFieldName('telephone9'));
    $input->setValues($options);
    $input->setForm($this->getForm());
    
    $div = new Ideasa_Data_Form_Div('telephone9', $input, array('id' => $input->getId()));
    echo $div->toHtml();
     */
  }

  public function printMobile() {
    $map = Ideasa_Data_Config::getCustomerField('mobile');
    $input = new Ideasa_Data_Form_Text(array('name' => $this->getFieldName('mobile'),
        'required' => $this->isMobileRequired(),
        'title' => $map['title'],
        'label' => $map['label'],
        'class' => $map['class']
    ));

    $input->setId($this->getFieldId('mobile'));
    $input->setValue($this->htmlEscape($this->getObject()->getMobile()));
    $input->setForm($this->form);

    $div = new Ideasa_Data_Form_Div('mobile', $input, array('id' => $input->getId()));
    echo $div->toHtml();
    
    /*
    $options = array(array('value' => 'S', 'label' => Mage::helper('idecheckoutvm')->__('Tenho 9 digitos')));

    $parameters = array('onchange' => ($this->isShippingAddress() ? 'shipping.bindMobile(this);' : 'billing.bindMobile(this);'));
    $input = new Ideasa_Data_Form_Checkboxes($parameters);
    $input->setId($this->getFieldName('mobile9'));
    $input->setValues($options);
    $input->setForm($this->getForm());

    $div = new Ideasa_Data_Form_Div('mobile9', $input, array('id' => $input->getId()));
    echo $div->toHtml();
     */
  }

  public function printFax() {
    $map = $this->mappedFields['fax'];
    $map['id'] = $this->getFieldId('fax');
    $map['name'] = $this->getFieldName('fax');
    $map['required'] = $this->isFaxRequired();

    $input = new Ideasa_Data_Form_Text($map);
    $input->setId($map['id']);
    $input->setValue($this->htmlEscape($this->getObject()->getFax()));
    $input->setForm($this->form);

    $line = new Ideasa_Data_Form_Div('fax', $input, null);
    $line->setId($map['id']);
    echo $line->toHtml();
  }

  /**
   * 
   * 
   * @param type $attributtes
   * @param type $addrType
   * @return Ideasa_Data_Form_Select
   */
  protected function makeCountriesSelectBox($attributtes = array()) {
    $countryId = $this->getObject()->getCountryId();
    if (is_null($countryId)) {
      $countryId = Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::GERAL_COUNTRY);
    }
    $input = new Ideasa_Data_Form_Select($attributtes);
    $input->setId($attributtes['id']);
    $input->setValue($countryId);
    $input->setValues($this->getCountryOptions());
    $input->setClass('validate-select');
    if ($this->isShippingAddress()) {
      $input->setExtraParams('onchange="shipping.setSameAsBilling(false);"');
    }

    return $input;
  }

  public function isBillingAddress() {
    return self::BILLING == $this->addressType;
  }

  public function isShippingAddress() {
    return self::SHIPPING == $this->addressType;
  }

}
