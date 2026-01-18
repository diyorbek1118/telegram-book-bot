<?php

namespace Admin;

use Core\Database;
use PDO;

class BookModel
{
    public static function create($title, $filePath)
    {
        $db = Database::connect();
        $stmt = $db->prepare(
            "INSERT INTO books (title, file_path) VALUES (?, ?)"
        );
        $stmt->execute([$title, $filePath]);
    }

    public static function findByTitle($title)
    {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM books WHERE title LIKE ? LIMIT 1");
        $stmt->execute(['%' . $title . '%']);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findById($id)
    {
        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT * FROM books WHERE id = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function count()
    {
        $db = Database::connect();
        return $db->query("SELECT COUNT(*) FROM books")->fetchColumn();
    }
}
