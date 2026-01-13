<?php

require __DIR__ . '/vendor/autoload.php';

require __DIR__ . '/src/helpers.php';
require __DIR__ . '/src/Core/Router.php';

use Telegram\Bot\Api;

$config = require __DIR__ . '/src/config.php';

$telegram = new Api($config['bot_token']);

$router = new Core\Router($telegram, $config);

$offset = 0;

echo "Bot running...\n";

while (true) {
    $updates = $telegram->getUpdates([
        'offset'  => $offset,
        'timeout' => 30,
    ]);

    foreach ($updates as $update) {
        $offset = $update->getUpdateId() + 1;
        $router->handle($update);
    }

    sleep(1);
}
