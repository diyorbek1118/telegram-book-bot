<?php
namespace User\Menus;

use Telegram\Bot\Keyboard\Keyboard;

class UserMenu
{
    public static function main()
    {
        return Keyboard::make()
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(false)
            ->row([
                Keyboard::button(['text' => '💰 Balansni ko‘rish']),
                Keyboard::button(['text' => '💳 To‘lov'])
            ])
            ->row([
                Keyboard::button(['text' => '🔍 Qidirish'])
            ])
            ->row([
                Keyboard::button(['text' => '📚 Mening kitoblarim']),
                Keyboard::button(['text' => '👥 Referal'])
            ]);
    }
}
