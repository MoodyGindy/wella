<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

$payload = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $payload['action'] ?? '';
$productId = isset($payload['product_id']) ? (int) $payload['product_id'] : null;
$quantity = isset($payload['quantity']) ? (int) $payload['quantity'] : 1;

try {
    switch ($action) {
        case 'add':
            if (!$productId) {
                throw new InvalidArgumentException('Invalid product.');
            }
            $product = get_product($productId);
            if (!$product || !(int) $product['is_active']) {
                throw new InvalidArgumentException('Product unavailable.');
            }
            add_to_cart($productId, $quantity);
            echo json_encode(['status' => 'ok', 'message' => 'Product added to cart.']);
            break;

        case 'update':
            if (!$productId) {
                throw new InvalidArgumentException('Invalid product.');
            }
            update_cart($productId, $quantity);
            echo json_encode(['status' => 'ok', 'message' => 'Cart updated.']);
            break;

        case 'remove':
            if (!$productId) {
                throw new InvalidArgumentException('Invalid product.');
            }
            remove_from_cart($productId);
            echo json_encode(['status' => 'ok', 'message' => 'Removed from cart.', 'redirect' => true]);
            break;

        case 'clear':
            clear_cart();
            echo json_encode(['status' => 'ok', 'message' => 'Cart cleared.', 'redirect' => true]);
            break;

        default:
            throw new InvalidArgumentException('Unknown action.');
    }
} catch (Throwable $exception) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $exception->getMessage(),
    ]);
}

