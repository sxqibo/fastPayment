# fastPayment

## 通用支付接口，目前已接入：通联网上收银统一下单接口功能
## 运行环境
PHP7.0

## 安装方法
1. 可以在你的项目根目录运行：`composer require sxqibo/fast-payment`

## 快速使用
### 通联初始化类
```
$params = [
    'cus_id'      => '',
    'app_id'      => '',
    'public_key'  => '',
    'private_key' => '',
];

$service = new FastPayment\UnionPay($params);

```

### 1. 统一支付接口
```
$data = [
    'amount'   => 1000, // 交易金额-单位为分-否-15
    'order_no' => 'T' . time(), // 商户交易单号-商户的交易订单号-否-32
    'pay_type' => 'W02', // 支付宝APP支付

    'title'      => '测试商品11', // 订单标题-订单商品名称，为空则以商户名作为商品名称-是-100-最大100个字节(50个中文字符)-
    'remark'     => '备注11', // 备注-备注信息-是-160-最大160个字节(80个中文字符)禁止出现+，空格，/，?，%，#，&，=这几类特殊符号
    'valid_time' => '5', // 有效时间-订单有效时间，以分为单位，不填默认为5分钟-是-2
    'acct'       => 'oe1Tu4gfv40yNdxs3h9RrsFDxLbw', // 支付平台用户标识-JS支付时使用：微信支付-用户的微信openid、支付宝支付-用户user_id、微信小程序-用户小程序的openid、云闪付JS-用户userId-是-32
    'notify_url' => 'http://api.newthink.cc/12321', // 交易结果通知地址-接收交易结果的异步通知回调地址，通知url必须为直接可访问的url，不能携带参数。-是-256
    'front_url'  => 'http::/www.baidu.com', // 支付完成跳转-必须为https协议地址，且不允许带参数-是-128-只支持payType=U02云闪付JS支付、payType=W02微信JS支付
];

$result = $service->pay($data);
var_dump($result);
```
>使用场景：请求扫码支付的二维码串（支持支付宝、QQ钱包、云闪付),公众号JS支付（支付宝，微信，QQ钱包，云闪付）,微信小程序支付

### 2. 统一扫码接口
```
$data = [
    'amount'         => 2000, // 交易金额-单位为分-否-15
    'order_no'       => 'T' . time(), // 商户交易单号-商户的交易订单号-否-32
    'auth_code'      => 'wxp://f2f0Cf0h0KUa048VLANefcfH5HeRihHaK3H5', // 支付授权码-如微信,支付宝,银联的付款二维码 - 否-32
    'title'          => '测试111', // 订单标题-订单商品名称，为空则以商户名作为商品名称-是-100-最大100个字节(50个中文字符)-
    'remark'         => '备注', // 备注-备注信息-是-160-最大160个字节(80个中文字符)禁止出现+，空格，/，?，%，#，&，=这几类特殊符号
    'goods_tag'      => 'youhui', // 订单支付标识-订单优惠标记，用于区分订单是否可以享受优惠，字段内容在微信后台配置券时进行设置，说明详见代金券或立减优惠-是-32-只对微信支付有效W01交易方式不支持
    'benefit_detail' => '优惠信息', // 优惠信息-Benefitdetail的json字符串,注意是String-是-不限制-仅支持微信单品优惠、W01交易方式不支持、支付宝智慧门店/支付宝单品优惠
];

$result = $service->scanqrpay($data);
var_dump($result);
```
> 使用场景：扫一扫付款二维码,获取付款二维码内容调用此接口进行收款.支持微信,支付宝,手机qq,银联规范付款二维码
### 3. 交易撤销
```
$refundNo     = 'R123123';
$refundAmount = 1000; // 分
$result       = $service->cancel($refundNo, $refundAmount, ['old_order_no' => 'T1623394747']);
var_dump($result);
```
> 使用场景：只能撤销当天的交易，全额退款，实时返回退款结果

### 4. 交易退款
```
$refundNo     = 'R123123';
$refundAmount = 1000; // 分
$result       = $service->refund($refundNo, $refundAmount, ['old_order_no' => 'T1623394747']);
var_dump($result);
```
> 使用场景：支持部分金额退款，隔天交易退款。（建议在交易完成后间隔几分钟（最短2分钟）再调用退款接口，避免出现订单状态同步不及时导致退款失败。）

### 5. 交易查询
```
$orderNo = '1234';
$result  = $service->query($orderNo);
var_dump($result);
```
> 使用场景：同时支持统一支付、支付退货两种种交易的查询

### 6. 根据授权码(付款码)获取用户ID
```
$authCode = '01'; // 01-微信付款码 02-银联userAuth
$authType = '136048058474886014';
$result   = $service->getAuthCodeToUserId($authCode, $authType);
var_dump($result);
```
> 使用场景：
  通过微信付款码换取openid
  通过银联userAuth的code(非付款码)换取userid

### 7. 微信人脸授权码获取
```
$storeId   = ''; // 门店编号-由商户定义， 各门店唯一
$storeName = '';  // 门店名称 - 有商户定义-否
$rawData   = ''; // //  初始化数据。由微信人脸SDK的接口返回。//获取方式参见微信官方刷脸支付接口：//[获取数据 getWxpayfaceRawdata](#获取数据 getWxpayfaceRawdata)
$result    = $service->getWxFacePayInfo($storeId, $storeName, $rawData);
var_dump($result);
```
> 使用场景：使用微信rawdata换取authcode

### 8. 订单关闭
```
$orderNo = '123456';
$result  = $service->close($orderNo);
var_dump($result);
```
> 使用场景：对于处理中的交易,可调用该接口直接将未付款的交易进行关闭。

### 9. 获取交易类型列表
```
$result = $service->getTrxCodeList();
var_dump($result);
```
### 10. 获取交易方式列表
```
$result = $service->getPayTypeList();
var_dump($result);
```
### 其他
- [通联接口手册](https://aipboss.allinpay.com/know/devhelp/main.php?pid=15)

- [通联官网](https://vsp.allinpay.com/login)

## 代码贡献

如果您有发现有BUG，欢迎 Star，欢迎 PR ！

## 商务合作
手机和微信: 18903467858
欢迎商务联系！合作共赢！
