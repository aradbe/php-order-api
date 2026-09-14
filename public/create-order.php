<?php

require_once __DIR__ . "/../src/Database.php";
require_once __DIR__ . "/../src/OrderService.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);

    echo json_encode([
        "error" => "Method not allowed"
    ]);

    exit;
}

$input = json_decode(
    file_get_contents("php://input"),
    true
);

$productId = (int) ($input["product_id"] ?? 0);
$quantity = (int) ($input["quantity"] ?? 0);

try {
    $database = new Database();

    $service = new OrderService(
        $database->getConnection()
    );

    $orderId = $service->createOrder(
        $productId,
        $quantity
    );

    http_response_code(201);

    echo json_encode([
        "success" => true,
        "order_id" => $orderId
    ]);
} catch (Throwable $exception) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "error" => $exception->getMessage()
    ]);
}