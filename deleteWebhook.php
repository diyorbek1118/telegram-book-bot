<?php
require __DIR__ . '/vendor/autoload.php';
use Telegram\Bot\Api;

$telegram = new Api('BOT_TOKEN');
$telegram->deleteWebhook();
echo "Webhook o‘chirildi.\n";
