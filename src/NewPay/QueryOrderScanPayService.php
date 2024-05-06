<?php

namespace Sxqibo\FastPayment\NewPay;

use Exception;
use Sxqibo\FastPayment\Common\HttpUtil;

/**
 * 查询接口-扫码API
 *
 * https://www.yuque.com/chenyanfei-sjuaz/uhng8q/nghr8z
 */
final class QueryOrderScanPayService
{
    public function query(ScanPayQueryModel $scanPayQueryModel)
    {
        $request= $scanPayQueryModel->getModelData();

        // post请求接口
        $content = HttpUtil::post($request, ScanPayQueryModel::REQUEST_URL);



        try {
            $detailList = $scanPayQueryModel->getDetail($content);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $detailList;
    }
}
