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

class Ideasa_IdeCheckoutvm_Helper_Mageversion extends Mage_Core_Helper_Abstract {
    /**
     * 
     */
    const VERSION_1400 = '1.4.0.0';

    const VERSION_1401 = '1.4.0.1';

    const VERSION_1410 = '1.4.1.0';

    const VERSION_1420 = '1.4.2.0';

    const VERSION_1500 = '1.5.0.0';

    const VERSION_1501 = '1.5.0.1';

    const VERSION_1510 = '1.5.1.0';

    const VERSION_1600 = '1.6.0.0';

    const VERSION_1610 = '1.6.1.0';

    const VERSION_1620 = '1.6.2.0';

    const VERSION_1700 = '1.7.0.0';

    const VERSION_1701 = '1.7.0.1';

    const VERSION_1702 = '1.7.0.2';

    /**
     * If we are using enterprise wersion or not
     * @return int
     */
    public function isEnterprise() {
        return (int) is_object(Mage::getConfig()->getNode('global/models/enterprise_enterprise'));
    }

    public function isCommunity14() {
        if ($this->isEnterprise()) {
            return false;
        }
        $info = Mage::getVersionInfo();
        $version = "{$info['major']}.{$info['minor']}";
        return ($version == '1.4');
    }

    public function isCommunity15() {
        if ($this->isEnterprise()) {
            return false;
        }
        $info = Mage::getVersionInfo();
        $version = "{$info['major']}.{$info['minor']}";
        return ($version == '1.5');
    }

    public function isCommunity16() {
        if ($this->isEnterprise()) {
            return false;
        }
        $info = Mage::getVersionInfo();
        $version = "{$info['major']}.{$info['minor']}";
        return ($version == '1.6');
    }

    public function isCommunity17() {
        if ($this->isEnterprise()) {
            return false;
        }
        $info = Mage::getVersionInfo();
        $version = "{$info['major']}.{$info['minor']}";
        return ($version == '1.7');
    }

    /**
     * Verificar se a versão do Magento é <strong>maior</strong> ou <strong>igual</strong> <br />
     * 1.4.
     * @return type boolean
     */
    public function isCommunityGreaterOrEqual14() {
        if ($this->isEnterprise()) {
            return false;
        }
        $info = Mage::getVersionInfo();
        $version = intval("{$info['major']}{$info['minor']}}");
        return ($version >= 14);
    }

    /**
     * Verificar se a versão do Magento é <strong>maior</strong> ou <strong>igual</strong> <br />
     * 1.5.
     * @return type boolean
     */
    public function isCommunityGreaterOrEqual15() {
        if ($this->isEnterprise()) {
            return false;
        }
        $info = Mage::getVersionInfo();
        $version = intval("{$info['major']}{$info['minor']}}");
        return ($version >= 15);
    }

    /**
     * Verificar se a versão do Magento é <strong>maior</strong> ou <strong>igual</strong> <br />
     * 1.6.
     * @return type boolean
     */
    public function isCommunityGreaterOrEqual16() {
        if ($this->isEnterprise()) {
            return false;
        }
        $info = Mage::getVersionInfo();
        $version = intval("{$info['major']}{$info['minor']}}");
        return ($version >= 16);
    }

    /**
     * Verificar se a versão do Magento é <strong>maior</strong> ou <strong>igual</strong> <br />
     * 1.7.
     * @return type boolean
     */
    public function isCommunityGreaterOrEqual17() {
        if ($this->isEnterprise()) {
            return false;
        }
        $info = Mage::getVersionInfo();
        $version = intval("{$info['major']}{$info['minor']}}");
        return ($version >= 17);
    }

    /**
     * Verifica se a versão do magento é <strong>igual</strong> a 1.4.0.0.
     * 
     * @return type boolean
     */
    public function isCommunity1400() {
        if ($this->isEnterprise()) {
            return false;
        }
        $info = Mage::getVersionInfo();
        $version = "{$info['major']}.{$info['minor']}.{$info['revision']}.{$info['patch']}";
        return ($version == self::VERSION_1400);
    }

    /**
     * Verifica se a versão do magento é <strong>igual</strong> a 1.4.0.1.
     * 
     * @return type boolean
     */
    public function isCommunity1401() {
        if ($this->isEnterprise()) {
            return false;
        }
        $info = Mage::getVersionInfo();
        $version = "{$info['major']}.{$info['minor']}.{$info['revision']}.{$info['patch']}";
        return ($version == self::VERSION_1401);
    }

