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
use Sxqibo\FastPayment\Common\Client;


/**
 * 微信投诉
 * @doc https://pay.weixin.qq.com/docs/merchant/products/consumer-complaint/introduction.html
 */
class Complaints extends BaseService
{
    // +----------------------------------------------------------------------
    // | 一： 主动查询投诉信息
    // +----------------------------------------------------------------------

    /**
     * 1. 1查询投诉单列表
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
     * 1.2 查询投诉单详情
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
     * 1.3 查询投诉单协商历史
     * @param string $complaintId 投诉单号
     * @param array  $query       查询参数
     * @return array
     * @throws Exception
     */
    public function getBillNegotiationHistory(string $complaintId, array $query = []): array
    {
        $endPoint = [
            'url'    => $this->base . "/merchant-service/complaints-v2/{$complaintId}/negotiation-historys",
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

    // +----------------------------------------------------------------------
    // | 三： 商户处理用户投诉
    // +----------------------------------------------------------------------

    /**
     * 3.1 回复用户
     * @param string $complaintId 投诉单号
     * @param array  $query       包体参数
     * @return array
     * @throws Exception
     */
    public function handleResponse(string $complaintId, array $body): array
    {
        $endPoint = [
            'url'    => $this->base . "/merchant-service/complaints-v2/{$complaintId}/response",
            'method' => 'POST',
        ];

        // 默认查询参数
        $defaultBody = [
            'complainted_mchid' => $body['complainted_mchid'], // 【被诉商户号】 投诉单对应的被诉商户号
            'response_content'  => $body['response_content'] ?? '', //【回复内容】 具体的投诉处理方案，限制200个字符以内。
            'response_images'   => $body['response_images'] ?? [], //【回复图片】 传入调用“商户上传反馈图片”接口返回的media_id，最多上传4张图片凭证
            'jump_url'          => $body['jump_url'] ?? '', // 【跳转链接】 商户可在回复中附加跳转链接，引导用户跳转至商户客诉处理页面，链接需满足HTTPS格式。注：配置文字链属于灰度功能, 若有需要请使用超管邮箱，按照要求发送邮件申请。邮件要求详情见：https://kf.qq.com/faq/211207a6zMBj211207ZnIr2A.html。
            'jump_url_text'     => $body['jump_url_text'] ?? '', // 【跳转链接文案】 实际展示给用户的文案，附在回复内容之后。用户点击文案，即可进行跳转。注：若传入跳转链接，则跳转链接文案为必传项，二者缺一不可。
        ];

        $body = array_merge($defaultBody, $body);

        $result = $this->client->requestApi($endPoint, [], $body, $this->headers, true);

        return $this->handleResult($result);
    }

    /**
     * 3.2 反馈处理完成
     * @param string $complaintId 投诉单号
     * @param array  $query       包体参数
     * @return array
     * @throws Exception
     */
    public function handleComplete(string $complaintId, array $body): array
    {
        $endPoint = [
            'url'    => $this->base . "/merchant-service/complaints-v2/{$complaintId}/complete",
            'method' => 'POST',
        ];

        // 默认查询参数
        $defaultBody = [
            'complainted_mchid' => $body['complainted_mchid'], // 【被诉商户号】 投诉单对应的被诉商户号
        ];

        $body = array_merge($defaultBody, $body);

        $result = $this->client->requestApi($endPoint, [], $body, $this->headers, true);

        return $this->handleResult($result);
    }

    /**
     * 3.3 更新退款审批结果
     * @param string $complaintId 投诉单号
     * @param array  $query       包体参数
     * @return array
     * @throws Exception
     */
    public function handleUpdateRefundProgress(string $complaintId, array $body): array
    {
        $endPoint = [
            'url'    => $this->base . "/merchant-service/complaints-v2/{$complaintId}/update-refund-progress",
            'method' => 'POST',
        ];

        // 默认查询参数
        $defaultBody = [
            'action'                => $body['action'] ?? 'APPROVE', // 【审批动作】 同意 或 拒绝,如果没传“同意”
            'launch_refund_day'     => $body['launch_refund_day'] ?? 0, // 【预计发起退款时间】 在同意退款时返回，预计将在多少个工作日内能发起退款, 0代表当天
            'reject_reason'         => $body['reject_reason'] ?? '', // 【拒绝退款原因】 在拒绝退款时返回拒绝退款的原因
            'reject_media_list'     => $body['reject_media_list'] ?? [], // 【拒绝退款的举证图片列表】 在拒绝退款时，如果有拒绝的图片举证，可以提供 最多上传4张图片, 传入调用“商户上传反馈图片”接口返回的media_id，最多上传4张图片凭证
            'remark'                => $body['remark'] ?? '',  // 【备注】 任何需要向微信支付客服反馈的信息
        ];

        $body = array_merge($defaultBody, $body);

        $result = $this->client->requestApi($endPoint, [], $body, $this->headers, true);

        return $this->handleResult($result);
    }

    // +----------------------------------------------------------------------
    // | 四： 商户反馈图片
    // +----------------------------------------------------------------------

    /**
     * 4.1 商户上传反馈图片
     * @param string $fileFullName 图片全路径
     * @return array
     * @throws Exception
     */
    public function uploadImage(string $fileFullName): array
    {
        $endPoint = [
            'url'    => $this->base . "/merchant-service/images/upload",
            'method' => 'POST',
        ];

        // todo 处理图片，得到如下两个参数
        $fileName = '';
        $sha256 = '';
        $bitFile = ''; // 处理后的二进制图片

        $handleHeader = [
            'file' => $bitFile, //必填，将媒体图片进行二进制转换，得到的媒体图片二进制内容，在请求body中上传此二进制内容。媒体图片只支持jpg、bmp、png格式，文件大小不能超过2M
            'meta' => [
                'filename' => $fileName, //必填，文件名，如：16968341945092.jpg
                'sha256'   => $sha256,   //必填，文件sha256编码,如：544acbea3112b7259796dbda33dd39f079549f58842975eb8df167ccb2fcae42
            ]
        ];

        $headers = array_merge($handleHeader, $this->headers);

        $result = $this->client->requestApi($endPoint, [], [], $headers, true);

        return $this->handleResult($result);
    }

    /**
     * 4.2 图片请求接口
     * @param string $mediaId 图片id
     * @return array
     * @throws Exception
     */
    public function getMediaData(string $mediaId): array
    {
        $endPoint = [
            'url'    => $this->base . "/merchant-service/images/{$mediaId}",
            'method' => 'GET',
        ];

        $result = $this->client->requestApi($endPoint, [], [], $this->headers, true);

        return $this->handleResult($result);
    }

/**
     * 卸载连接实例
     * @return void
     */
    public function clear()
    {
        Client::$clientInstance = null;
    }
}
