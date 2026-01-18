<?php

namespace User;

use Core\Database;

class UserStateModel
{
    public static function set($chatId, $step, $temp = null)
    {
        $db = Database::connect();
        $stmt = $db->prepare("
            REPLACE INTO user_states (chat_id, step, temp_data)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$chatId, $step, $temp]);
    }

    public static function get($chatId)
    {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM user_states WHERE chat_id = ?");
        $stmt->execute([$chatId]);
        return $stmt->fetch();
    }

    public static function clear($chatId)
    {
        $db = Database::connect();
        $stmt = $db->prepare("DELETE FROM user_states WHERE chat_id = ?");
        $stmt->execute([$chatId]);
    }
}
