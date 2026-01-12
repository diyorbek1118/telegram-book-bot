<?php
namespace Core;

class Database
{
    private static ?\PDO $connection = null;

    public static function connect(): \PDO
    {
        if (self::$connection === null) {
            $dbPath = __DIR__ . '/../../database/bot.db';
            $dbDir = dirname($dbPath);
            
            if (!is_dir($dbDir)) {
                mkdir($dbDir, 0755, true);
            }

            self::$connection = new \PDO('sqlite:' . $dbPath);
            self::$connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            self::$connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            
            self::initializeTables();
        }

        return self::$connection;
    }

    private static function initializeTables(): void
    {
        $schemaPath = __DIR__ . '/../../database/schema.sql';
        if (file_exists($schemaPath)) {
            $schema = file_get_contents($schemaPath);
            self::$connection->exec($schema);
        }
    }

    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::connect()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}