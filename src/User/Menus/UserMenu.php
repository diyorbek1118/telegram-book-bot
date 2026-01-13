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
                Keyboard::button(['text' => '💰 Balans']),
                Keyboard::button(['text' => '💳 To\'lov'])
            ])
            ->row([
                Keyboard::button(['text' => '🔍 Qidirish'])
            ])
            ->row([
                Keyboard::button(['text' => '📚 Mening kitoblarim']),
                Keyboard::button(['text' => '⚙️ Sozlamalar'])
            ]);
    }
}