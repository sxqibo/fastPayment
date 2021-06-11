<?php


namespace Sxqibo\FastPayment;

use Sxqibo\FastPayment\common\Client;
use Exception;
use Sxqibo\FastPayment\common\Utility;

/**
 * 通联支付类
 *
 * Class UnionPay
 * @package Sxqibo\FastPayment
 */
class UnionPay
{
    private $serviceEndPoint     = 'https://vsp.allinpay.com/apiweb/unitorder'; // 生产环境
    private $testServiceEndPoint = 'https://test.allinpaygd.com/apiweb/unitorder'; // 测试环境

    private $appId; // 应用ID
    private $cusId; // 商户号
    // private $orgId; // 机构号
    private $privateKey;
    private $publicKey;
    private $apiVersion = '11'; // 版本号，默认11

    private $headers;
    private $client;

    public function __construct($params = [])
    {
        try {
            $this->appId = $params['app_id'];
            $this->cusId = $params['cus_id'];
            // $this->orgId      = $params['org_id'];
            $this->publicKey  = $params['public_key'];
            $this->privateKey = $params['private_key'];

            $this->headers = [
                'Content-Type' => ' application/x-www-form-urlencoded;charset=UTF-8'
            ];

            $this->client = new Client();
        } catch (\Exception $e) {
            throw new Exception('初始化实例异常');
        }

    }

    /**
     * 获取请求节点信息
     *
     * @param $key
     * @param false $isDebug
     * @return mixed|string[]
     * @throws Exception
     */
    public function getEndPoint($key, $isDebug = false)
    {
        $endpoints = [
            'pay'                 => [
                'method' => 'POST',
                'uri'    => '/pay',
                'remark' => '统一支付接口'
            ],
            'scanqrpay'           => [
                'method' => 'POST',
                'uri'    => '/scanqrpay',
                'remark' => '统一扫码接口'
            ],
            'cancel'              => [
                'method' => 'POST',
                'uri'    => '/cancel',
                'remark' => '取消交易'
            ],
            'refund'              => [
                'method' => 'POST',
                'uri'    => '/cancel',
                'remark' => '取消交易'
            ],
            'query'               => [
                'method' => 'POST',
                'uri'    => '/query',
                'remark' => '交易查询'
            ],
            'getAuthCodeToUserId' => [
                'method' => 'POST',
                'uri'    => '/authcodetouserid',
                'remark' => '根据授权码(付款码)获取用户ID'
            ],
            'getWxFacePayInfo'    => [
                'method' => 'POST',
                'uri'    => '/wxfacepayinfo',
                'remark' => '微信人脸授权码获取'
            ],
            'close'               => [
                'method' => 'POST',
                'uri'    => '/订单关闭',
                'remark' => '微信人脸授权码获取'
            ]
        ];

        if (isset($endpoints[$key])) {
            if ($isDebug) {
                $path = $this->testServiceEndPoint;
            } else {
                $path = $this->serviceEndPoint;
            }

            $temp                   = $endpoints[$key]['uri'];
            $endpoints[$key]['url'] = $path . $temp;

            return $endpoints[$key];
        } else {
            throw new Exception('未找到对应的接口信息 ' . $key);
        }
    }

