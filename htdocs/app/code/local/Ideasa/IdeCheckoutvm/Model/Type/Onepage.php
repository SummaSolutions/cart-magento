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

class Ideasa_IdeCheckoutvm_Model_Type_Onepage {

  private $logger;

  /**
   * Checkout types: Checkout as Guest, Register, Logged In Customer
   */
  const METHOD_GUEST = 'guest';
  const METHOD_REGISTER = 'register';
  const METHOD_CUSTOMER = 'customer';

  /**
   * Error message of "customer already exists"
   *
   * @var string
   */
  private $customerEmailExistsMessage = '';

  /**
   * @var Mage_Customer_Model_Session
   */
  protected $customerSession;

  /**
   * @var Mage_Checkout_Model_Session
   */
  protected $checkoutSession;

  /**
   * @var Mage_Sales_Model_Quote
   */
  protected $quote = null;

  /**
   * @var Mage_Checkout_Helper_Data
   */
  protected $helper;

  /**
   * Class constructor
   * Set customer already exists message
   */
  public function __construct() {
    $this->helper = Mage::helper('checkout');
    $this->customerEmailExistsMessage = Mage::helper('checkout')->__('There is already a customer registered using this email address. Please login using this email address or enter a different email address to register your account.');
    $this->checkoutSession = Mage::getSingleton('checkout/session');
    $this->customerSession = Mage::getSingleton('customer/session');
    $this->logger = Ideasa_IdeCheckoutvm_Logger::getLogger(__CLASS__);
  }

  /**
   * Get frontend checkout session object
   *
   * @return Mage_Checkout_Model_Session
   */
  public function getCheckout() {
    return $this->checkoutSession;
  }

  /**
   * Quote object getter
   *
   * @return Mage_Sales_Model_Quote
   */
  public function getQuote() {
    if ($this->quote === null) {
      $this->quote = $this->checkoutSession->getQuote();
    }
    return $this->quote;
  }

  /**
   * Declare checkout quote instance
   *
   * @param Mage_Sales_Model_Quote $quote
   * @return Mage_Checkout_Model_Type_Onepage
   */
  public function setQuote(Mage_Sales_Model_Quote $quote) {
    $this->quote = $quote;
    return $this;
  }

  /**
   * Get customer session object
   *
   * @return Mage_Customer_Model_Session
   */
  public function getCustomerSession() {
    return $this->customerSession;
  }

  /**
   * 
   *
   * @param Mage_Sales_Model_Quote $quote
   */
  public function setDefaultShippingMethod(Mage_Sales_Model_Quote $quote) {
    if (is_null($quote) || $quote->isVirtual()) {
      return $this;
    }
    $shippingMethod = $quote->getShippingAddress()->getShippingMethod();
    if (!empty($shippingMethod)) {
      return $this;
    }

    $defaultShippingMethod = Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::GERAL_SHIPPING_METHOD, $quote->getStore());
    if (empty($defaultShippingMethod)) {
      $this->logger->info('Método de Entrega default não configurado.');
    }

    $defaultCountry = Mage::getStoreConfig(Ideasa_IdeCheckoutvm_ConfiguracoesSystem::GERAL_COUNTRY, $quote->getStore());
    $defaultCountry = ($defaultCountry != '' ? $defaultCountry : 'BR');
    $address = $quote->getShippingAddress();
    $address->setCountryId($defaultCountry)->setCollectShippingrates(true);

    $shippingRates = $quote->getShippingAddress()->getGroupedAllShippingRates();
    if (empty($shippingRates)) {
      return $this;
    }
    $codes = array();
    foreach ($shippingRates as $rates) {
      foreach ($rates as $rate) {
        $codes[] = $rate->getCode();
      }
    }
    if (empty($codes)) {
      $this->logger->warn('Nenhum códigos para Método de Entrega encontrado.');
      return $this;
    }

