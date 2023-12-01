<?php

namespace Sxqibo\FastPayment\NewPay;

/**
 * 5.8 退款接口
 *
 * https://www.yuque.com/chenyanfei-sjuaz/uhng8q/stxmz7
 */
final class RefundService
{
    public function refund(RefundModel $refundModel)
    {
        $request = $refundModel->getModelData();

        $content = HttpUtil::post($request, RefundModel::REQUEST_URL);

        $content = json_decode($content, true);
        $bool = $refundModel->verifySign($content);

        if ($bool) {
            return $content;
        }

        return [$content, '返回值验签失败'];
    }
}