    /**
     * 同意支付接口
     *
     * @param $params
     * @param false $isDebug 是否调试模式
     * @throws Exception
     */
    public function pay($params, $isDebug = false)
    {
        // 注释格式：参数-参数名称-取值-可空-最大长度-备注
        $data = [
            'trxamt'  => $params['amount'], // 交易金额-单位为分-否-15
            'reqsn'   => $params['order_no'], // 商户交易单号-商户的交易订单号-否-32
            'paytype' => $params['pay_type'], // 交易方式-详见附录3.3 交易方式-否-3

            'body'          => $params['title'] ?? '', // 订单标题-订单商品名称，为空则以商户名作为商品名称-是-100-最大100个字节(50个中文字符)-
            'remark'        => $params['remark'] ?? '', // 备注-备注信息-是-160-最大160个字节(80个中文字符)禁止出现+，空格，/，?，%，#，&，=这几类特殊符号
            'validtime'     => $params['valid_time'] ?? '5', // 有效时间-订单有效时间，以分为单位，不填默认为5分钟-是-2
            'acct'          => $params['acct'] ?? '', // 支付平台用户标识-JS支付时使用：微信支付-用户的微信openid、支付宝支付-用户user_id、微信小程序-用户小程序的openid、云闪付JS-用户userId-是-32
            'notify_url'    => $params['notify_url'] ?? '', // 交易结果通知地址-接收交易结果的异步通知回调地址，通知url必须为直接可访问的url，不能携带参数。-是-256
            'limit_pay'     => $params['limit_pay'] ?? '', // 支付限制-no_credit--指定不能使用信用卡支付-是-32-暂时只对微信支付和支付宝有效,仅支持no_credit
            'sub_appid'     => $params['sub_appid'] ?? '', // 微信子appid-商户微信号-是-32-只对微信支付有效
            'goods_tag'     => $params['goods_tag'] ?? '', // 订单支付标识-订单优惠标记，用于区分订单是否可以享受优惠，字段内容在微信后台配置券时进行设置，说明详见代金券或立减优惠-是-32-只对微信支付有效W01交易方式不支持
            'benefitdetail' => $params['benefit_detail'] ?? '', // 优惠信息-Benefitdetail的json字符串,注意是String-是-不限制-仅支持微信单品优惠、W01交易方式不支持、支付宝智慧门店/支付宝单品优惠
            'chnlstoreid'   => $params['channel_store_id'] ?? '', // 渠道门店编号-商户在支付渠道端的门店编号-是-不限制:例如对于支付宝支付，支付宝门店编号、对于微信支付，微信门店编号、W01交易方式不支持
            'extendparams'  => $params['extend_params'] ?? '', // 拓展参数-son字符串，注意是String、一般用于渠道的活动参数填写-是
            'cusip'         => $params['cus_ip'] ?? '', // 终端ip-用户下单和调起支付的终端ip地址-是-16-payType=U02云闪付JS支付不为空
            'front_url'     => $params['front_url'] ?? '', // 支付完成跳转-必须为https协议地址，且不允许带参数-是-128-只支持payType=U02云闪付JS支付、payType=W02微信JS支付
            'subbranch'     => $params['subbranch'] ?? '', // 门店编号-线下场景使用-是-8-门店号需要跟通联商务经理申请
            'idno'          => $params['id_no'] ?? '', // 证件号-实名交易必填.填了此字段就会验证证件号和姓名-是-32-暂只支持支付宝支付,微信支付(微信支付的刷卡支付除外)
            'truename'      => $params['true_name'] ?? '', // 付款人真实姓名-实名交易必填.填了此字段就会验证证件号和姓名-是-32-暂只支持支付宝支付,微信支付(微信支付的刷卡支付除外)

            /**
             * 参数名称：分账信息
             * 取值：格式:cusid:type:amount;cusid:type:amount…其中
             * cusid:接收分账的通联商户号
             * type分账类型（01：按金额  02：按比率）
             * 如果分账类型为02，则分账比率为0.5表示50%。如果分账类型为01，则分账金额以元为单位表示
             * 可空：是
             * 长度：1024
             * 备注：开通此业务需开通分账配置
             */
            'asinfo'        => $params['as_info'] ?? '',

            'fqnum' => $params['fq_num'] ?? '', // 花呗分期- 6 花呗分期6期/12 花呗分期12期 - 是-4 - 暂只支持支付宝花呗分期仅支持A01/A02
        ];

        $paramsStr = $this->getParamsStr($data);
        $endPoint  = $this->getEndPoint('pay', $isDebug);
        $result    = $this->client->requestApi($endPoint, [], $paramsStr, $this->headers, true);

        return $this->handleResult($result);
    }

