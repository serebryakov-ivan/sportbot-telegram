<?php
// Установка вебхука

require_once '../config/config.php';

$config = require_once '../config/config.php';
$token = $config['telegram']['token'];
$webhook_url = $config['telegram']['webhook_url'];

$url = "https://api.telegram.org/bot{$token}/setWebhook?url={$webhook_url}";

$response = file_get_contents($url);
echo $response;