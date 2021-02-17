# 聚宝 Pay Api Document

## 1 接口规则

### 1.1 接口协议

#### 调用聚宝 Pay接口须遵守以下规则：

1. 请求方式一律使用 POST 并将请求的数据参数的JSON字符串以 http body的方式传递

2. 请求的数据格式统一使用JSON格式，若参数有json字符串请转义双引号（\"）

3. 字符串编码请统一使用UTF-8

4. 签名算法MD5

#### 注意：
1. 接口文档部分字段使用`xxxxxxxxxxx`做了信息脱敏处理，注意甄别

### 1.2 参数签名

1.2.1. 假设请求参数如下：

```
{
        "mch_id": "10502105689001",
        "pay_channel_id": "304",
        "total_fee": "10000",
        "out_trade_no": "2021020920221357790",
        "notify_url": "https://www.baidu.com",
        "body": "abc",
        "attach": "11",
        "return_url": "https://www.baidu.com",
        "sign": "adsfagasgagahhsshshsh"
    }
```

1.2.2. 将参数按照键值对（keyvalue）的形式排列,按照参数名ASCII字典序排序,并用&连接

```
str = "attach11bodyabcmch_id10502105689001notify_urlhttps://www.baidu.comout_trade_no2021020920221357790pay_channel_id304return_urlhttps://www.baidu.comtotal_fee10000"
```

1.2.3. 再在开头和结尾拼接上密钥字符串 `685c6e3eca5c964b2b8240aa151cb75d`

```
"685c6e3eca5c964b2b8240aa151cb75d" + str + "685c6e3eca5c964b2b8240aa151cb75d"
```
即 str 为:

```
685c6e3eca5c964b2b8240aa151cb75dattach11bodyabcmch_id10502105689001notify_urlhttps://www.baidu.comout_trade_no2021020920221357790pay_channel_id304return_urlhttps://www.baidu.comtotal_fee10000685c6e3eca5c964b2b8240aa151cb75d
```


1.2.4. 最后计算MD5值并将md5字符串转换成大写

```
sign = strtoupper(md5(str))
```

sign的值为:

```
FF35FA2EBFB0DD709C33D58A26EEABDE
```

### 1.3 请求头

####  示例

```
Content-Type: application/json,
Content-Length: 128,

```

### 1.4 PHP签名示例代码：


```
<?php
/**
 * 参数数组数据签名
 * @param array $params 参数
 * @param string $md5_key 密钥
 * @return string 签名
 */
function makeSign($params, $md5_key)
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
```


### 1.5 接口域名

```

https://api.epepsb.cn

```

### 1.6 响应码说明

响应码| 响应码业务说明 | 逻辑处理
-----|--------------|-----------------------------------------------
0   |通用错误|未归类的错误，需提示用户或做相应的容错处理
200|成功|按照具体业务逻辑处理
201|刷卡交易未知状态|系统繁忙、用户支付中、输入密码等原因，需调用 `/order/query` 查询交易状态
404|接口地址错误|根据文档检查接口地址
4001|请求数据格式错误|请使用JSON数据格式
4002|无效AppId|检查AppId以及相应的密钥是否正确
4003|缺少参数|按照接传递正确参数
4004|签名错误|检查签名逻辑以及相应的密钥是否正确
4005|通讯出错|按提示排查代码错误
4006|服务器繁忙|稍后再重新调用该接口
4007|请求方式错误|请使用POST方式
40100|无效请求||根据提示处理
40101|订单已支付|
40102|订单不存在|
40103|订单已关闭|
40104|订单已撤销|
40105|订单已退款|
40106|订单号重复|
40107|余额不足|检查商户账户余额是否不足
40108|订单超过退款期限|无法完成退款
40109|缺少参数|按照接口提示补全参数
40110|编码格式错误|请使用UTF8编码格式
40111|每个二维码只可以用一次|重新获取用户支付二维码
40400|系统繁忙|系统繁忙，稍后重试
40500|支付错误|提示用户相应的信息
40600|未知错误|未归类的错误
490001|时间参数格式非法(非法时间戳)|传递正确的时间参数
491001|不是合法的json参数|传递正确的json格式参数
491002|参数长度不能超过限定长度|检查参数长度
491003|参数不能为空|传递参数值
499999|未知错误
492001|参数格式不符合指定要求|如不符合正则表达式
492002|数据不存在|如订单数据不存在
492003|上游厂商不支持此功能|排查上游是否开通这项功能
492004|请求接口出现超时|请求上游的curl出现超时
492005|上游接口响应为空|上游没有返回值
492006|支付宝汇率接口报错|(比如: ILLEGAL_SIGN)
492007|解析支付宝汇率接口响应的报文失败|上游返回的数据解析失败
493001|退款单号重复|商户提交的退款单号重复

