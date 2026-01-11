<?php

require __DIR__ . '/vendor/autoload.php';

use Telegram\Bot\Api;

$telegram = new Api($_ENV['BOT_TOKEN']);

$telegram->setWebhook([
    'url' => $_ENV['APP_URL'] . '/public/index.php'
]);

echo "Webhook o‘rnatildi!";
