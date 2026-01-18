<?php

namespace Core;

use User\UserModel;
use Admin\AdminRouter;
use User\UserRouter;
use Admin\AdminStateModel;
use Admin\BookModel;
use Telegram\Bot\FileUpload\InputFile; // faylni import qilamiz
use User\UserStateModel;
use Telegram\Bot\Keyboard\Keyboard;

class Router
{
    private $telegram;
    private $config;

    public function __construct($telegram, $config)
    {
        $this->telegram = $telegram;
        $this->config   = $config;
    }

    public function handle($update)
    {
        /* =====================
           CALLBACK QUERY
        ====================== */
        if (isset($update['callback_query'])) {

            $callback = $update['callback_query'];
            $callbackData      = $callback['data'];
            $callbackFromId    = $callback['from']['id'];
            $callbackChatId    = $callback['message']['chat']['id'];
            $callbackMessageId = $callback['message']['message_id'];

            // ✅ To'lovni tasdiqlash
            if (str_starts_with($callbackData, 'approve|')) {

                if (!in_array($callbackFromId, $this->config['admins'])) return;

                [, $userTelegramId, $amount] = explode('|', $callbackData);

                UserModel::addBalance($userTelegramId, (int)$amount);

                $this->telegram->sendMessage([
                    'chat_id' => $userTelegramId,
                    'text' => "✅ To‘lov tasdiqlandi!\n💰 Balans +{$amount} UZS"
                ]);

                $this->telegram->editMessageText([
                    'chat_id' => $callbackChatId,
                    'message_id' => $callbackMessageId,
                    'text' => "✅ Tasdiqlandi\n👤 {$userTelegramId}\n💰 {$amount} UZS"
                ]);

                return;
            }

            // 🔄 Boshqa summa tugmasi
            if (str_starts_with($callbackData, 'retry|')) {

                if (!in_array($callbackFromId, $this->config['admins'])) return;

                [, $userTelegramId] = explode('|', $callbackData);

                // Admin summani kiritishi uchun holat
                AdminStateModel::set(
                    $callbackFromId,
                    'waiting_top_up_amount',
                    [
                        'user_id' => $userTelegramId
                    ]
                );

                $this->telegram->sendMessage([
                    'chat_id' => $callbackFromId,
                    'text' => "🔄 Iltimos, foydalanuvchi {$userTelegramId} uchun summani kiriting (UZS):"
                ]);

                $this->telegram->editMessageText([
                    'chat_id' => $callbackChatId,
                    'message_id' => $callbackMessageId,
                    'text' => "🔄 Admin summani kiritmoqda\n👤 {$userTelegramId}"
                ]);

                return;
            }


            // CALLBACK QUERY handle qismi
            if (str_starts_with($callbackData, 'download_')) {

                $bookId = (int) str_replace('download_', '', $callbackData);

                $book = BookModel::findById($bookId);
                if (!$book) return;

                // 💰 Balansni tekshirish
                $user = UserModel::find($callbackFromId);
                if ($user['balance'] < 5000) {
                    $this->telegram->answerCallbackQuery([
                        'callback_query_id' => $callback['id'],
                        'text' => "❌ Balansingiz yetarli emas!"
                    ]);
                    return;
                }

                // Balansni yechamiz
                UserModel::decreaseBalance($callbackChatId, 5000);

                UserModel::addBook($callbackFromId, $bookId);
                // Faylni yuboramiz
                $this->telegram->sendDocument([
                    'chat_id' => $callbackChatId,
                    'document' => InputFile::create($book['file_path'], basename($book['file_path'])),
                    'caption' => "📘 {$book['title']}\n💰 5000 UZS yechildi"
                ]);

                //  xabarni o'chiramiz
                $this->telegram->deleteMessage([
                    'chat_id' => $callbackChatId,
                    'message_id' => $callbackMessageId
                ]);


                $this->telegram->answerCallbackQuery([
                    'callback_query_id' => $callback['id'],
                    'text' => "🎉 Zo‘r! Kitobingiz tayyor ✅ 5000 UZS sizning balansingizdan yechildi. Hozir yuklab olishingiz mumkin! 📚"
                ]);


                return;
            }
        }

        /* =====================
           MESSAGE
        ====================== */
        if (!isset($update['message'])) return;

        $message = $update['message'];
        $chatId  = $message['chat']['id'];
        $text    = $message['text'] ?? '';

        /* =====================
           /start
        ====================== */
        if (str_starts_with($text, '/start')) {

            $parts = explode(' ', $text);
            $refCode = $parts[1] ?? null;

            $username = $message['from']['username'] ?? null;

            $user = UserModel::find($chatId);

            if (!$user) {

                $referrer = null;

                if ($refCode) {
                    $referrer = UserModel::findByReferralCode($refCode);
                }

                UserModel::createRef(
                    $chatId,
                    $username,
                    $referrer ? $referrer['telegram_id'] : null
                );

                // 🎁 REFERAL BONUS
                if ($referrer) {

                    UserModel::addBalance($referrer['telegram_id'], 1000);

                    $this->telegram->sendMessage([
                        'chat_id' => $referrer['telegram_id'],
                        'text' => "🎉 Siz referal orqali yangi foydalanuvchi qo‘shdingiz!\n💰 +1000 so‘m"
                    ]);
                }

                $user = UserModel::find($chatId);
            }

            // keyin admin/user start
            if (in_array($chatId, $this->config['admins'])) {
                AdminRouter::start($this->telegram, $chatId);
            } else {
                UserRouter::start($this->telegram, $chatId, $user);
            }

            return;
        }

        /* =====================
           ADMIN FLOW
        ====================== */
        if (in_array($chatId, $this->config['admins'])) {
            AdminRouter::handle($this->telegram, $chatId, $text, $message);
            return;
        }

        /* =====================
           DEFAULT USER FLOW
        ====================== */
        UserRouter::handle($this->telegram, $chatId, $text, $this->config);
    }
}