### 1.7 通用响应数据结构

响应数据为Json格式,Key-Map 描述如下:

属性    | 说明 | 示例
-------|------|-------
code   | 业务响应码 | 200
message| 业务提示消息，根据业务结果，可直接使用该属性值提示用户 | Success
data   | 业务数据，需根据相应接口进行逻辑处理,有时为空(不存在该属性) |

正常示例

```
{
  "ret": 0,
  "msg": "success",
  "data": {
    "appid": "1000258",
    "attach": "",
    "bank_type": "",
    "body": "Shopping"
  }
}

```


失败示例

```
{
  "ret": 40500,
  "msg": "订单号重复"
}
```


## 2 接口列表

### 2.1 下单


### Api:

```
_m=pay_gateway&_a=apply_pay
```
### Parameters 请求参数

字段|变量名|必填|类型|描述
----|----|----|----|----
商户号|mch_id|是|String|mch_id,由商户后台获取，或者登录获取
支付渠道枚举|pay_channel_id|是|int|支付渠道枚举201:微信h5支付
订单金额|total_fee|是|Int|支付金额 单位为"分" 如10即0.10元
商户自身订单号|out\_trade\_no|可选|String|如果商户有自己的订单系统，可以自己生成订单号，否则由聚宝生成
异步通知url|notify_url|可选|String|异步通知url
body|body|可选|String|商品名称
附加数据|attach|可选|String(128)|附加数据，支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
支付完成跳转地址|return_url|可选|支付完成后的跳转地址

#### pay_channel_id 参数说明

参数值|描述
----------|----------
101|支付宝wap支付
201|微信H5支付


支付返回后，status,并根据其结果，决定是否调用订单查询接口进行结果查询处理

### 订单支付状态 status 说明

```
1:未支付
2:支付成功
3:已退款
4:已关闭
```

### 正确响应数据说明

响应结果response.data数据说明

字段|变量名|类型|描述
----|----|---|----
订单id|id|Int|如:10357
蓝海订单编号|sn|String|示例: 1120180209xxxxxxxxxxxxxxxxxx 唯一标号，可以用于查询，关闭，退款等操作
货币|fee_type|String|交易货币 如 HKD,AUD
商户名称|mch_name|String|如 "聚宝 Pay"
商户Id|out\_trade\_no|String|商户订单号 如: "1120180209xxxxxxxxxxxxxxxx"
支付方交易号|transaction_id|String| P563xxxxxxxxxxxxx
支付提供方|provider|String|如:alipay,wechat
订单时间|create_time|Int|时间戳 如: 1518155270
支付时间|time_end|Int|成功支付的时间戳 如1518155297
交易状态|trade_state|String| NOTPAY
二维码文本|qrcode|String|扫码支付时存在，客户端使用第三方工具，将该内容生成二维码，供用户扫描付款 如 "https://qr.alipay.com/bax03112k12liy7lrysg2004", "weixin://wxpay/bizpayurl?pr=HBXdDeM"
实际支付金额|total_fee|Int|用户需要支付的金额 单位为"分" 如:10
优惠金额|discount|Int|优惠金额，用于商家自身系统集成，显示 如:2
数据签名|sign|String|如"7FB42F08C85670A86431F9710xxxxxx",用于本地校验


### 请求示例

#### 支付宝二维码示例:

请求参数

