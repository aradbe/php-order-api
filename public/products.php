<?php

require_once __DIR__ . "/../src/Database.php";

header("Content-Type: application/json");

$database = new Database();
$pdo = $database->getConnection();

$statement = $pdo->query(
    "SELECT id, name, price, stock
     FROM products
     ORDER BY id ASC"
);

$products = $statement->fetchAll();

echo json_encode($products);