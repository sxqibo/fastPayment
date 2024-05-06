<?php

namespace Sxqibo\FastPayment\NewPay;

use Sxqibo\FastPayment\Common\HttpUtil;

/**
 * 5.9 微信&支付宝扫码（B扫C）
 *
 * https://www.yuque.com/chenyanfei-sjuaz/uhng8q/zokydupg793yle6v#bGx4G
 */
final class PayService
{
    /**
     * 5.9.1 支付接口
     */
    public function pay(PayModel $payModel)
    {
        $request = $payModel->getModelData();

        $content = HttpUtil::post($request, PayModel::REQUEST_URL);

        $content = json_decode($content, true);
        $bool = $payModel->verifySign($content);

        if ($bool) {
            return $content;
        }

        return [$content, '返回值验签失败'];
    }

    /**
     * 5.9.2 交易取消接口
     */
    public function payCancel(PayCancelModel $payCancelModel)
    {
        $request = $payCancelModel->getModelData();

        $content = HttpUtil::post($request, PayCancelModel::REQUEST_URL);

        $content = json_decode($content, true);
        $bool = $payCancelModel->verifySign($content);

        if ($bool) {
            return $content;
        }

        return [$content, '返回值验签失败'];
    }

    public function scp03(Scp03Model $scp03Model)
    {
        $request = $scp03Model->getModelData();

        $content = HttpUtil::post($request, Scp03Model::REQUEST_URL);

        $content = json_decode($content, true);
        $bool = $scp03Model->verifySign($content);

        if ($bool) {
            return $content;
        }

        return [$content, '返回值验签失败'];
    }

    public function scp04(Scp04Model $scp04Model)
    {
        $request = $scp04Model->getModelData();

        $content = HttpUtil::post($request, Scp04Model::REQUEST_URL);

        $content = json_decode($content, true);
        $bool = $scp04Model->verifySign($content);

        if ($bool) {
            return $content;
        }

        return [$content, '返回值验签失败'];
    }
}
