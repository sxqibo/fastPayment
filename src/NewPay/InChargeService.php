<?php

namespace Sxqibo\FastPayment\NewPay;

use Sxqibo\FastPayment\Common\HttpUtil;

/**
 * 5.1 微信公众号&支付宝生活号
 *
 * https://www.yuque.com/chenyanfei-sjuaz/uhng8q/hekpg2
 */
final class InChargeService
{
    public function inCharge(InChargeModel $inchargeModel)
    {
        $request = $inchargeModel->getModelData();

        $content = HttpUtil::post($request, InChargeModel::REQUEST_URL);

        $content = json_decode($content, true);
        $bool = $inchargeModel->verifySign($content);

        if ($bool) {
            return $content;
        }

        return [$content, '返回值验签失败'];
    }
}
