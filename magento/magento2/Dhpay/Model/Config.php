<?php
/**
 * Copyright © 2016 Payssion All rights reserved.
 */

namespace Pay\Dhpay\Model;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Description of Config
 *
 * @author Payssion Technical <technical@payssion.com>
 */
class Config
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfigInterface;

    public function __construct(
    \Magento\Framework\App\Config\ScopeConfigInterface $configInterface
    )
    {
        $this->_scopeConfigInterface = $configInterface;
    }

    public function getApiKey()
    {
        $api_key = $this->_scopeConfigInterface->getValue('payment/pay/api_key', 'store');
        return $api_key;
    }

    public function getSecretKey()
    {
    	$secret_key = $this->_scopeConfigInterface->getValue('payment/pay/secret_key', 'store'); 
        return $secret_key;
    }

    public function isTestMode()
    {
       return $this->_scopeConfigInterface->getValue('payment/pay/test_mode', 'store') == 1;
    }
    
    public function getRemark()
    {
    	return $this->_scopeConfigInterface->getValue("web/unsecure/base_url", 'store');
    }
}