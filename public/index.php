<?php
require __DIR__ . '/../vendor/autoload.php';

use Telegram\Bot\Api;

$telegram = new Api($_ENV['BOT_TOKEN'] ?? '');

file_put_contents('log.txt', date('Y-m-d H:i:s') . " - Webhook hit\n", FILE_APPEND);

$update = $telegram->getWebhookUpdate();

if ($update->getMessage()) {
    $chatId = $update->getMessage()->getChat()->getId();
    $text = $update->getMessage()->getText();

    if ($text === '/start') {
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => "✅ Bot ishlayapti!\n/start komandasi qabul qilindi"
        ]);
    }
}
