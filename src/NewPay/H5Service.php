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

        return HttpUtil::post($request, H5Model::REQUEST_URL);
    }
}
