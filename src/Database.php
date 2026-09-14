<?php

class Database
{
    private PDO $pdo;

    public function __construct()
    {
        $dsn = "mysql:host=localhost;dbname=php_order_api;charset=utf8mb4";

        $this->pdo = new PDO(
            $dsn,
            "root",
            "",
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}