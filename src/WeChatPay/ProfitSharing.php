<?php

namespace Sxqibo\FastPayment\WeChatPay;

use Exception;
use Sxqibo\FastPayment\Common\Utility;

/**
 * 分账功能
 * Class Transfer
 * @package Sxqibo\FastPayment\WeChatPay
 */
class ProfitSharing extends BaseService
{

    public function getEndPoint($key)
    {
        $endpoints = [
            'createOrder'           => [
                'uri'    => '/profitsharing/orders',
                'method' => 'POST',
                'remark' => '请求分账API'
            ],
            'getOrdersDetail'       => [
                'uri'    => "/profitsharing/orders/%s",
                'method' => 'GET',
                'remark' => '查询分账结果API'
            ],
            'returnOrders'          => [
                'uri'    => "/profitsharing/return-orders",
                'method' => 'POST',
                'remark' => '请求分账回退API'
            ],
            'getReturnOrdersInfo'   => [
                'uri'    => "/profitsharing/return-orders/%s",
                'method' => 'GET',
                'remark' => '查询分账回退结果API'
            ],
            'unfreeze'              => [
                'uri'    => "/profitsharing/orders/unfreeze",
                'method' => 'POST',
                'remark' => '解冻剩余资金API'
            ],
            'getTransactionsAmount' => [
                'uri'    => '/profitsharing/transactions/%s/amounts',
                'method' => 'GET',
                'remark' => '查询剩余待分金额API',
            ],
            'addReceivers'          => [
                'uri'    => '/profitsharing/receivers/add',
                'method' => 'POST',
                'remark' => '添加分账接收方API',
            ],
            'deleteReceivers'       => [
                'uri'    => '/profitsharing/receivers/delete',
                'method' => 'POST',
                'remark' => '删除分账接收方API',
            ],
        ];

        if (isset($endpoints[$key])) {
            $temp                   = $endpoints[$key]['uri'];
            $endpoints[$key]['url'] = $this->base . $temp;
            return $endpoints[$key];
        } else {
            throw new Exception('未找到对应的接口信息 ' . $key);
        }
    }

    /**
     * 请求分账API
     *
     * @param $data
     * @return array
     * @throws Exception
     */
    public function createOrder($data)
    {
        $endPoint = $this->getEndPoint('createOrder');

        $receiverList = $data['receivers'];
        $newReceivers = [];

        foreach ($receiverList as $item) {

            $temp = [
                'type'        => $item['type'], // 分账接收方类型 - 1、MERCHANT_ID：商户号，2、PERSONAL_OPENID：个人openid（由父商户APPID转换得到）
                'account'     => $item['account'], // 分账接收方帐号 - 1、分账接收方类型为MERCHANT_ID时，分账接收方账号为商户号,2、分账接收方类型为PERSONAL_OPENID时，分账接收方账号为个人openid
                'amount'      => $item['amount'], // 分账金额 - 分账金额，单位为分，只能为整数，不能超过原订单支付金额及最大分账比例金额
                'description' => $item['description'], // 分账描述 - 分账的原因描述，分账账单中需要体现
                /**
                 *  可选项，在接收方类型为个人的时可选填，若有值，会检查与 name 是否实名匹配，不匹配会拒绝分账请求
                 * 1、分账接收方类型是PERSONAL_OPENID，是个人姓名的密文（选传，传则校验） 此字段的加密方法详见：
                 */
                'name'        => $item['name'] ?? '', // 分账个人接收方姓名
            ];

            // 判断接收方是否是个人
            if ($item['type'] == 'PERSONAL_OPENID') {
                // 分账个人接收方姓名
                $temp['name'] = Utility::getWePayEncrypt($temp['name'], $this->config['platform_public']);
            }

            $newReceivers[] = $temp;
        }

        $newData = [
            'appid'            => $this->config['appid'], // 微信分配的商户appid
            'transaction_id'   => $data['transaction_id'], // 微信支付订单号
            'out_order_no'     => $data['order_no'], // 商户系统内部的分账单号，在商户系统内部唯一，同一分账单号多次请求等同一次。只能是数字、大小写字母_-|*@
            'unfreeze_unsplit' => $data['unfreeze_unsplit'] ?? true, // 是否解冻剩余未分资金 -  1、如果为true，该笔订单剩余未分账的金额会解冻回分账方商户；2、如果为false，该笔订单剩余未分账的金额不会解冻回分账方商户，可以对该笔订单再次进行分账。
            'receivers'        => $newReceivers, //  分账接收方列表，可以设置出资商户作为分账接受方，最多可有50个分账接收方
        ];

        $newData = json_encode($newData);
        $result  = $this->client->requestApi($endPoint, [], $newData, $this->headers, true);

        return $this->handleResult($result);
    }

    /**
     * 查询分账结果API
     *
     * @param $outOrderNo string    商户分账单号
     * @param $transactionId string 微信订单号
     * @return array
     * @throws Exception
     */
    public function getOrdersDetail($outOrderNo, $transactionId)
    {
        $endPoint        = $this->getEndPoint('getOrdersDetail');
        $endPoint['url'] = sprintf($endPoint['url'], $outOrderNo);

        $params = [
            'transaction_id' => $transactionId
        ];

        $result = $this->client->requestApi($endPoint, $params, [], $this->headers, true);

        return $this->handleResult($result);
    }


