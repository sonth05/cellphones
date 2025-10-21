<!-- Hero Banner -->
<section class="hero-banner">
    <div class="container">
        <div class="hero-content">
            <h1>Điện thoại, laptop, tablet chính hãng</h1>
            <p>Giá tốt nhất - Giao hàng miễn phí - Bảo hành chính hãng</p>
            <a href="/products" class="btn btn-primary">Mua ngay</a>
        </div>
    </div>
</section>

<!-- Categories -->
<section class="categories-section">
    <div class="container">
        <h2>Danh mục sản phẩm</h2>
        <div class="categories-grid">
            <?php foreach ($categories as $category): ?>
                <div class="category-card">
                    <a href="/category?slug=<?= $category['slug'] ?>">
                        <div class="category-image">
                            <img src="<?= $category['image'] ?? '/public/images/category-placeholder.jpg' ?>" alt="<?= htmlspecialchars($category['name']) ?>">
                        </div>
                        <h3><?= htmlspecialchars($category['name']) ?></h3>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="featured-products">
    <div class="container">
        <h2>Sản phẩm nổi bật</h2>
        <div class="products-grid">
            <?php foreach ($featuredProducts as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <a href="/product?id=<?= $product['id'] ?>">
                            <img src="<?= $product['image'] ?? '/public/images/product-placeholder.jpg' ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        </a>
                        <?php if ($product['original_price'] && $product['original_price'] > $product['price']): ?>
                            <div class="product-discount">
                                <?= round((($product['original_price'] - $product['price']) / $product['original_price']) * 100) ?>%
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3><a href="/product?id=<?= $product['id'] ?>"><?= htmlspecialchars($product['name']) ?></a></h3>
                        <div class="product-price">
                            <span class="current-price"><?= number_format($product['price']) ?>₫</span>
                            <?php if ($product['original_price'] && $product['original_price'] > $product['price']): ?>
                                <span class="original-price"><?= number_format($product['original_price']) ?>₫</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-actions">
                            <button class="btn btn-primary add-to-cart" data-product-id="<?= $product['id'] ?>">
                                <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center">
            <a href="/products" class="btn btn-outline">Xem tất cả sản phẩm</a>
        </div>
    </div>
</section>

<!-- Promotions -->
<section class="promotions-section">
    <div class="container">
        <h2>Khuyến mãi hấp dẫn</h2>
        <div class="promotions-grid">
            <div class="promotion-card">
                <div class="promotion-image">
                    <img src="/public/images/promotion-1.jpg" alt="Khuyến mãi 1">
                </div>
                <div class="promotion-content">
                    <h3>Giảm giá lên đến 50%</h3>
                    <p>Áp dụng cho tất cả sản phẩm điện thoại</p>
                    <a href="/products?sale=1" class="btn btn-primary">Xem ngay</a>
                </div>
            </div>
            
            <div class="promotion-card">
                <div class="promotion-image">
                    <img src="/public/images/promotion-2.jpg" alt="Khuyến mãi 2">
                </div>
                <div class="promotion-content">
                    <h3>Trả góp 0%</h3>
                    <p>Mua điện thoại trả góp không lãi suất</p>
                    <a href="/products?installment=1" class="btn btn-primary">Tìm hiểu</a>
                </div>
            </div>
            
            <div class="promotion-card">
                <div class="promotion-image">
                    <img src="/public/images/promotion-3.jpg" alt="Khuyến mãi 3">
                </div>
                <div class="promotion-content">
                    <h3>Giao hàng miễn phí</h3>
                    <p>Miễn phí vận chuyển cho đơn hàng từ 500k</p>
                    <a href="/products" class="btn btn-primary">Mua ngay</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features -->
<section class="features-section">
    <div class="container">
        <div class="features-grid">
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <h3>Giao hàng nhanh</h3>
                <p>Giao hàng trong 24h tại TP.HCM và Hà Nội</p>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Bảo hành chính hãng</h3>
                <p>Bảo hành 12-24 tháng tại các trung tâm chính hãng</p>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <h3>Thanh toán linh hoạt</h3>
                <p>COD, chuyển khoản, thẻ tín dụng, ví điện tử</p>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>Hỗ trợ 24/7</h3>
                <p>Hotline 1800.2097 hỗ trợ khách hàng 24/7</p>
            </div>
        </div>
    </div>
</section>
