<?php

namespace Sxqibo\FastPayment\NewPay;

/**
 * 5.4 付款到银行
 *
 * 文档地址：https://www.yuque.com/chenyanfei-sjuaz/uhng8q/ccdtg7
 */
final class SinglePayService
{
    public function singlePay(SinglePayModel $singlePayModel)
    {
        $request = $singlePayModel->getModelData();

        $content = HttpUtil::post($request, SinglePayModel::REQUEST_URL);

        $content = json_decode($content, true);
        $bool = $singlePayModel->verifySign($content);

        if ($bool) {
            return $content;
        }

        return '返回值验签失败';
    }
}
