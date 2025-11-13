<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

require_admin_auth();

$pdo = get_db_connection();

$productId = isset($_GET['id']) ? (int) $_GET['id'] : null;
$isEdit = $productId !== null;

$adminPageTitle = $isEdit ? 'Edit Product' : 'Add Product';

$product = [
    'name' => '',
    'description' => '',
    'price' => '',
    'stock_quantity' => '',
    'category_id' => '',
    'is_active' => 1,
    'featured_image' => '',
];
$galleryImages = [];

if ($isEdit) {
    $existing = get_product($productId);
    if (!$existing) {
        header('Location: ' . site_url('admin/products.php'));
        exit;
    }
    $product = array_merge($product, $existing);
    $galleryImages = $existing['images'] ?? [];
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product['name'] = trim($_POST['name'] ?? '');
    $product['description'] = trim($_POST['description'] ?? '');
    $product['price'] = trim($_POST['price'] ?? '');
    $product['stock_quantity'] = (int) ($_POST['stock_quantity'] ?? 0);
    $product['category_id'] = $_POST['category_id'] !== '' ? (int) $_POST['category_id'] : null;
    $product['is_active'] = isset($_POST['is_active']) ? 1 : 0;

    if ($product['name'] === '') {
        $errors[] = 'Name is required.';
    }

    if ($product['price'] === '' || !is_numeric($product['price'])) {
        $errors[] = 'Price must be a valid number.';
    }

    if ($product['stock_quantity'] < 0) {
        $errors[] = 'Stock cannot be negative.';
    }

    $featuredImagePath = null;

    if (!empty($_FILES['featured_image']['name'])) {
        try {
            $featuredImagePath = save_uploaded_image($_FILES['featured_image']);
        } catch (RuntimeException $exception) {
            $errors[] = $exception->getMessage();
        }
    }

    if (empty($errors)) {
        if ($isEdit) {
            $stmt = $pdo->prepare('UPDATE products
                SET name = :name,
                    description = :description,
                    price = :price,
                    stock_quantity = :stock_quantity,
                    category_id = :category_id,
                    is_active = :is_active' .
                ($featuredImagePath ? ', featured_image = :featured_image' : '') .
                ' WHERE id = :id');

            $params = [
                ':name' => $product['name'],
                ':description' => $product['description'],
                ':price' => $product['price'],
                ':stock_quantity' => $product['stock_quantity'],
                ':category_id' => $product['category_id'],
                ':is_active' => $product['is_active'],
                ':id' => $productId,
            ];

            if ($featuredImagePath) {
                $params[':featured_image'] = $featuredImagePath;
            }

            $stmt->execute($params);
        } else {
            $stmt = $pdo->prepare('INSERT INTO products (name, slug, description, price, stock_quantity, category_id, is_active, featured_image, created_at)
                                   VALUES (:name, :slug, :description, :price, :stock_quantity, :category_id, :is_active, :featured_image, NOW())');

            $stmt->execute([
                ':name' => $product['name'],
                ':slug' => slugify($product['name']),
                ':description' => $product['description'],
                ':price' => $product['price'],
                ':stock_quantity' => $product['stock_quantity'],
                ':category_id' => $product['category_id'],
                ':is_active' => $product['is_active'],
                ':featured_image' => $featuredImagePath ?? null,
            ]);

            $productId = (int) $pdo->lastInsertId();
        }

        if ($featuredImagePath) {
            $product['featured_image'] = $featuredImagePath;
        }

        if (!empty($_FILES['gallery']['name']) && is_array($_FILES['gallery']['name'])) {
            $fileCount = count($_FILES['gallery']['name']);

            $galleryStmt = $pdo->prepare('INSERT INTO product_images (product_id, image_path) VALUES (:product_id, :image_path)');

            for ($i = 0; $i < $fileCount; $i++) {
                if (empty($_FILES['gallery']['name'][$i])) {
                    continue;
                }
                $file = [
                    'name' => $_FILES['gallery']['name'][$i],
                    'type' => $_FILES['gallery']['type'][$i],
                    'tmp_name' => $_FILES['gallery']['tmp_name'][$i],
                    'error' => $_FILES['gallery']['error'][$i],
                    'size' => $_FILES['gallery']['size'][$i],
                ];
                try {
                    $imagePath = save_uploaded_image($file);
                    if ($imagePath) {
                        $galleryStmt->execute([
                            ':product_id' => $productId,
                            ':image_path' => $imagePath,
                        ]);
                    }
                } catch (RuntimeException $exception) {
                    $errors[] = $exception->getMessage();
                }
            }
        }

        if (empty($errors)) {
            header('Location: ' . site_url('admin/products.php'));
            header('Location: ' . site_url('admin/products.php'));
            exit;
        }
    }
}

$categories = get_categories();

require_once __DIR__ . '/includes/header.php';
?>

<?php foreach ($errors as $error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endforeach; ?>

<form class="form-card" method="post" enctype="multipart/form-data">
    <div class="form-grid">
        <div>
            <label for="name">Product name</label>
            <input type="text" id="name" name="name" required value="<?= htmlspecialchars($product['name']) ?>">
        </div>
        <div>
            <label for="price">Price</label>
            <input type="number" step="0.01" id="price" name="price" required value="<?= htmlspecialchars((string) $product['price']) ?>">
        </div>
        <div>
            <label for="stock_quantity">Stock quantity</label>
            <input type="number" id="stock_quantity" name="stock_quantity" min="0" value="<?= (int) $product['stock_quantity'] ?>">
        </div>
        <div>
            <label for="category_id">Category</label>
            <select id="category_id" name="category_id">
                <option value="">Uncategorized</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int) $category['id'] ?>" <?= (int) $product['category_id'] === (int) $category['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div>
        <label for="description">Description</label>
        <textarea id="description" name="description"><?= htmlspecialchars($product['description']) ?></textarea>
    </div>

    <div>
        <label for="featured_image">Featured image</label>
        <input type="file" id="featured_image" name="featured_image" accept="image/*">
        <?php if (!empty($product['featured_image'])): ?>
            <p>Current: <a href="<?= htmlspecialchars($product['featured_image']) ?>" target="_blank">View image</a></p>
        <?php endif; ?>
    </div>

    <div>
        <label for="gallery">Gallery images</label>
        <input type="file" id="gallery" name="gallery[]" accept="image/*" multiple>
    </div>

    <?php if (!empty($galleryImages)): ?>
        <div class="product-thumbs" style="margin-bottom:1.5rem;">
            <?php foreach ($galleryImages as $image): ?>
                <img src="<?= htmlspecialchars($image['image_path']) ?>" alt="Product image">
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <label>
        <input type="checkbox" name="is_active" <?= (int) $product['is_active'] === 1 ? 'checked' : '' ?>>
        Visible on storefront
    </label>

    <div style="margin-top:1.5rem;">
        <button class="btn-primary" type="submit"><?= $isEdit ? 'Update product' : 'Create product' ?></button>
        <a class="btn-secondary" href="<?= site_url('admin/products.php') ?>">Cancel</a>
    </div>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

