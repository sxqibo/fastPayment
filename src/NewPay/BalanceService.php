<?php

namespace Sxqibo\FastPayment\NewPay;

use Sxqibo\FastPayment\Common\HttpUtil;

/**
 * 5.11 商户账户余额查询接口
 *
 * https://www.yuque.com/chenyanfei-sjuaz/uhng8q/stxmz7
 */
final class BalanceService
{
    /**
     * 获取余额
     * @param BalanceModel $balanceModel
     * @return array|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function queryBalance(BalanceModel $balanceModel)
    {
        $request = $balanceModel->getModelData();

        $content = HttpUtil::post($request, BalanceModel::REQUEST_URL);

        $content = json_decode($content, true);
        $bool = $balanceModel->verifySign($content);

        if ($bool) {
            return $content;
        }

        return [$content, '返回值验签失败'];
    }
}
