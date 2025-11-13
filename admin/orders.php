<?php
declare(strict_types=1);

$adminPageTitle = 'Orders';

require_once __DIR__ . '/includes/header.php';

$orders = get_orders();
?>

<?php if (!empty($orders)): ?>
    <table class="admin-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Status</th>
            <th>Date</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td>#<?= (int) $order['id'] ?></td>
                <td><?= htmlspecialchars($order['customer_name']) ?></td>
                <td><?= format_currency((float) $order['total_amount']) ?></td>
                <td><?= htmlspecialchars(ucfirst($order['status'])) ?></td>
                <td><?= htmlspecialchars($order['created_at']) ?></td>
                <td><a class="btn-secondary" href="<?= site_url('admin/order_detail.php?id=' . (int) $order['id']) ?>">View</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No orders yet.</p>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

