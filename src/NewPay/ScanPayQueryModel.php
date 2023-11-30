<?php

namespace Sxqibo\FastPayment\NewPay;

use Exception;
use ReflectionClass;

/**
 * 查询接口-扫码API
 *
 * https://www.yuque.com/chenyanfei-sjuaz/uhng8q/nghr8z
 */
final class ScanPayQueryModel extends BaseModel
{
    const REQUEST_URL = 'https://gateway.hnapay.com/website/queryOrderResult.htm';

    /** @var string 版本 */
    const VERSION = "2.8";

    /** @var string 查询模式 */
    /** @var string 单笔 */
    const MODE_SINGLE = '1';
    /** @var string 批量 */
    const MODE_MULTI = '2';

    /** @var string 查询类型 */
    /** @var string 支付订单 */
    const TYPE_PAY = '1';
    /** @var string 退款订单 */
    const TYPE_REFUND = '2';

    /** @var string[] queryDetail的字段 */
    private $queryDetail = [
        'orderID', 'orderAmount', 'payAmount', 'acquiringTime',
        'completeTime', 'orderNo', 'stateCode', 'respCode',
        'respMsg', 'targetOrderId', 'vasType', 'vasOrderId',
        'vasFeeAmt', 'realBankOrderId', 'userId', 'buyerLogonId'
    ];

    /** @var string[] 签名字段 */
    const SIGN_FIELD =  [
        'version', 'serialID', 'mode', 'type', 'orderID', 'beginTime',
        'endTime', 'partnerID', 'remark', 'charset', 'signType'
    ];

    private $version = '';
    private $serialID = '';
    private $mode = '';
    private $type = '';
    private $orderID = '';
    private $beginTime = '';
    private $endTime = '';
    private $partnerID = '';
    private $remark = '';
    private $charset = '';
    private $signType = '';
    private $signMsg = '';


    public function __construct()
    {
        $this->version = self::VERSION;
        $this->charset = self::CHARSET_UTF8;
        $this->signType = self::SIGNTYPE_RSA;
    }

    /**
     * 数据赋值
     *
     * @param $data
     * @return void
     */
    public function copy($data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }

        $err = $this->verify();
        if (!empty($err)) {
            throw new Exception($err);
        }

        $this->signMsg = RsaUtil::buildSignForBin2Hex($this->getSignData(), $this->privateKey);
    }

    public function getModelData(): array
    {
        return parent::getData(__CLASS__, $this);
    }

    /**
     * 获取需要签名的数据
     *
     * @return string
     */
    public function getSignData()
    {
        $fields = [];

        foreach (self::SIGN_FIELD as $field) {
            $fields[$field] = $this->$field;
        }
        // var_dump($fields);
        return http_build_query($fields);
    }

    public function verify()
    {
        if (empty($this->serialID)) {
            return '请求序列号不能为空';
        }

        if (empty($this->mode)) {
            return '查询模式不能为空';
        }

        if ($this->mode != self::MODE_SINGLE
            && $this->mode != self::MODE_MULTI) {
            return '查询模式的取值不正确';
        }

        if ($this->mode == self::MODE_SINGLE && empty($this->orderID)) {
            return '单笔查询必须传入 orderID';
        }

        if ($this->mode == self::MODE_MULTI) {
            $this->orderID = '';
        }

        if (empty($this->type)) {
            return '查询类型不能为空';
        }

        if ($this->type != self::TYPE_PAY
            && $this->type != self::TYPE_REFUND) {
            return '询类型的取值不正确';
        }

        if (empty($this->partnerID)) {
            return '商户ID不能为空';
        }

        return '';
    }

    public function getDetail($responseData)
    {
        // 内容转数组，此处返回的不是json串
        parse_str($responseData, $arr);

        if ($arr['resultCode'] != '0000') {
            throw new Exception((new NewPayCode())->getResultCode($arr['resultCode']));
        }

        if ($arr['queryDetailsSize'] == 0) {
            throw new Exception('无查询结果');
        }

        if ($arr['queryDetailsSize'] == -1) {
            throw new Exception('查询出现异常');
        }

        // 获取详情
        $detail = $arr['queryDetails'];
        $queryDetail = $this->getQueryDetail($detail);

        $arr['queryDetailsArr'] = $queryDetail;

        return $arr;
    }

    /**
     * 把查询详情字符串转换为数组
     *
     * @param $queryDetails
     * @return array
     */
    public function getQueryDetail($queryDetails): array
    {
        $queryDetailsArr = explode('|', $queryDetails);

        $queryDetailsArray = [];
        foreach ($queryDetailsArr as $queryDetail) {
            $queryDetailArr = [];
            $query = explode(',', $queryDetail);
            $cnt = 0;
            foreach ($this->queryDetail as $detail) {
                $queryDetailArr[$detail] = $query[$cnt];
                $cnt ++;
            }
            $queryDetailsArray[] = $queryDetailArr;
        }

        return $queryDetailsArray;
    }
}