```
{
  "appid": "1000258",
  "payment": "alipay.qrcode",
  "total_fee": 10,
  "wallet": "CN",
  "sign": "520489313B46B5D403CCD8Axxxxxxxx"
}
```

响应结果

```
{
  "code": 200,
  "message": "success",
  "data": {
    "appid": "1000258",
    "attach": "",
    "bank_type": "",
    "body": "",
    "cash_fee": "0",
    "cash_fee_type": "",
    "create_time": 1518155270,
    "detail": "",
    "fee_type": "HKD",
    "id": "13152",
    "is_subscribe": "N",
    "mch_name": "聚宝 Pay",
    "nonce_str": "JnFOwTyJLm",
    "out_trade_no": "1120180209xxxxxxxxxxxxxxxxx",
    "total_fee": 10,
    "discount": 0,
    "pay_amount": 10,
    "provider": "alipay",
    "qrcode": "https://qr.alipay.com/bax03112k12liy7lrysg2004",
    "sn": "1120180209xxxxxxxxxxxxxx",
    "time_end": 0,
    "trade_state": "NOTPAY",
    "trade_type": "NATIVE",
    "transaction_id": "P5631Vxxxxxxxxxxxxx",
    "sign": "7FB42F08C85670A86431xxxxxxxxxxx"
  }
}
```

如果在app中实现alipay，需使用Alipay sdk唤起alipay。

参考支付宝: https://docs.open.alipay.com/204/105695/

#### 支付宝WAP线上示例

```
{
  "appid": "1000258",
  "payment": alipay.wappay,
  "total_fee": "20",
  "wallet": "CN",
  "notify_url": "http://聚宝.com/notify",
  "h5_redirect_url": "http://聚宝.com/notify",
  "store_id" : "1000342",
  "body" : "shopping",
  "sign": "1FBFA9773ACEA258829477xxxxxxxxxx"
}
```

响应

```
{
  "code": 200,
  "message": "success",
  "data": {
    "appid": 1000258,
    "attach": "",
    "bank_type": "",
    "body": "shopping",
    "cash_fee": "0",
    "cash_fee_type": "",
    "create_time": "1557469674",
    "detail": "",
    "discount": "0",
    "fee_type": "HKD",
    "id": "1006228",
    "is_subscribe": "N",
    "mch_name": "聚宝 Pay",
    "nonce_str": "FBWRgJU1GC",
    "out_trade_no": "1120190510xxxxxxxxxxxxxxxx",
    "pay_amount": "20",
    "provider": "alipay",
    "qrcode": "https://api.yedpay.com/o-wap/NMJVPOM78RMMK70RL8",
    "sn": "1120190510xxxxxxxxxxxxxxxx",
    "time_end": 0,
    "total_fee": "20",
    "total_refund_fee" : 0,
    "trade_state": "NOTPAY",
    "trade_type": "WAPPAY",
    "transaction_id": "",
    "wallet": "CN",
    "sign": "5355B47A4F99F86E46658Fxxxxxxxxxx"
  }
}
```

#### 混合二维码示例

请求参数

```
{
  "appid": "1000258",
  "discount": 0,
  "notify_url": "https://payment.comenix.com/index/debug",
  "payment": "聚宝.qrcode",
  "total_fee": 13,
  "sign": "1FBFA9773ACEA258829477Exxxxxxxxxxx"
}
```

响应

```
{
  "code": 200,
  "message": "success",
  "data": {
    "appid": 1000258,
    "attach": "",
    "bank_type": "",
    "body": "",
    "cash_fee": "0",
    "cash_fee_type": "",
    "create_time": "1526957792",
    "detail": "",
    "discount": "0",
    "fee_type": "HKD",
    "id": "97736",
    "is_subscribe": "N",
    "mch_name": "聚宝 Pay",
    "nonce_str": "kROW6XeRn6",
    "out_trade_no": "1120180522xxxxxxxxxxxxxxxx",
    "pay_amount": "13",
    "provider": "聚宝",
    "qrcode": "http://api.hk.聚宝pay.com/order/qrcode/97736",
    "sn": "1120180522xxxxxxxxxxxxxxxxx",
    "time_end": 0,
    "total_fee": "13",
    "trade_state": "NOTPAY",
    "trade_type": "NATIVE",
    "transaction_id": "",
    "wallet": "",
    "sign": "8663CC409008CA4ED66D1F9xxxxxxxxx"
  }
}
```


