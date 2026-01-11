<?php

require __DIR__ . '/../vendor/autoload.php';

use Telegram\Bot\Api;

$telegram = new Api($_ENV['BOT_TOKEN']);

$update = $telegram->getWebhookUpdate();

if ($update->getMessage()) {

    $message = $update->getMessage();
    $chatId  = $message->getChat()->getId();
    $text    = $message->getText();

    if ($text === '/start') {
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => "✅ Railway PHP bot ishlayapti!\n/start komandasi qabul qilindi"
        ]);
    }
}
