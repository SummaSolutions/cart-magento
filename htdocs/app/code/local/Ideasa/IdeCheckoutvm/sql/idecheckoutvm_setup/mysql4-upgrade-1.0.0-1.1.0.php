<?php
$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();

$installer->run("DELETE FROM {$this->getTable('core/config_data')} WHERE path LIKE  '%idecheckoutvm/ordering_fields%'");

$attribute = Mage::getSingleton('customer/attribute');
/* * *****************************************************************************
 * ******************************** Tipo Pessoa *********************************
 * ***************************************************************************** */
$tipoPessoa = $attribute->loadByCode('customer', 'tipo_pessoa');
if ($tipoPessoa->getId() === null) {
    $this->addAttribute('customer', 'tipo_pessoa', array(
        'type' => 'varchar',
        'input' => 'select',
        'label' => 'Tipo Pessoa',
        'source' => 'idecheckoutvm/customer_attribute_source_pessoa_view',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible' => true,
        'required' => false,
        'user_defined' => true,
        'default' => '',
        'visible_on_front' => true,
        'is_configurable' => false,
        'frontend_class' => 'tipo-pessoa'
    ));
}

if (version_compare(Mage::getVersion(), '1.4.2', '>=')) {
    Mage::getSingleton('eav/config')->getAttribute('customer', 'tipo_pessoa')
            ->setData('used_in_forms', array('adminhtml_customer',
                'adminhtml_checkout',
                'adminhtml_customer_address',
                'checkout_register',
                'customer_account_create',
                'customer_account_edit',
                'customer_address_edit',
                'customer_register_address'
                ))
            ->save();
};
if (!$conn->tableColumnExists($this->getTable('sales/quote'), 'customer_tipo_pessoa')) {
    $installer->run("ALTER TABLE {$this->getTable('sales/quote')} ADD `customer_tipo_pessoa` VARCHAR(1) DEFAULT 'F'");
}
if (!$conn->tableColumnExists($this->getTable('sales/order'), 'customer_tipo_pessoa')) {
    $installer->run("ALTER TABLE {$this->getTable('sales/order')} ADD `customer_tipo_pessoa` VARCHAR(1) DEFAULT 'F'");
}
/* * *****************************************************************************
 * ******************************** Reg. Geral *********************************
 * ***************************************************************************** */

$rg = $attribute->loadByCode('customer', 'rg');
if ($rg->getId() === null) {
    $this->addAttribute('customer', 'rg', array(
        'type' => 'varchar',
        'input' => 'text',
        'frontend_input' => 'text',
        'label' => 'RG',
        'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible' => true,
        'required' => false,
        'user_defined' => true,
        'default' => '',
        'visible_on_front' => true,
        'is_configurable' => true,
        'frontend_class' => 'rg'
    ));
}

if (version_compare(Mage::getVersion(), '1.4.2', '>=')) {
    Mage::getSingleton('eav/config')->getAttribute('customer', 'rg')
            ->setData('used_in_forms', array('adminhtml_customer',
                'customer_account_create',
                'customer_account_edit',
                'checkout_register',
                'adminhtml_customer_address',
                'customer_address_edit',
                'customer_register_address',
                'adminhtml_checkout'))
            ->save();
};
if (!$conn->tableColumnExists($this->getTable('sales/quote'), 'customer_rg')) {
    $installer->run("ALTER TABLE {$this->getTable('sales/quote')} ADD `customer_rg` VARCHAR(20) NULL");
}
if (!$conn->tableColumnExists($this->getTable('sales/order'), 'customer_rg')) {
    $installer->run("ALTER TABLE {$this->getTable('sales/order')} ADD `customer_rg` VARCHAR(20) NULL");
}
/* * *****************************************************************************
 * ******************************* Razão Social *******************************
 * ***************************************************************************** */
$inscEst = $attribute->loadByCode('customer', 'razao_social');
if ($inscEst->getId() === null) {
    $this->addAttribute('customer', 'razao_social', array(
        'type' => 'varchar',
        'input' => 'text',
        'frontend_input' => 'text',
        'label' => 'Razão Social',
        'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible' => true,
        'required' => false,
        'user_defined' => true,
        'default' => '',
        'visible_on_front' => true,
        'is_configurable' => true,
        'frontend_class' => 'razao-social'
    ));
}

if (version_compare(Mage::getVersion(), '1.4.2', '>=')) {
    Mage::getSingleton('eav/config')->getAttribute('customer', 'razao_social')
            ->setData('used_in_forms', array('adminhtml_customer',
                'customer_account_create',
                'customer_account_edit',
                'checkout_register',
                'adminhtml_customer_address',
                'customer_address_edit',
                'customer_register_address',
                'adminhtml_checkout'))
            ->save();
};
if (!$conn->tableColumnExists($this->getTable('sales/quote'), 'customer_razao_social')) {
    $installer->run("ALTER TABLE {$this->getTable('sales/quote')} ADD `customer_razao_social` VARCHAR(255) NULL");
}
if (!$conn->tableColumnExists($this->getTable('sales/order'), 'customer_razao_social')) {
    $installer->run("ALTER TABLE {$this->getTable('sales/order')} ADD `customer_razao_social` VARCHAR(255) NULL");
}
/* * *****************************************************************************
 * ******************************* Nome Fantasia *******************************
 * ***************************************************************************** */