    /**
     * Verifica se a versão do magento é <strong>igual</strong> a 1.4.1.0.
     * 
     * @return type boolean
     */
    public function isCommunity1410() {
        if ($this->isEnterprise()) {
            return false;
        }
        $info = Mage::getVersionInfo();
        $version = "{$info['major']}.{$info['minor']}.{$info['revision']}.{$info['patch']}";
        return ($version == self::VERSION_1410);
    }

    /**
     * Verifica se a versão do magento é <strong>igual</strong> a 1.4.2.0.
     * 
     * @return type boolean
     */
    public function isCommunity1420() {
        if ($this->isEnterprise()) {
            return false;
        }
        $info = Mage::getVersionInfo();
        $version = "{$info['major']}.{$info['minor']}.{$info['revision']}.{$info['patch']}";
        return ($version == self::VERSION_1420);
    }

    /**
     * Verifica se a versão do magento é <strong>igual</strong> a 1.5.0.0.
     * 
     * @return type boolean
     */
    public function isCommunity1500() {
        if ($this->isEnterprise()) {
            return false;
        }
        $info = Mage::getVersionInfo();
        $version = "{$info['major']}.{$info['minor']}.{$info['revision']}.{$info['patch']}";
        return ($version == self::VERSION_1500);
    }

    /**
     * Verifica se a versão do magento é <strong>igual</strong> a 1.5.1.0.
     * 
     * @return type boolean
     */
    public function isCommunity1501() {
        if ($this->isEnterprise()) {
            return false;
        }
        $info = Mage::getVersionInfo();
        $version = "{$info['major']}.{$info['minor']}.{$info['revision']}.{$info['patch']}";
        return ($version == self::VERSION_1501);
    }

    /**
     * Verifica se a versão do magento é <strong>igual</strong> a 1.5.1.0.
     * 
     * @return type boolean
     */
    public function isCommunity1510() {
        if ($this->isEnterprise()) {
            return false;
        }
        $info = Mage::getVersionInfo();
        $version = "{$info['major']}.{$info['minor']}.{$info['revision']}.{$info['patch']}";
        return ($version == self::VERSION_1510);
    }

    /**
     * Verifica se a versão do magento é <strong>igual</strong> a 1.6.0.0.
     * 
     * @return type boolean
     */
    public function isCommunity1600() {
        if ($this->isEnterprise()) {
            return false;
        }
        $info = Mage::getVersionInfo();
        $version = "{$info['major']}.{$info['minor']}.{$info['revision']}.{$info['patch']}";
        return ($version == self::VERSION_1600);
    }

    /**
     * Verifica se a versão do magento é <strong>igual</strong> a 1.6.1.0.
     * 
     * @return type boolean
     */
    public function isCommunity1610() {
        if ($this->isEnterprise()) {
            return false;
        }
        $info = Mage::getVersionInfo();
        $version = "{$info['major']}.{$info['minor']}.{$info['revision']}.{$info['patch']}";
        return ($version == self::VERSION_1610);
    }

    /**
     * Verifica se a versão do magento é <strong>igual</strong> a 1.6.2.0.
     * 
     * @return type boolean
     */
    public function isCommunity1620() {
        if ($this->isEnterprise()) {
            return false;
        }
        $info = Mage::getVersionInfo();
        $version = "{$info['major']}.{$info['minor']}.{$info['revision']}.{$info['patch']}";
        return ($version == self::VERSION_1620);
    }

    /**
     * Verifica se a versão do magento é <strong>igual</strong> a 1.7.0.0.
     * 
     * @return type boolean
     */
    public function isCommunity1700() {
        if ($this->isEnterprise()) {
            return false;
        }
        $info = Mage::getVersionInfo();
        $version = "{$info['major']}.{$info['minor']}.{$info['revision']}.{$info['patch']}";
        return ($version == self::VERSION_1700);
    }

    /**
     * Verifica se a versão do magento é <strong>igual</strong> a 1.7.0.1.
     * 
     * @return type boolean
     */
    public function isCommunity1701() {
        if ($this->isEnterprise()) {
            return false;
        }
        $info = Mage::getVersionInfo();
        $version = "{$info['major']}.{$info['minor']}.{$info['revision']}.{$info['patch']}";
        return ($version == self::VERSION_1701);
    }

    /**
     * Verifica se a versão do magento é <strong>igual</strong> a 1.7.0.2.
     * 
     * @return type boolean
     */
    public function isCommunity1702() {
        if ($this->isEnterprise()) {
            return false;
        }
        $info = Mage::getVersionInfo();
        $version = "{$info['major']}.{$info['minor']}.{$info['revision']}.{$info['patch']}";
        return ($version == self::VERSION_1702);
    }

}