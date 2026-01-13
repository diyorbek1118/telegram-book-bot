<?php

namespace Admin;

require_once __DIR__ . '/Menus/AdminMenu.php';

class AdminRouter
{
    public static function handleMessage($telegram, $message): void
    {
        $chatId = $message->getChat()->getId();
        $text   = $message->getText() ?? '';

        if ($text === '/start') {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "👮 Admin panel",
                'reply_markup' => Menus\AdminMenu::main()
            ]);
        }
    }

    public static function handleCallback($telegram, $callback): void
    {
        $chatId = $callback->getMessage()->getChat()->getId();
        $data   = $callback->getData();

        if ($data === 'admin_upload') {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => '📤 Kitob yuklash (keyin qilinadi)'
            ]);
        }

        if ($data === 'admin_books') {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => '📚 Kitoblar ro‘yxati (keyin qilinadi)'
            ]);
        }
    }
}
