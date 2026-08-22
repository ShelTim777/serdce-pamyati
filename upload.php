<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$key = isset($_GET['key']) ? $_GET['key'] : '';
if (!$key || strpos($key, 'orders/') !== 0 || strpos($key, '..') !== false) {
  http_response_code(400);
  echo json_encode(['error' => 'bad key']);
  exit;
}
$key = preg_replace('/[^A-Za-z0-9_\/.\-а-яА-Я]/', '_', $key);

$dir = __DIR__ . '/' . dirname($key);
if (!is_dir($dir)) { @mkdir($dir, 0755, true); }

$in = file_get_contents('php://input');
if ($in === false || strlen($in) === 0) {
  http_response_code(400);
  echo json_encode(['error' => 'empty body']);
  exit;
}
file_put_contents(__DIR__ . '/' . $key, $in);

$proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
echo json_encode(['ok' => true, 'url' => $proto . '://serdce-pamyati.ru/' . $key]);
?>