    /**
     * 请求分账回退API
     *
     * @param $data
     * @return array
     * @throws Exception
     */
    public function returnOrders($data)
    {
        $endPoint = $this->getEndPoint('returnOrders');

        $newData = [
            'out_return_no' => $data['return_no'],// 商户回退单号 - 此回退单号是商户在自己后台生成的一个新的回退单号，在商户后台唯一
            'return_mchid'  => $data['return_mchid'], // 回退商户号 - 分账回退的出资商户，只能对原分账请求中成功分给商户接收方进行回退
            'amount'        => $data['amount'], // 回退金额 - 需要从分账接收方回退的金额，单位为分，只能为整数，不能超过原始分账单分出给该接收方的金额
            'description'   => $data['description'], // 回退描述 - 分账回退的原因描述
        ];

        // order_id 和 out_order_no 二选一
        // 微信分账单号 - 微信分账单号，微信系统返回的唯一标识。
        !empty($data['wechat_order_id']) && $newData['order_id'] = $data['wechat_order_id'];
        // 商户分账单号 - 商户系统内部的分账单号，在商户系统内部唯一，同一分账单号多次请求等同一次。 取值范围：[0-9a-zA-Z_*@-]
        !empty($data['order_no']) && $newData['out_order_no'] = $data['order_no'];

        $newData = json_encode($newData);
        $result  = $this->client->requestApi($endPoint, [], $newData, $this->headers, true);

        return $this->handleResult($result);
    }

    /**
     * 查询分账回退结果API
     *
     * @param $outReturnNo string 商户回退单号
     * @param $outOrderNo  string 商户回退单号
     * @return array
     * @throws Exception
     */
    public function getReturnOrdersInfo($outReturnNo, $outOrderNo)
    {
        $endPoint        = $this->getEndPoint('getReturnOrdersInfo');
        $endPoint['url'] = sprintf($endPoint['url'], $outReturnNo);

        $params = [
            'out_order_no' => $outOrderNo
        ];

        $result = $this->client->requestApi($endPoint, $params, [], $this->headers, true);

        return $this->handleResult($result);
    }

    /**
     *  解冻剩余资金API
     *
     * @param $transactionId string 微信订单号
     * @param $outOrderNo string 商户分账单号
     * @param $description string 分账描述
     * @return array
     * @throws Exception
     */
    public function unfreeze($transactionId, $outOrderNo, $description)
    {
        $endPoint = $this->getEndPoint('unfreeze');
        $data     = [
            'transaction_id' => $transactionId,
            'out_order_no'   => $outOrderNo,
            'description'    => $description,
        ];

        $data   = json_encode($data);
        $result = $this->client->requestApi($endPoint, [], $data, $this->headers, true);

        return $this->handleResult($result);
    }

    /**
     * 查询剩余待分金额API
     *
     * @param $transactionId
     * @return array
     * @throws Exception
     */
    public function getTransactionsAmount($transactionId)
    {
        $endPoint        = $this->getEndPoint('getTransactionsAmount');
        $endPoint['url'] = sprintf($endPoint['url'], $transactionId);
        $result          = $this->client->requestApi($endPoint, [], [], $this->headers, true);

        return $this->handleResult($result);
    }

    /**
     * 添加分账接收方API
     *
     * @param $data
     * @return array
     * @throws Exception
     */
    public function addReceivers($data)
    {
        $endPoint = $this->getEndPoint('addReceivers');
        $newData  = [
            'appid'           => $this->config['appid'], // 应用ID
            'type'            => $data['type'], // 分账接收方类型 - MERCHANT_ID：商户ID PERSONAL_OPENID：个人openid（由父商户APPID转换得到）
            'account'         => $data['account'], // 分账接收方帐号 - 类型是MERCHANT_ID时，是商户号 类型是PERSONAL_OPENID时，是个人openid
            /**
             * 与分账方的关系类型
             * 子商户与接收方的关系。 本字段值为枚举：
             * STORE：门店
             * STAFF：员工
             * STORE_OWNER：店主
             * PARTNER：合作伙伴
             * HEADQUARTER：总部
             * BRAND：品牌方
             * DISTRIBUTOR：分销商
             * USER：用户
             * SUPPLIER： 供应商
             * CUSTOM：自定义
             */
            'relation_type'   => $data['relation_type'],

            /**
             * 自定义的分账关系
             * 子商户与接收方具体的关系，本字段最多10个字。当字段relation_type的值为CUSTOM时，本字段必填;
             * 当字段relation_type的值不为CUSTOM时，本字段无需填写。示例值：代理商
             */
            'custom_relation' => $data['custom_relation'] ?? ''
        ];

        /**
         * 分账接收方类型是MERCHANT_ID时，是商户全称（必传），当商户是小微商户或个体户时，是开户人姓名 分账接收方类型是PERSONAL_OPENID时，是个人姓名（选传，传则校验）
         * 1、此字段需要加密,加密方法详见：敏感信息加密说明
         */
        // 分账个人接收方姓名
        !empty($data['name']) && $newData['name'] = Utility::getWePayEncrypt($data['name'], $this->config['platform_public']);

        $newData = json_encode($newData);
        $result  = $this->client->requestApi($endPoint, [], $newData, $this->headers, true);

        return $this->handleResult($result);
    }

    /**
     * 删除分账接收方API
     *
     * @param $type string     分账接收方类型
     * @param $account string  分账接收方账号
     * @return array
     * @throws Exception
     */
    public function deleteReceivers($type, $account)
    {
        $endPoint = $this->getEndPoint('deleteReceivers');
        $data     = [
            'appid'   => $this->config['appid'], // 应用ID
            'type'    => $type, // 分账接收方类型 - 枚举值：MERCHANT_ID：商户号 PERSONAL_OPENID：个人openid（由父商户APPID转换得到）
            'account' => $account // 分账接收方账号 - 类型是MERCHANT_ID时，是商户号 类型是PERSONAL_OPENID时，是个人openid
        ];

        $newData = json_encode($data);
        $result  = $this->client->requestApi($endPoint, [], $newData, $this->headers, true);

        return $this->handleResult($result);
    }

}
