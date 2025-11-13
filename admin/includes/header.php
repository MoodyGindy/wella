<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/functions.php';

require_admin_auth();

$adminPageTitle = $adminPageTitle ?? 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($adminPageTitle) ?> · WellaResin Admin</title>
    <link rel="stylesheet" href="<?= asset_url('css/styles.css') ?>">
    <style>
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        .admin-sidebar {
            width: 240px;
            background: var(--dark);
            color: #fff;
            padding: 1.5rem 1rem;
        }

        .admin-sidebar a {
            color: rgba(255, 255, 255, 0.9);
            display: block;
            padding: 0.6rem 0.8rem;
            border-radius: 8px;
            margin-bottom: 0.4rem;
        }

        .admin-sidebar a:hover,
        .admin-sidebar a:focus {
            background: rgba(255, 255, 255, 0.12);
        }

        .admin-content {
            flex: 1;
            padding: 2rem;
            background: var(--light);
        }

        table.admin-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        table.admin-table th,
        table.admin-table td {
            padding: 0.85rem 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        table.admin-table th {
            background: rgba(123, 92, 214, 0.12);
        }
    </style>
</head>
<body>
<div class="admin-layout">
    <aside class="admin-sidebar">
        <h2>WellaResin</h2>
        <p><?= htmlspecialchars(get_admin_display_name() ?? 'Admin') ?></p>
        <nav>
            <a href="<?= site_url('admin/index.php') ?>">Dashboard</a>
            <a href="<?= site_url('admin/products.php') ?>">Products</a>
            <a href="<?= site_url('admin/categories.php') ?>">Categories</a>
            <a href="<?= site_url('admin/orders.php') ?>">Orders</a>
            <a href="<?= site_url('admin/logout.php') ?>">Log out</a>
            <a href="<?= site_url('public/index.php') ?>" target="_blank">View Site</a>
        </nav>
    </aside>
    <main class="admin-content">
        <h1><?= htmlspecialchars($adminPageTitle) ?></h1>

