<?php

namespace User;

use User\Menus\UserMenu;
use User\UserModel;
use User\UserStateModel;
use Admin\BookModel;
use Core\Router;
use Telegram\Bot\Keyboard\Keyboard;

class UserRouter
{
    public static function start($telegram, $chatId, $user)
    {
        $balance = number_format($user['balance'], 2);

        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => "📚 Elektron kitoblar botiga xush kelibsiz!\n\n💰 Balans: {$balance} UZS",
            'reply_markup' => UserMenu::main()
        ]);
    }

    public static function handle($telegram, $chatId, $text, $config)
    {
        $state = UserStateModel::get($chatId);

        // 🔍 Qidirish bosildi
        if ($text === '🔍 Qidirish') {

            UserStateModel::set($chatId, 'waiting_book_name');

            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "📖 Kitob nomini kiriting:"
            ]);
            return;
        }

        // ✏️ Kitob nomi kiritildi
        if ($state && $state['step'] === 'waiting_book_name') {

            $book = BookModel::findByTitle($text);

            if (!$book) {
                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "❌ Bunday kitob topilmadi",
                    'reply_markup' => UserMenu::main()
                ]);
                UserStateModel::clear($chatId);
                return;
            }

            UserStateModel::clear($chatId);

            $keyboard = Keyboard::make()
                ->inline()
                ->row([
                    Keyboard::inlineButton([
                        'text' => '⬇️ Yuklab olish (5000 UZS)',
                        'callback_data' => 'download_' . $book['id']
                    ])
                ]);

            $telegram->sendMessage([
                'chat_id' => $chatId,
                'parse_mode' => 'HTML',
                'text' => "🎉 <b>Topildi!</b>\n\n" .
                    "📘 <b>Kitob nomi:</b> {$book['title']}\n" .
                    "💰 Narxi: 5000 UZS\n\n" .
                    "⬇️ Quyidagi tugmani bosib, kitobni yuklab olishingiz mumkin.",
                'reply_markup' => $keyboard
            ]);

            return;
        }
            // Mening kitoblarim
        if ($text === '📚 Mening kitoblarim') {
            $books = UserModel::getBooks($chatId);

            if (empty($books)) {
                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "📚 Siz hali biror kitob sotib olmadingiz",
                    'reply_markup' => UserMenu::main()
                ]);
                return;
            }

            foreach ($books as $book) {
                $keyboard = Keyboard::make()
                    ->inline()
                    ->row([
                        Keyboard::inlineButton([
                            'text' => '⬇️ Yuklab olish',
                            'callback_data' => 'download_' . $book['id']
                        ])
                    ]);

                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "📘 {$book['title']}",
                    'reply_markup' => $keyboard
                ]);
            }
        }

        // 💰 Balans
        if ($text === '💰 Balansni ko‘rish') {
            $user = UserModel::find($chatId);
            $balance = number_format($user['balance'], 2);

            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "💰 Balans: {$balance} UZS",
                'reply_markup' => UserMenu::main()
            ]);
            return;
        }

        if ($text === '👥 Referal') {

            // user ma'lumotini qayta olamiz (yangilangan bo‘lishi uchun)
            $user = UserModel::find($chatId);

            if (!$user['referral_code']) {
                $code = UserModel::generateReferralCode($chatId);
            } else {
                $code = $user['referral_code'];
            }

            $link = "https://t.me/{$config['bot_username']}?start={$code}";

            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' =>
                "👥 Sizning referal linkingiz:\n\n" .
                    $link . "\n\n" .
                    "🎁 Har bir taklif qilingan foydalanuvchi uchun +1000 so‘m balansingizga qo‘shiladi."
            ]);


            return;
        }

        if ($text === '💳 To‘lov') {
            // 1️⃣ Step: foydalanuvchi summani kiritsin
            UserStateModel::set($chatId, 'waiting_top_up_amount');

            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "💳 To‘lov summasini kiriting (UZS):"
            ]);
            return;
        }

        if ($state && $state['step'] === 'waiting_top_up_amount') {

            if (!is_numeric($text) || $text <= 0) {
                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "❌ Faqat raqam kiriting"
                ]);
                return;
            }

            $amount = (int)$text;
            UserStateModel::clear($chatId);

            // 👤 USER GA XABAR
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'parse_mode' => 'HTML',
                'text' =>
                "💳 <b>To‘lov maʼlumotlari</b>\n\n" .
                    "💰 Summa: {$amount} UZS\n" .
                    "🏦 Karta: <code>8600 12** **** 3456</code>\n" .
                    "👤 Egasi: HHDSoft\n\n" .
                    "⏳ To‘lovingiz admin tomonidan tekshiriladi"
            ]);

            $keyboard = Keyboard::make()
                ->inline()
                ->row([
                    Keyboard::inlineButton([
                        'text' => '✅ Tasdiqlash',
                        'callback_data' => "approve|{$chatId}|{$amount}"
                    ]),
                    Keyboard::inlineButton([
                        'text' => '🔄 Boshqa summa',
                        'callback_data' => "retry|{$chatId}"
                    ])
                ]);


            foreach ($config['admins'] as $adminId) {
                $telegram->sendMessage([
                    'chat_id' => $adminId,
                    'parse_mode' => 'HTML',
                    'text' =>
                    "💳 <b>Yangi to‘lov</b>\n\n" .
                        "👤 Telegram ID: <code>{$chatId}</code>\n" .
                        "💰 Summa: {$amount} UZS",
                    'reply_markup' => $keyboard
                ]);
            }

            return;
        }
    }
}
