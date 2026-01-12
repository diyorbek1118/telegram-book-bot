<?php

namespace Admin\Menus;

use Telegram\Bot\Keyboard\Keyboard;

class AdminMenu
{
    public static function main()
    {
        return Keyboard::make()
            ->inline()
            ->row([
                Keyboard::inlineButton([
                    'text' => '📤 Kitob yuklash',
                    'callback_data' => 'admin_upload'
                ])
            ])
            ->row([
                Keyboard::inlineButton([
                    'text' => '📚 Kitoblar ro‘yxati',
                    'callback_data' => 'admin_books'
                ])
            ]);
    }
}
