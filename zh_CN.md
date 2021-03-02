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

1.2.2. 将参数按照键值对（keyvalue）的形式排列,按照参数名ASCII字典序排序

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
    "ret":0,
    "msg":"success",
    "data":{
        "out_trade_no":"20210223213715011892",
        "order_sn":"20210223213715012841",
        "code_url":"http://boxpay.cn/index.php?_m=pay¶meter=20210223213715012841"
    },
    "_trace_no":"20210223213714-5996-6035050af0592"
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
商户订单号|out\_trade\_no|可选|String|如果商户有自己的订单系统，可以自己生成订单号，否则由聚宝生成
异步通知url|notify_url|可选|String|异步通知url
body|body|可选|String|商品名称
附加数据|attach|可选|String(128)|附加数据，支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
支付完成跳转地址|return_url|可选|支付完成后的跳转地址
版本|version|可选|nodejs和java语言必填固定值2

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

字段|变量名|必填|类型|描述
----|----|---|---|----
商户订单号|out_trade_no|是|String|示例: 20210209xxxxxxxxxxxxxxxxxx
平台订单号|order_sn|是|String|示例: 20210209xxxxxxxxxxxxxxxxxx
支付地址|code_url|是|String|示例: https://aaaaa.com/index.php?_m=pay&parameter=20210209xxxx
数据签名|sign|是|String|如"7FB42F08C85670A86431F9710xxxxxx",用于本地校验

### 请求示例

#### 微信H5示例

```
{
    "mch_id":"10502105689001",
    "pay_channel_id":"201",
    "total_fee":"1",
    "notify_url":"http://www.baidu.com",
    "body":"111",
    "attach":"222",
    "return_url":"http://www.baidu.com",
    "sign":"505D2D1030B1D6562933723EB952632D"
}
```

响应

```
{
    "ret":0,
    "msg":"success",
    "data":{
        "out_trade_no":"20210223213715011892",
        "order_sn":"20210223213715012841",
        "code_url":"http://boxpay.cn/index.php?_m=parameter=20210223213715012841"
    },
    "_trace_no":"20210223213714-5996-6035050af0592"
}
```

### 回调

支付完成后，平台会把相关支付结果通过数据流的形式发送给商户，商户需要接收处理，并按文档规范返回应答。
回调参数

字段|变量名|必填|类型|描述
----|----|---|---|----
商户号|mch_id|是|String|商户号
商户订单号|out_trade_no|是|String|商户订单号
平台订单号|order_sn|是|String|平台订单号
支付渠道枚举|pay_channel_id|是|int|支付渠道枚举201:微信h5支付
body|body|可选|String|商品名称
订单金额|total_fee|是|int|订单金额
订单状态|status|是|int|订单状态
附加数据|attach|可选|String(128)|附加数据
实付金额|cash_fee|是|int|实付金额
支付时间|finish_time|是|datetime|示例:2021-02-17 20:03:40
随机字符串|nonce_str|是|String|随机字符串 如:O2r8GjZ46e
数据签名|sign|是|String|如"7FB42F08C85670A86431F9710xxxxxx",用于本地校验


响应参数
 SUCCESS

1. 同样的通知可能会多次发送给商户系统。商户系统必须能够正确处理重复的通知

2. 后台通知交互时，如果平台收到商户的应答不符合规范或超时，平台会判定本次通知失败，按照机制重新发送通知，


### 2.2 订单查询

#### Api:

```
_m=payment&_a=query
```
#### Parameters 请求参数

字段|变量名|必填|类型|描述
----|----|----|----|----
商户号|mch_id|是|String|商户号
平台订单号|order_sn|是|String|平台订单号
数据签名|sign|是|String|如"7FB42F08C85670A86431F9710xxxxxx"

### 正确响应数据说明

响应结果response.data数据说明

字段|变量名|必填|类型|描述
----|----|---|---|----
商户号|mch_id|是|String|商户号
商户订单号|out_trade_no|是|String|商户订单号
平台订单号|order_sn|是|String|平台订单号
支付渠道枚举|pay_channel_id|是|int|支付渠道枚举
订单金额|total_fee|是|int|订单金额
订单状态|status|是|int|订单状态
实付金额|cash_fee|是|int|实付金额
订单创建时间|created_at|是|datetime|示例:2021-02-17 20:02:40
支付时间|finish_time|是|datetime|示例:2021-02-17 20:03:40
body|body|可选|String|商品名称
附加数据|attach|可选|String(128)|附加数据
