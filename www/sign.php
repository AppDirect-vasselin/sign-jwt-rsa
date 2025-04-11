<?php

const INTEGRATION_KEY = '08c7c70f-1ea8-4f01-bea6-a30e6278aede';
CONST PRIVATE_KEY = '-----BEGIN PRIVATE KEY-----
MIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQC+HynBnDfcpZbS
N8GKiM13xSwdeniuNjASkQMmeNybEbXcIvfcaaa0x0gvLVWhD59+Q++UXPUunWMR
B6Y+y2BCxoQitqbsxEw7s4zit6c61dE1hwljLv2lJ3ABS2rSouwIf6TSCmb5SA3c
gJTMWQQrfmmUUQV3IPR4gfA6dADyNAept6NZsR1A1NGt+aGZ/uF47pMFTtzxnE2/
EEZsKhGgRsN8GGr6pyY6hXoQje1rshAlX1h7DqfVPxPrqiwwFLYEN+owS1oOyVjW
I9H6eIRuEQXVusDnwSdc7eZcoKHTa7WxVsZjyUP0PcZU1r+QgADsX9jVY+eSHhyO
BI3J3VwJAgMBAAECggEAX3N33FtY9G8yTHJvueS6+4HDhz1Ar35HHlstsLgHUddP
bakktcVZS/OtOvpQvl8AnTzwYJVXB0M/RIKJpYcn2f6ewmrfXYmkGGkpt32q+O/2
bCBSxzgFYlBtfEjn5b6kMBQdpNJSJp9VTjpD+mBKpqd9zFftHYUvF/1spXwuKWq1
xLAGxutvsMDDOhPXL/isy8zyodLre9/9Ul8Mbfte6JzJr9nML/P/cCAQ6/LVjjAO
/58a/4W+GR2qs0y3W7pB83eet4faXLCHsJ5uskwSmw6VuS6XNjsz9tWFzcjPSaiN
zJYnvE29a3bDeLkd4AJfJC7JPCHU128TvY+frByXDQKBgQDs3WOqSal6Xy/pdn7d
iYbNf0vTyMbcQoN8l5Kz+KRotLRW1JMKH9/zcj5aGk0DmoRlB0RPCWnNXr0Yx/GG
Gc0Fw3remIfo92ZHQ7ipFcqPhPhQL7vHFs7difHFVw0qgTOM8tM4ajF0RZtW/NZQ
Y/FNYhA0Pq3nPokMQO1glru2zwKBgQDNexQThjqL928FF1iFpqvvvuU2RXF/zW/e
8jkDKJcWwcMgsF0VRvyzkjMm7t8v4T2ADCu+lVe4kojkN/Z3knN+sY7p4QUKHudb
Sl7nMIRu5i1oQhW5CZ+ujqs1F8sGlDDlfSqRL0o6EAdZCozxyI2uULaJO7nUV3En
0wE15nv1pwKBgQDR9ej7sLIfPDMvQo9wdxDcJcOkZqwH4JnHHmC3UWcDPXNCKMpy
RhDxLkqg+gFceWJ0X32ohdKYbis2xT2Nvl8jhcTrfc+zrWGnOBt/Hkbg82BdTAKq
lsgmTrnWULTmAYMdpL2gm1ldFLp+4D5lIkJqcsukVA85FRDMoN0KKnMYtwKBgQCI
Zyi3oUZiPbn8JjhEWJUQjKd59HVYwpZ96EvCdgJ+P9f5bjoMctgzmFzOfom4Lgx2
YR304QyfYN7wqNm7HIQA2XumdBK1Wjh42JDRZdq0B6ai7COhdusCpICwuP+0eVFQ
Nlu4jyz2CEfoauGPa3PqAHh79yLv+7AJIMBP+JXXRwKBgQC3BI8Ahu/z9D+lKtZR
KCF+H7fC9QO84r4a82dKJY4VZwLXPfMywAxrlKG5UZ6BpPo8vKygP6ksALNQ6nJf
x3c4/56qqe+mzJJLtGgy7Y74tfXJ2w9Cret5R8uy8QjR5pJp1SmVqHOQMe1+X2FL
U1LWd0W30wSqjGGkahjJzC/w3Q==
-----END PRIVATE KEY-----';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

/*$payload = json_decode(file_get_contents('php://input'), true);

if (!$payload || !isset($payload['integrationKey']) || !isset($payload['privateKey'])) {
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}
    
$integrationKey = $payload['integrationKey'];
$privateKey = $payload['privateKey'];
*/

$integrationKey = INTEGRATION_KEY;
$privateKey = PRIVATE_KEY;

/* HEADER */
$headers = json_encode([
    'alg' => 'RS256',
    'typ' => 'JWT',
]);
$base64Header = _base64UrlEncode($headers);
/* HEADER */

/* BODY */
$dateTime = new \DateTime();
$issuedAt = $dateTime->getTimeStamp();
$expiresAt = $dateTime->add(new \DateInterval('PT15M'))->getTimeStamp();
$body = json_encode([
    'iss' => $integrationKey,
    'aud' => 'https://zone.itcloud.ca/api/partner',
    'iat' => $issuedAt,
    'exp' => $expiresAt,
    'scope' => 'report.read',
]);
$base64Body = _base64UrlEncode($body);
/* BODY */

$privateKeyId = openssl_pkey_get_private($privateKey);
if (!$privateKeyId) {
    echo json_encode(['error' => 'Invalid private key']);
    exit;
}

// Construire la signature
$token = "$base64Header.$base64Body";
$signature = '';
openssl_sign($token, $signature, $privateKeyId, OPENSSL_ALGO_SHA256);

$signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

echo json_encode(['signedToken' => $token . "." . $signature]);

function _base64UrlEncode($text){
    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($text));
}