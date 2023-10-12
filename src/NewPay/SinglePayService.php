<?php

namespace Sxqibo\FastPayment\NewPay;

use Exception;
use GuzzleHttp\Client;

/**
 * 付款到银行
 *
 * 文档地址：https://www.yuque.com/chenyanfei-sjuaz/uhng8q/ccdtg7
 */
final class SinglePayService
{
    public function singlePay(SinglePayModel $singlePayModel)
    {
        $msgText = $singlePayModel->getPayInfo();

        // 付款公司钥/网关公钥(付款).pem
        $msgCiphertext = json_encode($msgText, JSON_UNESCAPED_UNICODE);
        $result = $this->publicEncrypt($msgCiphertext, $singlePayModel->publicKey);

        $singlePayModel->msgCiphertext = $result;

        $request = $singlePayModel->getData();

        // 付款公私钥/商户私钥（付款）.pem
        // 计算签名
        $request['signValue'] = RsaUtil::buildSignForBase64($singlePayModel->getSignData(), $singlePayModel->privateKey);

        $content = HttpUtil::post($request, SinglePayModel::REQUEST_URL);

        $content = json_decode($content, true);
        $bool = RsaUtil::verifySignForBase64($content['signValue'], $singlePayModel->publicKey, $signParam = Util::getStringData(SinglePayModel::VERIFY_FIELD, $content));

        if ($bool) {
            return $content;
        }

        return '返回值验签失败';
    }

    /**
     * 付款detail的加密
     *
     * @param $input
     * @param $pk
     * @return false|string
     */
    public function publicEncrypt($input, $pk)
    {
        $split = str_split($input, 117);

        $crypto = '';

        foreach ($split as $chunk) {
            $isOkey = openssl_public_encrypt($chunk, $output, $pk, OPENSSL_PKCS1_PADDING);
            if (!$isOkey) {
                return false;
            }
            $crypto .= $output;
        }

        return base64_encode($crypto);
    }

}
