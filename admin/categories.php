<?php
declare(strict_types=1);

$adminPageTitle = 'Categories';

require_once __DIR__ . '/includes/header.php';

$categories = get_categories();
?>

<div style="margin-bottom:1rem;">
    <a class="btn-primary" href="<?= site_url('admin/category_form.php') ?>">Add category</a>
</div>

<?php if (!empty($categories)): ?>
    <table class="admin-table">
        <thead>
        <tr>
            <th>Name</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($categories as $category): ?>
            <tr>
                <td><?= htmlspecialchars($category['name']) ?></td>
                <td style="display:flex; gap:0.5rem;">
                    <a class="btn-secondary" href="<?= site_url('admin/category_form.php?id=' . (int) $category['id']) ?>">Edit</a>
                    <form action="<?= htmlspecialchars(site_url('admin/category_delete.php')) ?>" method="post" onsubmit="return confirm('Delete this category?');">
                        <input type="hidden" name="id" value="<?= (int) $category['id'] ?>">
                        <button class="btn-secondary" type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No categories yet. <a href="<?= site_url('admin/category_form.php') ?>">Add one</a>.</p>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