#### 刷卡支付示例

请求参数

```
{
  "appid": "1000258",
  "code": "134602370743606195",
  "payment": "micropay",
  "total_fee": 5,
  "sign": "5D8883E85FB4D721A0CFxxxxxxxxxxxxxx"
}
```

响应

```
{
  "code": 200,
  "message": "OK",
  "data": {
    "appid": "1000258",
    "attach": "",
    "bank_type": "",
    "body": "Shopping",
    "cash_fee": "0",
    "cash_fee_type": "",
    "create_time": 1517996545,
    "detail": "",
    "fee_type": "HKD",
    "id": "11763",
    "is_subscribe": "N",
    "mch_name": "聚宝 Pay",
    "nonce_str": "5L0CLFvTA1",
    "out_trade_no": "1120180207xxxxxxxxxxxxxxx",
    "provider": "wechat",
    "qrcode": "",
    "sn": "1120180207xxxxxxxxxxxxxxxx",
    "time_end": 0,
    "total_fee": 5,
    "discount":2,
    "pay_amount":3,
    "trade_state": "NOTPAY",
    "trade_type": "MICROPAY",
    "transaction_id": "",
    "sign": "9E93F481EBD5E06xxxxxxxxxxxxxxxxxxxx"
  }
}
```


#### 支付宝APP示例

请求参数

```
{
    "appid":"1000258",
    "payment":"alipay.app",
    "total_fee":"20",
    "wallet":"CN",
    "notify_url":"http://聚宝pay.com/",
    "h5_redirect_url":"http://聚宝pay.com/",
    "body":"ITMobileTestProduct",
    "sign":"CDB471EEDACDF3661F7xxxxxxxxxxxxx"
}
```

响应结果


