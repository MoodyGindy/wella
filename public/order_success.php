<?php
declare(strict_types=1);

$pageTitle = 'Order Received';
$pageDescription = 'Thank you for your order from WellaResin.';

require_once __DIR__ . '/../includes/header.php';

$orderId = isset($_GET['id']) ? (int) $_GET['id'] : null;
?>

<section class="container">
    <div class="empty-state">
        <h1>Thank you!</h1>
        <?php if ($orderId): ?>
            <p>Your order #<?= $orderId ?> has been received. We will message you on WhatsApp soon to confirm delivery details.</p>
        <?php else: ?>
            <p>Your order has been received. We will message you on WhatsApp soon to confirm delivery details.</p>
        <?php endif; ?>
        <a class="btn-primary" href="<?= site_url('public/index.php') ?>">Back to home</a>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

