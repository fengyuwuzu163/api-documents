package com.boxpay.sdk.util;

import java.util.Map;
import java.util.Date;
import java.util.TreeMap;
import java.util.Iterator;
import org.apache.commons.codec.digest.DigestUtils;
import java.nio.charset.StandardCharsets;

public class BoxUtilController {

    /**
     *
     * @param params
     * @param appSecret
     * @return
     */
    public static String makeSign(Map<String, String> params, String appSecret) {

        StringBuilder signSb = new StringBuilder();
        TreeMap<String, Object> sortMap = new TreeMap<>(params);
        for (Map.Entry<String, Object> entry : sortMap.entrySet()) {
            signSb.append(entry.getKey()).append(entry.getValue());
        }

        String boxSignString = autoToUtf8(signSb.toString());
        String SignString = appSecret + boxSignString + appSecret;

        System.out.println(SignString);

        return DigestUtils.md5Hex(SignString).toUpperCase();
    }

    /**
     * 获取当前秒时间戳
     * @return
     */
    public static String time() {
        Date date = new Date();
        if (null == date) {
            return null;
        }
        String timestamp = String.valueOf(date.getTime()/1000);
        return timestamp;
    }

    private static String autoToUtf8(String str) {
        if (str == null || "".equals(str.trim())) {
            return null;
        }

        String newStr = new String(str.getBytes(StandardCharsets.ISO_8859_1), StandardCharsets.UTF_8);

        if (str.length() == newStr.length())
            return str;

        return newStr;
    }

    /**
     *
     * @param params
     * @return
     */
    public static String toQueryString(Map<String, String> params){

        StringBuilder signSb = new StringBuilder();
        TreeMap<String, Object> sortMap = new TreeMap<>(params);
        for (Map.Entry<String, Object> entry : sortMap.entrySet()) {
            if (entry.getValue() != null) {
                signSb.append(entry.getKey()).append("=").append(entry.getValue()).append("&");
            }
        }

        String reString = signSb.toString();
        String resString = reString.substring(0, reString.length()-1);

        return resString;
    }

    /**
     * 验证签名
     */
    public static Boolean checkSign(Map<String, String> params, String appSecret)
    {
        String boxSign = params.get("sign");
        params.remove("sign");

        String mySign = makeSign(params, appSecret);

        System.out.println(mySign);

        return boxSign.equals(mySign);

    }




}
