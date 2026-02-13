<?php

require_once 'app/config/database.php';

class Product
{

    public static function all()
    {

        $db = Database::connect();

        return $db->query("SELECT * FROM products ORDER BY id DESC")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create($name, $description)
    {

        $db = Database::connect();

        $stmt = $db->prepare("
            INSERT INTO products (name, description)
            VALUES (?,?)
        ");

        $stmt->execute([$name, $description]);
    }
}