```
{
    "code":200,
    "message":"success",
    "data":{
        "app":"{"_input_charset":"UTF-8","body":"ITMobileTestProduct","currency":"HKD","forex_biz":"FP","it_b_pay":"30m","notify_url":"https:\/\/api.tlinx.hk\/mct1\/paystatus\/notify\/payment\/AlipayHKOL","out_trade_no":"915687149300xxxxxxxxxxxxxx","partner":"208853102xxxxxxx","payment_inst":"ALIPAYCN","payment_type":1,"product_code":"NEW_WAP_OVERSEAS_SELLER","refer_url":"http:\/\/api-mirror.hk.聚宝pay.com\/alipay\/order\/entry","seller_id":"20885310xxxxxxxxxxxx","service":"mobile.securitypay.pay","subject":"ITMobileTestProduct","total_fee":0.2,"sign":"cyouJ7enNDZEU0P94jVQnJGZKloWPCMmsyYu6x7YWUr1UeCtyTmJc0VecE3JKw8Qr9%2Bfcg6VbCwpgFz63WpfibJ7gQT4dz98jjcvLm%2B6CL3ra4P%2FQ5nwlVPIZq8HmFZNRE%2BH90c9FZ18KEzgLUibS9AYCSuvh8SMep1jeZP8lshOV6ZoVB9myyQdzG9qruhAyE69w%2FhT6JI32Wrr3UAPKhYDd7zCbOboW2aXCtcONuL%2BEoiNBft%2BintUCxR4otvKJEwjeXDZfPsEobQioPIHQuTNflsK2BOfiwUcxROoy9Wc0LFt32GWni9MVpg9u5P2v%2FRBHxAdQ%3D%3D","sign_type":"RSA"}",
        "app_format": "_input_charset=\"UTF-8\"&body=\"测试\"&currency=\"HKD\"&forex_biz=\"FP\"&it_b_pay=\"30m\"&notify_url=\"https://api.tlinx.hk/mct1/paystatus/notify/payment/AlipayHKOL\"&out_trade_no=\"91607479xxxxxxxxxxxxxxxxx\"&partner=\"208853102xxxxxxx\"&payment_inst=\"ALIPAYCN\"&payment_type=\"1\"&product_code=\"NEW_WAP_OVERSEAS_SELLER\"&refer_url=\"http://summer.聚宝tech.co/alipay/order/entry\"&seller_id=\"20885310xxxxxxx\"&service=\"mobile.securitypay.pay\"&subject=\"测试\"&total_fee=\"0.1\"&sign=\"wW6B%2BLPJ1fURqcm1pK1HfO2aDv6%2BF%2F2G9TJJrV51X2QIlp5hmuOR9QhnPEcbo0qlCZQ0BIgS1M0v1zjAO7huX%2FwUYEWN%2FBl5UfF%2FI2%2BWolh9dnInJDek7hDSGyCpjhV0E6T8eHJTDD3%2F%2FYJZ%2F3O9em%2F5iOxnInnOaxvJM8WUc6zBVVyCQmq6JE94lpN7rBQL2zDss13iJUPVXkuCZ1OccbtcisZlWtj%2FIFxDrFgSOTVhbvEfN1Zj5vSwVHO4iKil1YZqJg9LaU%2BfYzuPwff9GYhcZ5vhAwDitEPse0LjrauLlPKVbDWZGQ2JRHwMqFzEJ7RmGTqB3xxxxxxxxxxxxx\"&sign_type=\"RSA\"",
        "appid":1000258,
        "attach":"",
        "bank_type":"",
        "body":"ITMobileTestProduct",
        "cash_fee":"0",
        "cash_fee_type":"",
        "create_time":"1568714930",
        "detail":"",
        "discount":"0",
        "fee_type":"HKD",
        "id":"1338084",
        "is_subscribe":"N",
        "mch_name":"聚宝 Pay",
        "nonce_str":"r2340tF8Mv",
        "openid":"ow8Pv05TbDLNPxxxxxxxxxxxxxxxxxxxx",
        "out_trade_no":"1120190917xxxxxxxxxxxxxxxxxx",
        "pay_amount":"20",
        "provider":"alipay",
        "qrcode":"",
        "refundable":0,
        "sn":"1120190917xxxxxxxxxxxxxxxxxxx",
        "time_end":0,
        "total_fee":"20",
        "total_refund_fee":0,
        "trade_state":"NOTPAY",
        "trade_type":"APP",
        "transaction_id":"91568714xxxxxxxxxxxxxxxxxx",
        "wallet":"CN",
        "sign":"D2FBD87FDB2CB5727xxxxxxxxxxxxxxxxx"
    }
}
```

### 公众号、小程序示例

请求参数

```
{
  "appid": "1000258",
  "payment": "wechat.jsapi",
  "sub_appid": "wx6f4b43xxxxxxxxxxxx",
  "sub_openid": "oxoPW5SUhIxxxxxxxxxxxxxxx",
  "total_fee": 2,
  "wallet": "CN",
  "sign": "D6BF87F2831B3F66Axxxxxxxxxxxxxxxxx"
}
```

响应结果

```
{
  "code": 200,
  "message": "success",
  "data": {
    "appid": 1000258,
    "attach": "",
    "bank_type": "",
    "body": "Shopping",
    "cash_fee": "0",
    "cash_fee_type": "",
    "create_time": "1530669821",
    "detail": "",
    "discount": "0",
    "fee_type": "HKD",
    "id": "171811",
    "is_subscribe": "N",
    "jsapi": "{\"appId\":\"wx6f4b43exxxxxxxxxxxxx\",\"timeStamp\":\"1530669821\",\"nonceStr\":\"lNTHG8Uf5K72iVgZEyJH\",\"package\":\"prepay_id=wx04100341876500f618f0cxxxxxxxxxxxxxx\",\"signType\":\"MD5\",\"paySign\":\"CED5552DA5C377F3E65818C7A66AF45C\"}",
    "mch_name": "聚宝 Pay",
    "nonce_str": "1jnQ6C4rfk",
    "openid": "oxoPW5SUhI5lxxxxxxxxxxxxx",
    "out_trade_no": "1120180704xxxxxxxxxxxxxxxxxxxx",
    "pay_amount": "2",
    "provider": "wechat",
    "qrcode": "",
    "refundable": 0,
    "sn": "1120180704xxxxxxxxxxxxxxxxxxxxx",
    "time_end": 0,
    "total_fee": "2",
    "total_refund_fee": 0,
    "trade_state": "NOTPAY",
    "trade_type": "JSAPI",
    "transaction_id": "",
    "wallet": "CN",
    "sign": "5C22D88E332511B4FBD2xxxxxxxxxxxxxx"
  }
}

```

