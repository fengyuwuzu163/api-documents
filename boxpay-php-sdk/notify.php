<?php
require 'vendor/autoload.php';

use \Src\BoxPay;

$data = $_POST;


$boxConfig = [
    'boxMchId' => '10502105689001',
    'boxKey' => 'a310b409c82e490b8db24ea1c1112db7',
];

$BoxPay = new BoxPay($boxConfig);

if(isset($data['sign']) && $BoxPay::BoxVerifySign($data)){
    $out_trade_no = $data['out_trade_no'];/*商户订单号*/
    $status = $data['status']; /*订单状态*/
    /*处理业务逻辑*/

    echo 'success';
}else{
    echo 'fail';
}
