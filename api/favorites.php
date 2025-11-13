<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

$payload = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $payload['action'] ?? '';
$productId = isset($payload['product_id']) ? (int) $payload['product_id'] : null;

try {
    if (!$productId) {
        throw new InvalidArgumentException('Invalid product.');
    }

    switch ($action) {
        case 'add':
            add_to_favorites($productId);
            echo json_encode(['status' => 'ok', 'message' => 'Added to favorites.']);
            break;

        case 'remove':
            remove_from_favorites($productId);
            echo json_encode(['status' => 'ok', 'message' => 'Removed from favorites.']);
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

