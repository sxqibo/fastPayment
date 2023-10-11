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
        $request['signValue'] = $this->buildSign($singlePayModel->getSignData(), $singlePayModel->privateKey);

        $content = HttpUtil::post($request, SinglePayModel::REQUEST_URL);

        $bool = $this->verifySign(json_decode($content, true), $singlePayModel->publicKey);

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
     * @throws Exception
     */
    public function verifySign($data, $publicKey): bool
    {
        // 生成验签字符串
        $signParam = Util::getStringData(SinglePayModel::VERIFY_FIELD, $data);

        // 验签
        $res = openssl_get_publickey($publicKey);
        return (bool)openssl_verify($signParam, base64_decode($data['signValue']), $res);
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

}
