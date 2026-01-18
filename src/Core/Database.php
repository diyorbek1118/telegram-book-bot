<?php

namespace Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;

    public static function connect(): PDO
    {
        if (self::$pdo === null) {
            $config = require __DIR__ . '/../config.php';
            $db = $config['db'];

            // Socket orqali ulanish
            $dsn = "mysql:unix_socket={$db['socket']};dbname={$db['dbname']};charset={$db['charset']}";

            try {
                self::$pdo = new PDO($dsn, $db['user'], $db['pass'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
            } catch (PDOException $e) {
                die('DB error: ' . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}
