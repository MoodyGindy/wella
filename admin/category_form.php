<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

require_admin_auth();

$pdo = get_db_connection();

$categoryId = isset($_GET['id']) ? (int) $_GET['id'] : null;
$isEdit = $categoryId !== null;
$adminPageTitle = $isEdit ? 'Edit Category' : 'Add Category';

$category = [
    'name' => '',
];
$errors = [];

if ($isEdit) {
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = :id');
    $stmt->execute([':id' => $categoryId]);
    $existing = $stmt->fetch();
    if (!$existing) {
        header('Location: ' . site_url('admin/categories.php'));
        exit;
    }
    $category = $existing;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category['name'] = trim($_POST['name'] ?? '');

    if ($category['name'] === '') {
        $errors[] = 'Name is required.';
    }

    if (empty($errors)) {
        if ($isEdit) {
            $stmt = $pdo->prepare('UPDATE categories SET name = :name WHERE id = :id');
            $stmt->execute([':name' => $category['name'], ':id' => $categoryId]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO categories (name) VALUES (:name)');
            $stmt->execute([':name' => $category['name']]);
        }

        header('Location: ' . site_url('admin/categories.php'));
        exit;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<?php foreach ($errors as $error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endforeach; ?>

<form class="form-card" method="post">
    <div>
        <label for="name">Category name</label>
        <input type="text" id="name" name="name" required value="<?= htmlspecialchars($category['name']) ?>">
    </div>
    <div style="margin-top:1.5rem;">
        <button class="btn-primary" type="submit"><?= $isEdit ? 'Update category' : 'Create category' ?></button>
        <a class="btn-secondary" href="<?= site_url('admin/categories.php') ?>">Cancel</a>
    </div>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

