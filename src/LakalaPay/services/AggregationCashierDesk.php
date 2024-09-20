<?php
// +----------------------------------------------------------------------
// | NewThink [ Think More,Think Better! ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2030 http://www.sxqibo.com All rights reserved.
// +----------------------------------------------------------------------
// | 版权所有：山西岐伯信息科技有限公司
// +----------------------------------------------------------------------
// | Author:  hongwei  Date:2024/8/28 Time:4:51 PM
// +----------------------------------------------------------------------

namespace Sxqibo\FastPayment\LakalaPay\services;

use GuzzleHttp\Exception\GuzzleException;

/**
 * 聚合收银台
 */
class AggregationCashierDesk extends Base
{
    protected string $apiVersion = '3.0';

    /**
     * 收银台订单创建
     * @access public
     * @param string $outOrderNo  商户订单号
     * @param int    $totalAmount 订单金额，单位：分
     * @param string $orderInfo   订单标题
     * @param array  $extraData   额外参数
     * @return array
     * @throws GuzzleException
     * @link   http://open.lakala.com/#/home/document/detail?id=283
     */
    public function counterOrderSpecialCreate(string $outOrderNo, int $totalAmount, string $orderInfo, array $extraData = []): array
    {
        // 请求包体
        $reqData = array_merge([
            'merchant_no' => $this->options['merchant_no']
        ], [
            'out_order_no'         => $outOrderNo,
            'total_amount'         => $totalAmount,
            'order_info'           => $orderInfo,
            'order_efficient_time' => date('YmdHis', strtotime('+7 days')),
            'support_refund'       => 1,
            'support_repeat_pay'   => 1,
            // 'counter_param'        => ['pay_mode' => 'ALIPAY'], // 指定支付类型  注意：测试环境传入该参数报错“错误信息：业务异常，错误码：999999”
            // 'notify_url'           => '',
            // 'busi_type_param'      => [['busi_type' => 'SCPAY']]
        ], $extraData);

        return $this->sendPostRequest('/api/v3/ccss/counter/order/special_create', [
            'req_time' => date('YmdHis'),
            'version'  => $this->apiVersion,
            'req_data' => $reqData,
        ]);
    }

    /**
     * 收银台订单查询
     * @access public
     * @param string $outOrderNo 商户订单号
     * @param string $payOrderNo 拉卡拉订单号
     * @param string $channelId  渠道号
     * @return array
     * @throws GuzzleException
     * @link   http://open.lakala.com/#/home/document/detail?id=284
     */
    public function counterOrderQuery(string $outOrderNo = '', string $payOrderNo = '', string $channelId = ''): array
    {
        // 请求包体
        $reqData = [];

        if ($outOrderNo) {
            $reqData['merchant_no']  = $this->options['merchant_no'];
            $reqData['out_order_no'] = $outOrderNo;
        } else {
            $reqData['pay_order_no'] = $payOrderNo;
            $reqData['channel_id']   = $channelId;
        }

        return $this->sendPostRequest('/api/v3/ccss/counter/order/query', [
            'req_time' => date('YmdHis'),
            'version'  => $this->apiVersion,
            'req_data' => $reqData,
        ]);
    }

    /**
     * 收银台订单关单
     * @access public
     * @param string $outOrderNo 商户订单号
     * @param string $payOrderNo 拉卡拉订单号
     * @param string $channelId  渠道号
     * @return array
     * @throws GuzzleException
     * @link   http://open.lakala.com/#/home/document/detail?id=722
     */
    public function counterOrderClose(string $outOrderNo = '', string $payOrderNo = '', string $channelId = ''): array
    {
        // 请求包体
        $reqData = [
            'merchant_no' => $this->options['merchant_no'],
            'channel_id'  => $channelId,
        ];

        if ($outOrderNo) {
            $reqData['out_order_no'] = $outOrderNo;
        } else {
            $reqData['pay_order_no'] = $payOrderNo;
        }

        return $this->sendPostRequest('/api/v3/ccss/counter/order/close', [
            'req_time' => date('YmdHis'),
            'version'  => $this->apiVersion,
            'req_data' => $reqData,
        ]);
    }
}
