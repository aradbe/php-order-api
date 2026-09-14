<?php

require_once __DIR__ . "/../src/Database.php";

header("Content-Type: application/json");

$orderId = (int) ($_GET["id"] ?? 0);

if ($orderId <= 0) {
    http_response_code(400);

    echo json_encode([
        "error" => "A positive order ID is required"
    ]);

    exit;
}

$database = new Database();
$pdo = $database->getConnection();

$statement = $pdo->prepare(
    "SELECT
        o.id,
        o.total_price,
        o.status,
        o.created_at,
        p.name AS product_name,
        oi.quantity,
        oi.unit_price
     FROM orders AS o
     INNER JOIN order_items AS oi
        ON oi.order_id = o.id
     INNER JOIN products AS p
        ON p.id = oi.product_id
     WHERE o.id = :order_id"
);

$statement->execute([
    "order_id" => $orderId
]);

$order = $statement->fetch();

if (!$order) {
    http_response_code(404);

    echo json_encode([
        "error" => "Order not found"
    ]);

    exit;
}

echo json_encode($order);