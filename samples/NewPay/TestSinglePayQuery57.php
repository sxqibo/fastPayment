<?php

use Sxqibo\FastPayment\NewPay\SinglePayQueryModel;
use Sxqibo\FastPayment\NewPay\SinglePayQueryService;

require_once '../../vendor/autoload.php';

class TestSinglePayQuery
{
    /**
     * 测试付款到银行的查询
     */
    public function singlePayQuery($merId, $orderId, $payeeName, $payeeAccount, $tranAmt, $submitTime)
    {
        $data = [
            'merId' => $merId,
            'merOrderId' => $orderId,
            'submitTime' => $submitTime,
        ];

        $singlePayQueryModel = new SinglePayQueryModel();

        $privateKey = 'MIICdQIBADANBgkqhkiG9w0BAQEFAASCAl8wggJbAgEAAoGBAJAR7r0PNqaepl06
EW4ANGxwQHrE0VKdUoosSV98sV/TRs6AYDHF1JgISjsh42xADvRG+YFSFe2/tjlw
n/b6k4FkA0LwIqD6Yph5pBYaZWZsZEDvPbN1L0kOYuBYkfDccVlwq3OKBKKl4rub
oGvfwubCB2WoHmGOWiCbzs04PSlXAgMBAAECgYAqVJqJAkdUfZj0G3OzlmvQ0Mqh
R+MZGxB9eLW2ULTtKu7LDf01oqLsaMMmzLY9wDSkoZX94ViAGqw4BFd8AfQEx6rA
6KPDFNxfptS0EPGHgTuPFECL7qVV5XScsO4RDMOpgjSGfcqviLt3Nbkl4vwL2TKg
yfG84HxOdL/wBgo3wQJBAMaaHRWbPLIoLnQADQqksWjlkRIu69bdZfZ4o+NNxrss
qNEYWxv6kFqV7jY0MXl79VeTTaYDwkhShM1jn9XES9sCQQC5tS9hbuFZW9s/woVs
a9Qjrh4oXPMFybC/7MKbMuiTpS2e2zIjJhQpwgIG3ftGc2phOIH8HUv3hd4ooVsJ
2O81AkAf7NA2E/FK1ki5XvS5vEXEjfqnCKHitU5Zs4Ts2ijTF9e/XQHwWnPwC9/y
GKvHUpTa0hQOVtZZV+J/Pb+I1ng3AkBROSXX/58gbkSexn2ExkSqtmUKUl0YkvZz
eyJCryl6Kiyh5k0vgmAfQ3OPfVeBoMlObGCt3EJ1qF9adfhTfkZhAkAEd8C2TDCc
Wd1eE/NAGKY+T2LCRikheV9Qevh98PPau0XcVTVGAO6SArgqFZ4Q2CrPSm+cahiG
qFFNgB2o3d32';
        $publicKey = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCSU82GM2kEr/s9mvUmMvZjm/Yq
15nWSHWl+rPemOP0/WGCu7xI96OnK94IA5YMMdmou27Nlk2M8+g29IEOBleAwZkI
8MW9FO8ceFI+l0uyBuisd6GEKPLOB7CQb7XKkis/a9dLqxR+aGiWgkC7/E8dlNUm
JUV53TGrgZ2yiVeyVQIDAQAB';

        $singlePayQueryModel->setPrivateKey($privateKey);
        $singlePayQueryModel->setPublicKey($publicKey);

        $singlePayQueryModel->copy($data);

        $singlePayQueryService = new SinglePayQueryService();
        $result = $singlePayQueryService->query($singlePayQueryModel);

        var_dump($result);
    }
}

function test1()
{
    $newPayTest = new TestSinglePayQuery();
    $merId = '';
    $orderId = '';
    $payeeName = '';
    $payeeAccount = '';
    $tranAmt = 1;
    $submitTime = '20231130';
    $newPayTest->singlePayQuery($merId, $orderId, $payeeName, $payeeAccount, $tranAmt, $submitTime);
}

test1();
