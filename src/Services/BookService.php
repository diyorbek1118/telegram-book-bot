<?php
namespace Services;

use Core\Database;

class BookService
{
    public static function create(string $title, string $author, string $fileId): int
    {
        $sql = "INSERT INTO books (title, author, file_id) VALUES (:title, :author, :file_id)";
        Database::query($sql, [
            'title' => $title,
            'author' => $author,
            'file_id' => $fileId
        ]);
        return Database::connect()->lastInsertId();
    }

    public static function getAll(): array
    {
        $sql = "SELECT * FROM books ORDER BY id DESC";
        return Database::query($sql)->fetchAll();
    }

    public static function getById(int $id): ?array
    {
        $sql = "SELECT * FROM books WHERE id = :id";
        $result = Database::query($sql, ['id' => $id])->fetch();
        return $result ?: null;
    }

    public static function search(string $query): array
    {
        $sql = "SELECT * FROM books WHERE title LIKE :query OR author LIKE :query ORDER BY id DESC LIMIT 10";
        return Database::query($sql, ['query' => "%$query%"])->fetchAll();
    }

    public static function delete(int $id): bool
    {
        $sql = "DELETE FROM books WHERE id = :id";
        Database::query($sql, ['id' => $id]);
        return true;
    }
}