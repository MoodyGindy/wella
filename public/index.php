<?php
declare(strict_types=1);

$pageTitle = 'Discover Handcrafted Resin Art';
$pageDescription = 'Browse unique resin creations, decor, and accessories by WellaResin.';
$bodyClass = 'has-hero';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/instagram.php';

$categories = get_categories();
$latestProducts = get_products(['limit' => 6]);
$instagramPosts = fetch_instagram_posts(6);
?>

<section
    class="hero hero--with-image"
    style="--hero-image: url('<?= site_url('admin/uploads/hero.png') ?>');"
>
    <div class="container hero-inner">
        <div class="hero-copy">
            <h1>Handmade Resin Pieces for Your Space</h1>
            <p>
                Each WellaResin product is crafted with love and attention to detail.
                Discover coasters, trays, jewelry, and décor that add sparkle to your daily life.
            </p>
            <div class="hero-actions">
                <a class="btn-primary" href="<?= site_url('public/products.php') ?>">Shop the Collection</a>
                <a class="btn-secondary" href="#instagram">See Instagram</a>
            </div>
        </div>
    </div>
</section>

<section class="container">
    <h2 class="section-title">Shop by Category</h2>
    <div class="product-grid">
        <?php foreach ($categories as $category): ?>
            <a class="product-card" href="<?= site_url('public/products.php?category=' . (int) $category['id']) ?>">
                <span class="badge">Explore</span>
                <h3><?= htmlspecialchars($category['name']) ?></h3>
                <p>Discover resin pieces curated in the <?= htmlspecialchars($category['name']) ?> collection.</p>
            </a>
        <?php endforeach; ?>
        <?php if (empty($categories)): ?>
            <div class="empty-state">
                <p>Categories will appear here once added in the dashboard.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="container">
    <h2 class="section-title">Latest Arrivals</h2>
    <?php if (!empty($latestProducts)): ?>
        <div class="product-grid">
            <?php foreach ($latestProducts as $product): ?>
                <div class="product-card">
                    <a href="<?= site_url('public/product.php?id=' . (int) $product['id']) ?>">
                        <?php $imageSrc = image_url($product['featured_image']); ?>
                        <img src="<?= htmlspecialchars($imageSrc ?: 'https://images.unsplash.com/photo-1616627453281-190a2e732db9?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80') ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    </a>
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <span class="price"><?= format_currency((float) $product['price']) ?></span>
                    <?php
                    $isFavorite = is_favorite((int) $product['id']);
                    $favoriteLabel = $isFavorite ? 'Remove favorite' : 'Add to favorites';
                    ?>
                    <div class="actions">
                        <button class="btn-primary btn-full" data-action="add-to-cart" data-product-id="<?= (int) $product['id'] ?>">
                            Add to cart
                        </button>
                        <button
                            class="btn-secondary btn-heart <?= $isFavorite ? 'is-active' : '' ?>"
                            data-action="toggle-favorite"
                            data-product-id="<?= (int) $product['id'] ?>"
                            data-is-favorite="<?= $isFavorite ? '1' : '0' ?>"
                            aria-label="<?= $favoriteLabel ?>"
                        >
                            <span class="sr-only"><?= $favoriteLabel ?></span>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <p>Products will appear here once you add them from the dashboard.</p>
        </div>
    <?php endif; ?>
</section>

<section class="container" id="instagram">
    <h2 class="section-title">Latest on Instagram</h2>
    <?php if (!empty($instagramPosts)): ?>
        <div class="instagram-feed">
            <?php foreach ($instagramPosts as $post): ?>
                <?php if (empty($post['image_url']) || empty($post['permalink'])) {
                    continue;
                } ?>
                <a href="<?= htmlspecialchars($post['permalink']) ?>" target="_blank" rel="noopener">
                    <img src="<?= htmlspecialchars($post['image_url']) ?>" alt="<?= htmlspecialchars(substr($post['caption'] ?? '', 0, 80)) ?>">
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <p>We couldn’t load the Instagram feed right now. <a href="https://www.instagram.com/wellaresin_33/" target="_blank" rel="noopener">Visit @wellaresin_33</a>.</p>
        </div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

