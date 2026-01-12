<?php
namespace Admin\States;

use Core\Database;

class AdminState
{
    public static function set(int $telegramId, string $state, array $data = []): void
    {
        $sql = "INSERT OR REPLACE INTO admin_states (telegram_id, state, data) 
                VALUES (:telegram_id, :state, :data)";
        
        Database::query($sql, [
            'telegram_id' => $telegramId,
            'state' => $state,
            'data' => json_encode($data)
        ]);
    }

    public static function get(int $telegramId): ?array
    {
        $sql = "SELECT * FROM admin_states WHERE telegram_id = :telegram_id";
        $result = Database::query($sql, ['telegram_id' => $telegramId])->fetch();
        
        if ($result) {
            $result['data'] = json_decode($result['data'], true) ?? [];
            return $result;
        }
        
        return null;
    }

    public static function clear(int $telegramId): void
    {
        $sql = "DELETE FROM admin_states WHERE telegram_id = :telegram_id";
        Database::query($sql, ['telegram_id' => $telegramId]);
    }
}