### 微信APP示例

请求参数

```
{
  "appid": "1000258",
  "payment": "wechat.app",
  "sub_appid": "wx6f4xxxxxxxxxxxxxxxxxx",
  "total_fee": 501,
  "wallet": "CN",
  "store_id": "1000342",
  "body": "test",
  "sign": "0BDCCE6962C76082E6xxxxxxxxxxxxxxx"
}
```
响应结果

```
{
  "code": 200,
  "message": "success",
  "data": {
    "appid": 1000343,
    "attach": "",
    "bank_type": "",
    "body": "test",
    "cash_fee": "0",
    "cash_fee_type": "",
    "create_time": "1554110899",
    "detail": "",
    "discount": "0",
    "fee_type": "HKD",
    "id": "829084",
    "is_subscribe": "N",
    "app": "{\"appid\":\"wxa4dxxxxxxxxxxxxxxx\",\"partnerid\":\"1503284471\",\"prepayid\":\"wx20190xxxxxxxxxxxxxxxx\",\"package\":\"Sign=WXPay\",\"noncestr\":\"63L8okvqA33lCR2eCBfR\",\"timestamp\":\"1554110898\",\"paySign\":\"9C48C140210DA72B1DED2xxxxxxx\"}",
    "mch_name": "聚宝 Pay",
    "nonce_str": "3YcBzhHncs",
    "out_trade_no": "1120190401xxxxxxxxxxxxxxxxx",
    "pay_amount": "2",
    "provider": "wechat",
    "qrcode": "",
    "refundable": 0,
    "sn": "1120180704xxxxxxxxxxxxxxxxxxxx",
    "time_end": 0,
    "total_fee": "501",
    "total_refund_fee": 0,
    "trade_state": "NOTPAY",
    "trade_type": "APP",
    "transaction_id": "",
    "wallet": "CN",
    "sign": "1A878D7E06559821Cxxxxxxxxxx"
  }
}

```

### 银联UPOP示例

请求参数

```
{
  "appid": "1000258",
  "payment": "unionpay.link",
  "total_fee": 10,
  "wallet": "CN",
  "notify_url":"http://聚宝pay.com/",
  "sign": "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
}
```
响应结果

```
{
  "code": 200,
  "message": "success",
  "data": {
    "adapter": "chinaums",
    "appid": 1000258,
    "attach": "",
    "bank_type": "",
    "body": "",
    "cash_fee": "0",
    "cash_fee_type": "",
    "create_time": "1606806091",
    "detail": "",
    "discount": "0",
    "fee_type": "HKD",
    "id": "2275866",
    "is_print": "0",
    "is_subscribe": "N",
    "mch_name": "聚宝 Pay",
    "nonce_str": "rZblCN3Tn5",
    "out_trade_no": "1120201201150xxxxxxxxxxxxxxxx",
    "pay_amount": "10",
    "provider": "unionpay",
    "qrcode": "https://apigw.gnete.com.hk/easyLinkApi/Payment/CreateChannelData?amount=0.1&currency=344&accessKey=1989fc10edf88de13c7176c2b3956b9e08b94cfe6d77231d0069fxxxxxxxxxxx&paymentInfoId=69cf4a55e6164cd9b293xxxxxxxxxxxx",
    "refundable": 0,
    "sn": "1120201201150xxxxxxxxxxxxxxxx",
    "time_end": 0,
    "total_fee": "10",
    "total_refund_fee": 0,
    "trade_state": "USERPAYING",
    "trade_type": "LINK",
    "transaction_id": "8a8994a975789xxxxxxxxxxxxxxxxxxx",
    "wallet": "CN",
    "sign": "670031A0FC96E723AA9xxxxxxxxxxxxx"
  }
}

```

