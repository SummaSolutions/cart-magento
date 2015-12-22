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

class Ideasa_Data_Config {

  const ELEMENT_TEXT = 'Ideasa_Data_Form_Text';
  const ELEMENT_PASSWORD = 'Ideasa_Data_Form_Password';
  const ELEMENT_SELECT = 'Ideasa_Data_Form_Select';
  const ELEMENT_CHECKBOX = 'Ideasa_Data_Form_Checkbox';
  const ELEMENT_RADIOS = 'Ideasa_Data_Form_Radios';

  private static $_customerFields = array(
      'prefix' => array('id' => null,
          'name' => null,
          'class' => 'prefix',
          'instance' => null,
          'label' => 'Prefix',
          'title' => 'Prefix',
          'required' => false,
          'autocomplete' => 'off'),
      'suffix' => array('id' => null,
          'name' => null,
          'class' => 'suffix',
          'instance' => null,
          'label' => 'Suffix',
          'title' => 'Suffix',
          'required' => false,
          'autocomplete' => 'off'),
      'firstname' => array('id' => null,
          'name' => null,
          'class' => 'firstname',
          'instance' => self::ELEMENT_TEXT,
          'label' => 'First Name',
          'title' => 'First Name',
          'required' => true,
          'autocomplete' => 'off'),
      'middlename' => array('id' => null,
          'name' => null,
          'class' => 'middlename',
          'instance' => self::ELEMENT_TEXT,
          'label' => 'M.I.',
          'title' => 'M.I.',
          'required' => false,
          'autocomplete' => 'off'),
      'lastname' => array('id' => null,
          'name' => null,
          'class' => 'lastname',
          'instance' => self::ELEMENT_TEXT,
          'label' => 'Last Name',
          'title' => 'Last Name',
          'required' => true,
          'autocomplete' => 'off'),
      'email' => array('id' => 'email',
          'name' => 'email',
          'class' => 'email',
          'instance' => self::ELEMENT_TEXT,
          'label' => 'Email Address',
          'title' => 'Email Address',
          'required' => true,
          'autocomplete' => 'off'),
      'company' => array('id' => 'company',
          'name' => 'company',
          'class' => 'company',
          'instance' => self::ELEMENT_TEXT,
          'label' => 'Company',
          'title' => 'Company',
          'required' => false,
          'autocomplete' => 'off'),
      'street1' => array('id' => 'street1',
          'name' => '[street][]',
          'class' => 'street1',
          'instance' => self::ELEMENT_TEXT,
          'label' => 'Street Address',
          'title' => 'Street Address',
          'required' => true,
          'autocomplete' => 'off'),
      'street2' => array('id' => 'street2',
          'name' => '[street][]',
          'class' => 'street2',
          'instance' => self::ELEMENT_TEXT,
          'label' => 'Street Address_2',
          'title' => 'Street Address_2',
          'required' => true,
          'autocomplete' => 'off'),
      'street3' => array('id' => 'street3',
          'name' => '[street][]',
          'class' => 'street3',
          'instance' => self::ELEMENT_TEXT,
          'label' => 'Street Address_3',
          'title' => 'Street Address_3',
          'required' => true,
          'autocomplete' => 'off'),
      'street4' => array('id' => 'street4',
          'name' => '[street][]',
          'class' => 'street4',
          'instance' => self::ELEMENT_TEXT,
          'label' => 'Street Address_4',
          'title' => 'Street Address_4',
          'required' => true,
          'autocomplete' => 'off'),
      'city' => array('id' => 'city',
          'name' => 'city',
          'class' => 'city',
          'instance' => self::ELEMENT_TEXT,
          'label' => 'City',
          'title' => 'City',
          'required' => true,
          'autocomplete' => 'off'),
      'country' => array('id' => 'country',
          'name' => 'country',
          'class' => 'country',
          'instance' => self::ELEMENT_TEXT,
          'label' => 'Country',
          'title' => 'Country',
          'required' => true,
          'autocomplete' => 'off'),
      'region' => array('id' => 'region_id',
          'name' => 'region_id',
          'class' => 'region_id',
          'instance' => self::ELEMENT_TEXT,
          'label' => null,
          'title' => 'State/Province',
          'required' => false,
          'autocomplete' => 'off'),
      'region_id' => array('id' => 'region_id',
          'name' => 'region_id',
          'class' => 'region_id',
          'instance' => self::ELEMENT_SELECT,
          'label' => null,
          'title' => 'State/Province',
          'required' => true,
          'autocomplete' => 'off'),
      'dob' => array('id' => 'dob',
          'name' => 'dob',
          'class' => 'dob',
          'instance' => self::ELEMENT_SELECT,
          'label' => null,
          'title' => 'Date of Birth',
          'required' => true,
          'autocomplete' => 'off'),
      'postcode' => array('id' => 'postcode',
          'name' => 'postcode',
          'class' => 'postcode',
          'instance' => self::ELEMENT_TEXT,
          'label' => 'ZIP',
          'title' => 'ZIP',
          'required' => true,
          'autocomplete' => 'off'),
      'telephone' => array('id' => 'telephone',
          'name' => 'telephone',
          'class' => 'telephone',
          'instance' => self::ELEMENT_TEXT,
          'label' => 'Telephone',
          'title' => 'Telephone',
          'required' => true,
          'autocomplete' => 'off'),
      'fax' => array('id' => 'fax',
          'name' => 'fax',
          'class' => 'fax',
          'instance' => self::ELEMENT_TEXT,
          'label' => 'Fax',
          'title' => 'Fax',
          'required' => true,
          'autocomplete' => 'off'),
      'taxvat' => array('id' => 'taxvat',
          'name' => 'taxvat',
          'class' => 'taxvat',
          'instance' => self::ELEMENT_TEXT,
          'label' => 'CPF number',
          'title' => 'CPF number',
          'required' => true,
          'autocomplete' => 'off'),
      'gender' => array('id' => 'gender',
          'name' => 'gender',
          'class' => 'gender',
          'instance' => self::ELEMENT_TEXT,
          'label' => 'Gender',
          'title' => 'Gender',
          'required' => true,
          'autocomplete' => 'off'),
      'customer_password' => array('id' => 'customer_password',
          'name' => 'customer_password',
          'class' => 'customer_password',
          'instance' => self::ELEMENT_PASSWORD,
          'label' => 'Password',
          'title' => 'Password',
          'required' => true,
          'autocomplete' => 'off'),
      'confirm_password' => array('id' => 'confirm_password',
          'name' => 'confirm_password',
          'class' => 'confirm_password',
          'instance' => self::ELEMENT_PASSWORD,
          'label' => 'Confirm Password',
          'title' => 'Confirm Password',
          'required' => true,
          'autocomplete' => 'off'),
      'tipo_pessoa' => array('id' => 'tipo_pessoa',
          'name' => 'tipo_pessoa',
          'class' => 'tipo_pessoa',
          'instance' => self::ELEMENT_RADIOS,
          'label' => null,
          'title' => 'Tipo de pessoa',
          'required' => false,
          'autocomplete' => 'off'),
      'rg' => array('id' => 'rg',
          'name' => 'rg',
          'class' => 'rg',
          'instance' => self::ELEMENT_TEXT,
          'label' => 'R.G.',
          'title' => 'Registro Geral',
          'required' => false,
          'autocomplete' => 'off'),
      'insc_est' => array('id' => 'insc_est',
          'name' => 'insc_est',
          'class' => 'insc_est',
          'instance' => self::ELEMENT_TEXT,
          'label' => 'Inscricao Estadual',
          'title' => 'Inscricao Estadual',
          'required' => false,
          'autocomplete' => 'off'),
      'razao_social' => array('id' => 'razao_social',
          'name' => 'razao_social',
          'class' => 'razao_social',
          'instance' => self::ELEMENT_TEXT,
          'label' => 'Razao Social',
          'title' => 'Razao Social',
          'required' => false,
          'autocomplete' => 'off'),
      'nome_fantasia' => array('id' => 'nome_fantasia',
          'name' => 'nome_fantasia',
          'class' => 'nome_fantasia',
          'instance' => self::ELEMENT_TEXT,
          'label' => 'Nome Fantasia',
          'title' => 'Nome Fantasia',
          'required' => false,
          'autocomplete' => 'off'),
      'mobile' => array('id' => 'mobile',
          'name' => 'mobile',
          'class' => 'mobile',
          'instance' => self::ELEMENT_TEXT,
          'label' => 'Celular',
          'title' => 'Telefone Celular',
          'required' => false,
          'autocomplete' => 'off'),
  );

  /**
   * Retorna um mapeamento do customer.
   * 
   * @param string $key
   * @return type
   */
  public static function getCustomerField($key) {
    if (array_key_exists($key, self::$_customerFields)) {
      return self::$_customerFields[$key];
    }
    return null;
  }

  /**
   * Retorna todos os mapeamento do customer.
   * 
   * @param string $key
   * @return type
   */
  public static function getCustomerFields() {
    return self::$_customerFields;
  }

  /**
   * Verifica se o campo está no nosso mapeamento.
   * 
   * @param string $key
   * @return type
   */
  public static function isMappedField($key) {
    if (array_key_exists($key, self::$_customerFields)) {
      return true;
    }
    return false;
  }

}
