<?php
use Tygh\Registry;
use Tygh\Session;

if (!defined('BOOTSTRAP')) { die('Access denied'); }
$addons_path = Registry::get('config.dir.addons');
include $addons_path . 'dhpay' . '/payments/dhpay.php';