    /**
     * 统一扫码接口
     * @param $params array 参数
     * @param false $isDebug 是否调试模式
     * @throws Exception
     */
    public function scanqrpay($params, $isDebug = false)
    {
        // 注释格式：参数-参数名称-取值-可空-最大长度-备注
        $data = [
            'trxamt'   => $params['amount'], // 交易金额-单位为分-否-15
            'reqsn'    => $params['order_no'], // 商户交易单号-商户的交易订单号-否-32
            'authcode' => $params['auth_code'] ?? '', // 支付授权码-如微信,支付宝,银联的付款二维码 - 否-32
            'body'     => $params['title'] ?? '', // 订单标题-订单商品名称，为空则以商户名作为商品名称-是-100-最大100个字节(50个中文字符)-
            'remark'   => $params['remark'] ?? '', // 备注-备注信息-是-160-最大160个字节(80个中文字符)禁止出现+，空格，/，?，%，#，&，=这几类特殊符号

            'goods_tag'     => $params['goods_tag'] ?? '', // 订单支付标识-订单优惠标记，用于区分订单是否可以享受优惠，字段内容在微信后台配置券时进行设置，说明详见代金券或立减优惠-是-32-只对微信支付有效W01交易方式不支持
            'benefitdetail' => $params['benefit_detail'], // 优惠信息-Benefitdetail的json字符串,注意是String-是-不限制-仅支持微信单品优惠、W01交易方式不支持、支付宝智慧门店/支付宝单品优惠
            'chnlstoreid'   => $params['channel_store_id'] ?? '', // 渠道门店编号-商户在支付渠道端的门店编号-是-不限制:例如对于支付宝支付，支付宝门店编号、对于微信支付，微信门店编号、W01交易方式不支持
            'subbranch'     => $params['subbranch'] ?? '', // 门店编号-线下场景使用-是-8-门店号需要跟通联商务经理申请
            'extendparams'  => $params['extend_params'] ?? '', // 拓展参数-son字符串，注意是String、一般用于渠道的活动参数填写-是
            'idno'          => $params['id_no'] ?? '', // 证件号-实名交易必填.填了此字段就会验证证件号和姓名-是-32-暂只支持支付宝支付,微信支付(微信支付的刷卡支付除外)
            'truename'      => $params['true_name'] ?? '', // 付款人真实姓名-实名交易必填.填了此字段就会验证证件号和姓名-是-32-暂只支持支付宝支付,微信支付(微信支付的刷卡支付除外)

            /**
             * 参数名称：分账信息
             * 取值：格式:cusid:type:amount;cusid:type:amount…其中
             * cusid:接收分账的通联商户号
             * type分账类型（01：按金额  02：按比率）
             * 如果分账类型为02，则分账比率为0.5表示50%。如果分账类型为01，则分账金额以元为单位表示
             * 可空：是
             * 长度：1024
             * 备注：开通此业务需开通分账配置
             */
            'asinfo'        => $params['as_info'] ?? '',

            'fqnum' => $params['fq_num'] ?? '', // 花呗分期- 6 花呗分期6期/12 花呗分期12期 - 是-4 - 暂只支持支付宝花呗分期仅支持A01/A02
        ];

        $paramsStr = $this->getParamsStr($data);
        $endPoint  = $this->getEndPoint('scanqrpay', $isDebug);

        $result = $this->client->requestApi($endPoint, [], $paramsStr, $this->headers, true);

        return $this->handleResult($result);
    }

    /**
     * 交易撤销 - 当天交易用撤销
     * @param $refundNo
     * @param $refundAmount
     * @param array $extraParams
     * @param false $isDebug
     * @throws Exception
     */
    public function cancel($refundNo, $refundAmount, $extraParams = [], $isDebug = false)
    {
        $params = [
            'reqsn'    => $refundNo, // 商户的退款交易订单号
            'trxamt'   => $refundAmount, // 退款金额 单位为分
            'oldreqsn' => $extraParams['old_order_no'] ?? '', // 原交易的商户订单号
            'oldtrxid' => $extraParams['old_trx_id'] ?? '', // 原交易的收银宝平台流水
        ];

        $endPoint  = $this->getEndPoint('cancel', $isDebug);
        $paramsStr = $this->getParamsStr($params);
        $result    = $this->client->requestApi($endPoint, [], $paramsStr, $this->headers, true);

        return $this->handleResult($result);
    }

    /**
     * 交易退款 - 当天交易请用撤销,非当天交易才用此退货接口
     * @param $refundNo
     * @param $refundAmount
     * @param array $extraParams
     * @param false $isDebug
     * @throws Exception
     */
    public function refund($refundNo, $refundAmount, $extraParams = [], $isDebug = false)
    {
        $params = [
            'reqsn'         => $refundNo, // 商户的退款交易订单号
            'trxamt'        => $refundAmount, // 退款金额 单位为分
            'oldreqsn'      => $extraParams['old_order_no'] ?? '', // 原交易的商户订单号
            'oldtrxid'      => $extraParams['old_trx_id'] ?? '', // 原交易的收银宝平台流水
            'remark'        => $extraParams['remark'] ?? '', // 备注
            'benefitdetail' => $extraParams['benefit_detail'] ?? '', // 优惠信息：只适用于银联单品优惠交易的退货
        ];

        $endPoint  = $this->getEndPoint('refund', $isDebug);
        $paramsStr = $this->getParamsStr($params);
        $result    = $this->client->requestApi($endPoint, [], $paramsStr, $this->headers, true);

        return $this->handleResult($result);
    }

