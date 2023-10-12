<?php

namespace Sxqibo\FastPayment\NewPay;

use GuzzleHttp\Client;

/**
 * 查询接口-扫码API
 *
 * https://www.yuque.com/chenyanfei-sjuaz/uhng8q/nghr8z
 */
final class QueryOrderScanPayService
{
    /** @var string[] queryDetail的字段 */
    private $queryDetail = [
        'orderID', 'orderAmount', 'payAmount', 'acquiringTime',
        'completeTime', 'orderNo', 'stateCode', 'respCode',
        'respMsg', 'targetOrderId', 'vasType', 'vasOrderId',
        'vasFeeAmt', 'realBankOrderId', 'userId', 'buyerLogonId'
    ];

    public function query(ScanPayQueryModel $scanPayQueryModel)
    {
        $verify = $scanPayQueryModel->verify();

        if (!empty($verify)) {
            return $verify;
        }

        $signData = $scanPayQueryModel->getSignData();

        $request= $scanPayQueryModel->getData();
        $request['signMsg'] = RsaUtil::buildSignForBin2Hex($signData, $scanPayQueryModel->privateKey);

        // post请求接口
        $content = HttpUtil::post($request, ScanPayQueryModel::REQUEST_URL);

        // 内容转数组，此处返回的不是json串
        parse_str($content, $arr);

        if ($arr['resultCode'] != '0000') {
            return $arr;
        }

        if ($arr['queryDetailsSize'] == 0) {
            return '无查询结果';
        }

        if ($arr['queryDetailsSize'] == -1) {
            return '查询出现异常';
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
