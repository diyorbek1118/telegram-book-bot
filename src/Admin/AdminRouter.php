<?php

namespace Admin;

use User\UserModel;
use Admin\AdminStateModel;
use Admin\Menus\AdminMenu;

class AdminRouter
{
    /**
     * /start bosilganda admin panel
     */
    public static function start($telegram, $chatId)
    {
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => "👮 Admin panelga xush kelibsiz",
            'reply_markup' => AdminMenu::main()
        ]);
    }

    /**
     * Admin message handler
     */
    public static function handle($telegram, $chatId, $text = null, $message = null)
    {
        $state = AdminStateModel::get($chatId);

        /* ===============================
           ADMIN BALANCE TOP-UP FLOW
        =============================== */
        if ($state && $state['step'] === 'waiting_top_up_amount') {

            // Kiritilgan summa tekshiruvi
            if (!is_numeric($text) || (int)$text <= 0) {
                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "❌ Iltimos, faqat musbat raqam kiriting.\nMasalan: 50000"
                ]);
                return;
            }

            $amount = (int)$text;

            // user_id temp_data ichidan olinadi
            $userTelegramId = $state['temp_data']['user_id'] ?? null;

            if (!$userTelegramId) {
                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "❌ Xatolik: foydalanuvchi aniqlanmadi. Qaytadan urinib ko‘ring."
                ]);
                AdminStateModel::clear($chatId);
                return;
            }

            // ✅ Balans qo‘shish
            UserModel::addBalance($userTelegramId, $amount);

            // 👤 Userga xabar
            $telegram->sendMessage([
                'chat_id' => $userTelegramId,
                'text' => "✅ Balansingiz oshirildi\n💰 +{$amount} UZS"
            ]);

            // 👮 Adminga tasdiq
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "✅ Balans muvaffaqiyatli qo‘shildi\n👤 User: {$userTelegramId}\n💰 Summa: {$amount} UZS",
                'reply_markup' => AdminMenu::main()
            ]);

            // 🧹 State tozalash
            AdminStateModel::clear($chatId);

            // MUHIM: default menu qayta chiqmasligi uchun
            return;
        }

        /* ===============================
           KEYINCHALIK BOSHQA ADMIN FLOWLAR
        =============================== */
        if ($state && $state['step'] === 'waiting_title' && $text) {
            // Bu yerga keyin kitob upload logikasi yoziladi
            return;
        }

        /* ===============================
           DEFAULT ADMIN PANEL
        =============================== */
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => "👮 Admin panelga xush kelibsiz",
            'reply_markup' => AdminMenu::main()
        ]);
    }
}
