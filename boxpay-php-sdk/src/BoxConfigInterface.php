<?php

namespace Src;

class BoxConfigInterface{

    /**
     * 聚宝支付域名
     */
    const BOX_PAY_DOMAIN = "https://api.234k.cn/";

    /**
     * 聚宝支付下单地址
     */
    const BOX_PAYMENT_URL = self::BOX_PAY_DOMAIN."index.php?_m=pay_gateway&_a=apply_pay";

    /**
     * 聚宝支付查询地址
     */
    const BOX_QUERY_URL = self::BOX_PAY_DOMAIN."index.php?_m=payment&_a=query";

    /**
     * 版本
     */
    const BOX_VERSION = 1;
}