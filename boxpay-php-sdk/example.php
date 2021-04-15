<?php

require 'vendor/autoload.php';

use \Src\BoxPay;

$boxConfig = [
    'boxMchId' => '10502105689001',
    'boxKey' => 'a310b409c82e490b8db24ea1c1112db7',
];


$BoxPay = new BoxPay($boxConfig);

$notify_url = "https://demo.234k.cn/notify.php";


$data = array(
    'out_trade_no' => '1050'.time(),
    'pay_channel_id' => '201',
    'total_fee' => '1000',
    'notify_url' => $notify_url,
    'body' => 'payment1000',
);


$result = $BoxPay->unifiedOrder($data);


print_r($result);

