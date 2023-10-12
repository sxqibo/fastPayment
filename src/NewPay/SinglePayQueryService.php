<?php

namespace Sxqibo\FastPayment\NewPay;

use GuzzleHttp\Client;

final class SinglePayQueryService
{

    public function query(SinglePayQueryModel $singlePayQueryModel)
    {
        $data = $singlePayQueryModel->getData();
        $signParam = Util::getStringData(SinglePayQueryModel::SIGN_FIELD, $singlePayQueryModel->getData());

        $data['signValue'] = RsaUtil::buildSignForBase64($signParam, $singlePayQueryModel->privateKey);

        $content = HttpUtil::post($data, SinglePayQueryModel::REQUEST_URL);

        $content = json_decode($content, true);

        if (in_array($content['resultCode'], [4444, 5555])) {
            return $content;
        }

        $bool = RsaUtil::verifySignForBase64($content['signValue'], $singlePayQueryModel->publicKey, Util::getStringData(SinglePayQueryModel::VERIFY_FIELD, $content));

        if ($bool) {
            return $content;
        }

        return '返回值验签失败';
    }
}
