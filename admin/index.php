<?php
declare(strict_types=1);

$adminPageTitle = 'Dashboard';

require_once __DIR__ . '/includes/header.php';

$pdo = get_db_connection();
$productCount = (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$categoryCount = (int) $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
$orderCount = (int) $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$recentOrders = $pdo->query('SELECT id, customer_name, total_amount, status, created_at FROM orders ORDER BY created_at DESC LIMIT 5')->fetchAll();
?>

<section>
    <div class="product-grid">
        <div class="product-card">
            <h3>Products</h3>
            <p>Total items in catalogue</p>
            <strong><?= $productCount ?></strong>
            <a class="btn-secondary" href="<?= site_url('admin/products.php') ?>">Manage products</a>
        </div>
        <div class="product-card">
            <h3>Categories</h3>
            <p>Organize items</p>
            <strong><?= $categoryCount ?></strong>
            <a class="btn-secondary" href="<?= site_url('admin/categories.php') ?>">Manage categories</a>
        </div>
        <div class="product-card">
            <h3>Orders</h3>
            <p>Orders received</p>
            <strong><?= $orderCount ?></strong>
            <a class="btn-secondary" href="<?= site_url('admin/orders.php') ?>">View orders</a>
        </div>
    </div>
</section>

<section style="margin-top:2rem;">
    <h2>Recent Orders</h2>
    <?php if (!empty($recentOrders)): ?>
        <table class="admin-table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($recentOrders as $order): ?>
                <tr>
                    <td><a href="<?= site_url('admin/order_detail.php?id=' . (int) $order['id']) ?>">#<?= (int) $order['id'] ?></a></td>
                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                    <td><?= format_currency((float) $order['total_amount']) ?></td>
                    <td><?= htmlspecialchars(ucfirst($order['status'])) ?></td>
                    <td><?= htmlspecialchars($order['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No orders yet.</p>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

