<?php
// +----------------------------------------------------------------------
// | NewThink [ Think More,Think Better! ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2030 http://www.sxqibo.com All rights reserved.
// +----------------------------------------------------------------------
// | 版权所有：山西岐伯信息科技有限公司
// +----------------------------------------------------------------------
// | Author:  hongwei  Date:2024/8/29 Time:4:04 PM
// +----------------------------------------------------------------------

namespace Sxqibo\FastPayment\LakalaPay\services;

use GuzzleHttp\Exception\GuzzleException;

class Merchant extends Base
{
    public string $apiVersion = '3.0';

    /**
     * 商户服务 其他 扫码银行卡退货 退货
     * @access public
     * @param string $outOrderNo       商户请求流水号
     * @param int    $refundAmount     退款金额，单位：分
     * @param string $originOutTradeNo 原商户交易流水号
     * @param string $originLogNo      交易返回的拉卡拉统一交易单号，扫码交易为66开头
     * @param array  $locationInfo     地址位置信息，值为：['request_ip' => '172.22.66.186']
     * @return array
     * @throws GuzzleException
     * @link   https://o.lakala.com/#/home/document/detail?id=892
     */
    public function tradeRefund(string $outOrderNo, int $refundAmount, string $originOutTradeNo, string $originLogNo, array $locationInfo): array
    {
        // 请求包体
        $reqData = [
            'merchant_no'         => $this->options['merchant_no'],
            'term_no'             => $this->options['term_no'],
            'out_trade_no'        => $outOrderNo,
            'refund_amount'       => $refundAmount,
            'origin_out_trade_no' => $originOutTradeNo,
            'origin_log_no'       => $originLogNo,
            'location_info'       => $locationInfo
        ];

        return $this->sendPostRequest('/api/v3/rfd/refund_front/refund', [
            'req_time' => date('YmdHis'),
            'version'  => $this->apiVersion,
            'req_data' => $reqData,
        ]);
    }

    /**
     * 商户服务 其他 扫码银行卡退货 退货查询
     * @access public
     * @param string $outOrderNo 退货订单商户请求流水号
     * @return array
     * @throws GuzzleException
     * @link   https://o.lakala.com/#/home/document/detail?id=893
     */
    public function tradeRefundQuery(string $outOrderNo): array
    {
        // 请求包体
        $reqData = [
            'merchant_no'  => $this->options['merchant_no'],
            'term_no'      => $this->options['term_no'],
            'out_trade_no' => $outOrderNo
        ];

        return $this->sendPostRequest('/api/v3/rfd/refund_front/refund_query', [
            'req_time' => date('YmdHis'),
            'version'  => $this->apiVersion,
            'req_data' => $reqData,
        ]);
    }

    /**
     * 商户服务 其他 全类型退货 统一退货  【  未测试 】
     * @access public
     * @param string $outOrderNo   商户请求流水号
     * @param int    $refundAmount 退款金额，单位：分
     * @param string $bizType      原交易类型:1 银行卡，2 外卡，3 扫码，4 线上
     * @param string $tradeDate    原交易日期：yyyyMMdd
     * @param string $logNo        原交易返回的拉卡拉统一交易单号，扫码交易为66开头
     * @return array
     * @throws GuzzleException
     * @link   https://o.lakala.com/#/home/document/detail?id=549
     */
    public function tradeUniformRefund(string $outOrderNo, int $refundAmount, string $bizType, string $tradeDate, string $logNo): array
    {
        // 请求包体
        $reqData = [
            'merchant_no'       => $this->options['merchant_no'],
            'term_no'           => $this->options['term_no'],
            'out_trade_no'      => $outOrderNo,
            'refund_amount'     => $refundAmount,
            'origin_biz_type'   => $bizType,
            'origin_trade_date' => $tradeDate,
            'origin_log_no'     => $logNo
        ];

        return $this->sendPostRequest('/api/v3/lams/trade/trade_refund', [
            'req_time' => date('YmdHis'),
            'version'  => $this->apiVersion,
            'req_data' => $reqData,
        ]);
    }

    /**
     * 商户服务 其他 全类型退货 退货查询 【  未测试 】
     * @access public
     * @param string $outOrderNo       商户请求流水号
     * @param string $bizType          原交易类型:1 银行卡，2 外卡，3 扫码，4 线上
     * @param string $refundTradeDate  原退款交易日期：yyyyMMDD
     * @param string $refundOutOrderNo 原退款交易商户请求流水号
     * @return array
     * @throws GuzzleException
     * @link   https://o.lakala.com/#/home/document/detail?id=206
     */
    public function tradeUniformRefundQuery(string $outOrderNo, string $bizType, string $refundTradeDate, string $refundOutOrderNo): array
    {
        // 请求包体
        $reqData = [
            'merchant_no'         => $this->options['merchant_no'],
            'term_no'             => $this->options['term_no'],
            'out_trade_no'        => $outOrderNo,
            'origin_trade_date'   => $refundTradeDate,
            'origin_biz_type'     => $bizType,
            'origin_out_trade_no' => $refundOutOrderNo
        ];

        return $this->sendPostRequest('/api/v3/lams/trade/trade_refund_query', [
            'req_time' => date('YmdHis'),
            'version'  => $this->apiVersion,
            'req_data' => $reqData,
        ]);
    }
}
