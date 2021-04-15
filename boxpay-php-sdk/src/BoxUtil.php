<?php

namespace Src;

class BoxUtil{

    /**
     * 生成签名
     */
    public static function makeSign($params, $md5_key)
    {
        /* 不能带入这个参数 */
        if (isset($params['_sign'])) {
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

        return strtoupper(md5($md5_key . $str . $md5_key));
    }

    /**
     * 接口签名验证
     *
     * @param [type] $app_key
     * @param array $params 带有_sign的提交参数数组
     * @return bool|int true 签名验证成功 int 签名验证失败错误码
     */
    public static function verifySign($sign, $params, $md5_key)
    {
        if (isset($params['sign'])) {
            return false;
        }

        $mySign = self::makeSign($params, $md5_key);

        if ($mySign != $sign) {
            return false;
        }

        return true;
    }
}