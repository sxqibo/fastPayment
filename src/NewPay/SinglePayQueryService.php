<?php

namespace Sxqibo\FastPayment\NewPay;

use GuzzleHttp\Client;

final class SinglePayQueryService
{

    public function query(SinglePayQueryModel $singlePayQueryModel)
    {
        $data = $singlePayQueryModel->getData();
        $signParam = Util::getStringData(SinglePayQueryModel::SIGN_FIELD, $singlePayQueryModel->getData());

//        $data['signValue'] = $this->buildSign($signParam, $singlePayQueryModel->privateKey);
        $data['signValue'] = RsaUtil::buildSignForBase64($signParam, $singlePayQueryModel->privateKey);

        $content = HttpUtil::post($data, SinglePayQueryModel::REQUEST_URL);

        $content = json_decode($content, true);

        if (in_array($content['resultCode'], [4444, 5555])) {
            return $content;
        }

        $bool = $this->verifySign($content, $singlePayQueryModel->publicKey);

        if ($bool) {
            return $content;
        }

        return '返回值验签失败';
    }

    /**
     * 对返回值验签
     *
     * @param $data
     * @param $publicKey
     * @return bool
     * @throws \Exception
     */
    public function verifySign($data, $publicKey): bool
    {
        // 生成验签字符串
        $signParam = Util::getStringData(SinglePayQueryModel::VERIFY_FIELD, $data);

        // 验签
        $res = openssl_get_publickey($publicKey);
        return (bool)openssl_verify($signParam, base64_decode($data['signValue']), $res);
    }
}
