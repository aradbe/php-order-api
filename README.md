# PHP Order API

A small vanilla PHP and MySQL API for creating orders and managing product stock.

I built this project to practise backend fundamentals after learning PHP:
OOP, PDO, prepared statements, validation, SQL relationships, JSON APIs,
exceptions, and database transactions.

## Features

- List available products
- Create an order for a product
- Validate product IDs and quantities
- Reject orders with insufficient stock
- Create the order and reduce stock in one transaction
- Retrieve an order with its product information
- Return JSON responses and appropriate HTTP status codes
- Use a small responsive browser interface to view products and place orders

## Endpoints

### List products

`GET /products.php`

### Browser interface

`GET /`

### Create an order

`POST /create-order.php`

Example JSON body:

```json
{
    "product_id": 2,
    "quantity": 3
}

```

### Get an order

`GET /order.php?id=2`

## Database

The project uses three related tables:

- `products`
- `orders`
- `order_items`

`order_items` connects an order to its products using foreign keys.

## Transaction Flow

1. Validate the input.
2. Begin a transaction.
3. Select and lock the product row.
4. Check available stock.
5. Create the order.
6. Create the order item.
7. Reduce product stock.
8. Commit all changes.

If any step fails, the transaction is rolled back.

## Running Locally

1. Start MySQL using XAMPP.
2. Import `database/schema.sql`.
3. Start the PHP server:

```powershell
C:\xampp\php\php.exe -S 127.0.0.1:8080 -t public
```

4. Open `http://127.0.0.1:8080/products.php`.

## Possible Improvements

- Support multiple products in one request
- Move database credentials into environment variables
- Add authentication
- Add automated tests
- Avoid returning internal exception details in production