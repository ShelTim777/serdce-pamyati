<?php
$SHOP_ID = '1438442';
$SECRET_KEY = 'live_Yy7z9DCy8-IO2M7aYr28_H_5PC-uXFPrpbhSJ9xVQus';

$amount = isset($_GET['amount']) ? $_GET['amount'] : '2990';
$order = 'ORD-' . round(microtime(true) * 1000);

$body = json_encode([
  'amount' => ['value' => number_format((float)$amount, 2, '.', ''), 'currency' => 'RUB'],
  'capture' => true,
  'confirmation' => ['type' => 'redirect', 'return_url' => 'https://serdce-pamyati.ru/order.html?order=' . $order],
  'description' => 'Табличка и страница памяти',
  'metadata' => ['order' => $order]
]);

$ctx = stream_context_create([
  'http' => [
    'method' => 'POST',
    'header' => "Content-Type: application/json\r\nAuthorization: Basic " . base64_encode($SHOP_ID . ':' . $SECRET_KEY) . "\r\nIdempotence-Key: " . $order . "\r\n",
    'content' => $body,
    'timeout' => 30,
    'ignore_errors' => true
  ]
]);
$res = file_get_contents('https://api.yookassa.ru/v3/payments', false, $ctx);
$d = json_decode($res, true);

if ($d && isset($d['confirmation']['confirmation_url'])) {
  header('Location: ' . $d['confirmation']['confirmation_url'], true, 302);
  exit;
}
header('Content-Type: application/json');
http_response_code(500);
echo $res;
?>