$inscEst = $attribute->loadByCode('customer', 'nome_fantasia');
if ($inscEst->getId() === null) {
    $this->addAttribute('customer', 'nome_fantasia', array(
        'type' => 'varchar',
        'input' => 'text',
        'frontend_input' => 'text',
        'label' => 'Nome Fantasia',
        'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible' => true,
        'required' => false,
        'user_defined' => true,
        'default' => '',
        'visible_on_front' => true,
        'is_configurable' => true,
        'frontend_class' => 'nome-fantasia'
    ));
}

if (version_compare(Mage::getVersion(), '1.4.2', '>=')) {
    Mage::getSingleton('eav/config')->getAttribute('customer', 'nome_fantasia')
            ->setData('used_in_forms', array('adminhtml_customer',
                'customer_account_create',
                'customer_account_edit',
                'checkout_register',
                'adminhtml_customer_address',
                'customer_address_edit',
                'customer_register_address',
                'adminhtml_checkout'))
            ->save();
};
if (!$conn->tableColumnExists($this->getTable('sales/quote'), 'customer_nome_fantasia')) {
    $installer->run("ALTER TABLE {$this->getTable('sales/quote')} ADD `customer_nome_fantasia` VARCHAR(255) NULL");
}
if (!$conn->tableColumnExists($this->getTable('sales/order'), 'customer_nome_fantasia')) {
    $installer->run("ALTER TABLE {$this->getTable('sales/order')} ADD `customer_nome_fantasia` VARCHAR(255) NULL");
}
/* * *****************************************************************************
 * ******************************* Insc. Estadual *******************************
 * ***************************************************************************** */
$inscEst = $attribute->loadByCode('customer', 'insc_est');
if ($inscEst->getId() === null) {
    $this->addAttribute('customer', 'insc_est', array(
        'type' => 'varchar',
        'input' => 'text',
        'frontend_input' => 'text',
        'label' => 'Insc. Est.',
        'source' => '',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible' => true,
        'required' => false,
        'user_defined' => true,
        'default' => '',
        'visible_on_front' => true,
        'is_configurable' => true,
        'frontend_class' => 'insc-est'
    ));
}

if (version_compare(Mage::getVersion(), '1.4.2', '>=')) {
    Mage::getSingleton('eav/config')->getAttribute('customer', 'insc_est')
            ->setData('used_in_forms', array('adminhtml_customer',
                'customer_account_create',
                'customer_account_edit',
                'checkout_register',
                'adminhtml_customer_address',
                'customer_address_edit',
                'customer_register_address',
                'adminhtml_checkout'))
            ->save();
};
if (!$conn->tableColumnExists($this->getTable('sales/quote'), 'customer_insc_est')) {
    $installer->run("ALTER TABLE {$this->getTable('sales/quote')} ADD `customer_insc_est` VARCHAR(20) NULL");
}
if (!$conn->tableColumnExists($this->getTable('sales/order'), 'customer_insc_est')) {
    $installer->run("ALTER TABLE {$this->getTable('sales/order')} ADD `customer_insc_est` VARCHAR(20) NULL");
}
/* * *****************************************************************************
 * ******************************** Mobile Fone *********************************
 * ***************************************************************************** */
$mobileFone = $attribute->loadByCode('customer_address', 'mobile');
if ($mobileFone->getId() === null) {
    $this->addAttribute('customer_address', 'mobile', array(
        'type' => 'varchar',
        'label' => 'Celular',
        'input' => 'text',
        'frontend_input' => 'text',
        'global' => 1,
        'visible' => 1,
        'required' => false,
        'user_defined' => 1,
        'default' => '',
        'visible_on_front' => 1,
        'is_configurable' => false,
        'frontend_class' => 'mobile-fone'
    ));
}

if (version_compare(Mage::getVersion(), '1.4.2', '>=')) {
    Mage::getSingleton('eav/config')->getAttribute('customer_address', 'mobile')
            ->setData('used_in_forms', array('adminhtml_customer',
                'adminhtml_checkout',
                'adminhtml_customer_address',
                'checkout_register',
                'customer_account_create',
                'customer_account_edit',
                'customer_address_edit',
                'customer_register_address'))
            ->save();
};
if (!$conn->tableColumnExists($this->getTable('sales/quote_address'), 'mobile')) {
    $installer->run("ALTER TABLE {$this->getTable('sales/quote_address')} ADD `mobile` VARCHAR(30) NULL");
}
if (!$conn->tableColumnExists($this->getTable('sales/order_address'), 'mobile')) {
    $installer->run("ALTER TABLE {$this->getTable('sales/order_address')} ADD `mobile` VARCHAR(30) NULL");
}

$installer->endSetup();