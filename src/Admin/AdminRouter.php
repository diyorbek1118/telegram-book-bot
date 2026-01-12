<?php
namespace Admin;

use Admin\Menus\AdminMenu;
use Admin\Handlers\BookUploadHandler;
use Admin\Handlers\BookListHandler;
use Admin\States\AdminState;

class AdminRouter
{
    public static function handleMessage($context): void
    {
        $text = $context->message['text'] ?? '';
        
        // Agar admin state'da bo'lsa (kitob yuklayotgan bo'lsa)
        $state = AdminState::get($context->chatId);
        if ($state) {
            BookUploadHandler::handleState($context);
            return;
        }

        // Oddiy komandalar
        if ($text === '/admin' || $text === '/start') {
            $context->telegram->sendMessage([
                'chat_id' => $context->chatId,
                'text' => '👑 Admin panel',
                'reply_markup' => AdminMenu::main()
            ]);
        }
    }

    public static function handleCallback($context): void
    {
        $data = $context->callbackQuery['data'] ?? '';

        switch ($data) {
            case 'admin_upload':
                BookUploadHandler::start($context);
                break;

            case 'admin_books':
                BookListHandler::show($context);
                break;

            default:
                // Kitob o'chirish: delete_book_123
                if (strpos($data, 'delete_book_') === 0) {
                    $bookId = (int)str_replace('delete_book_', '', $data);
                    BookListHandler::delete($context, $bookId);
                }
                break;
        }

        $context->telegram->answerCallbackQuery([
            'callback_query_id' => $context->callbackQuery['id']
        ]);
    }
}