<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'CellphoneS - Điện thoại, laptop, tablet, phụ kiện chính hãng' ?></title>
    <meta name="description" content="<?= $description ?? 'Hệ thống 154 cửa hàng bán lẻ điện thoại, máy tính laptop, smartwatch, gia dụng, thiết bị IT, phụ kiện chính hãng - Giá tốt, trả góp 0%, giao miễn phí.' ?>">
    
    <!-- CSS -->
    <link href="/public/css/styles.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <!-- Top bar -->
            <div class="header-top">
                <div class="header-top-left">
                    <span><i class="fas fa-phone"></i> Hotline: 1800.2097</span>
                    <span><i class="fas fa-map-marker-alt"></i> 154 cửa hàng</span>
                </div>
                <div class="header-top-right">
                    <?php if (\App\Middleware\AuthMiddleware::isLoggedIn()): ?>
                        <?php $user = \App\Middleware\AuthMiddleware::getUser(); ?>
                        <div class="user-menu">
                            <span>Xin chào, <?= htmlspecialchars($user['name']) ?></span>
                            <?php if ($user['role'] === 'customer'): ?>
                                <a href="/profile">Tài khoản</a>
                                <a href="/orders">Đơn hàng</a>
                            <?php else: ?>
                                <a href="/admin">Quản trị</a>
                            <?php endif; ?>
                            <a href="/logout">Đăng xuất</a>
                        </div>
                    <?php else: ?>
                        <a href="/login">Đăng nhập</a>
                        <a href="/register">Đăng ký</a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Main header -->
            <div class="header-main">
                <div class="header-logo">
                    <a href="/">
                        <img src="/public/images/logo.png" alt="CellphoneS" height="50">
                    </a>
                </div>
                
                <div class="header-search">
                    <form action="/search" method="GET">
                        <input type="text" name="q" placeholder="Tìm kiếm sản phẩm..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                
                <div class="header-actions">
                    <a href="/cart" class="cart-link">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count">0</span>
                    </a>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="header-nav">
                <ul class="nav-menu">
                    <li><a href="/">Trang chủ</a></li>
                    <li class="dropdown">
                        <a href="/products">Sản phẩm <i class="fas fa-chevron-down"></i></a>
                        <div class="dropdown-menu">
                            <?php if (isset($categories)): ?>
                                <?php foreach ($categories as $category): ?>
                                    <a href="/category?slug=<?= $category['slug'] ?>"><?= htmlspecialchars($category['name']) ?></a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </li>
                    <li><a href="/products?featured=1">Sản phẩm nổi bật</a></li>
                    <li><a href="/products?sale=1">Khuyến mãi</a></li>
                    <li><a href="/about">Giới thiệu</a></li>
                    <li><a href="/contact">Liên hệ</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main content -->
    <main class="main-content">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Về CellphoneS</h3>
                    <ul>
                        <li><a href="/about">Giới thiệu</a></li>
                        <li><a href="/careers">Tuyển dụng</a></li>
                        <li><a href="/news">Tin tức</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Hỗ trợ khách hàng</h3>
                    <ul>
                        <li><a href="/help">Trung tâm hỗ trợ</a></li>
                        <li><a href="/shipping">Chính sách vận chuyển</a></li>
                        <li><a href="/return">Chính sách đổi trả</a></li>
                        <li><a href="/warranty">Bảo hành</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Liên hệ</h3>
                    <ul>
                        <li><i class="fas fa-phone"></i> 1800.2097</li>
                        <li><i class="fas fa-envelope"></i> support@cellphones.vn</li>
                        <li><i class="fas fa-map-marker-alt"></i> 154 cửa hàng toàn quốc</li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Kết nối với chúng tôi</h3>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 CellphoneS. Tất cả quyền được bảo lưu.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="/public/js/main.js"></script>
</body>
</html>
