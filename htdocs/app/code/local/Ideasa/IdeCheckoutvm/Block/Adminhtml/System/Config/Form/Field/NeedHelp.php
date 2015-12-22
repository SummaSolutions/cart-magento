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

class Ideasa_IdeCheckoutvm_Block_Adminhtml_System_Config_Form_Field_NeedHelp extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface {

    /**
     * Render element html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element) {
        $imgHelp = $this->getSkinUrl('idecheckoutvm/images/help.png');
        return sprintf('<tr class="system-fieldset-sub-head" id="row_%s"><td colspan="5"><h6 style="background-color: #FFF8E9;padding: 5px" id="%s"><img src=' . $imgHelp . '></img>%s</h6></td></tr>', $element->getHtmlId(), $element->getHtmlId(), $element->getLabel()
        );
    }
}