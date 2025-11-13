<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';

function site_url(string $path = ''): string
{
    static $baseUri = null;

    if ($baseUri === null) {
        $rootPath = str_replace('\\', '/', realpath(__DIR__ . '/..'));
        $documentRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? $rootPath), '/');
        $relative = str_replace($documentRoot, '', $rootPath);
        $relative = str_replace('\\', '/', $relative);
        $baseUri = $relative === '' ? '' : '/' . ltrim($relative, '/');
    }

    $trimmedPath = ltrim($path, '/');

    if ($trimmedPath === '') {
        return $baseUri === '' ? '/' : $baseUri;
    }

    if ($baseUri === '') {
        return '/' . $trimmedPath;
    }

    return rtrim($baseUri, '/') . '/' . $trimmedPath;
}

function asset_url(string $path): string
{
    return site_url('assets/' . ltrim($path, '/'));
}

function image_url(?string $path): ?string
{
    if (empty($path)) {
        return null;
    }

    if (preg_match('~^https?://~i', $path)) {
        return $path;
    }

    return site_url(ltrim($path, '/'));
}

function ensure_session(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $_SESSION['cart'] = $_SESSION['cart'] ?? [];
    $_SESSION['favorites'] = $_SESSION['favorites'] ?? [];
}

function format_currency(float $value): string
{
    return '$' . number_format($value, 2);
}

function get_categories(): array
{
    $pdo = get_db_connection();
    $stmt = $pdo->query('SELECT id, name FROM categories ORDER BY name ASC');
    return $stmt->fetchAll();
}

function get_products(array $filters = []): array
{
    $pdo = get_db_connection();
    $sql = 'SELECT p.id, p.name, p.slug, p.description, p.price, p.stock_quantity, p.featured_image, p.is_active, c.name AS category_name
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            WHERE 1=1';

    $params = [];

    if (empty($filters['include_inactive'])) {
        $sql .= ' AND p.is_active = 1';
    }

    if (!empty($filters['category_id'])) {
        $sql .= ' AND p.category_id = :category_id';
        $params[':category_id'] = (int) $filters['category_id'];
    }

    if (!empty($filters['search'])) {
        $sql .= ' AND (p.name LIKE :search OR p.description LIKE :search)';
        $params[':search'] = '%' . $filters['search'] . '%';
    }

    $sql .= ' ORDER BY p.created_at DESC';

    if (!empty($filters['limit'])) {
        $sql .= ' LIMIT :limit';
        $params[':limit'] = (int) $filters['limit'];
    }

    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        if ($key === ':limit') {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        } else {
            $stmt->bindValue($key, $value);
        }
    }
    $stmt->execute();

    return $stmt->fetchAll();
}

function get_product(int $productId): ?array
{
    $pdo = get_db_connection();
    $stmt = $pdo->prepare('SELECT p.*, c.name AS category_name
                           FROM products p
                           LEFT JOIN categories c ON c.id = p.category_id
                           WHERE p.id = :id');
    $stmt->execute([':id' => $productId]);
    $product = $stmt->fetch();

    if (!$product) {
        return null;
    }

    $product['images'] = get_product_images($productId);

    return $product;
}

function get_product_images(int $productId): array
{
    $pdo = get_db_connection();
    $stmt = $pdo->prepare('SELECT id, image_path FROM product_images WHERE product_id = :id ORDER BY id ASC');
    $stmt->execute([':id' => $productId]);
    return $stmt->fetchAll();
}

/**
 * @return array<int, int> productId => quantity
 */
function get_cart(): array
{
    ensure_session();
    return $_SESSION['cart'];
}

function add_to_cart(int $productId, int $quantity = 1): void
{
    ensure_session();

    $quantity = max(1, $quantity);
    $_SESSION['cart'][$productId] = ($_SESSION['cart'][$productId] ?? 0) + $quantity;
}

function update_cart(int $productId, int $quantity): void
{
    ensure_session();

    if ($quantity <= 0) {
        unset($_SESSION['cart'][$productId]);
        return;
    }

    $_SESSION['cart'][$productId] = $quantity;
}

function remove_from_cart(int $productId): void
{
    ensure_session();
    unset($_SESSION['cart'][$productId]);
}

function clear_cart(): void
{
    ensure_session();
    $_SESSION['cart'] = [];
}

function get_cart_details(): array
{
    $cart = get_cart();

    if (empty($cart)) {
        return [];
    }

    $pdo = get_db_connection();
    $placeholders = implode(',', array_fill(0, count($cart), '?'));
    $stmt = $pdo->prepare("SELECT id, name, price, featured_image, stock_quantity FROM products WHERE id IN ($placeholders)");
    $stmt->execute(array_keys($cart));
    $products = $stmt->fetchAll();

    $details = [];

    foreach ($products as $product) {
        $productId = (int) $product['id'];
        $quantity = $cart[$productId] ?? 0;
        $lineTotal = $product['price'] * $quantity;

        $details[] = [
            'product' => $product,
            'quantity' => $quantity,
            'line_total' => $lineTotal,
        ];
    }

    return $details;
}

function get_cart_total(): float
{
    return array_reduce(
        get_cart_details(),
        fn (float $carry, array $item) => $carry + (float) $item['line_total'],
        0.0
    );
}

function get_favorites(): array
{
    ensure_session();
    return $_SESSION['favorites'];
}

function add_to_favorites(int $productId): void
{
    ensure_session();
    if (!in_array($productId, $_SESSION['favorites'], true)) {
        $_SESSION['favorites'][] = $productId;
    }
}

function remove_from_favorites(int $productId): void
{
    ensure_session();
    $_SESSION['favorites'] = array_values(
        array_filter(
            $_SESSION['favorites'],
            static fn ($id) => (int) $id !== $productId
        )
    );
}

function is_favorite(int $productId): bool
{
    return in_array($productId, get_favorites(), true);
}

function get_favorite_products(): array
{
    $favoriteIds = get_favorites();

    if (empty($favoriteIds)) {
        return [];
    }

    $pdo = get_db_connection();
    $placeholders = implode(',', array_fill(0, count($favoriteIds), '?'));
    $stmt = $pdo->prepare("SELECT id, name, price, featured_image FROM products WHERE id IN ($placeholders)");
    $stmt->execute($favoriteIds);

    return $stmt->fetchAll();
}

function slugify(string $text): string
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);

    return $text ?: 'item-' . uniqid();
}