    $totalCodes = (int) count($codes);
    if ($totalCodes === 1) {
      $quote->getShippingAddress()->setShippingMethod($codes[0]);
      return $this;
    }
    if (empty($shippingMethod) || !in_array($shippingMethod, $codes)) {
      if (in_array($defaultShippingMethod, $codes)) {
        $quote->getShippingAddress()->setShippingMethod($defaultShippingMethod);
      }
    }
  }

  /**
   * Initialize quote state to be valid for one page checkout
   *
   * @return Mage_Checkout_Model_Type_Onepage
   */
  public function initCheckout() {
    /**
     * Reset multishipping flag before any manipulations with quote address
     * addAddress method for quote object related on this flag
     */
    if ($this->getQuote()->getIsMultiShipping()) {
      $this->getQuote()->setIsMultiShipping(false);
      $this->getQuote()->save();
    }

    $customerSession = $this->getCustomerSession();
    /*
     * want to load the correct customer information by assigning to address
     * instead of just loading from sales/quote_address
     */
    $customer = $customerSession->getCustomer();
    if ($customer) {
      $this->getQuote()->assignCustomer($customer);
    }

    // informar método de entrega
    $this->setDefaultShippingMethod($this->getQuote());

    // no magento 1.4.
    if (Mage::helper('idecheckoutvm/mageversion')->isCommunity14()) {
      if (!$this->getQuote()->isVirtual()) {
        $this->getQuote()->getShippingAddress()->setSameAsBilling(true);
      }
    }

    return $this;
  }

  /**
   *
   * @return type 
   */
  public function getCheckoutMethod() {
    if ($this->getCustomerSession()->isLoggedIn()) {
      return self::METHOD_CUSTOMER;
    }

    // FIXME: colocar outras maneiras de autenticacao
    if (!$this->getQuote()->getCheckoutMethod()) {
      $accountHelper = Mage::helper('idecheckoutvm/account');

      if ($accountHelper->isRequiredRegistration()) {
        $this->getQuote()->setCheckoutMethod(self::METHOD_REGISTER);
      } else if ($accountHelper->isAllowGuest()) {
        // inicia como METHOD_GUEST, mas depois pode trocar para METHOD_REGISTER se o cliente escolher se cadastrar
        $this->getQuote()->setCheckoutMethod(self::METHOD_GUEST);
      } else if ($accountHelper->isDisableRegistration()) {
        $this->getQuote()->setCheckoutMethod(self::METHOD_GUEST);
      } else if ($accountHelper->isAutoGenerateAccount()) {
        $this->getQuote()->setCheckoutMethod(self::METHOD_REGISTER);
      } else if ($accountHelper->isRegistrationSuccess()) {
        // TODO: implementar se for o caso
      }
    }
    return $this->getQuote()->getCheckoutMethod();
  }

  public function isMethodGuest() {
    return ($this->getCheckoutMethod() == self::METHOD_GUEST);
  }

  public function isMethodRegister() {
    return ($this->getCheckoutMethod() == self::METHOD_REGISTER);
  }

  public function isMethodCustomer() {
    return ($this->getCheckoutMethod() == self::METHOD_CUSTOMER);
  }

  // FIXME: rever autenticacao
  public function validate() {
    $quote = $this->getQuote();
    if ($quote->getIsMultiShipping()) {
      Mage::throwException($this->helper->__('Invalid checkout type.'));
    }

    $helper = Mage::helper('idecheckoutvm/account');
    // FIXME: validar depois
    if ($this->isMethodRegister()) {
      
    }
    if ($this->isMethodGuest()) {
      if (!$helper->isAllowGuest() && !$helper->isDisableRegistration()) {
        Mage::throwException($this->helper->__('Sorry, guest checkout is not allowed, please contact support.'));
      }
    }
  }

  /**
   *
   * @return Ideasa_IdeCheckoutvm_Model_Type_Onepage 
   */
  public function saveOrder() {
    $this->validate();
    $newCustomer = false;

    if ($this->getCustomerSession()->isLoggedIn()) {
      $this->_prepareCustomerQuote();
    } else {
      $accountHelper = Mage::helper('idecheckoutvm/account');
      if ($accountHelper->isRequiredRegistration()) {
        if ($this->isMethodRegister()) {
          $this->_prepareNewCustomerQuote();
          $newCustomer = true;
        }
      } else if ($accountHelper->isAllowGuest()) {
        if ($this->isMethodGuest()) {
          $this->_prepareGuestQuote();
        } else if ($this->isMethodRegister()) {
          $this->_prepareNewCustomerQuote();
          $newCustomer = true;
        }
      } else if ($accountHelper->isDisableRegistration()) {
        if ($this->isMethodGuest()) {
          $this->_prepareGuestQuote();
        }
      } else if ($accountHelper->isAutoGenerateAccount()) {
        if ($this->isMethodRegister()) {
          $this->_prepareNewCustomerQuote();
          $newCustomer = true;
        }
      } else if ($accountHelper->isRegistrationSuccess()) {
        
      }
    }

    $servicequote = Mage::getModel('idecheckoutvm/service_quote', $this->getQuote());
    $order = $servicequote->submitAll();

    if ($newCustomer) {
      try {
        $this->_involveNewCustomer();
      } catch (Exception $e) {
        Mage::logException($e);
      }
    }
    $this->getCheckout()->setLastQuoteId($this->getQuote()->getId())
        ->setLastSuccessQuoteId($this->getQuote()->getId())
        ->clearHelperData();

    $order = $servicequote->getOrder();
    if ($order) {
      Mage::dispatchEvent('checkout_type_onepage_save_order_after', array('order' => $order, 'quote' => $this->getQuote()));
      $redirectUrl = $this->getQuote()->getPayment()->getOrderPlaceRedirectUrl();
      if (!$redirectUrl && $order->getCanSendNewEmailFlag()) {
        try {
          $order->sendNewOrderEmail();
        } catch (Exception $e) {
          Mage::logException($e);
        }
      }

      $this->getCheckout()->setLastOrderId($order->getId())->setRedirectUrl($redirectUrl)->setLastRealOrderId($order->getIncrementId());
      $agreement = $order->getPayment()->getBillingAgreement();
      if ($agreement) {
        $this->getCheckout()->setLastBillingAgreementId($agreement->getId());
      }
    }

    $profiles = $servicequote->getRecurringPaymentProfiles();
    if ($profiles) {
      $ids = array();
      foreach ($profiles as $profile)
        $ids[] = $profile->getId();

      $this->getCheckout()->setLastRecurringProfileIds($ids);
    }
    Mage::dispatchEvent('checkout_submit_all_after', array('order' => $order, 'quote' => $this->getQuote(), 'recurring_profiles' => $profiles));

    return $this;
  }

  /**
   * 
   * 
   * @param type $address
   * @return type
   */
  public function validateAddress($address) {
    $errors = array();
    $address->implodeStreetAddress();
    $formInputs = Mage::getStoreConfig('idecheckoutvm/address');
    $type = $address->getAddressType();
    $helper = Mage::helper('customer');

    if (!Zend_Validate::is($address->getFirstname(), 'NotEmpty')) {
      $errors[] = Ideasa_IdeCheckoutvm_ErrorField::getInstance($type . ':firstname', $helper->__('Please enter the first name.'));
    }
    if (!Zend_Validate::is($address->getLastname(), 'NotEmpty')) {
      $errors[] = Ideasa_IdeCheckoutvm_ErrorField::getInstance($type . ':lastname', $helper->__('Please enter the last name.'));
    }
    if ($formInputs['company'] === 'req' && !Zend_Validate::is($address->getCompany(), 'NotEmpty')) {
      $errors[] = Ideasa_IdeCheckoutvm_ErrorField::getInstance($type . ':company', $helper->__('Please enter the company.'));
    }
    if ($formInputs['state'] === 'req' && $address->getCountryModel()->getRegionCollection()->getSize() && !Zend_Validate::is($address->getRegionId(), 'NotEmpty')) {
      $errors[] = Ideasa_IdeCheckoutvm_ErrorField::getInstance($type . ':state', $helper->__('Please enter the state/province.'));
    }
    if ($formInputs['address'] === 'req' && !Zend_Validate::is($address->getStreet(1), 'NotEmpty')) {
      $errors[] = Ideasa_IdeCheckoutvm_ErrorField::getInstance($type . ':street1', $helper->__('Please enter the street.'));
    }
    if ($formInputs['city'] === 'req' && !Zend_Validate::is($address->getCity(), 'NotEmpty')) {
      $errors[] = Ideasa_IdeCheckoutvm_ErrorField::getInstance($type . ':city', $helper->__('Please enter the city.'));
    }

    $optZip = Mage::helper('directory')->getCountriesWithOptionalZip();
    if ($formInputs['zip'] === 'required' && !in_array($address->getCountryId(), $optZip) && !Zend_Validate::is($address->getPostcode(), 'NotEmpty')) {
      $errors[] = Ideasa_IdeCheckoutvm_ErrorField::getInstance($type . ':zip', $helper->__('Please enter the zip code.'));
    }
    if ($formInputs['phone'] === 'req' && !Zend_Validate::is($address->getTelephone(), 'NotEmpty')) {
      $errors[] = Ideasa_IdeCheckoutvm_ErrorField::getInstance($type . ':phone', $helper->__('Please enter the phone number.'));
    }
    if ($formInputs['fax'] === 'req' && !Zend_Validate::is($address->getFax(), 'NotEmpty')) {
      $errors[] = Ideasa_IdeCheckoutvm_ErrorField::getInstance($type . ':fax', $helper->__('Please enter the fax.'));
    }
    if ($formInputs['country'] === 'req' && !Zend_Validate::is($address->getCountryId(), 'NotEmpty')) {
      $errors[] = Ideasa_IdeCheckoutvm_ErrorField::getInstance($type . ':country', $helper->__('Please choose the country.'));
    }
    if (empty($errors) || $address->getShouldIgnoreValidation()) {
      return true;
    }

    return $errors;
  }

  /**
   * Save billing address information to quote
   * This method is called by One Page Checkout JS (AJAX) while saving the billing information.
   *
   * @param   array $data
   * @param   int $customerAddressId
   * @return  Mage_Checkout_Model_Type_Onepage
   */
  public function saveBilling($data, $customerAddressId) {
    if (empty($data)) {
      Ideasa_IdeCheckoutvm_BusinessException::throwException($this->helper->__('Invalid data.'));
    }

    // reve os métodos de criação de conta
    $helper = Mage::helper('idecheckoutvm/account');
    if ($helper->isRequiredRegistration()) {
      $this->getQuote()->setCheckoutMethod(self::METHOD_REGISTER);
    } else if ($helper->isDisableRegistration()) {
      $this->getQuote()->setCheckoutMethod(self::METHOD_GUEST);
    } else if ($helper->isAllowGuest()) {
      if (isset($data['register_account']) && $data['register_account'] == '1') {
        $this->getQuote()->setCheckoutMethod(self::METHOD_REGISTER);
      } else {
        $this->getQuote()->setCheckoutMethod(self::METHOD_GUEST);
      }
    } else if ($helper->isRegistrationSuccess()) {
      // FIXME: URGENTE, implementar
    } else if ($helper->isAutoGenerateAccount()) {
      $this->getQuote()->setCheckoutMethod(self::METHOD_REGISTER);
    }

    $address = $this->getQuote()->getBillingAddress();
    $addressForm = Mage::getModel('idecheckoutvm/customer_form');
    $addressForm->setFormCode('customer_address_edit')->setEntityType('customer_address')->setIsAjaxRequest(Mage::app()->getRequest()->isAjax());

    if (!empty($customerAddressId)) {
      $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
      if ($customerAddress->getId()) {
        if ($customerAddress->getCustomerId() != $this->getQuote()->getCustomerId()) {
          Ideasa_IdeCheckoutvm_BusinessException::throwException($this->helper->__('Customer Address is not valid.'));
        }
        $address->importCustomerAddress($customerAddress)->setSaveInAddressBook(0);
        $addressForm->setEntity($address);
        $addressData = $address->getData();
        $addressData['postcode'] = str_replace('-', '', $addressData['postcode']);
        $addressData['addressType'] = 'billing';
        $addressErrors = $addressForm->validateData($addressData);

        if ($addressErrors !== true) {
          Mage::log($addressErrors);
          Ideasa_IdeCheckoutvm_ValidatorException::throwException($this->helper->__('Please check billing address information.'), $addressErrors);
        }
      }
    } else {
      $addressForm->setEntity($address);
      // emulate request object
      $addressData = $addressForm->extractData($addressForm->prepareRequest($data));
      $addressData['postcode'] = str_replace('-', '', $addressData['postcode']);
      $addressData['addressType'] = 'billing';
      $addressErrors = $addressForm->validateData($addressData);
      
      if ($addressErrors !== true) {
        Mage::log($addressErrors);
        Ideasa_IdeCheckoutvm_ValidatorException::throwException($this->helper->__('Please check billing address information.'), $addressErrors);
      }
      $addressForm->compactData($addressData);
      //unset billing address attributes which were not shown in form
      foreach ($addressForm->getAttributes() as $attribute) {
        if (!isset($data[$attribute->getAttributeCode()])) {
          $address->setData($attribute->getAttributeCode(), null);
        }
      }
      $address->setCustomerAddressId(null);
      // Additional form data, not fetched by extractData (as it fetches only attributes)
      $address->setSaveInAddressBook(empty($data['save_in_address_book']) ? 0 : 1);
    }
    // set email for newly created user
    if (!$address->getEmail() && $this->getQuote()->getCustomerEmail()) {
      $address->setEmail($this->getQuote()->getCustomerEmail());
    }

    $validateRes = $this->validateAddress($address);
    if ($validateRes !== true) {
      Ideasa_IdeCheckoutvm_ValidatorException::throwException($this->helper->__('Please check billing address information.'), $validateRes);
    }

    $address->implodeStreetAddress();

    $customerValidator = $this->_validateCustomerData($data);
    if ($customerValidator !== true) {
      Ideasa_IdeCheckoutvm_ValidatorException::throwException($this->helper->__('Please check customer information.'), $customerValidator);
    }

    if (!$this->getQuote()->getCustomerId() && $this->isMethodRegister()) {
      if ($this->_customerEmailExists($address->getEmail(), Mage::app()->getWebsite()->getId())) {
        $emailValidator = Ideasa_IdeCheckoutvm_ErrorField::getInstance('billing:email', $this->customerEmailExistsMessage);
        Ideasa_IdeCheckoutvm_ValidatorException::throwException($this->helper->__('Please check customer information.'), $emailValidator);
      }
    }

    if (!$this->getQuote()->isVirtual()) {
      /**
       * Billing address using otions
       */
      $usingCase = isset($data['use_for_shipping']) ? (int) $data['use_for_shipping'] : 0;

      switch ($usingCase) {
        case 0:
          $shipping = $this->getQuote()->getShippingAddress();
          $shipping->setSameAsBilling(0);
          break;
        case 1:
          $billing = clone $address;
          $billing->unsAddressId()->unsAddressType();
          $shipping = $this->getQuote()->getShippingAddress();
          $shippingMethod = $shipping->getShippingMethod();

          // Billing address properties that must be always copied to shipping address
          $requiredBillingAttributes = array('customer_address_id');

          // don't reset original shipping data, if it was not changed by customer
          foreach ($shipping->getData() as $shippingKey => $shippingValue) {
            if (!is_null($shippingValue) && !is_null($billing->getData($shippingKey)) && !isset($data[$shippingKey]) && !in_array($shippingKey, $requiredBillingAttributes)) {
              $billing->unsetData($shippingKey);
            }
          }

          $shipping->addData($billing->getData())
              ->setSameAsBilling(1)
              ->setSaveInAddressBook(0)
              ->setShippingMethod($shippingMethod)
              ->setCollectShippingRates(true);
          break;
      }
    }
    $this->getQuote()->collectTotals();
    $this->getQuote()->save();

    if (!$this->getQuote()->isVirtual()) {
      $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
    }

    return array();
  }

  /**
   * Validate customer data and set some its data for further usage in quote
   * Will return either true or array with error messages
   *
   * @param array $data
   * @return true|array
   */
  protected function _validateCustomerData(array $data) {
    $customerForm = Mage::getModel('idecheckoutvm/customer_form');
    $customerForm->setFormCode('checkout_register')->setIsAjaxRequest(Mage::app()->getRequest()->isAjax());

    $quote = $this->getQuote();
    if ($quote->getCustomerId()) {
      $customer = $quote->getCustomer();
      $customerForm->setEntity($customer);
      $customerData = $quote->getCustomer()->getData();
    } else {
      //$customer = Mage::getModel('customer/customer');
      $customer = Mage::getModel('idecheckoutvm/customer_customer');
      $customerForm->setEntity($customer);
      $customerRequest = $customerForm->prepareRequest($data);
      $customerData = $customerForm->extractData($customerRequest);
    }

    $customerData['addressType'] = 'billing';
    $customerErrors = $customerForm->validateData($customerData);
    if ($customerErrors !== true) {
      return $customerErrors;
    }
    if ($quote->getCustomerId()) {
      return true;
    }
    $customerForm->compactData($customerData);

    $accountHelper = Mage::helper('idecheckoutvm/account');
    $password = null;
    $confirmPassword = null;
    if ($this->isMethodRegister()) {
      if ($accountHelper->isRequiredRegistration()) {
        $password = $customerRequest->getParam('customer_password');
        $confirmPassword = $customerRequest->getParam('confirm_password');
      } else if ($accountHelper->isAllowGuest()) {
        if ($customerRequest->getParam('register_account') == '1') {
          $password = $customerRequest->getParam('customer_password');
          $confirmPassword = $customerRequest->getParam('confirm_password');
        }
      } else if ($accountHelper->isAutoGenerateAccount()) {
        $password = $customer->generatePassword();
        $confirmPassword = $password;
      }
    } else {
      // spoof customer password for guest
      $password = $customer->generatePassword();
      $confirmPassword = $password;
      // set NOT LOGGED IN group id explicitly,
      $customer->setGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
    }
    // spoof customer password for guest
    $customer->setPassword($password);
    $customer->setConfirmation($confirmPassword);

    $customerValidator = $customer->validate();
    if ($customerValidator !== true) {
      return $customerValidator;
    }
    if ($this->isMethodRegister()) {
      $quote->setPasswordHash($customer->encryptPassword($customer->getPassword()));
    }
    $quote->getBillingAddress()->setEmail($customer->getEmail());

    // copy customer data to quote
    Mage::helper('core')->copyFieldset('customer_account', 'to_quote', $customer, $quote);

    return true;
  }

  /**
   * Save checkout shipping address
   *
   * @param   array $data
   * @param   int $customerAddressId
   * @return  Mage_Checkout_Model_Type_Onepage
   */
  public function saveShipping($data, $customerAddressId) {
    if (empty($data)) {
      Ideasa_IdeCheckoutvm_BusinessException::throwException($this->helper->__('Invalid data.'));
    }
    $address = $this->getQuote()->getShippingAddress();

    /* @var $addressForm Mage_Customer_Model_Form */
    $addressForm = Mage::getModel('idecheckoutvm/customer_form');
    $addressForm->setFormCode('customer_address_edit')->setEntityType('customer_address')->setIsAjaxRequest(Mage::app()->getRequest()->isAjax());

    if (!empty($customerAddressId)) {
      $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
      if ($customerAddress->getId()) {
        if ($customerAddress->getCustomerId() != $this->getQuote()->getCustomerId()) {
          Ideasa_IdeCheckoutvm_BusinessException::throwException($this->helper->__('Customer Address is not valid.'));
        }
        $address->importCustomerAddress($customerAddress)->setSaveInAddressBook(0);
        $addressForm->setEntity($address);

        $addressData = $address->getData();
        $addressData['addressType'] = 'billing';
        $addressErrors = $addressForm->validateData($address->getData());
        if ($addressErrors !== true) {
          Ideasa_IdeCheckoutvm_ValidatorException::throwException($this->helper->__('Please check shipping address information.'), $addressErrors);
        }
      }
    } else {
      $addressForm->setEntity($address);
      // emulate request object
      $addressData = $addressForm->extractData($addressForm->prepareRequest($data));
      $addressData['addressType'] = 'billing';
      $addressErrors = $addressForm->validateData($addressData);
      if ($addressErrors !== true) {
        Ideasa_IdeCheckoutvm_ValidatorException::throwException($this->helper->__('Please check shipping address information.'), $addressErrors);
      }
      $addressForm->compactData($addressData);
      // unset shipping address attributes which were not shown in form
      foreach ($addressForm->getAttributes() as $attribute) {
        if (!isset($data[$attribute->getAttributeCode()])) {
          $address->setData($attribute->getAttributeCode(), null);
        }
      }
      $address->setCustomerAddressId(null);
      // Additional form data, not fetched by extractData (as it fetches only attributes)
      $address->setSaveInAddressBook(empty($data['save_in_address_book']) ? 0 : 1);
      $address->setSameAsBilling(empty($data['same_as_billing']) ? 0 : 1);
    }
    $address->implodeStreetAddress();
    $address->setCollectShippingRates(true);

    $validateRes = $this->validateAddress($address);
    if ($validateRes !== true) {
      Ideasa_IdeCheckoutvm_ValidatorException::throwException($this->helper->__('Please check shipping address information.'), $validateRes);
    }
    $this->getQuote()->collectTotals()->save();

    return array();
  }

  /**
   * Specify quote shipping method
   *
   * @param   string $shippingMethod
   * @return  array
   */
  public function saveShippingMethod($shippingMethod) {
    if (empty($shippingMethod)) {
      return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid shipping method.'));
    }
    $rate = $this->getQuote()->getShippingAddress()->getShippingRateByCode($shippingMethod);
    if (!$rate) {
      return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid shipping method.'));
    }
    $this->getQuote()->getShippingAddress()->setShippingMethod($shippingMethod);

    return array();
  }

  /**
   * Save billing address information to quote
   * This method is called by One Page Checkout JS (AJAX) while saving the billing information.
   *
   * @param   array $data
   * @param   int $customerAddressId
   * @return  Mage_Checkout_Model_Type_Onepage
   */
  public function updateBilling($data, $customerAddressId) {
    if (empty($data)) {
      return null;
    }
    $quote = $this->getQuote();

    $address = $quote->getBillingAddress();
    $addressForm = Mage::getModel('idecheckoutvm/customer_form');
    $addressForm->setFormCode('customer_address_edit')->setEntityType('customer_address')->setIsAjaxRequest(Mage::app()->getRequest()->isAjax());

    if (!empty($customerAddressId)) {
      $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
      if ($customerAddress->getId()) {
        if ($customerAddress->getCustomerId() != $quote->getCustomerId()) {
          Ideasa_IdeCheckoutvm_BusinessException::throwException($this->helper->__('Customer Address is not valid.'));
        }
        $address->importCustomerAddress($customerAddress)->setSaveInAddressBook(0);
        $addressForm->setEntity($address);
        $addressData = $address->getData();
        $addressData['addressType'] = 'billing';
      }
    } else {
      $addressForm->setEntity($address);
      // emulate request object
      $addressData = $addressForm->extractData($addressForm->prepareRequest($data));
      $addressData['addressType'] = 'billing';
      $addressForm->compactData($addressData);
      //unset billing address attributes which were not shown in form
      foreach ($addressForm->getAttributes() as $attribute) {
        if (!isset($data[$attribute->getAttributeCode()])) {
          $address->setData($attribute->getAttributeCode(), NULL);
        }
      }
      $address->setCustomerAddressId(null);
      // Additional form data, not fetched by extractData (as it fetches only attributes)
      $address->setSaveInAddressBook(empty($data['save_in_address_book']) ? 0 : 1);
    }
    $address->implodeStreetAddress();

    if (!$quote->isVirtual()) {
      /**
       * Billing address using otions
       */
      $usingCase = isset($data['use_for_shipping']) ? (int) $data['use_for_shipping'] : 0;

      switch ($usingCase) {
        case 0:
          $shipping = $quote->getShippingAddress();
          $shipping->setSameAsBilling(0);
          break;
        case 1:
          $billing = clone $address;
          $billing->unsAddressId()->unsAddressType();
          $shipping = $quote->getShippingAddress();
          $shippingMethod = $shipping->getShippingMethod();

          // Billing address properties that must be always copied to shipping address
          $requiredBillingAttributes = array('customer_address_id');

          // don't reset original shipping data, if it was not changed by customer
          foreach ($shipping->getData() as $shippingKey => $shippingValue) {
            if (!is_null($shippingValue) && !is_null($billing->getData($shippingKey)) && !isset($data[$shippingKey]) && !in_array($shippingKey, $requiredBillingAttributes)) {
              $billing->unsetData($shippingKey);
            }
          }
          $shipping->addData($billing->getData())
              ->setSameAsBilling(1)
              ->setShippingMethod($shippingMethod)
              ->setCollectShippingRates(true);
          break;
      }
    }

    if (!$quote->isVirtual()) {
      //Recollect Shipping rates for shipping methods
      $quote->getShippingAddress()->setCollectShippingRates(true);
    }
    return array();
  }

  /**
   * Prepare quote for guest checkout order submit
   *
   * @return Mage_Checkout_Model_Type_Onepage
   */
  protected function _prepareGuestQuote() {
    $quote = $this->getQuote();
    $quote->setCustomerId(null)
        ->setCustomerEmail($quote->getBillingAddress()->getEmail())
        ->setCustomerIsGuest(true)
        ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
    return $this;
  }

  /**
   * Prepare quote for customer registration and customer order submit
   *
   * @return Mage_Checkout_Model_Type_Onepage
   */
  protected function _prepareNewCustomerQuote() {
    $quote = $this->getQuote();
    $billing = $quote->getBillingAddress();
    $shipping = $quote->isVirtual() ? null : $quote->getShippingAddress();

    $customer = $quote->getCustomer();
    $customerBilling = $billing->exportCustomerAddress();
    $customer->addAddress($customerBilling);
    $billing->setCustomerAddress($customerBilling);
    $customerBilling->setIsDefaultBilling(true);

    if ($shipping && !$shipping->getSameAsBilling()) {
      $customerShipping = $shipping->exportCustomerAddress();
      $customer->addAddress($customerShipping);
      $shipping->setCustomerAddress($customerShipping);
      $customerShipping->setIsDefaultShipping(true);
    } else {
      $customerBilling->setIsDefaultShipping(true);
    }

    // TODO
    Mage::helper('core')->copyFieldset('checkout_onepage_quote', 'to_customer', $quote, $customer);

    $customer->setPassword($customer->decryptPassword($quote->getPasswordHash()));
    $customer->setPasswordHash($customer->hashPassword($customer->getPassword()));
    $quote->setCustomer($customer)->setCustomerId(true);
  }

  /**
   * Prepare quote for customer order submit
   *
   * @return Mage_Checkout_Model_Type_Onepage
   */
  protected function _prepareCustomerQuote() {
    $quote = $this->getQuote();
    $billing = $quote->getBillingAddress();
    $shipping = $quote->isVirtual() ? null : $quote->getShippingAddress();

    $customer = $this->getCustomerSession()->getCustomer();
    if (!$billing->getCustomerId() || $billing->getSaveInAddressBook()) {
      $customerBilling = $billing->exportCustomerAddress();
      $customer->addAddress($customerBilling);
      $billing->setCustomerAddress($customerBilling);
    }

    if ($shipping && !$shipping->getSameAsBilling() &&
        (!$shipping->getCustomerId() || $shipping->getSaveInAddressBook())) {
      $customerShipping = $shipping->exportCustomerAddress();
      $customer->addAddress($customerShipping);
      $shipping->setCustomerAddress($customerShipping);
    }

    if (isset($customerBilling) && !$customer->getDefaultBilling()) {
      $customerBilling->setIsDefaultBilling(true);
    }
    if ($shipping && isset($customerShipping) && !$customer->getDefaultShipping()) {
      $customerShipping->setIsDefaultShipping(true);
    } else if (isset($customerBilling) && !$customer->getDefaultShipping()) {
      $customerBilling->setIsDefaultShipping(true);
    }
    $quote->setCustomer($customer);
  }

  /**
   * Specify quote payment method
   *
   * @param   array $data
   * @return  array
   */
  public function savePayment($data) {
    if (empty($data)) {
      return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid data.'));
    }
    $quote = $this->getQuote();
    $quote->getPayment()->setMethod($data['method'])->getMethodInstance();
    if ($quote->isVirtual()) {
      $quote->getBillingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
    } else {
      $quote->getShippingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
    }

    // shipping totals may be affected by payment method
    if (!$quote->isVirtual() && $quote->getShippingAddress()) {
      $quote->getShippingAddress()->setCollectShippingRates(true);
    }
    $payment = $quote->getPayment();
    $payment->importData($data);
    $quote->save();

    return array();
  }

  /**
   * Involve new customer to system
   *
   * @return Mage_Checkout_Model_Type_Onepage
   */
  protected function _involveNewCustomer() {
    $customer = $this->getQuote()->getCustomer();
    if ($customer->isConfirmationRequired()) {
      $customer->sendNewAccountEmail('confirmation', '', $this->getQuote()->getStoreId());
      $url = Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail());
      $this->getCustomerSession()->addSuccess(
          Mage::helper('customer')->__('Account confirmation is required. Please, check your e-mail for confirmation link. To resend confirmation email please <a href="%s">click here</a>.', $url)
      );
    } else {
      $customer->sendNewAccountEmail('registered', '', $this->getQuote()->getStoreId());
      $this->getCustomerSession()->loginById($customer->getId());
    }
    return $this;
  }

  /**
   * Check if customer email exists
   *
   * @param string $email
   * @param int $websiteId
   * @return false|Mage_Customer_Model_Customer
   */
  protected function _customerEmailExists($email, $websiteId = null) {
    $customer = Mage::getModel('customer/customer');
    if ($websiteId) {
      $customer->setWebsiteId($websiteId);
    }
    $customer->loadByEmail($email);
    if ($customer->getId()) {
      return $customer;
    }
    return false;
  }

  /**
   * 
   * 
   * @param type $agreements
   * @return type
   */
  public function validateAgreements($agreements, $postAgree) {
    $errors = array();
    $isDifferent = array_diff($agreements, $postAgree);
    if ($isDifferent) {
      $helper = Mage::helper('idecheckoutvm');
      foreach ($agreements as $key => $value) {
        if (!in_array($value, $postAgree)) {
          $errors[] = Ideasa_IdeCheckoutvm_ErrorField::getInstance('agreement-' . $value, $helper->__('Agreement is required'));
        }
      }
    }

    if (empty($errors)) {
      return true;
    }

    return $errors;
  }

}
