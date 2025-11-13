<?php
declare(strict_types=1);

$adminPageTitle = 'Products';

require_once __DIR__ . '/includes/header.php';

$products = get_products(['include_inactive' => true]);
?>

<div style="margin-bottom:1rem;">
    <a class="btn-primary" href="<?= site_url('admin/product_form.php') ?>">Add product</a>
</div>

<?php if (!empty($products)): ?>
    <table class="admin-table">
        <thead>
        <tr>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Status</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td><?= htmlspecialchars($product['category_name'] ?? '—') ?></td>
                <td><?= format_currency((float) $product['price']) ?></td>
                <td><?= (int) $product['stock_quantity'] ?></td>
                <td><?= (int) $product['is_active'] === 1 ? 'Active' : 'Hidden' ?></td>
                <td style="display:flex; gap:0.5rem;">
                    <a class="btn-secondary" href="<?= site_url('admin/product_form.php?id=' . (int) $product['id']) ?>">Edit</a>
                    <form action="<?= htmlspecialchars(site_url('admin/product_delete.php')) ?>" method="post" onsubmit="return confirm('Delete this product?');">
                        <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                        <button class="btn-secondary" type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No products yet. <a href="<?= site_url('admin/product_form.php') ?>">Add your first product</a>.</p>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

