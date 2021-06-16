<?php

namespace Sxqibo\FastPayment\WeChatPay;

use Exception;
use Sxqibo\FastPayment\Common\Utility;

class Transfer extends BaseService
{
    /**
     * 批量转账功能
     *
     * @param $data
     * @return array
     * @throws Exception
     */
    public function batches($data)
    {
        $endPoint      = [
            'url'    => $this->base . '/transfer/batches',
            'method' => 'POST',
        ];
        $detailList    = $data['transfer_detail_list'];
        $newDetailList = [];
        foreach ($detailList as $item) {
            $newDetailList[] = [
                'out_detail_no'   => $item['detail_no'], // 商家明细单号 - 商户系统内部区分转账批次单下不同转账明细单的唯一标识，要求此参数只能由数字、大小写字母组成
                'transfer_amount' => $item['transfer_amount'], // 转账金额 - 转账金额单位为分
                'transfer_remark' => $item['transfer_remark'], //转账备注 - 单条转账备注（微信用户会收到该备注），UTF8编码，最多允许32个字符
                'openid'          => $item['openid'], // 用户在直连商户应用下的用户标示 - 用户在直连商户appid下的唯一标识
                'user_name'       => Utility::getWePayEncrypt($item['user_name'], $this->config['cert_public']), // 收款用户姓名 - 1、收款方姓名。采用标准RSA算法，公钥由微信侧提供 2、该字段需进行加密处理，加密方法详见敏感信息加密说明。(提醒：必须在HTTP头中上送Wechatpay-Serial)
            ];
        }
        $newData = [
            'appid'                => $this->config['appid'], // 直连商户的appid - 申请商户号的appid或商户号绑定的appid（企业号corpid即为此appid）
            'out_batch_no'         => $data['batch_no'], // 商家批次单号 - 商户系统内部的商家批次单号，要求此参数只能由数字、大小写字母组成，在商户系统内部唯一
            'batch_name'           => $data['batch_name'], // 批次名称 - 该笔批量转账的名称
            'batch_remark'         => $data['batch_remark'], // 批次备注- 转账说明，UTF8编码，最多允许32个字符
            'total_amount'         => $data['total_amount'], // 转账总金额 - 转账金额单位为“分”。转账总金额必须与批次内所有明细转账金额之和保持一致，否则无法发起转账操作
            'total_num'            => count($newDetailList), // 转账总笔数 - 一个转账批次单最多发起三千笔转账。转账总笔数必须与批次内所有明细之和保持一致，否则无法发起转账操作
            'transfer_detail_list' => $newDetailList, // 转账明细列表 - 发起批量转账的明细列表，最多三千笔
        ];

        $newData = json_encode($newData);
        $result  = $this->client->requestApi($endPoint, [], $newData, $this->headers, true);

        // file_put_contents('test.txt', json_encode($result));

        return $this->handleResult($result);
    }

    /**
     * 商家批次单号查询批次单API
     *
     * @param $outBatchNo
     * @return array
     * @throws Exception
     */
    public function getBatchesByOutBatchNo($outBatchNo, $extarParams = [])
    {
        $endPoint = [
            'url'    => $this->base . '/transfer/batches/out-batch-no/' . $outBatchNo,
            'method' => 'GET',
        ];

        $params = [
            'need_query_detail' => $extarParams['need_query_detail'] ?? true, // 是否查询转账明细单
            'offset'            => $extarParams['offset'] ?? 0, // 该次请求资源（转账明细单）的起始位置，从0开始，默认值为0
            'limit'             => $extarParams['limit'] ?? 20, // 最大资源条数 - 最小20条，最大100条，不传则默认20条
            /**
             *
             * ALL：全部。需要同时查询转账成功和转账失败的明细单
             * SUCCESS：转账成功。只查询转账成功的明细单
             * FAIL：转账失败。只查询转账失败的明细单
             */
            'detail_status'     => $extarParams['detail_status'] ?? 'ALL', // 明细状态
        ];
        $result = $this->client->requestApi($endPoint, $params, [], $this->headers, true);

        return $this->handleResult($result);
    }
    
    /**
     * 商家明细单号查询明细单API
     *
     * @param $outBatchNo  string 商家批次单号
     * @param $outDetailNo string 商家明细单号
     * @return array
     * @throws Exception
     */
    public function getBatchesDetailByOutDetailNo($outBatchNo, $outDetailNo)
    {
        $endPoint = [
            'url'    => $this->base . "/transfer/batches/out-batch-no/{$outBatchNo}/details/out-detail-no/{$outDetailNo}",
            'method' => 'GET',
        ];

        $result = $this->client->requestApi($endPoint, [], [], $this->headers, true);

        return $this->handleResult($result);
    }
}
