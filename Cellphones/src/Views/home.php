<?php
$title = 'CellphoneS - Điện thoại, Laptop, Phụ kiện chính hãng';
$content = ob_get_clean();
ob_start();
?>

<!-- Hero Banner -->
<section class="hero-banner">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="hero-slide" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="container">
                        <div class="row align-items-center min-vh-50">
                            <div class="col-lg-6">
                                <h1 class="display-4 fw-bold text-white mb-4">iPhone 15 Pro Max</h1>
                                <p class="lead text-white mb-4">Trải nghiệm camera 48MP với chip A17 Pro mạnh mẽ nhất từ Apple</p>
                                <div class="d-flex align-items-center mb-4">
                                    <span class="text-white h4 me-3">Từ 28.990.000đ</span>
                                    <span class="text-white-50 text-decoration-line-through">29.990.000đ</span>
                                </div>
                                <a href="/product/iphone-15-pro-max-256gb" class="btn btn-light btn-lg px-4">
                                    <i class="fas fa-shopping-cart me-2"></i>Mua ngay
                                </a>
                            </div>
                            <div class="col-lg-6 text-center">
                                <img src="<?= BASE_URL ?>/public/images/products/iphone-15-pro-max-hero.png" alt="iPhone 15 Pro Max" class="img-fluid" style="max-height: 400px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="hero-slide" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <div class="container">
                        <div class="row align-items-center min-vh-50">
                            <div class="col-lg-6">
                                <h1 class="display-4 fw-bold text-white mb-4">Samsung Galaxy S24 Ultra</h1>
                                <p class="lead text-white mb-4">Camera 200MP với S Pen và hiệu năng AI vượt trội</p>
                                <div class="d-flex align-items-center mb-4">
                                    <span class="text-white h4 me-3">Từ 26.990.000đ</span>
                                    <span class="text-white-50 text-decoration-line-through">27.990.000đ</span>
                                </div>
                                <a href="/product/samsung-galaxy-s24-ultra-512gb" class="btn btn-light btn-lg px-4">
                                    <i class="fas fa-shopping-cart me-2"></i>Mua ngay
                                </a>
                            </div>
                            <div class="col-lg-6 text-center">
                                <img src="<?= BASE_URL ?>/public/images/products/galaxy-s24-ultra-hero.png" alt="Samsung Galaxy S24 Ultra" class="img-fluid" style="max-height: 400px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="hero-slide" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <div class="container">
                        <div class="row align-items-center min-vh-50">
                            <div class="col-lg-6">
                                <h1 class="display-4 fw-bold text-white mb-4">MacBook Air M3</h1>
                                <p class="lead text-white mb-4">Hiệu năng mạnh mẽ với chip M3, màn hình Liquid Retina 13.6 inch</p>
                                <div class="d-flex align-items-center mb-4">
                                    <span class="text-white h4 me-3">Từ 24.990.000đ</span>
                                    <span class="text-white-50 text-decoration-line-through">25.990.000đ</span>
                                </div>
                                <a href="/product/macbook-air-m3-256gb" class="btn btn-light btn-lg px-4">
                                    <i class="fas fa-shopping-cart me-2"></i>Mua ngay
                                </a>
                            </div>
                            <div class="col-lg-6 text-center">
                                <img src="<?= BASE_URL ?>/public/images/products/macbook-air-m3-hero.png" alt="MacBook Air M3" class="img-fluid" style="max-height: 400px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</section>

