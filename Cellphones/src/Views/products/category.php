<?php
$title = ($category['name'] ?? 'Danh mục') . ' - CellphoneS';
$useMirrorStyles = true;
$content = ob_get_clean();
ob_start();
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
            <?php if (!empty($breadcrumb)): ?>
                <?php foreach ($breadcrumb as $crumb): ?>
                    <li class="breadcrumb-item <?= $crumb['id'] == $category['id'] ? 'active' : '' ?>">
                        <?php if ($crumb['id'] == $category['id']): ?>
                            <?= htmlspecialchars($crumb['name']) ?>
                        <?php else: ?>
                            <a href="/category?slug=<?= htmlspecialchars($crumb['slug']) ?>"><?= htmlspecialchars($crumb['name']) ?></a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="breadcrumb-item active"><?= htmlspecialchars($category['name'] ?? 'Danh mục') ?></li>
            <?php endif; ?>
        </ol>
    </nav>

    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Bộ lọc</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="/category">
                        <input type="hidden" name="slug" value="<?= htmlspecialchars($category['slug']) ?>">

                        <!-- Subcategories -->
                        <?php if (!empty($subcategories)): ?>
                        <div class="mb-4">
                            <h6>Danh mục con</h6>
                            <ul class="list-unstyled">
                                <?php foreach ($subcategories as $sub): ?>
                                    <li class="mb-2">
                                        <a href="/category?slug=<?= htmlspecialchars($sub['slug']) ?>" class="text-decoration-none">
                                            <?= htmlspecialchars($sub['name']) ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <!-- Brand Filter -->
                        <?php if (!empty($brands)): ?>
                        <div class="mb-4">
                            <h6>Thương hiệu</h6>
                            <select name="brand" class="form-select">
                                <option value="">Tất cả thương hiệu</option>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?= htmlspecialchars($brand) ?>" <?= ($filters['brand'] ?? '') == $brand ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($brand) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>

                        <!-- Price Range -->
                        <div class="mb-4">
                            <h6>Khoảng giá</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" name="min_price" class="form-control" placeholder="Từ" value="<?= htmlspecialchars($filters['min_price'] ?? '') ?>">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="max_price" class="form-control" placeholder="Đến" value="<?= htmlspecialchars($filters['max_price'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-danger">Áp dụng bộ lọc</button>
                            <a href="/category?slug=<?= htmlspecialchars($category['slug']) ?>" class="btn btn-outline-secondary">Xóa bộ lọc</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h4 mb-0"><?= htmlspecialchars($category['name']) ?></h1>
                <div class="text-muted"><?= count($products) ?> sản phẩm</div>
            </div>

            <div class="row g-4">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="product-card card h-100 cps-card">
                                <div class="product-image position-relative">
                                    <img src="<?= BASE_URL ?>/public/<?= $product['image'] ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                                    <div class="product-badges position-absolute top-0 start-0 p-2 d-flex gap-2">
                                        <?php if ($product['original_price'] && $product['original_price'] > $product['price']): ?>
                                            <span class="badge bg-danger">Giảm giá</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title mb-1"><?= htmlspecialchars($product['name']) ?></h6>
                                    <p class="text-muted small mb-2"><?= htmlspecialchars($product['brand']) ?></p>
                                    <div class="price-section mb-3">
                                        <span class="h5 text-danger fw-bold"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
                                        <?php if ($product['original_price'] && $product['original_price'] > $product['price']): ?>
                                            <span class="text-muted text-decoration-line-through ms-2"><?= number_format($product['original_price'], 0, ',', '.') ?>đ</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mt-auto">
                                        <a href="/product?slug=<?= htmlspecialchars($product['slug']) ?>" class="btn btn-danger w-100">
                                            <i class="fas fa-shopping-cart me-2"></i>Mua ngay
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center text-muted py-5">Không có sản phẩm phù hợp</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../layouts/main.php'; ?>


