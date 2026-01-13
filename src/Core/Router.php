<?php

namespace Core;

use Telegram\Bot\Api;

require_once __DIR__ . '/../Admin/AdminRouter.php';
require_once __DIR__ . '/../User/UserRouter.php';

class Router
{
    private Api $telegram;
    private array $config;

    public function __construct(Api $telegram, array $config)
    {
        $this->telegram = $telegram;
        $this->config   = $config;
    }

    public function handle($update): void
    {
        $message  = $update->getMessage();
        $callback = $update->getCallbackQuery();

        if ($message) {
            $chatId = $message->getChat()->getId();

            if (isAdmin($chatId, $this->config['admins'])) {
                \Admin\AdminRouter::handleMessage($this->telegram, $message);
            } else {
                \User\UserRouter::handleMessage($this->telegram, $message);
            }
        }

        if ($callback) {
            $chatId = $callback->getMessage()->getChat()->getId();

            if (isAdmin($chatId, $this->config['admins'])) {
                \Admin\AdminRouter::handleCallback($this->telegram, $callback);
            } else {
                \User\UserRouter::handleCallback($this->telegram, $callback);
            }
        }
    }
}