<!-- Categories Section -->
<section class="categories-section py-5">
    <div class="container">
        <h2 class="text-center mb-5">Danh mục sản phẩm</h2>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="category-card text-center">
                    <div class="category-icon mb-3">
                        <i class="fas fa-mobile-alt fa-3x text-danger"></i>
                    </div>
                    <h5>Điện thoại</h5>
                    <p class="text-muted">iPhone, Samsung, Xiaomi...</p>
                    <a href="/category/dien-thoai" class="btn btn-outline-danger">Xem tất cả</a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="category-card text-center">
                    <div class="category-icon mb-3">
                        <i class="fas fa-laptop fa-3x text-danger"></i>
                    </div>
                    <h5>Laptop</h5>
                    <p class="text-muted">MacBook, Gaming, Văn phòng...</p>
                    <a href="/category/laptop" class="btn btn-outline-danger">Xem tất cả</a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="category-card text-center">
                    <div class="category-icon mb-3">
                        <i class="fas fa-headphones fa-3x text-danger"></i>
                    </div>
                    <h5>Phụ kiện</h5>
                    <p class="text-muted">Tai nghe, sạc, ốp lưng...</p>
                    <a href="/category/phu-kien" class="btn btn-outline-danger">Xem tất cả</a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="category-card text-center">
                    <div class="category-icon mb-3">
                        <i class="fas fa-home fa-3x text-danger"></i>
                    </div>
                    <h5>Smarthome</h5>
                    <p class="text-muted">Camera, bóng đèn thông minh...</p>
                    <a href="/category/smarthome" class="btn btn-outline-danger">Xem tất cả</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="featured-products py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Sản phẩm nổi bật</h2>
            <a href="/products?featured=1" class="btn btn-danger">Xem tất cả</a>
        </div>
        <div class="row g-4">
            <?php if (!empty($featuredProducts)): ?>
                <?php foreach ($featuredProducts as $product): ?>
                    <div class="col-lg-3 col-md-6">
                        <div class="product-card card h-100">
                            <div class="product-image">
                                <img src="<?= BASE_URL ?>/public/<?= $product['image'] ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                                <div class="product-badges">
                                    <?php if ($product['original_price'] && $product['original_price'] > $product['price']): ?>
                                        <span class="badge bg-danger">Giảm giá</span>
                                    <?php endif; ?>
                                    <?php if ($product['is_featured']): ?>
                                        <span class="badge bg-warning">Nổi bật</span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-actions">
                                    <button class="btn btn-sm btn-light wishlist-btn" data-product-id="<?= $product['id'] ?>">
                                        <i class="far fa-heart"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger quick-view-btn" data-product-id="<?= $product['id'] ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title"><?= htmlspecialchars($product['name']) ?></h6>
                                <p class="text-muted small mb-2"><?= htmlspecialchars($product['brand']) ?></p>
                                <div class="price-section mb-3">
                                    <span class="h5 text-danger fw-bold"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
                                    <?php if ($product['original_price'] && $product['original_price'] > $product['price']): ?>
                                        <span class="text-muted text-decoration-line-through ms-2"><?= number_format($product['original_price'], 0, ',', '.') ?>đ</span>
                                    <?php endif; ?>
                                </div>
                                <div class="mt-auto">
                                    <?php if ($product['stock'] > 0): ?>
                                        <a href="/product/<?= $product['slug'] ?>" class="btn btn-danger w-100">
                                            <i class="fas fa-shopping-cart me-2"></i>Mua ngay
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-secondary w-100" disabled>
                                            <i class="fas fa-times me-2"></i>Hết hàng
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted">Chưa có sản phẩm nổi bật</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Latest Products -->
<section class="latest-products py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Sản phẩm mới nhất</h2>
            <a href="/products" class="btn btn-danger">Xem tất cả</a>
        </div>
        <div class="row g-4">
            <?php if (!empty($latestProducts)): ?>
                <?php foreach ($latestProducts as $product): ?>
                    <div class="col-lg-3 col-md-6">
                        <div class="product-card card h-100">
                            <div class="product-image">
                                <img src="<?= BASE_URL ?>/public/<?= $product['image'] ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                                <div class="product-badges">
                                    <span class="badge bg-success">Mới</span>
                                </div>
                                <div class="product-actions">
                                    <button class="btn btn-sm btn-light wishlist-btn" data-product-id="<?= $product['id'] ?>">
                                        <i class="far fa-heart"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger quick-view-btn" data-product-id="<?= $product['id'] ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title"><?= htmlspecialchars($product['name']) ?></h6>
                                <p class="text-muted small mb-2"><?= htmlspecialchars($product['brand']) ?></p>
                                <div class="price-section mb-3">
                                    <span class="h5 text-danger fw-bold"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
                                    <?php if ($product['original_price'] && $product['original_price'] > $product['price']): ?>
                                        <span class="text-muted text-decoration-line-through ms-2"><?= number_format($product['original_price'], 0, ',', '.') ?>đ</span>
                                    <?php endif; ?>
                                </div>
                                <div class="mt-auto">
                                    <?php if ($product['stock'] > 0): ?>
                                        <a href="/product/<?= $product['slug'] ?>" class="btn btn-danger w-100">
                                            <i class="fas fa-shopping-cart me-2"></i>Mua ngay
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-secondary w-100" disabled>
                                            <i class="fas fa-times me-2"></i>Hết hàng
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted">Chưa có sản phẩm mới</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services-section py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3 col-md-6 text-center">
                <div class="service-item">
                    <div class="service-icon mb-3">
                        <i class="fas fa-shipping-fast fa-2x text-danger"></i>
                    </div>
                    <h6>Giao hàng nhanh</h6>
                    <p class="text-muted small">Giao hàng trong 2-4 giờ tại TP.HCM</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 text-center">
                <div class="service-item">
                    <div class="service-icon mb-3">
                        <i class="fas fa-shield-alt fa-2x text-danger"></i>
                    </div>
                    <h6>Bảo hành chính hãng</h6>
                    <p class="text-muted small">100% hàng chính hãng, bảo hành uy tín</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 text-center">
                <div class="service-item">
                    <div class="service-icon mb-3">
                        <i class="fas fa-undo fa-2x text-danger"></i>
                    </div>
                    <h6>Đổi trả dễ dàng</h6>
                    <p class="text-muted small">Đổi trả trong 7 ngày nếu không hài lòng</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 text-center">
                <div class="service-item">
                    <div class="service-icon mb-3">
                        <i class="fas fa-headset fa-2x text-danger"></i>
                    </div>
                    <h6>Hỗ trợ 24/7</h6>
                    <p class="text-muted small">Hotline 1900.2091 hỗ trợ mọi lúc</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/layouts/main.php'; ?>