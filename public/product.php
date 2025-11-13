<?php
declare(strict_types=1);

$productId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

require_once __DIR__ . '/../includes/functions.php';

$product = $productId > 0 ? get_product($productId) : null;

if (!$product) {
    http_response_code(404);
    $pageTitle = 'Product not found';
    require_once __DIR__ . '/../includes/header.php';
    ?>
    <section class="container">
        <div class="empty-state">
            <h1>Product not found</h1>
            <p>The product you were looking for is no longer available.</p>
            <a class="btn-primary" href="<?= site_url('public/products.php') ?>">Back to shop</a>
        </div>
    </section>
    <?php
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$pageTitle = $product['name'] ?? 'Product details';
$pageDescription = substr($product['description'] ?? '', 0, 150);

require_once __DIR__ . '/../includes/header.php';
?>

<section class="container">
    <div class="product-detail">
        <div class="product-gallery">
            <?php $imageSrc = image_url($product['featured_image']); ?>
            <img src="<?= htmlspecialchars($imageSrc ?: 'https://images.unsplash.com/photo-1616628182501-3e3e47cdc68f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80') ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            <?php if (!empty($product['images'])): ?>
                <div class="product-thumbs">
                    <?php foreach ($product['images'] as $image): ?>
                        <img src="<?= htmlspecialchars(image_url($image['image_path'])) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="product-info">
            <h1><?= htmlspecialchars($product['name']) ?></h1>
            <?php if (!empty($product['category_name'])): ?>
                <span class="badge"><?= htmlspecialchars($product['category_name']) ?></span>
            <?php endif; ?>
            <div class="price"><?= format_currency((float) $product['price']) ?></div>
            <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            <?php
            $isFavoriteDetail = is_favorite((int) $product['id']);
            $favoriteDetailLabel = $isFavoriteDetail ? 'Remove favorite' : 'Add to favorites';
            ?>
            <div class="product-actions">
                <button class="btn-primary btn-full" data-action="add-to-cart" data-product-id="<?= (int) $product['id'] ?>">Add to cart</button>
                <button
                    class="btn-secondary btn-heart <?= $isFavoriteDetail ? 'is-active' : '' ?>"
                    data-action="toggle-favorite"
                    data-product-id="<?= (int) $product['id'] ?>"
                    data-is-favorite="<?= $isFavoriteDetail ? '1' : '0' ?>"
                    aria-label="<?= $favoriteDetailLabel ?>"
                >
                    <span class="sr-only"><?= $favoriteDetailLabel ?></span>
                </button>
            </div>
            <p><strong>Delivery:</strong> Cash on delivery available across the region.</p>
        </div>
    </div>
</section>

<style>
.product-detail {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    align-items: start;
    margin-top: 2rem;
}

.product-gallery img {
    width: 100%;
    border-radius: 12px;
    box-shadow: var(--shadow);
}

.product-thumbs {
    margin-top: 1rem;
    display: flex;
    gap: 0.75rem;
}

.product-thumbs img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 10px;
    cursor: pointer;
}

.product-info .price {
    font-size: 1.8rem;
    margin: 1rem 0;
    font-weight: 700;
    color: var(--primary);
}

.product-actions {
    display: flex;
    gap: 0.75rem;
    margin: 1.5rem 0;
    align-items: center;
}

.product-actions .btn-primary {
    flex: 1;
}

.product-actions .btn-heart {
    flex: 0 0 auto;
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

