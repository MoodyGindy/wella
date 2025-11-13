<?php
declare(strict_types=1);

$pageTitle = 'Shop Products';
$pageDescription = 'Browse all resin art and accessories available from WellaResin.';

require_once __DIR__ . '/../includes/header.php';

$categoryId = isset($_GET['category']) ? (int) $_GET['category'] : null;
$search = trim($_GET['search'] ?? '');

$filters = [];
if ($categoryId) {
    $filters['category_id'] = $categoryId;
}
if ($search !== '') {
    $filters['search'] = $search;
}

$categories = get_categories();
$products = get_products($filters);
?>

<section class="container">
    <h1 class="section-title">Our Collection</h1>

    <form class="form-card" method="get">
        <div class="form-grid">
            <div>
                <label for="search">Search</label>
                <input type="text" id="search" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search for coasters, trays, jewelry...">
            </div>
            <div>
                <label for="category">Category</label>
                <select id="category" name="category">
                    <option value="">All categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= (int) $category['id'] ?>" <?= $categoryId === (int) $category['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="align-self:flex-end;">
                <button class="btn-primary" type="submit">Apply Filters</button>
            </div>
        </div>
    </form>

    <?php if (!empty($products)): ?>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <a href="<?= site_url('public/product.php?id=' . (int) $product['id']) ?>">
                        <?php $imageSrc = image_url($product['featured_image']); ?>
                        <img src="<?= htmlspecialchars($imageSrc ?: 'https://images.unsplash.com/photo-1616627453281-190a2e732db9?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80') ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    </a>
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <span class="price"><?= format_currency((float) $product['price']) ?></span>
                    <?php if (!empty($product['category_name'])): ?>
                        <span class="badge"><?= htmlspecialchars($product['category_name']) ?></span>
                    <?php endif; ?>
                    <p><?= htmlspecialchars(substr($product['description'], 0, 100)) ?>...</p>
                    <?php
                    $isFavorite = is_favorite((int) $product['id']);
                    $favoriteLabel = $isFavorite ? 'Remove favorite' : 'Add to favorites';
                    ?>
                    <div class="actions">
                        <button class="btn-primary btn-full" data-action="add-to-cart" data-product-id="<?= (int) $product['id'] ?>">Add to cart</button>
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
            <p>No products match your filters. Try adjusting your search or check back later.</p>
        </div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

