<?php
declare(strict_types=1);

$pageTitle = 'Your Cart';
$pageDescription = 'Review the items in your WellaResin cart.';

require_once __DIR__ . '/../includes/header.php';

$cartItems = get_cart_details();
$cartTotal = get_cart_total();
?>

<section class="container">
    <h1 class="section-title">Shopping Cart</h1>

    <?php if (!empty($cartItems)): ?>
        <div class="cart-wrapper">
            <table class="cart-table">
                <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($cartItems as $item): ?>
                    <?php $product = $item['product']; ?>
                    <tr>
                        <td>
                            <div>
                                <strong><?= htmlspecialchars($product['name']) ?></strong>
                            </div>
                        </td>
                        <td>
                            <form data-role="update-cart" data-product-id="<?= (int) $product['id'] ?>">
                                <input type="number" name="quantity" min="1" value="<?= (int) $item['quantity'] ?>">
                                <button class="btn-secondary" type="submit">Update</button>
                            </form>
                        </td>
                        <td><?= format_currency((float) $product['price']) ?></td>
                        <td><?= format_currency((float) $item['line_total']) ?></td>
                        <td>
                            <button class="btn-secondary" data-action="remove-from-cart" data-product-id="<?= (int) $product['id'] ?>">Remove</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-total">
                <span>Total:</span>
                <strong><?= format_currency((float) $cartTotal) ?></strong>
                <a class="btn-primary" href="<?= site_url('public/checkout.php') ?>">Proceed to checkout</a>
            </div>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <p>Your cart is currently empty.</p>
            <a class="btn-primary" href="<?= site_url('public/products.php') ?>">Continue shopping</a>
        </div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

