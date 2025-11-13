<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

require_admin_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . site_url('admin/products.php'));
    exit;
}

$productId = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($productId > 0) {
    $pdo = get_db_connection();

    $pdo->prepare('DELETE FROM product_images WHERE product_id = :id')->execute([':id' => $productId]);
    $pdo->prepare('DELETE FROM products WHERE id = :id')->execute([':id' => $productId]);
}

header('Location: ' . site_url('admin/products.php'));
exit;

