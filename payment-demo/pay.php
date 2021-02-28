<?php


require_once "config.php";

header("Content-type: text/html; charset=UTF-8");
function curlPost($url, $post_data = array(), $timeout = 5, $header = "", $data_type = "") {
    $header = empty($header) ? '' : $header;
    //支持json数据数据提交
    if($data_type == 'json'){
        $post_string = json_encode($post_data);
    }elseif($data_type == 'array') {
        $post_string = $post_data;
    }elseif(is_array($post_data)){
        $post_string = http_build_query($post_data, '', '&');
    }

    $ch = curl_init();    // 启动一个CURL会话
    curl_setopt($ch, CURLOPT_URL, $url);     // 要访问的地址
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // 对认证证书来源的检查   // https请求 不验证证书和hosts
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
    //curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    //curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    curl_setopt($ch, CURLOPT_POST, true); // 发送一个常规的Post请求
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);     // Post提交的数据包
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);     // 设置超时限制防止死循环
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    //curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);     // 获取的信息以文件流的形式返回
//    curl_setopt($ch, CURLOPT_HTTPHEADER, $header); //模拟的header头
    $result = curl_exec($ch);

    // 打印请求的header信息
    //$a = curl_getinfo($ch);
    //var_dump($a);

    curl_close($ch);
    return $result;
}

function makeSign($params, $md5_key)
{
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

    return strtoupper(md5($md5_key . $str . $md5_key));
}

$data = array(
	'mch_id' => $mch_id,
	'out_trade_no' => '1050'.time(),
    'pay_channel_id' => '201',
	'total_fee' => '10100',
	'notify_url' => $notify_url,
	'body' => '商品',
);


$data['sign'] = makeSign($data,$key);
$response = curlPost($url, $data, 10, "", "");
print_r($response);