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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Paypal Data helper
 */
class Idev_Dhpay_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_MERCHANT_ID = 'dhpay/settings/merchant_id';
    const XML_PATH_PRIVATE_KEY  = 'dhpay/settings/private_key';
    const XML_PATH_MODE   = 'dhpay/settings/mode';
    const XML_PATH_CHECKOUT_METHOD   = 'dhpay/settings/checkout_method';
    const XML_PATH_STYLE_LAYOUT   = 'dhpay/settings/style_layout';
    const XML_PATH_STYLE_BODY   = 'dhpay/settings/style_body';
    const XML_PATH_STYLE_TITLE   = 'dhpay/settings/style_title';
    const XML_PATH_STYLE_BUTTON   = 'dhpay/settings/style_button';

}
