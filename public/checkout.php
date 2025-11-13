<?php
declare(strict_types=1);

$pageTitle = 'Checkout';
$pageDescription = 'Complete your WellaResin order with cash-on-delivery details.';

require_once __DIR__ . '/../includes/header.php';

$cartItems = get_cart_details();
$cartTotal = get_cart_total();

$errors = [];
$formData = [
    'name' => '',
    'address' => '',
    'phone' => '',
    'whatsapp' => '',
    'notes' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'name' => trim($_POST['name'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'whatsapp' => trim($_POST['whatsapp'] ?? ''),
        'notes' => trim($_POST['notes'] ?? ''),
    ];

    if ($formData['name'] === '') {
        $errors[] = 'Please enter your full name.';
    }

    if ($formData['address'] === '') {
        $errors[] = 'Please provide your full delivery address.';
    }

    if ($formData['whatsapp'] === '') {
        $errors[] = 'Please include a WhatsApp number so we can confirm your order.';
    }

    if (empty($cartItems)) {
        $errors[] = 'Your cart is empty.';
    }

    if (empty($errors)) {
        try {
            $orderId = create_order($formData);
            header('Location: ' . site_url('public/order_success.php?id=' . $orderId));
            exit;
        } catch (Throwable $e) {
            $errors[] = 'We could not place your order. Please try again or contact us on WhatsApp.';
        }
    }
}
?>

<section class="container">
    <h1 class="section-title">Checkout</h1>

    <?php if (!empty($cartItems)): ?>
        <div class="form-card">
            <?php foreach ($errors as $error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endforeach; ?>

            <h2>Order Summary</h2>
            <ul>
                <?php foreach ($cartItems as $item): ?>
                    <li><?= (int) $item['quantity'] ?> × <?= htmlspecialchars($item['product']['name']) ?> — <?= format_currency((float) $item['line_total']) ?></li>
                <?php endforeach; ?>
            </ul>
            <p><strong>Total:</strong> <?= format_currency((float) $cartTotal) ?></p>

            <form method="post">
                <div class="form-grid">
                    <div>
                        <label for="name">Full name *</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($formData['name']) ?>" required>
                    </div>
                    <div>
                        <label for="whatsapp">WhatsApp number *</label>
                        <input type="text" id="whatsapp" name="whatsapp" value="<?= htmlspecialchars($formData['whatsapp']) ?>" required>
                    </div>
                    <div>
                        <label for="phone">Phone number</label>
                        <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($formData['phone']) ?>">
                    </div>
                </div>

                <div>
                    <label for="address">Full address *</label>
                    <textarea id="address" name="address" required><?= htmlspecialchars($formData['address']) ?></textarea>
                </div>

                <div>
                    <label for="notes">Order notes (optional)</label>
                    <textarea id="notes" name="notes"><?= htmlspecialchars($formData['notes']) ?></textarea>
                </div>

                <p><strong>Payment method:</strong> Cash on delivery. We will contact you via WhatsApp to confirm the order.</p>

                <button class="btn-primary" type="submit">Place order</button>
            </form>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <p>Your cart is empty. Add items to proceed with checkout.</p>
            <a class="btn-primary" href="<?= site_url('public/products.php') ?>">Browse products</a>
        </div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