### 拿到api数据(data.jsapi使用JSON.parse(data.jsapi)转为JSON对象)后参考微信文档，完成h5调用

[https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=7_7&index=6](https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=7_7&index=6)


### 香港钱包公众号支付

#### 1. 引入wechat js

```
<script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
```

#### 2. 通过config接口注入权限验证配置

```
<script type="text/javascript">
	wx.config({
        beta : true,
	    debug: true, // 调试作用，true为打开 false为关闭，开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
	    appId: '', // 必填，公众号的唯一标识
	    timestamp: , // 必填，生成签名的时间戳
	    nonceStr: '', // 必填，生成签名的随机串
	    signature: '',// 必填，签名参考：https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141115
	    jsApiList: ["getH5PrepayRequest"] // 必填，需要使用的JS接口列表
});
</script>
```

#### 3. 传入预支付时返回的相关参数 ，通过ready接口处理成功验证

```
<script type="text/javascript">
wx.ready(function(){
	    // config信息验证后会执行ready方法，所有接口调用都必须在config接口获得结果之后，config是一个客户端的异步操作，所以如果需要在页面加载时就调用相关接口，则须把相关接口放在ready函数中调用来确保正确执行。对于用户触发时才调用的接口，则可以直接调用，不需要放在ready函数中。
	    //该参数为BOPAY服务端返回的数据
	    var payConfig = {
               "appId" : ""// 公众号的唯一标识，
               "timeStamp" : "",生成签名的时间戳
               "nonceStr" : "", // 商户生成的随机字符串。由商户生成后传入
               "package" : "",//统一下单接口返回的prepay_id参数值，并附加商户归属地信息
               "signType" : "SHA1",//字段名称：签名方式；参数类型：字符串类型；字段来源：按照文档中所示填入，目前仅支持SHA1
               "paySign" : "" // 签名，详见5.2签名算法_JSAPI
         }
	     wx.invoke('getH5PrepayRequest', payConfig,function(res){//回调函数
             var msg = res.errMsg || res.err_msg;    //不同的版本定义的字段名不一致
             if(!msg){}
             if(-1 != msg.indexOf("ok")){//调用成功
             }else if(-1 != msg.indexOf("cancel")){//用户取消
             }else{//失败
             }
        });
	    });
</script>

```

### 回调

支付完成后，平台会把相关支付结果通过数据流的形式发送给商户，商户需要接收处理，并按文档规范返回应答。
回调参数

字段|变量名|类型|描述
----|----|---|----
付款银行|bank_type|String|付款银行编码,如:CFT
付款金额|cash_fee|Int|用户付款的金额，单位为"分" 如：20
支付货币类型|cash_fee_type|String|交易货币 如 CNY,HKD,AUD
币种|fee_type|String|币种 如 CNY,HKD,AUD
商户订单号|out\_trade\_no|String|商户订单号 如: "11201802091347484054542598"
支付方交易号|transaction_id|String| 如: P5631VZG299QZN94JD
支付完成时间|time_end|String|如:20190402162714
订单金额|total_fee|Int|订单金额 如：20
交易类型|trade_type|String|如: NATIVE
appid|appid|String| appid,由商户后台获取，或者登录获取
随机字符串|nonce_str|String|随机字符串 如:O2r8GjZ46e
数据签名|sign|String|如"7FB42F08C85670A86431xxxxxxxxxxxx",用于本地校验

响应参数
 SUCCESS

1. 同样的通知可能会多次发送给商户系统。商户系统必须能够正确处理重复的通知

2. 后台通知交互时，如果平台收到商户的应答不符合规范或超时，平台会判定本次通知失败，按照机制重新发送通知，


