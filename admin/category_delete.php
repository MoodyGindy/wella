<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

require_admin_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . site_url('admin/categories.php'));
    exit;
}

$categoryId = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($categoryId > 0) {
    $pdo = get_db_connection();
    $pdo->prepare('UPDATE products SET category_id = NULL WHERE category_id = :id')->execute([':id' => $categoryId]);
    $pdo->prepare('DELETE FROM categories WHERE id = :id')->execute([':id' => $categoryId]);
}

header('Location: ' . site_url('admin/categories.php'));
exit;

