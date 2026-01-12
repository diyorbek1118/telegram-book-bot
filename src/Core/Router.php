<?php
namespace Core;

use Telegram\Bot\Api;
use Admin\AdminRouter;
use User\UserRouter;

class Router
{
    private Api $telegram;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    public function handle($update): void
    {
        // Context yaratish
        $context = new Context($update, $this->telegram);

        // Message handler
        if (isset($update['message'])) {
            if (isAdmin($context->userId)) {
                AdminRouter::handleMessage($context);
            } else {
                UserRouter::handleMessage($context);
            }
        }

        // Callback query handler
        if (isset($update['callback_query'])) {
            if (isAdmin($context->userId)) {
                AdminRouter::handleCallback($context);
            } else {
                UserRouter::handleCallback($context);
            }
        }
    }
}