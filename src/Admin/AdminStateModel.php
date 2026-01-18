<?php

namespace Admin;

use Core\Database;

class AdminStateModel
{
    public static function get($telegramId)
    {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM admin_states WHERE telegram_id=?");
        $stmt->execute([$telegramId]);
        $row = $stmt->fetch();
        if (!$row) return null;

        // Agar temp_data mavjud bo'lsa, decode qilamiz
        if ($row['temp_data']) {
            $decoded = json_decode($row['temp_data'], true);
            $row['temp_data'] = $decoded ?: $row['temp_data'];
        }

        return $row;
    }

    public static function set($telegramId, $step, $tempData = null)
    {
        $db = Database::connect();

        // Agar tempData array bo'lsa, json_encode qilamiz
        if (is_array($tempData)) {
            $tempData = json_encode($tempData);
        }

        $stmt = $db->prepare("
            REPLACE INTO admin_states (telegram_id, step, temp_data)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$telegramId, $step, $tempData]);
    }

    public static function clear($telegramId)
    {
        $db = Database::connect();
        $stmt = $db->prepare("DELETE FROM admin_states WHERE telegram_id=?");
        $stmt->execute([$telegramId]);
    }
}
