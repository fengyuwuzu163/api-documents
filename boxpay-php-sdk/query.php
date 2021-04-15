<?php

require 'vendor/autoload.php';

use \Src\BoxPay;

$boxConfig = [
    'boxMchId' => '10502105689001',
    'boxKey' => 'a310b409c82e490b8db24ea1c1112db7',
];

$BoxPay = new BoxPay($boxConfig);

$data = array(
    'order_sn' => '20210415213427423121',
);

$result = $BoxPay->orderQuery($data);


print_r($result);