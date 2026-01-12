<?php
require 'vendor/autoload.php';
require 'src/config.php';
require 'src/helpers.php';

use Telegram\Bot\Api;
use Core\Router;

$telegram = new Api(BOT_TOKEN);
$router = new Router($telegram);

echo "Bot running...\n";

$offset = 0;

while (true) {
    try {
        $updates = $telegram->getUpdates(['offset' => $offset, 'timeout' => 30]);
        
        foreach ($updates as $update) {
            $router->handle($update);
            $offset = $update->getUpdateId() + 1;
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        sleep(5);
    }
}