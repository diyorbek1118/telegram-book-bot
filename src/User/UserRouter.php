<?php

namespace User;

require_once __DIR__ . '/Menus/UserMenu.php';

class UserRouter
{
    public static function handleMessage($telegram, $message): void
    {
        $chatId = $message->getChat()->getId();
        $text   = $message->getText() ?? '';

        if ($text === '/start') {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "📚 Elektron kitoblar botiga xush kelibsiz",
                'reply_markup' => Menus\UserMenu::main()
            ]);
        }
    }

    public static function handleCallback($telegram, $callback): void
    {
        $chatId = $callback->getMessage()->getChat()->getId();
        $data   = $callback->getData();

        if ($data === 'user_balance') {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => '💰 Balans: 0 so‘m'
            ]);
        }

        if ($data === 'user_search') {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => '🔍 Qidiruv (keyin qilinadi)'
            ]);
        }

        if ($data === 'user_payment') {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => '💳 To‘lov (keyin qilinadi)'
            ]);
        }
    }
}
