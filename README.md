# fastPayment

## 通用支付接口，目前已接入：通联网上收银统一下单接口功能

## 2023.10.12 增加了关于新生支付的四个接口:扫码支付C扫B、扫码支付查询、代付款到银行、代付款到银行查询

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


## 新生支付

### 新生支付文档

- [新生支付文档地址](https://www.yuque.com/chenyanfei-sjuaz/uhng8q)

### 1.扫码支付C扫B
```php
$scanPayService = new ScanPayService();
$merOrderNum = $orderId;

// 数组中的key不要乱
// 如果不喜欢数组这样赋值，可以使用对象的属性进行逐个赋值
$data = [
    // 商户ID
    'merId' => $merId,
    // 订单ID
    'merOrderNum' => $merOrderNum,
    // 支付金额（单位：分）
    'tranAmt' => 1,
    // 支付方式（微信：ORGCODE_WECHATPAY 阿里：ORGCODE_ALIPAY）
    'orgCode' => ScanPayModel::ORGCODE_WECHATPAY,
    // 进件号
    'weChatMchId' => $weChatMchId,
];
// 公私钥在数据库存储，直接以普通字符串的形式存储即可
// 签名和验签使用
// 私钥的字符串
$privateKey = '';
// 公钥的字符串
$publicKey = '';

$scanPayModel = new ScanPayModel();
$scanPayModel->setPrivateKey($privateKey);
$scanPayModel->setPublicKey($publicKey);
// 讲data数据赋值到类中
// 如果不喜欢使用数组赋值，记不住数组的key可以使用属性赋值
// $scanPayModel->merId = $merId;
// $scanPayModel->merOrderNum = $merOrderNum;
// 这样赋值就不用调用 copy 方法了
$scanPayModel->copy($data);

// ScanPayModel 主要是是数据，具体业务在 scanPay 中
// 如果觉得不想用我封装的 scanPay，自己直接重写就可以了
// 其中的校验和验签方法可以调用
// 因为付款后的返回值的判断和处理可能每个人的方式不同
$result = $scanPayService->scanPay($scanPayModel);
print '返回的数据:' . $result . PHP_EOL;

$result = json_decode($result, true);

// NewPayCode 中是返回码的说明
// 新生支付的返回码很不全，文档中有的，我都已经写进去了
$newPayCode = new NewPayCode();
print $newPayCode->getResultCode($result['resultCode']) . PHP_EOL;
```

### 2.扫码查询

```php
$data = [
    // 查询流水
    'serialID' => md5(rand()),
    // 查询方式 单笔 MODE_SINGLE 批量 MODE_MULTI
    'mode' => ScanPayQueryModel::MODE_SINGLE,
    'type' => ScanPayQueryModel::TYPE_PAY,
    // 单笔需要给出查询的订单号，批量就不用了
    'orderID' => $orderId,
    // 单笔不需要给出查询的时间
    // 批量需要给出查询的时间范围，交易多的话，查询范围尽可能少，否则慢
    'beginTime' => date('YmdH') . '0000',
    'endTime' => date('YmdH') . '5959',
    // 商户ID
    'partnerID' => $merId,
];

$scanPayQueryModel = new ScanPayQueryModel();
// 赋值，也可以通过属性的方式赋值，看习惯吧
$scanPayQueryModel->copy($data);

// 收款公私钥/商户私钥（收款）.pem
$privateKey = '';
$scanPayQueryModel->setPrivateKey($privateKey);

$queryOrderScanPayService = new QueryOrderScanPayService();
var_dump($queryOrderScanPayService->query($scanPayQueryModel));
```

### 3.代付款到银行

```php
$data = [
    // 商户id
    'merId' => $merId,
    // 订单号
    'merOrderId' => $orderId,
    // 银行账户名或者新生用户名
    'payeeName' => $payeeName,
    // 银行卡号或者新生登录名
    'payeeAccount' => $payeeAccount,
    // 格式：数字（以元为单位）
    'tranAmt' => $tranAmt,
    // 付款类型 付款到银行 PAYTYPE_BANK 付款到账户 PAYTYPE_ACCCOUNT
    'payType' => SinglePayInfoModel::PAYTYPE_BANK,
    // 是否审核 不审核 AUDITFLAG_NO 审核 AUDITFLAG_YES
    'auditFlag' => SinglePayInfoModel::AUDITFLAG_NO,
    // 收款方类型 个人 PAYEETYPE_PERSON 企业 PAYEETYPE_COMPANY
    'payeeType' => SinglePayInfoModel::PAYEETYPE_PERSON,
    // 银行简码，当payType为1且payeeType为2时必填
    'bankCode' => ''
];

$singlePayService = new SinglePayService();
$singlePayModel = new SinglePayModel();

$singlePayModel->copy($data);

$privateKey = '';
$publicKey = '';

$singlePayModel->setPrivateKey($privateKey);
$singlePayModel->setPublicKey($publicKey);
$result = $singlePayService->singlePay($singlePayModel);
```

### 4.代付款到银行查询

```php
$data = [
    // 商户id
    'merId' => $merId,
    // 查询的订单id
    'merOrderId' => $orderId,
    // 提交的日期，年月日即可
    'submitTime' => $submitTime,
];

$singlePayQueryModel = new SinglePayQueryModel();
$singlePayQueryModel->copy($data);

$privateKey = '';
$publicKey = '';

$singlePayQueryModel->setPrivateKey($privateKey);
$singlePayQueryModel->setPublicKey($publicKey);

$singlePayQueryService = new SinglePayQueryService();
$result = $singlePayQueryService->query($singlePayQueryModel);
```

## 代码贡献

如果您有发现有BUG，欢迎 Star，欢迎 PR ！

## 商务合作
手机和微信: 18903467858
欢迎商务联系！合作共赢！
