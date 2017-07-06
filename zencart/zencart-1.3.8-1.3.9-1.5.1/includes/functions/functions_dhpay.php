<?php
function write_log($object) {
    if(MODULE_PAYMENT_DHPAY_DEBUG==true) {
        error_log(date("[Y-m-d H:i:s]")."\t" .$object ."\r\n", 3, __DIR__ . '/../../logs/dhpay_logs/'.date("Y-m-d").'.log');

    }
    return true;
}

function generatehmacMd5($data,$private_key,$remark = '')
{
	$hash_src = '';
	$hash_key = array('amount','currency', 'invoice_id', 'merchant_id',
                      'trans_time', 'trans_date', 'status', 'ref_no', 'order_no');
    if ($data['status'] == '02') {
        $hash_key[] = 'failure_reason';
    }
	// 按 key 名进行顺序排序
	sort($hash_key);
	foreach ($hash_key as $key) {
		$hash_src .= $data[$key];
	}//var_dump($data);exit;
	// 密钥放最前面
	$hash_src = $private_key . $hash_src;
	// sha256 算法
	$hash = hash('sha256', $hash_src);
	return strtoupper($hash);
}

?>