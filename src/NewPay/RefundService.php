<?php

namespace Sxqibo\FastPayment\NewPay;

/**
 * 退款
 *
 * https://www.yuque.com/chenyanfei-sjuaz/uhng8q/stxmz7
 */
final class RefundService
{
    public function refund(RefundModel $refundModel)
    {
        $msgText = $refundModel->getRefundInfo();

        $msgCiphertext = json_encode($msgText, JSON_UNESCAPED_UNICODE);
        $result = $this->publicEncrypt($msgCiphertext, $refundModel->publicKey);

        $refundModel->msgCiphertext = $result;

        $request = $refundModel->getData();

        $request['signValue'] = RsaUtil::buildSignForBase64($refundModel->getSignData(), $refundModel->privateKey);

        $content = HttpUtil::post($request, RefundModel::REQUEST_URL);

        $content = json_decode($content, true);
        $bool = RsaUtil::verifySignForBase64($content['signValue'], $refundModel->publicKey, Util::getStringData(RefundModel::VERIFY_FIELD, $content));

        if ($bool) {
            return $content;
        }

        return [$content, '返回值验签失败'];
    }

    private function publicEncrypt($input, $pk)
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
