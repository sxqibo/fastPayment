<?php
/**
 * 微信消费者投诉接口
 */
require_once '../../vendor/autoload.php';

use Sxqibo\FastPayment\WeChatPay\Complaints;

$options = [
    // 微信绑定APPID
    'appid'        => '',

    // 微信商户编号
    'mch_id'       => '1404807102',

    // 微信商户密钥
    'mch_v3_key'   => '1mfOoFUp4ktCOO8ktItbXdQTedsKoOXK',

    // 商户API私钥
    'cert_private' => '-----BEGIN PRIVATE KEY-----
MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCTsP4lKtTdxGBa
D6oq9HEdHeb9hdBRLTwn8KjCz7zo1WIvDtbPxZfQJZmbHBRly54B3bsF7p/3fWzB
aI8TXbZ58LhgbRv+Br12GEXHfHV9mV7BTdW2foiUTyGwCioN35fWuhK/eaMSngw1
HltO4cKepk5RDyrwNGGidZbnPhwbZOe+0NxapHYu/7qFBMDJKWG5hGS6SY0dErpM
ymqYO7Cg02Ktb0it6aWQFuK4OqseHVTYVlSOiLMBDXqmv2noiK6mBfwBchmhZaX7
enWif/fDrAw5SwIDWpkbn/DkgzTuqlGphkl8d2CpLGXGzu/tZ0FZKs1hHS6cjY2M
SgTO3unjAgMBAAECggEBAI3MXxW8O8f4JTIS3CSFsJxt+zrp4QovThRiwQTZgtxh
EvVvX9r4MTvM/d+oZAUgK8JK6qdVZgyuRV6kTsofLXWCIuOhnkCUpA7gWw6edgdy
20DoxAmFDwiluQhVme7b/+JPoHRqCqFzUPEnBi+EHeFIduTolScug5cBIzM9tKqv
D1R7YnGZYD3U3nOwqY20reLCFmwU7mWk5LoXIjR0piFlja0uP2vyuGChYN9/peef
+hNUOLijBnw2cQYs3Cp01XlUlftSOb16qXPyO7AIPVbIcRdxrSnv/EVbp7262it8
EOGCB8+QHZcHNVIg6wiry1q5/8Go6FU0OVGw/n8M4BECgYEAxDM9a51wblXw1HqH
pDd1nTvaISIkwan9DbXTuaYcklaOlHTnkbob5D6kFDJnqEsN06QYmQjUJPziDsCT
NnlC2kKKFvjbglmsdxIDedoBYCGUf3X9HagoU9L2HXF3+Mn50HAD2Ds1Eh8aRJ4g
DBpYEGhtO0BC1B7jq+j8sZaRYS0CgYEAwLTLnWhTiboJA6yCYR0+Yk2j6Y6cFxbc
tILdnJBqmnpLg6ZbOAFcB8FTbJLK6LDxPoP7rPhAbzI6z8/BarqPl6URg3h40j4U
Tm613C2CHC6DnEn0BAb5H4v9XVvXvGPfbgGzrrlSVQUZktHu2cK1aCHG7SzeYaBq
KoWZuzYbwU8CgYEApaYtYUKXvlBI+NxK0VcRsiLqU7ckGW6P/Jdbnw6kaNkzoBvj
t6HOErLgjTzRT8GudtXA/tP5aREpOxNUN1XCH8y2EhciHbfgaNeIn8R0DNnNKqBP
iE6FXeBrcwhuJltA077/P/0dHaOs5eorXIyRdaj1MGVdBwLdbjNTxgw9yjECgYBo
tVQ10Wwi19zyDBhD4Hn3PCymSYDy4s9Fnh4AZlAmY/EINao3AjYZWKiVxCVQzmQn
DdwAnluUj/x40nBMJ9bCFUUw5JLx2h16iJl0a53Y5kVI9L4MOiW/SHeA9NiCWtoa
kf4qIDRmUgEVT9CyriOX01KdqNWkwl8tf66KlNn77QKBgAToykkg4sHwvIfhsAFO
NMI7zoIsJFSvpFAV+a1wVXmHHJ2VKUBJH4eeCp/rBFIbBUngd6HQg+f7mu139GXC
NKA8+LtcLa+IjXNoWtxI1MLVP6GsiwZkEVxl3K8YQgX1OIm+C3uVQAk6W68E/vwO
poAYen2FQoDcrljOFdV/oG9V
-----END PRIVATE KEY-----
',

    // 商户API公钥
    'cert_public'  => '-----BEGIN CERTIFICATE-----
MIIEKDCCAxCgAwIBAgIULLMH1YaYn1iWCuU9ZcEWQgMEZXswDQYJKoZIhvcNAQEL
BQAwXjELMAkGA1UEBhMCQ04xEzARBgNVBAoTClRlbnBheS5jb20xHTAbBgNVBAsT
FFRlbnBheS5jb20gQ0EgQ2VudGVyMRswGQYDVQQDExJUZW5wYXkuY29tIFJvb3Qg
Q0EwHhcNMjMxMDIwMDczNDEwWhcNMjgxMDE4MDczNDEwWjCBgTETMBEGA1UEAwwK
MTQwNDgwNzEwMjEbMBkGA1UECgwS5b6u5L+h5ZWG5oi357O757ufMS0wKwYDVQQL
DCTlsbHopb/lspDkvK/kv6Hmga/np5HmioDmnInpmZDlhazlj7gxCzAJBgNVBAYM
AkNOMREwDwYDVQQHDAhTaGVuWmhlbjCCASIwDQYJKoZIhvcNAQEBBQADggEPADCC
AQoCggEBAJOw/iUq1N3EYFoPqir0cR0d5v2F0FEtPCfwqMLPvOjVYi8O1s/Fl9Al
mZscFGXLngHduwXun/d9bMFojxNdtnnwuGBtG/4GvXYYRcd8dX2ZXsFN1bZ+iJRP
IbAKKg3fl9a6Er95oxKeDDUeW07hwp6mTlEPKvA0YaJ1luc+HBtk577Q3Fqkdi7/
uoUEwMkpYbmEZLpJjR0SukzKapg7sKDTYq1vSK3ppZAW4rg6qx4dVNhWVI6IswEN
eqa/aeiIrqYF/AFyGaFlpft6daJ/98OsDDlLAgNamRuf8OSDNO6qUamGSXx3YKks
ZcbO7+1nQVkqzWEdLpyNjYxKBM7e6eMCAwEAAaOBuTCBtjAJBgNVHRMEAjAAMAsG
A1UdDwQEAwID+DCBmwYDVR0fBIGTMIGQMIGNoIGKoIGHhoGEaHR0cDovL2V2Y2Eu
aXRydXMuY29tLmNuL3B1YmxpYy9pdHJ1c2NybD9DQT0xQkQ0MjIwRTUwREJDMDRC
MDZBRDM5NzU0OTg0NkMwMUMzRThFQkQyJnNnPUhBQ0M0NzFCNjU0MjJFMTJCMjdB
OUQzM0E4N0FEMUNERjU5MjZFMTQwMzcxMA0GCSqGSIb3DQEBCwUAA4IBAQCsn5fA
QYD/ByDcQCYp9V0Qi2hGmLO5SG2WNpGNFHVK7MM4WLcZdoDWxoTCFkreyYPMNEjR
VzERo6KN9aHWqV49JMS2lR67+YQNscwM3Oa9Gxzy+ifjgFE7lSEeeho9E/JDtQRW
neiaYZ0urkeozeuqSXL5Jfu0sAGx36HcxGUnnQaXIrURgpNdDodkLtwU7p+CI+Av
TlPOQqwxHi4gBB+Bazy4f4TkQvtG0vNR5FvkWbgrbtFIoC2NjTjr8IU97W3SDk0w
+xasINgqUDG+Rdhv3ZjUb4acQXFAlLzjJVRkE7KgQNm6v4oelj8Cdyp0F66EcrRS
zVTuVrWyIrrV9dxI
-----END CERTIFICATE-----',
];

try {
    $service = new Complaints($options);
    // 测试查询投诉单列表
    $result = $service->getBillList();
    dd($result, '测试查询投诉单列表');

    // 测试查询投诉单详情
    $complainId = '';
    if ($complainId) {
        $result = $service->getBillDetail($complainId);
        dd($result, '测试查询投诉单详情');
    }

    // 测试查询投诉单协商历史
    $complainId = '';
    if ($complainId) {
        $result = $service->getBillNegotiationHistory($complainId);
        dd($result, '测试查询投诉单协商历史');
    }

} catch (\Exception $e) {
    dd($e->getMessage(), '错误信息');
}


function dd($data, $title = '打印测试')
{
    print_r('>>' . $title);
    print_r(PHP_EOL);
    print_r($data);
    print_r(PHP_EOL);
    print_r(str_repeat('----', 20));
    print_r(PHP_EOL);
}


