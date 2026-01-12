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
                Keyboard::button([
                    'text' => '💰 Balans'
                ]),
                 Keyboard::button([
                    'text' => '💳 To\'lov qilish'
                ])
                 ])
                   ->row([
                Keyboard::button([
                    'text' => '🔍 Kitob qidirish'
                ])
            ])
            ->row([
                Keyboard::button([
                    'text' => '📖  Qo\'llanma'
                ]),
                 Keyboard::button([
                    'text' => '✉️ Fikr bildirish'
                ])
                 ]);
    }
}