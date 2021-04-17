package com.boxpay.sdk.config;

public class BoxPayConfigController {

    // 设置自己的商户号
    static String MchId = "10502105689001";

    // 设置自己商户Key
    static String MchKey = "a310b409c82e490b8db24ea1c1112db7";

    static String BoxDomain = "https://api.234k.cn/";


    /**
     * 获取商户号
     * @return
     */
    public static String getMchId(){
        return MchId;
    }

    /**
     * 获取 KEY
     */
    public static String getMchKey(){
        return MchKey;
    }

    /**
     * 获取下单地址
     */
    public static String getPayUrl()
    {
        String payurl = BoxDomain + "/index.php?_m=pay_gateway&_a=apply_pay";
        return payurl;
    }

    /**
     * 获取查询地址
     */
    public static String getQueryUrl(){
        String queryurl = BoxDomain + "/index.php?_m=payment&_a=query";
        return queryurl;
    }
}
