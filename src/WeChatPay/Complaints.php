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

/**
 * 微信投诉
 * @doc https://pay.weixin.qq.com/docs/merchant/products/consumer-complaint/introduction.html
 */
class Complaints extends BaseService
{
    protected $client;
    protected $chain;

    public function __construct(array $options = [])
    {
        parent::__construct($options);
        $this->chain = '/v3/merchant-service/complaints-v2';
    }

    // +----------------------------------------------------------------------
    // | 一： 主动查询投诉信息
    // +----------------------------------------------------------------------

    public function handleReturnData($url, $query = null, $method = null)
    {
        try {
           if ($query && $method) {
                $res = $this->client->chain($url)->post($query);
            } else if ($query) {
                $res = $this->client->chain($url)->get($query);
            } else {
                $res = $this->client->chain($url)->get();
            }

            // 获取响应体并解析为数组
            $data = $res->getBody()->getContents();

            $jsonData = json_decode($data, true);

            return $this->handleResult($jsonData);

        } catch (\Exception $e) {
            // 处理请求或解析过程中可能出现的异常
            error_log("Error in handleReturnData: " . $e->getMessage());
            return null; // 或者返回一个默认值或空数组
        }
    }


    /**
     * 1.1 查询投诉单列表
     * @param array $query 查询参数
     * @return array
     * @throws Exception
     */
    public function getBillList(array $query = []): array
    {
        $defaultQuery = [
            'limit'             => 1,
            'offset'            => 0,
            'begin_date'        => date('Y-m-d'),
            'end_date'          => date('Y-m-d'),
            'complainted_mchid' => '',
        ];

        $query['query'] = array_merge($defaultQuery, $query);

        $url = $this->chain;

        return $this->handleReturnData($url, $query);
    }

    /**
     * 1.2 查询投诉单详情
     * @param string $complaintId 投诉单号
     * @return array
     * @throws Exception
     */
    public function getBillDetail(string $complaintId): array
    {
        $url = $this->chain . '/' . $complaintId;
        return $this->handleReturnData($url);
    }

    /**
     * 1.3 查询投诉单协商历史
     * @param string $complaintId 投诉单号
     * @param array $query 查询参数
     * @return array
     * @throws Exception
     */
    public function getBillNegotiationHistory(string $complaintId, array $query = []): array
    {
        $defaultQuery = [
            'limit'  => 10,
            'offset' => 0,
        ];

        $query = array_merge($defaultQuery, $query);

        $url = $this->chain . '/' . $complaintId . '/negotiation-historys';

        return $this->handleReturnData($url, $query);
    }

    // +----------------------------------------------------------------------
    // | 三： 商户处理用户投诉
    // +----------------------------------------------------------------------

    /**
     * 3.1 回复用户
     * @param string $complaintId 投诉单号
     * @param array $body 包体参数
     * @return array
     * @throws Exception
     */
    public function handleResponse(string $complaintId, array $body): array
    {
        // 默认查询参数
        $defaultBody = [
            'complainted_mchid' => $body['complainted_mchid'], // 【被诉商户号】 投诉单对应的被诉商户号
            'response_content'  => $body['response_content'] ?? '', //【回复内容】 具体的投诉处理方案，限制200个字符以内。
        ];

        //【回复图片】 传入调用“商户上传反馈图片”接口返回的media_id，最多上传4张图片凭证
        if (isset($body['response_images']) && !empty($body['response_images'])) {
            $defaultBody['response_images'] = $body['response_images'];
        }

        // 【跳转链接】 商户可在回复中附加跳转链接，引导用户跳转至商户客诉处理页面，链接需满足HTTPS格式
        // 注：配置文字链属于灰度功能, 若有需要请使用超管邮箱，按照要求发送邮件申请。
        // 邮件要求详情见：https://kf.qq.com/faq/211207a6zMBj211207ZnIr2A.html。
        // 【跳转链接文案】 实际展示给用户的文案，附在回复内容之后。用户点击文案，即可进行跳转。注：若传入跳转链接，则跳转链接文案为必传项，二者缺一不可。
        if (isset($body['jump_url']) && !empty($body['jump_url'])) {
            $defaultBody['jump_url'] = $body['jump_url'];
        }

        // 【跳转链接文案】 实际展示给用户的文案，附在回复内容之后。用户点击文案，即可进行跳转。注：若传入跳转链接，则跳转链接文案为必传项，二者缺一不可。
        if (isset($body['jump_url_text']) && !empty($body['jump_url_text'])) {
            $defaultBody['jump_url_text'] = $body['jump_url_text'];
        }

        $url = $this->chain . "/{$complaintId}/response";

        $query['json'] = $defaultBody;

        return $this->handleReturnData($url, $query, true);
    }

