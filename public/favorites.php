<?php
declare(strict_types=1);

$pageTitle = 'Your Favorites';
$pageDescription = 'Products you have marked as favorites on WellaResin.';

require_once __DIR__ . '/../includes/header.php';

$favoriteProducts = get_favorite_products();
?>

<section class="container">
    <h1 class="section-title">Favorites</h1>
    <?php if (!empty($favoriteProducts)): ?>
        <div class="product-grid">
            <?php foreach ($favoriteProducts as $product): ?>
                <div class="product-card">
                    <a href="<?= site_url('public/product.php?id=' . (int) $product['id']) ?>">
                        <?php $imageSrc = image_url($product['featured_image']); ?>
                        <img src="<?= htmlspecialchars($imageSrc ?: 'https://images.unsplash.com/photo-1616627453281-190a2e732db9?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80') ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    </a>
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <span class="price"><?= format_currency((float) $product['price']) ?></span>
                    <div class="actions">
                        <button class="btn-primary btn-full" data-action="add-to-cart" data-product-id="<?= (int) $product['id'] ?>">Add to cart</button>
                        <button
                            class="btn-secondary btn-heart is-active"
                            data-action="toggle-favorite"
                            data-product-id="<?= (int) $product['id'] ?>"
                            data-is-favorite="1"
                            aria-label="Remove favorite"
                        >
                            <span class="sr-only">Remove favorite</span>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <p>You haven’t added any favorites yet.</p>
            <a class="btn-primary" href="<?= site_url('public/products.php') ?>">Browse products</a>
        </div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

