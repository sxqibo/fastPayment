<?php


namespace Sxqibo\FastPayment;

use Exception;

/**
 * 通联支付类
 * 
 * Class UnionPay
 * @package Sxqibo\FastPayment
 */
class UnionPay
{
    private $serviceEndPoint = 'https://vsp.allinpay.com/apiweb/unitorder'; // 生产环境
    private $testServiceEndPoint = 'https://test.allinpaygd.com/apiweb/unitorder'; // 测试环境

    private $appId;
    private $curId;
    private $privateKey;
    private $publicKey;
    private $apiVersion = '11';

    private $headers;
    private $client;

    public function __construct($appId, $curId, $privateKey, $publicKey)
    {
        $this->appId      = $appId;
        $this->curId      = $curId;
        $this->publicKey  = $privateKey;
        $this->privateKey = $publicKey;
        $this->headers    = [
            'Content-Type' => ' application/x-www-form-urlencoded;charset=UTF-8'
        ];

        $this->client = new Client();
    }


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
            'orgid'     => $params['org_id'], // 机构号-代为发起交易的机构商户号-否-15
            'cusid'     => $params['cus_id'], // 商户号-实际交易的商户号-否-15
            'appid'     => $params['app_id'], // 应用ID-平台分配的APPID-否-8
            'version'   => $params['version'] ?? $this->apiVersion, // 版本号-接口版本号-否-2
            'trxamt'    => $params['amount'], // 交易金额-单位为分-否-15
            'reqsn'     => $params['order_no'], // 商户交易单号-商户的交易订单号-否-32
            'paytype'   => $params['pay_type'], // 交易方式-详见附录3.3 交易方式-否-3
            'randomstr' => $params['random_str'], // 随机字符串-商户自行生成的随机字符串-否-32

            'body'          => $params['title'] ?? '', // 订单标题-订单商品名称，为空则以商户名作为商品名称-是-100-最大100个字节(50个中文字符)-
            'remark'        => $params['remark'] ?? '', // 备注-备注信息-是-160-最大160个字节(80个中文字符)禁止出现+，空格，/，?，%，#，&，=这几类特殊符号
            'validtime'     => $params['valid_time'] ?? '5', // 有效时间-订单有效时间，以分为单位，不填默认为5分钟-是-2
            'acct'          => $params['acct'] ?? '', // 支付平台用户标识-JS支付时使用：微信支付-用户的微信openid、支付宝支付-用户user_id、微信小程序-用户小程序的openid、云闪付JS-用户userId-是-32
            'notify_url'    => $params['notify_url'] ?? '', // 交易结果通知地址-接收交易结果的异步通知回调地址，通知url必须为直接可访问的url，不能携带参数。-是-256
            'limit_pay'     => $params['limit_pay'] ?? '', // 支付限制-no_credit--指定不能使用信用卡支付-是-32-暂时只对微信支付和支付宝有效,仅支持no_credit
            'sub_appid'     => $params['sub_appid'] ?? '', // 微信子appid-商户微信号-是-32-只对微信支付有效
            'goods_tag'     => $params['goods_tag'] ?? '', // 订单支付标识-订单优惠标记，用于区分订单是否可以享受优惠，字段内容在微信后台配置券时进行设置，说明详见代金券或立减优惠-是-32-只对微信支付有效W01交易方式不支持
            'benefitdetail' => $params['benefit_detail'], // 优惠信息-Benefitdetail的json字符串,注意是String-是-不限制-仅支持微信单品优惠、W01交易方式不支持、支付宝智慧门店/支付宝单品优惠
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

            'fqnum'    => $params['fq_num'] ?? '', // 花呗分期- 6 花呗分期6期/12 花呗分期12期 - 是-4 - 暂只支持支付宝花呗分期仅支持A01/A02
            'signtype' => $params['sign_type'] ?? 'RSA', // 签名方式 RSA、SM2
        ];

        $data['sign'] = $this->getSign($params);
        $paramsStr    = AppUtil::ToUrlParams($params);

        $endPoint = $this->getEndPoint('pay', $isDebug);

        $result = $this->client->requestApi($endPoint, [], $paramsStr, $this->headers, true);

        return $this->handleResult($result);
    }

    /**
     * 统一扫码接口
     * @param $params 参数
     * @param false $isDebug 是否调试模式
     * @throws Exception
     */
    public function scanqrpay($params, $isDebug = false)
    {
        $data = [
            'orgid'     => $params['org_id'], // 机构号-代为发起交易的机构商户号-否-15
            'cusid'     => $params['cus_id'], // 商户号-实际交易的商户号-否-15
            'appid'     => $params['app_id'], // 应用ID-平台分配的APPID-否-8
            'version'   => $params['version'] ?? $this->apiVersion, // 版本号-接口版本号-否-2
            'randomstr' => $params['random_str'], // 随机字符串-商户自行生成的随机字符串-否-32
            'trxamt'    => $params['amount'], // 交易金额-单位为分-否-15
            'reqsn'     => $params['order_no'], // 商户交易单号-商户的交易订单号-否-32
            'authcode'  => $params['auth_code'] ?? '', // 支付授权码-如微信,支付宝,银联的付款二维码 - 否-32
            'body'      => $params['title'] ?? '', // 订单标题-订单商品名称，为空则以商户名作为商品名称-是-100-最大100个字节(50个中文字符)-
            'remark'    => $params['remark'] ?? '', // 备注-备注信息-是-160-最大160个字节(80个中文字符)禁止出现+，空格，/，?，%，#，&，=这几类特殊符号

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

            'fqnum'    => $params['fq_num'] ?? '', // 花呗分期- 6 花呗分期6期/12 花呗分期12期 - 是-4 - 暂只支持支付宝花呗分期仅支持A01/A02
            'signtype' => $params['sign_type'] ?? 'RSA', // 签名方式 RSA、SM2
        ];

        $data['sign'] = $this->getSign($params);
        $paramsStr    = AppUtil::ToUrlParams($params);

        $endPoint = $this->getEndPoint('scanqrpay', $isDebug);

        $result = $this->client->requestApi($endPoint, [], $paramsStr, $this->headers, true);

        return $this->handleResult($result);
    }


    // 关闭订单
    private function close($params, $isDebug)
    {

    }

    /**
     * 处理返回结果
     *
     * @param $result
     */
    private function handleResult($result)
    {
        $rspArray = json_decode($result, true);

        // todo 返回结果
        if (!validSign($rspArray)) {
        }
    }

    /**
     * 获取签名
     *
     * @param array $array
     * @throws Exception
     * @return string
     */
    protected function getSign(array $array)
    {
        ksort($array);
        $bufSignSrc = $this->toUrlParams($array);

        $privateKey = chunk_split($this->privateKey, 64, "\n");
        $key        = "-----BEGIN RSA PRIVATE KEY-----\n" . wordwrap($privateKey) . "-----END RSA PRIVATE KEY-----";

        if (!openssl_sign($bufSignSrc, $signature, $key)) {
            throw new Exception('签名失败');
        }

        $sign = base64_encode($signature);//加密后的内容通常含有特殊字符，需要编码转换下，在网络间通过url传输时要注意base64编码是否是url安全的

        return $sign;
    }

    /**
     * 处理URL参数
     *
     * @param $array
     * @return string
     */
    private function toUrlParams($array)
    {
        $buff = "";
        foreach ($array as $k => $v) {
            if ($v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }
}