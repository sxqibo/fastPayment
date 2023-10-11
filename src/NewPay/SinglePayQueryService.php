<?php

namespace Sxqibo\FastPayment\NewPay;

use GuzzleHttp\Client;

final class SinglePayQueryService
{

    public function query(SinglePayQueryModel $singlePayQueryModel)
    {
        $signParam = Util::getStringData(SinglePayQueryModel::SIGN_FIELD, $singlePayQueryModel->getData());

        $this->buildSign($signParam, $singlePayQueryModel->privateKey);

        $content = HttpUtil::post($singlePayQueryModel->getData(), SinglePayQueryModel::REQUEST_URL);

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
     * 生成签名
     *  发送数据时的签名
     *
     * @param $request
     * @param $privateKey
     * @return string
     * @throws \Exception
     */
    public function buildSign($request, $privateKey): string
    {
        // 付款公私钥/商户私钥（付款）.pem
        // 计算签名
        $res = openssl_get_privatekey($privateKey);

        openssl_sign($request, $signature, $res, OPENSSL_ALGO_SHA1);

        return base64_encode($signature);
    }

    /**
     * 对返回值验签
     *
     * @param $data
     * @param $publicKey
     * @return bool
     * @throws Exception
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