    /**
     * 3.2 反馈处理完成
     * @param string $complaintId 投诉单号
     * @param array $body 包体参数
     * @return array
     * @throws Exception
     */
    public function handleComplete(string $complaintId, array $body): array
    {
        $defaultBody = [
            'complainted_mchid' => $body['complainted_mchid'],
        ];

        $url = $this->chain . "/{$complaintId}/complete";

        $query['json'] = $defaultBody;

        return $this->handleReturnData($url, $query, true);
    }

    /**
     * 3.3 更新退款审批结果
     * @param string $complaintId 投诉单号
     * @param array $body 包体参数
     * @return array
     * @throws Exception
     */
    public function handleUpdateRefundProgress(string $complaintId, array $body): array
    {
        $defaultBody = [
            'action' => $body['action'] ?? 'APPROVE',
        ];

        if (isset($body['launch_refund_day'])
            && (!empty($body['launch_refund_day']) || $body['launch_refund_day'] == 0)) {
            $defaultBody['launch_refund_day'] = $body['launch_refund_day'];
        }

        if (isset($body['reject_reason']) && !empty($body['reject_reason'])) {
            $defaultBody['reject_reason'] = $body['reject_reason'];
        }

        if (isset($body['reject_media_list']) && !empty($body['reject_media_list'])) {
            $defaultBody['reject_media_list'] = $body['reject_media_list'];
        }

        if (isset($body['remark']) && !empty($body['remark'])) {
            $defaultBody['remark'] = $body['remark'];
        }


        $url = $this->chain . "/{$complaintId}/update-refund-progress";

        $query['json'] = $defaultBody;

        return $this->handleReturnData($url, $query, true);
    }

    // +----------------------------------------------------------------------
    // | 四： 商户反馈图片
    // +----------------------------------------------------------------------

//    /**
//     * 4.1 商户上传反馈图片
//     * @param string $fileFullName 图片全路径
//     * @return array
//     * @throws Exception
//     */
//    public function uploadImage(string $fileFullName): array
//    {
//        return $this->client->uploadImage($fileFullName);
//    }
//
//    /**
//     * 4.2 图片请求接口
//     * @param string $mediaId 图片id
//     * @return array
//     * @throws Exception
//     */
//    public function getMediaData(string $mediaId): array
//    {
//        $url = "https://api.mch.weixin.qq.com/v3/merchant-service/images/{$mediaId}";
//
//        $headers = [
//            'Authorization' => $this->generateAuthorizationHeader($url),
//            'Accept'        => 'application/json',
//        ];
//
//        $resp = $this->client->getClient()->get($url, ['headers' => $headers]);
//        return $this->handleResult($resp->getBody());
//    }
//
//    /**
//     * 生成授权头
//     * @param string $url 请求URL
//     * @return string
//     */
//    protected function generateAuthorizationHeader(string $url): string
//    {
//        $timestamp = time();
//        $nonce     = bin2hex(random_bytes(16));
//        $message   = $timestamp . "\n" . $nonce . "\n" . "GET" . "\n" . $url . "\n" . "";
//
//        $signature = base64_encode(hash_hmac('sha256', $message, base64_decode($this->config['mch_v3_key']), true));
//
//        return "WECHATPAY2-SHA256-RSA2048 {$this->config['mch_id']}:{$signature}:{$nonce}:{$timestamp}";
//    }
//
//    /**
//     * 卸载连接实例
//     * @return void
//     */
//    public function clear()
//    {
//        $this->client->clear();
//    }
}
