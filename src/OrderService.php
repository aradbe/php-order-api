<?php

class OrderService
{
    public function __construct(private PDO $pdo)
    {
    }
    public function createOrder(int $productId, int $quantity): int
    {
        if ($productId <= 0 || $quantity <= 0) {
            throw new InvalidArgumentException(
                "Product ID and quantity must be positive"
            );
        }

        $this->pdo->beginTransaction();

        try {
            $statement = $this->pdo->prepare(
                "SELECT id, price, stock
         FROM products
         WHERE id = :product_id
         FOR UPDATE"
            );

            $statement->execute([
                "product_id" => $productId
            ]);

            $product = $statement->fetch();

            if (!$product) {
                throw new RuntimeException("Product not found");
            }

            if ((int) $product["stock"] < $quantity) {
                throw new RuntimeException("Not enough stock");
            }

            $totalPrice = (float) $product["price"] * $quantity;

            $orderStatement = $this->pdo->prepare(
                "INSERT INTO orders (total_price)
     VALUES (:total_price)"
            );

            $orderStatement->execute([
                "total_price" => $totalPrice
            ]);

            $orderId = (int) $this->pdo->lastInsertId();

            $itemStatement = $this->pdo->prepare(
                "INSERT INTO order_items
        (order_id, product_id, quantity, unit_price)
     VALUES
        (:order_id, :product_id, :quantity, :unit_price)"
            );

            $itemStatement->execute([
                "order_id" => $orderId,
                "product_id" => $productId,
                "quantity" => $quantity,
                "unit_price" => $product["price"]
            ]);

            $stockStatement = $this->pdo->prepare(
                "UPDATE products
     SET stock = stock - :quantity
     WHERE id = :product_id"
            );

            $stockStatement->execute([
                "quantity" => $quantity,
                "product_id" => $productId
            ]);

            $this->pdo->commit();

            return $orderId;
        } catch (Throwable $exception) {
            $this->pdo->rollBack();

            throw $exception;
        }
    }
}

