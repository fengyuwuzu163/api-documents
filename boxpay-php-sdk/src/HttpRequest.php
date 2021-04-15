<?php

namespace Src;

class HttpRequest
{

    //创建静态私有的变量保存该类对象
    static private $instance;
    //防止使用new直接创建对象
    private function __construct()
    {
    }

    //防止使用clone克隆对象
    private function __clone()
    {
    }
    //单例
    static public function getInstance()
    {
        //判断$instance是否是Singleton的对象，不是则创建
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    static function post($url = '', $post_data = false, $header = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT,  'uac php sdk');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //这个是重点,不进行ssl验证,HTTPS就乖乖打开吧 .
        $output = curl_exec($ch);
        // $info = curl_getinfo($ch);
        curl_close($ch);
        return $output;
    }
    /*
    static function put($url = '', $put_data = false, $header = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); //定义请求地址
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //定义是否直接输出返回流
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT'); //定义请求类型，必须为大写
        //curl_setopt($ch, CURLOPT_HEADER,1); //定义是否显示状态头 1：显示 ； 0：不显示
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header); //定义header
        curl_setopt($ch, CURLOPT_POSTFIELDS, $put_data); //定义提交的数据
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //这个是重点。
        $res = curl_exec($ch);
        curl_close($ch); //关闭
        return $res;
    }

    static function delete($url, $header)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        //设置头
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header); //设置请求头
        curl_setopt($ch, CURLOPT_USERAGENT,  'uac php sdk');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //SSL认证。
        $output = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return (int)$http_code;
    }


    static function get($url, $header)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_HEADER, 1)#我不需要获取头部啊;
        //设置头
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT,  'uac php sdk');
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 16);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $output = curl_exec($ch);
        curl_close($ch);
        return  $output;
    }
    */
}
