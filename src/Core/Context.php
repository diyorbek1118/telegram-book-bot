<?php
namespace Core;

class Context
{
    public $telegram;
    public $chatId;
    public $userId;
    public $message;
    public $callbackQuery;

    public function __construct($update, $telegram)
    {
        $this->telegram = $telegram;

        if (isset($update['message'])) {
            $this->message = $update['message'];
            $this->chatId = $update['message']['chat']['id'];
            $this->userId = $update['message']['from']['id'];
        }

        if (isset($update['callback_query'])) {
            $this->callbackQuery = $update['callback_query'];
            $this->chatId = $update['callback_query']['message']['chat']['id'];
            $this->userId = $update['callback_query']['from']['id'];
        }
    }
}