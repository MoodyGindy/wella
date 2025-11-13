<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

ensure_session();

$pageTitle = $pageTitle ?? 'WellaResin';
$pageDescription = $pageDescription ?? 'Handcrafted resin decor and accessories by WellaResin.';
$bodyClass = trim($bodyClass ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
    <title><?= htmlspecialchars($pageTitle) ?> · WellaResin</title>
    <link rel="stylesheet" href="<?= asset_url('css/styles.css') ?>">
    <script>
        window.APP_BASE_URL = <?= json_encode(rtrim(site_url(), '/') . '/') ?>;
    </script>
</head>
<body<?= $bodyClass !== '' ? ' class="' . htmlspecialchars($bodyClass) . '"' : '' ?> data-base-url="<?= htmlspecialchars(rtrim(site_url(), '/') . '/') ?>">
    <header class="site-header">
        <div class="container">
            <a class="brand" href="<?= site_url('public/index.php') ?>">
                <img src="<?= htmlspecialchars(image_url('admin/uploads/66be5de4-0894-4d7b-9831-ea4e4927dd3c.png')) ?>" alt="WellaResin Logo" width="64">
                <span class="brand-title">WellaResin</span>
            </a>
            <nav class="main-nav">
                <a href="<?= site_url('public/index.php') ?>">Home</a>
                <a href="<?= site_url('public/products.php') ?>">Shop</a>
                <a href="<?= site_url('public/favorites.php') ?>">Favorites</a>
                <a href="<?= site_url('public/cart.php') ?>">Cart</a>
            </nav>
            <div class="header-meta">
                <a class="btn-primary" href="<?= site_url('public/checkout.php') ?>">Order Now</a>
            </div>
        </div>
    </header>
    <main class="site-main">