function require_admin_auth(): void
{
    ensure_session();

    if (empty($_SESSION['admin_user_id'])) {
        header('Location: ' . site_url('admin/login.php'));
        exit;
    }
}

function get_admin_display_name(): ?string
{
    ensure_session();
    return $_SESSION['admin_display_name'] ?? null;
}

function authenticate_admin(string $email, string $password): bool
{
    $pdo = get_db_connection();
    $stmt = $pdo->prepare('SELECT id, email, display_name, password_hash FROM admin_users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => strtolower(trim($email))]);
    $admin = $stmt->fetch();

    if (!$admin) {
        return false;
    }

    if (!password_verify($password, $admin['password_hash'])) {
        return false;
    }

    ensure_session();
    $_SESSION['admin_user_id'] = $admin['id'];
    $_SESSION['admin_display_name'] = $admin['display_name'];

    return true;
}

function logout_admin(): void
{
    ensure_session();
    unset($_SESSION['admin_user_id'], $_SESSION['admin_display_name']);
}

function save_uploaded_image(array $file): ?string
{
    if (empty($file['name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $extension = strtolower((string) pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($extension, $allowedExtensions, true)) {
        throw new RuntimeException('Unsupported image type.');
    }

    $uploadDir = __DIR__ . '/../admin/uploads/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }

    $filename = uniqid('img_', true) . '.' . $extension;
    $destination = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new RuntimeException('Failed to move uploaded file.');
    }

    return '/admin/uploads/' . $filename;
}

function create_order(array $data): int
{
    $cartItems = get_cart_details();

    if (empty($cartItems)) {
        throw new RuntimeException('Cart is empty.');
    }

    $pdo = get_db_connection();
    $pdo->beginTransaction();

    try {
        $orderStmt = $pdo->prepare(
            'INSERT INTO orders (customer_name, customer_address, customer_phone, customer_whatsapp, notes, total_amount, status, created_at)
             VALUES (:name, :address, :phone, :whatsapp, :notes, :total, :status, NOW())'
        );

        $orderStmt->execute([
            ':name' => $data['name'],
            ':address' => $data['address'],
            ':phone' => $data['phone'] ?? null,
            ':whatsapp' => $data['whatsapp'],
            ':notes' => $data['notes'] ?? null,
            ':total' => get_cart_total(),
            ':status' => 'pending',
        ]);

        $orderId = (int) $pdo->lastInsertId();

        $itemStmt = $pdo->prepare(
            'INSERT INTO order_items (order_id, product_id, quantity, unit_price, line_total)
             VALUES (:order_id, :product_id, :quantity, :unit_price, :line_total)'
        );

        $stockStmt = $pdo->prepare(
            'UPDATE products SET stock_quantity = GREATEST(stock_quantity - :quantity, 0) WHERE id = :product_id'
        );

        foreach ($cartItems as $item) {
            $product = $item['product'];

            $itemStmt->execute([
                ':order_id' => $orderId,
                ':product_id' => $product['id'],
                ':quantity' => $item['quantity'],
                ':unit_price' => $product['price'],
                ':line_total' => $item['line_total'],
            ]);

            $stockStmt->execute([
                ':quantity' => $item['quantity'],
                ':product_id' => $product['id'],
            ]);
        }

        $pdo->commit();
        clear_cart();

        return $orderId;
    } catch (Throwable $exception) {
        $pdo->rollBack();
        throw $exception;
    }
}

function get_orders(): array
{
    $pdo = get_db_connection();
    $stmt = $pdo->query('SELECT * FROM orders ORDER BY created_at DESC');
    return $stmt->fetchAll();
}

function get_order(int $orderId): ?array
{
    $pdo = get_db_connection();

    $stmt = $pdo->prepare('SELECT * FROM orders WHERE id = :id');
    $stmt->execute([':id' => $orderId]);
    $order = $stmt->fetch();

    if (!$order) {
        return null;
    }

    $itemsStmt = $pdo->prepare(
        'SELECT oi.*, p.name, p.featured_image
         FROM order_items oi
         JOIN products p ON p.id = oi.product_id
         WHERE oi.order_id = :order_id'
    );

    $itemsStmt->execute([':order_id' => $orderId]);
    $order['items'] = $itemsStmt->fetchAll();

    return $order;
}


