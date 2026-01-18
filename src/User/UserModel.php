<?php

namespace User;

use Core\Database;

class UserModel
{
    /**
     * Foydalanuvchini telegram_id bo'yicha topish
     */
    public static function find(int $telegramId): ?array
    {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM users WHERE telegram_id = ? LIMIT 1");
        $stmt->execute([$telegramId]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    /**
     * Foydalanuvchini yaratish
     */
    public static function create(int $telegramId, ?string $username = null): bool
    {
        $db = Database::connect();
        $stmt = $db->prepare("INSERT INTO users (telegram_id, username, balance) VALUES (?, ?, 0)");
        return $stmt->execute([$telegramId, $username]);
    }

    /**
     * Balansni oshirish
     */
    public static function addBalance(int $telegramId, int $amount): bool
    {
        $db = Database::connect();
        $stmt = $db->prepare("
            UPDATE users 
            SET balance = balance + :amount 
            WHERE telegram_id = :telegram_id
        ");
        return $stmt->execute([
            ':amount' => $amount,
            ':telegram_id' => $telegramId
        ]);
    }

    /**
     * Balansni kamaytirish
     */
    public static function decreaseBalance(int $telegramId, int $amount): bool
    {
        $db = Database::connect();
        $stmt = $db->prepare("
            UPDATE users 
            SET balance = balance - :amount 
            WHERE telegram_id = :telegram_id
        ");
        return $stmt->execute([
            ':amount' => $amount,
            ':telegram_id' => $telegramId
        ]);
    }

    /**
     * Foydalanuvchining joriy balansini olish
     */
    public static function getBalance(int $telegramId): int
    {
        $user = self::find($telegramId);
        return $user['balance'] ?? 0;
    }

    public static function generateReferralCode($telegramId)
{
    $code = 'ref_' . $telegramId;

    $db = Database::connect();
    $stmt = $db->prepare("UPDATE users SET referral_code=? WHERE telegram_id=?");
    $stmt->execute([$code, $telegramId]);

    return $code;
}

public static function findByReferralCode($code)
{
    $db = Database::connect();
    $stmt = $db->prepare("SELECT * FROM users WHERE referral_code=?");
    $stmt->execute([$code]);
    return $stmt->fetch();
}

public static function createRef($telegramId, $username = null, $referredBy = null)
{
    $db = Database::connect();
    $stmt = $db->prepare("
        INSERT INTO users (telegram_id, username, referred_by)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$telegramId, $username, $referredBy]);
}

public static function addBook($userId, $bookId)
{
    $db = Database::connect();
    $stmt = $db->prepare("INSERT INTO user_books (user_id, book_id) VALUES (?, ?)");
    $stmt->execute([$userId, $bookId]);
}

public static function getBooks($userId)
{
    $db = Database::connect();
    $stmt = $db->prepare("
        SELECT b.id, b.title 
        FROM user_books ub
        JOIN books b ON ub.book_id = b.id
        WHERE ub.user_id = ?
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}


}
