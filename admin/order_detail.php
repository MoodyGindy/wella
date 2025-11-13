<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

require_admin_auth();

$pdo = get_db_connection();
$orderId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($orderId <= 0) {
    header('Location: ' . site_url('admin/orders.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'] ?? 'pending';
    $allowedStatuses = ['pending', 'confirmed', 'in-progress', 'shipped', 'delivered', 'cancelled'];

    if (in_array($status, $allowedStatuses, true)) {
        $stmt = $pdo->prepare('UPDATE orders SET status = :status WHERE id = :id');
        $stmt->execute([':status' => $status, ':id' => $orderId]);
    }

    header('Location: ' . site_url('admin/order_detail.php?id=' . $orderId));
    exit;
}

$order = get_order($orderId);

if (!$order) {
    header('Location: ' . site_url('admin/orders.php'));
    exit;
}

$adminPageTitle = 'Order #' . $orderId;

require_once __DIR__ . '/includes/header.php';
?>

<section class="form-card">
    <h2>Order Info</h2>
    <p><strong>Customer:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
    <p><strong>WhatsApp:</strong> <?= htmlspecialchars($order['customer_whatsapp'] ?? '—') ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($order['customer_phone'] ?? '—') ?></p>
    <p><strong>Address:</strong><br><?= nl2br(htmlspecialchars($order['customer_address'])) ?></p>
    <p><strong>Notes:</strong><br><?= nl2br(htmlspecialchars($order['notes'] ?? '—')) ?></p>
    <p><strong>Total:</strong> <?= format_currency((float) $order['total_amount']) ?></p>
    <p><strong>Placed on:</strong> <?= htmlspecialchars($order['created_at']) ?></p>

    <form method="post" style="margin-top:1rem;">
        <label for="status">Status</label>
        <select id="status" name="status">
            <?php
            $statuses = [
                'pending' => 'Pending',
                'confirmed' => 'Confirmed',
                'in-progress' => 'In progress',
                'shipped' => 'Shipped',
                'delivered' => 'Delivered',
                'cancelled' => 'Cancelled',
            ];
            foreach ($statuses as $value => $label): ?>
                <option value="<?= $value ?>" <?= $order['status'] === $value ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn-primary" type="submit" style="margin-top:0.75rem;">Update status</button>
    </form>
</section>

<section style="margin-top:2rem;">
    <h2>Items</h2>
    <?php if (!empty($order['items'])): ?>
        <table class="admin-table">
            <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Unit price</th>
                <th>Subtotal</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($order['items'] as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= (int) $item['quantity'] ?></td>
                    <td><?= format_currency((float) $item['unit_price']) ?></td>
                    <td><?= format_currency((float) $item['line_total']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No items found for this order.</p>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

