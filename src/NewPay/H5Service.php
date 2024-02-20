<?php

namespace Sxqibo\FastPayment\NewPay;

/**
 * 5.3 支付宝H5
 *
 * https://www.yuque.com/chenyanfei-sjuaz/uhng8q/efqwi8
 */
final class H5Service
{
    public function h5(H5Model $h5Model)
    {
        $request = $h5Model->getModelData();

        $content = HttpUtil::post($request, H5Model::REQUEST_URL);

        $content = json_decode($content, true);
        $bool = $h5Model->verifySign($content);

        if ($bool) {
            return $content;
        }

        return [$content, '返回值验签失败'];
    }
}
