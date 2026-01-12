<?php
namespace Admin\Handlers;

use Services\BookService;
use Admin\States\AdminState;

class BookUploadHandler
{
    public static function start($context): void
    {
        AdminState::set($context->chatId, 'awaiting_book_title');
        
        $context->telegram->sendMessage([
            'chat_id' => $context->chatId,
            'text' => '📖 Kitob nomini kiriting:'
        ]);
    }

    public static function handleState($context): void
    {
        $state = AdminState::get($context->chatId);
        if (!$state) return;

        $text = $context->message['text'] ?? null;
        $document = $context->message['document'] ?? null;

        switch ($state['state']) {
            case 'awaiting_book_title':
                AdminState::set($context->chatId, 'awaiting_book_author', ['title' => $text]);
                $context->telegram->sendMessage([
                    'chat_id' => $context->chatId,
                    'text' => '✍️ Muallif nomini kiriting:'
                ]);
                break;

            case 'awaiting_book_author':
                $data = $state['data'];
                $data['author'] = $text;
                AdminState::set($context->chatId, 'awaiting_book_file', $data);
                $context->telegram->sendMessage([
                    'chat_id' => $context->chatId,
                    'text' => '📎 Kitob faylini yuboring:'
                ]);
                break;

            case 'awaiting_book_file':
                if (!$document) {
                    $context->telegram->sendMessage([
                        'chat_id' => $context->chatId,
                        'text' => '❌ Iltimos, fayl yuboring!'
                    ]);
                    return;
                }

                $data = $state['data'];
                $bookId = BookService::create(
                    $data['title'],
                    $data['author'],
                    $document['file_id']
                );
                
                AdminState::clear($context->chatId);

                $context->telegram->sendMessage([
                    'chat_id' => $context->chatId,
                    'text' => "✅ Kitob qo'shildi!\n\n" .
                              "📖 {$data['title']}\n" .
                              "✍️ {$data['author']}\n" .
                              "🆔 ID: $bookId"
                ]);
                break;
        }
    }
}