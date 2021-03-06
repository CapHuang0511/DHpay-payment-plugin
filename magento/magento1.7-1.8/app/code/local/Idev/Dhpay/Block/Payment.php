<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Phoenix
 * @package     Idev_Dhpay
 * @copyright   Copyright (c) 2013 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Idev_Dhpay_Block_Payment extends Idev_Dhpay_Block_Placeform
{
    /**
     * Return Payment logo src
     *
     * @return string
     */
    public function getDhpayLogoSrc()
    {
        $locale = Mage::getModel('dhpay/acc')->getLocale();
        $logoFilename = Mage::getDesign()
            ->getFilename('images' . DS . 'dhpay' . DS . 'banner_120_' . $locale . '.gif', array('_type' => 'skin'));

        if (file_exists($logoFilename)) {
            return $this->getSkinUrl('images/dhpay/banner_120_'.$locale.'.gif');
        }

        return $this->getSkinUrl('images/dhpay/banner_120_int.gif');
    }
}
