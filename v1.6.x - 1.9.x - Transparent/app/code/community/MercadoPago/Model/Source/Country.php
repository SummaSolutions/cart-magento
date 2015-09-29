<?php
/**
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL).
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
*
* @category   	Payment Gateway
* @package    	MercadoPago
* @author      	Gabriel Matsuoka (gabriel.matsuoka@gmail.com)
* @copyright  	Copyright (c) MercadoPago [http://www.mercadopago.com]
* @license    	http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
 
class MercadoPago_Model_Source_Country extends Mage_Payment_Model_Method_Abstract{
	
    public function toOptionArray (){

		$country = array();
		$country[] = array('value' => "mla", 'label'=> "Argentina");
		$country[] = array('value' => "mlb", 'label'=> "Brasil");
		$country[] = array('value' => "mco", 'label'=> "Colombia");
		$country[] = array('value' => "mlm", 'label'=> "Mexico");
		$country[] = array('value' => "mlc", 'label'=> "Chile");
		$country[] = array('value' => "mlv", 'label'=> "Venezuela");
		
		//force order by key
		ksort($country);
		return $country;
    
    }
}