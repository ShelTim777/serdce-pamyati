<?php
$ev = json_decode(file_get_contents('php://input'), true);
if (isset($ev['event']) && $ev['event'] === 'payment.succeeded') {
  $o = isset($ev['object']['metadata']['order']) ? $ev['object']['metadata']['order'] : 'unknown';
  $patch = json_encode([
    'paid' => true,
    'amount' => isset($ev['object']['amount']['value']) ? $ev['object']['amount']['value'] : '',
    'paidAt' => round(microtime(true) * 1000)
  ]);
  $ctx = stream_context_create([
    'http' => [
      'method' => 'PATCH',
      'header' => "Content-Type: application/json\r\n",
      'content' => $patch,
      'timeout' => 15,
      'ignore_errors' => true
    ]
  ]);
  @file_get_contents('https://serdce-pamyati-default-rtdb.europe-west1.firebasedatabase.app/orders/' . $o . '.json', false, $ctx);
}
header('Content-Type: application/json');
echo '{"ok":true}';
?>