    /**
     * 交易查询
     *
     * @param $orderNo
     * @param string $trxId
     * @param false $isDebug
     * @throws Exception
     */
    public function query($orderNo, $trxId = '', $isDebug = false)
    {
        $params = [
            'reqsn' => $orderNo, // 商户的交易订单号
            'trxid' => $trxId, // 平台交易流水
        ];

        $endPoint  = $this->getEndPoint('query', $isDebug);
        $paramsStr = $this->getParamsStr($params);

        $result = $this->client->requestApi($endPoint, [], $paramsStr, $this->headers, true);

        return $this->handleResult($result);
    }

    /**
     * 根据授权码(付款码)获取用户ID
     *
     * @param $authCode
     * @param $authType
     * @param array $extraParams
     * @param false $isDebug
     * @throws Exception
     */
    public function getAuthCodeToUserId($authCode, $authType, $extraParams = [], $isDebug = false)
    {
        $params = [
            'authcode' => $authCode, // 授权码（付款码）
            'authtype' => $authType, // 授权码类型 01-微信付款码 02-银联userAuth
            'sub_appid' => $extraParams['sub_appid'] ?? '' // 微信支付appid - 针对01有效
        ];

        $endPoint  = $this->getEndPoint('getAuthCodeToUserId', $isDebug);
        $paramsStr = $this->getParamsStr($params);
        $result    = $this->client->requestApi($endPoint, [], $paramsStr, $this->headers, true);

        return $this->handleResult($result);
    }

    /**
     * 微信人脸授权码获取
     *
     * @param $storeId
     * @param $storeName
     * @param $rawData
     * @param array $extraParams
     * @param false $isDebug
     * @throws Exception
     */
    public function getWxFacePayInfo($storeId, $storeName, $rawData, $extraParams = [], $isDebug = false)
    {
        $params = [
            'storeid'   => $storeId, // 门店编号-由商户定义， 各门店唯一 - 否-32
            'storename' => $storeName, // 门店名称 - 有商户定义-否-128
            'deviceid'  => $extraParams['device_id'] ?? '', // 终端设备编号- 终端设备编号，由商户定义。-是-32
            'attach'    => $extraParams['attach'] ?? '', // 附加字段。字段格式使用Json

            //  初始化数据。由微信人脸SDK的接口返回。
            //获取方式参见微信官方刷脸支付接口：
            //[获取数据 getWxpayfaceRawdata](#获取数据 getWxpayfaceRawdata)
            'rawdata'   => $rawData,
            'subappid'  => $extraParams['sub_appid'] ?? '', // 微信支付appid
        ];

        $paramsStr = $this->getParamsStr($params);

        $endPoint = $this->getEndPoint('getWxFacePayInfo', $isDebug);

        $result = $this->client->requestApi($endPoint, [], $paramsStr, $this->headers, true);

        return $this->handleResult($result);
    }

    /**
     * 关闭订单
     *
     * @param $params
     * @param $isDebug
     * @throws Exception
     */
    public function close($orderNo, $oldTrxId = '', $isDebug = false)
    {
        $params = [
            // oldreqsn和oldtrxid必填其一 //建议:商户如果同时拥有oldtrxid和oldreqsn,优先使用oldtrxid
            'oldreqsn' => $orderNo, // 原商户的下单的交易订单号
            'oldtrxid' => $oldTrxId, // 原通联平台交易流水
        ];

        $paramsStr = $this->getParamsStr($params);
        $endPoint  = $this->getEndPoint('close', $isDebug);
        $result    = $this->client->requestApi($endPoint, [], $paramsStr, $this->headers, true);

        return $this->handleResult($result);
    }

