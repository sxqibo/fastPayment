<?php

namespace Sxqibo\FastPayment\WePay;

use Sxqibo\FastPayment\Common\Client;
use Exception;

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
        $endPoint = [
            'url'    => $this->uri . 'transfer/batches',
            'method' => 'POST',
        ];
        $detailList = $data['transfer_detail_list'];
        $newDetailList = [];
        foreach ($detailList as $item) {
            $newDetailList[] = [
                'out_detail_no'   => $item['detail_no'],
                'transfer_amount' => $item['transfer_amount'],
                'transfer_remark' => $item['transfer_remark'],
                'openid'          => $item['openid'],
                'user_name'       => $item['user_name'],
                'user_id_card'    => $item['user_id_card'],
            ];
        }

        $newData = [
            'appid'                => $this->appid,
            'out_batch_no'         => $data['batch_no'],
            'batch_name'           => $data['title'],
            'batch_remark'         => $data['remark'],
            'total_amount'         => $data['total_amount'],
            'transfer_detail_list' => $detailList,
        ];

        $result = $this->client->requestApi($endPoint, [], $newData, $this->headers, true);

        return $this->handleResult($result);
    }

}
