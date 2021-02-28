<?php

require_once "config.php";


$data = $_POST;
$tenpaySign = strtoupper($data['sign']);
unset($data['sign']);

$isTenpaySign = isTenpaySign($data, $key);
if($isTenpaySign == $tenpaySign){
	$out_trade_no = $data['out_trade_no'];/*商户订单号*/
	/*处理业务逻辑*/

	echo 'success';
}else{
	echo 'fail';
}



function isTenpaySign($data, $md5_key){
    /* 不能带入这个参数 */
    if (isset($params['sign'])) {
        /* 返回空，默认就是有问题了 */
        return false;
    }
    ksort($params);
    $temp = [];
    foreach ($params as $key => $val) {
        $temp[] = $key . $val;
    }

    if (isset($params['version']) && 2 == $params['version']) {
        $str = join('', $temp);
    } else {
        $str = utf8_encode(join('', $temp));
    }

    $sign = strtoupper(md5($md5_key . $str . $md5_key));

    return $sign;
}