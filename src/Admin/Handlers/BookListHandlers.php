<?php
namespace Admin\Handlers;

use Services\BookService;
use Telegram\Bot\Keyboard\Keyboard;

class BookListHandler
{
    public static function show($context): void
    {
        $books = BookService::getAll();

        if (empty($books)) {
            $context->telegram->sendMessage([
                'chat_id' => $context->chatId,
                'text' => '📚 Hozircha kitoblar yo\'q'
            ]);
            return;
        }

        $text = "📚 *Kitoblar ro'yxati* (" . count($books) . " ta)\n\n";
        
        $keyboard = Keyboard::make()->inline();
        
        foreach ($books as $book) {
            $text .= "🆔 {$book['id']} | {$book['title']} - {$book['author']}\n";
            
            $keyboard->row([
                Keyboard::inlineButton([
                    'text' => "🗑 {$book['title']}",
                    'callback_data' => 'delete_book_' . $book['id']
                ])
            ]);
        }

        $context->telegram->sendMessage([
            'chat_id' => $context->chatId,
            'text' => $text,
            'parse_mode' => 'Markdown',
            'reply_markup' => $keyboard
        ]);
    }

    public static function delete($context, int $bookId): void
    {
        BookService::delete($bookId);
        
        $context->telegram->answerCallbackQuery([
            'callback_query_id' => $context->callbackQuery['id'],
            'text' => '✅ Kitob o\'chirildi',
            'show_alert' => true
        ]);

        // Ro'yxatni yangilash
        self::show($context);
    }
}