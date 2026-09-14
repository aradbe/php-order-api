<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Order API</title>
    <style>
        :root {
            color-scheme: dark;
            --background: #0f1720;
            --surface: #17212b;
            --surface-light: #202d38;
            --text: #e6edf3;
            --muted: #91a4b7;
            --accent: #79c7b5;
            --accent-dark: #163f3a;
            --danger: #ff9b9b;
            --border: #2b3b49;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background:
                radial-gradient(circle at top right, #193641 0, transparent 34rem),
                var(--background);
            color: var(--text);
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        main {
            width: min(960px, calc(100% - 32px));
            margin: 0 auto;
            padding: 64px 0;
        }

        header {
            margin-bottom: 36px;
        }

        .eyebrow {
            color: var(--accent);
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }

        h1 {
            margin: 10px 0 8px;
            font-size: clamp(2rem, 6vw, 3.7rem);
            letter-spacing: -0.04em;
        }

        header p,
        .muted {
            color: var(--muted);
        }

        .layout {
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) minmax(280px, 0.65fr);
            gap: 24px;
            align-items: start;
        }

        .panel {
            background: color-mix(in srgb, var(--surface) 94%, transparent);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 22px 60px rgba(0, 0, 0, 0.18);
        }

        h2 {
            margin: 0 0 18px;
            font-size: 1.15rem;
        }

        .products {
            display: grid;
            gap: 12px;
        }

        .product {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: center;
            padding: 16px;
            background: var(--surface-light);
            border-radius: 12px;
        }

        .product strong {
            display: block;
            margin-bottom: 4px;
        }

        .price {
            color: var(--accent);
            font-size: 1.05rem;
            font-weight: 700;
            white-space: nowrap;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--muted);
            font-size: 0.9rem;
        }

        select,
        input,
        button {
            width: 100%;
            min-height: 46px;
            border-radius: 10px;
            font: inherit;
        }

        select,
        input {
            margin-bottom: 18px;
            padding: 0 12px;
            border: 1px solid var(--border);
            background: var(--surface-light);
            color: var(--text);
        }

        button {
            border: 0;
            background: var(--accent);
            color: #0d2925;
            cursor: pointer;
            font-weight: 800;
        }

        button:hover {
            filter: brightness(1.08);
        }

        button:disabled {
            cursor: wait;
            opacity: 0.6;
        }

        .result {
            display: none;
            margin-top: 18px;
            padding: 14px;
            border-radius: 10px;
            background: var(--accent-dark);
            color: var(--accent);
        }

        .result.error {
            display: block;
            background: #3a2025;
            color: var(--danger);
        }

        .result.success {
            display: block;
        }

        @media (max-width: 720px) {
            main {
                padding: 36px 0;
            }

            .layout {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <main>
        <header>
            <div class="eyebrow">Vanilla PHP + MySQL</div>
            <h1>Order Inventory</h1>
            <p>A tiny storefront powered by a transaction-safe PHP API.</p>
        </header>

        <div class="layout">
            <section class="panel">
                <h2>Products</h2>
                <div id="products" class="products">
                    <p class="muted">Loading products…</p>
                </div>
            </section>

            <section class="panel">
                <h2>Create an order</h2>
                <form id="order-form">
                    <label for="product">Product</label>
                    <select id="product" required></select>

                    <label for="quantity">Quantity</label>
                    <input id="quantity" type="number" min="1" value="1" required>

                    <button type="submit">Place order</button>
                </form>
                <div id="result" class="result" aria-live="polite"></div>
            </section>
        </div>
    </main>

    <script>
        const productList = document.querySelector("#products");
        const productSelect = document.querySelector("#product");
        const orderForm = document.querySelector("#order-form");
        const quantityInput = document.querySelector("#quantity");
        const result = document.querySelector("#result");
        const submitButton = orderForm.querySelector("button");

        async function loadProducts() {
            const response = await fetch("products.php");
            const products = await response.json();

            productList.innerHTML = products.map(product => `
                <article class="product">
                    <div>
                        <strong>${escapeHtml(product.name)}</strong>
                        <span class="muted">${product.stock} in stock</span>
                    </div>
                    <span class="price">$${Number(product.price).toFixed(2)}</span>
                </article>
            `).join("");

            productSelect.innerHTML = products.map(product => `
                <option value="${product.id}" ${product.stock === 0 ? "disabled" : ""}>
                    ${escapeHtml(product.name)} — ${product.stock} available
                </option>
            `).join("");
        }

        function escapeHtml(value) {
            const element = document.createElement("div");
            element.textContent = value;
            return element.innerHTML;
        }

        orderForm.addEventListener("submit", async event => {
            event.preventDefault();
            submitButton.disabled = true;
            result.className = "result";
            result.textContent = "";

            try {
                const response = await fetch("create-order.php", {
                    method: "POST",
                    headers: {"Content-Type": "application/json"},
                    body: JSON.stringify({
                        product_id: Number(productSelect.value),
                        quantity: Number(quantityInput.value)
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || "Could not create order");
                }

                result.className = "result success";
                result.innerHTML = `Order <strong>#${data.order_id}</strong> created successfully.`;
                quantityInput.value = 1;
                await loadProducts();
            } catch (error) {
                result.className = "result error";
                result.textContent = error.message;
            } finally {
                submitButton.disabled = false;
            }
        });

        loadProducts().catch(() => {
            productList.innerHTML = '<p class="muted">Could not load products.</p>';
        });
    </script>
</body>
</html>