    /**
     * 获取交易类型列表
     *
     * @return array
     */
    public function getTrxCodeList()
    {
        $list = [
            ['code' => 'VSP501', 'name' => '微信支付'],
            ['code' => 'VSP502', 'name' => '微信支付撤销'],
            ['code' => 'VSP503', 'name' => '微信支付退款'],
            ['code' => 'VSP505', 'name' => '手机QQ 支付'],
            ['code' => 'VSP506', 'name' => '手机QQ支付撤销'],
            ['code' => 'VSP507', 'name' => '手机QQ支付退款'],
            ['code' => 'VSP511', 'name' => '支付宝支付'],
            ['code' => 'VSP512', 'name' => '支付宝支付撤销'],
            ['code' => 'VSP513', 'name' => '支付宝支付退款'],
            ['code' => 'VSP541', 'name' => '扫码支付'],
            ['code' => 'VSP542', 'name' => '扫码撤销'],
            ['code' => 'VSP543', 'name' => '扫码退货'],
            ['code' => 'VSP551', 'name' => '银联扫码支付'],
            ['code' => 'VSP552', 'name' => '银联扫码撤销'],
            ['code' => 'VSP553', 'name' => '银联扫码退货'],
            ['code' => 'VSP907', 'name' => '差错借记调整'],
            ['code' => 'VSP908', 'name' => '差错贷记调整']
        ];

        return $list;
    }

    /**
     * 获取交易方式列表
     *
     * @return array
     */
    public function getPayTypeList()
    {
        $list = [
            ['code' => 'W01', 'name' => '微信扫码支付'],
            ['code' => 'W02', 'name' => '微信JS支付'],
            ['code' => 'W06', 'name' => '微信小程序支付'],
            ['code' => 'A01', 'name' => '支付宝扫码支付'],
            ['code' => 'A02', 'name' => '支付宝JS支付'],
            ['code' => 'A03', 'name' => '支付宝APP支付'],
            ['code' => 'Q01', 'name' => '手机QQ扫码支付'],
            ['code' => 'Q02', 'name' => '手机QQ JS支付'],
            ['code' => 'U01', 'name' => '银联扫码支付(CSB)'],
            ['code' => 'U02', 'name' => '银联JS支付'],
        ];

        return $list;
    }

    /**
     * 整合提交的参数
     *
     * @param $data
     * @return mixed
     * @throws Exception
     */
    private function getParamsStr($params)
    {
        $publicParams = [
            // 'orgid'     => $this->orgId, // 机构号-代为发起交易的机构商户号-否-15
            'appid'     => $this->appId, // 应用ID-平台分配的APPID-否-8
            'cusid'     => $this->cusId, // 商户号-实际交易的商户号-否-15
            'randomstr' => time(),
            'signtype'  => $params['sign_type'] ?? 'RSA',
            'version'   => $this->apiVersion, // 版本号-接口版本号-否-2
        ];

        $params         = array_merge($publicParams, array_filter($params));
        $params['sign'] = $this->getSign($params);
        $paramsStr      = Utility::ToUrlParams($params);

        return $paramsStr;
    }


    /**
     * 处理返回结果
     *
     * @param $result
     */
    private function handleResult($result)
    {
        $code    = 0;
        $data    = [];
        $retCode = $result['retcode'] ?? '';
        $message = $result['retmsg'] ?? '';

        if ($retCode == 'FAIL' || !$retCode) {
            $code = -1;
        } else {
            // 验证通联支付签名
            $validateResult = Utility::validUnionPaySign($result, $this->publicKey);

            if (!$validateResult) {
                $code    = -1;
                $signRsp = strtolower($result["sign"]);
                $message = "验签失败:" . $signRsp;
            } else {
                $message = '成功';
            }

            $data = $result;
        }

        return ['code' => $code, 'message' => $message, 'data' => $data];
    }

    /**
     * 获取签名
     *
     * @param array $array
     * @return string
     * @throws Exception
     */
    protected function getSign(array $array)
    {
        ksort($array);
        $bufSignSrc = Utility::toUrlParams($array);

        $privateKey = chunk_split($this->privateKey, 64, "\n");
        $key        = "-----BEGIN RSA PRIVATE KEY-----\n" . wordwrap($privateKey) . "-----END RSA PRIVATE KEY-----";

        if (!openssl_sign($bufSignSrc, $signature, $key)) {
            throw new Exception('签名失败');
        }

        $sign = base64_encode($signature);//加密后的内容通常含有特殊字符，需要编码转换下，在网络间通过url传输时要注意base64编码是否是url安全的

        return urlencode($sign); // 需要进行url编码，不然接口会报签名错误
    }
}
