<?php
/**
 * E: chinadragon@hotmail.com
 * W:www.magento.con
 */
class Magento91_Dhpay_Model_Mysql4_Api_Debug extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('dhpay/api_debug', 'debug_id');
    }
}