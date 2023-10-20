<?php
// +----------------------------------------------------------------------
// | NewThink [ Think More,Think Better! ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2030 http://www.sxqibo.com All rights reserved.
// +----------------------------------------------------------------------
// | 版权所有：山西岐伯信息科技有限公司
// +----------------------------------------------------------------------
// | Author:  hongwei  Date:2023/10/20 Time:10:31 AM
// +----------------------------------------------------------------------

namespace Sxqibo\FastPayment\WeChatPay;


use Exception;

class Complaints extends BaseService
{
    /**
     * 查询投诉单列表
     * @param array $query 查询参数
     * @return array
     * @throws Exception
     */
    public function getBillList(array $query = []): array
    {
        $endPoint = [
            'url'    => $this->base . "/merchant-service/complaints-v2",
            'method' => 'GET',
        ];

        // 默认查询参数
        $defaultQuery = [
            'limit'             => 10, //【选填 integer】分页大小：设置该次请求返回的最大投诉条数，范围[1,50]
            'offset'            => 0, //【选填 integer】分页开始位置：该次请求的分页开始位置，从0开始计数，例如offset=10，表示从第11条记录开始返回。
            'begin_date'        => date('Y-m-d'), //【必填 string(10)】开始日期：投诉发生的开始日期。注意，查询日期跨度不超过30天
            'end_date'          => date('Y-m-d'), //【必填 string(10)】结束日期：投诉发生的结束日期。注意，查询日期跨度不超过30天
            'complainted_mchid' => '' //【选填 string(64)】被诉商户号：投诉单对应的被诉商户号。当服务商或渠道商查询指定子商户的投诉信息时需传入
        ];

        $query = array_merge($defaultQuery, $query);

        $result = $this->client->requestApi($endPoint, $query, [], $this->headers, true);

        return $this->handleResult($result);
    }

    /**
     * 查询投诉单详情
     * @param string $complaintId 投诉单号
     * @return array
     * @throws Exception
     */
    public function getBillDetail(string $complaintId): array
    {
        $endPoint = [
            'url'    => $this->base . "/merchant-service/complaints-v2/{$complaintId}",
            'method' => 'GET',
        ];

        $result = $this->client->requestApi($endPoint, [], [], $this->headers, true);

        return $this->handleResult($result);
    }

    /**
     * 查询投诉单协商历史
     * @param string $complaintId 投诉单号
     * @param array  $query       查询参数
     * @return array
     * @throws Exception
     */
    public function getBillNegotiationHistory(string $complaintId, array $query = []): array
    {
        $endPoint = [
            'url'    => $this->base . "/merchant-service/complaints-v2/{{$complaintId}}/negotiation-historys",
            'method' => 'GET',
        ];

        // 默认查询参数
        $defaultQuery = [
            'limit'  => 10, //【选填 integer】设置该次请求返回的最大协商历史条数，范围[1,300]
            'offset' => 0, //【选填 integer】分页开始位置：该次请求的分页开始位置，从0开始计数，例如offset=10，表示从第11条记录开始返回。
        ];

        $query = array_merge($defaultQuery, $query);

        $result = $this->client->requestApi($endPoint, $query, [], $this->headers, true);

        return $this->handleResult($result);
    }
}
