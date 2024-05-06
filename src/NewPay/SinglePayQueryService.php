<?php

namespace Sxqibo\FastPayment\NewPay;

use Sxqibo\FastPayment\Common\HttpUtil;

/**
 * 5.7 查询接口-代付
 *
 * https://www.yuque.com/chenyanfei-sjuaz/uhng8q/bfcc86
 */
final class SinglePayQueryService
{
    public function query(SinglePayQueryModel $singlePayQueryModel)
    {
        $data = $singlePayQueryModel->getModelData();

        $content = HttpUtil::post($data, SinglePayQueryModel::REQUEST_URL);

        $content = json_decode($content, true);

//        if (in_array($content['resultCode'], [4444, 5555])) {
//            return $content;
//        }

        $bool = $singlePayQueryModel->verifySign($content);

        if ($bool) {
            return $content;
        }

        return '返回值验签失败';
    }
}
