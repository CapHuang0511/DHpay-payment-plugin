<?php
/**
 * E: chinadragon@hotmail.com
 * W:www.magento.con
 */
class Magento91_Dhpay_Block_Redirect extends Mage_Core_Block_Abstract
{
	
	protected function _toHtml()
	{
		$standard = Mage::getModel('dhpay/payment');
			$html = '<html><body>';
			$html .= "<form action='".$standard->getConfigData('gateway')."' id='dhpay_payment_checkout' method='POST' name='dhpay_payment_checkout'>";
        foreach ($standard->getStandardCheckoutFormFields() as $field => $value) {
            	$html .= "<input type='hidden' name='". $field."' value='".$value."' />";
        }
				$html .= "</form>";
		
        $html.= $standard->getConfigData('redirectmsg');
		$html.= '<script type="text/javascript">document.getElementById("dhpay_payment_checkout").submit();</script>';
        $html.= '</body></html>';

        return $html;
    }
}