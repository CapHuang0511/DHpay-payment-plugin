<?php
/**
 * E: chinadragon@hotmail.com
 * W:www.magento.con
 */
class Magento91_Dhpay_Block_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        $this->setTemplate('dhpay/form.phtml');
        parent::_construct();
    }

}