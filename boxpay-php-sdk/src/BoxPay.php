<?php

namespace Src;

use Src\BoxConfigInterface;
use Src\BoxUtil;

class BoxPay{

    protected static $boxMchId = '';
    protected static $boxKey = '';

    public function __construct($boxConfig)
    {
        if(!isset($boxConfig['boxMchId']) || empty($boxConfig['boxMchId'])) {
            throw new \Exception('boxMchId be empty');
        }

        if(!isset($boxConfig['boxKey']) || empty($boxConfig['boxKey'])) {
            throw new \Exception('boxKey be empty');
        }

        self::$boxMchId = $boxConfig['boxMchId'];
        self::$boxKey = $boxConfig['boxKey'];
    }

    /**
     * 下单
     */
    public static function unifiedOrder($params)
    {
        if(!$params['pay_channel_id']){
            throw new \Exception('pay_channel_id be empty');
        }

        if(!$params['total_fee']){
            throw new \Exception('total_fee be empty');
        }

        $params['mch_id'] = self::$boxMchId;
        $params['sign'] = BoxUtil::makeSign($params, self::$boxKey);

        $http = HttpRequest::getInstance();
        $res = $http::post(BoxConfigInterface::BOX_PAYMENT_URL, $params);

        return $res;
    }

    /**
     * 查询
     */
    public static function orderQuery($params)
    {
        if(!$params['order_sn']){
            throw new \Exception('pay_channel_id be empty');
        }

        $params['mch_id'] = self::$boxMchId;
        $params['sign'] = BoxUtil::makeSign($params, self::$boxKey);

        $http = HttpRequest::getInstance();
        $res = $http::post(BoxConfigInterface::BOX_QUERY_URL, $params);

        return $res;
    }

    /**
     * 验证签名
     */
    public static function BoxVerifySign($params)
    {
        if(!$params['sign']){
            throw new \Exception('sign be empty');
        }

        $boxSign = $params['sign'];

        unset($params['sign']);

        return BoxUtil::verifySign($boxSign, $params, self::$boxKey);
    }
}