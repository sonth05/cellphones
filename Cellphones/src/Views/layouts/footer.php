    <!-- Footer -->
    <footer class="footer bg-dark text-white mt-5">
        <div class="container py-5">
            <div class="row">
                <!-- Company Info -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="text-danger mb-3">
                        <img src="<?= BASE_URL ?>/public/images/logo-white.png" alt="CellphoneS" height="30" class="me-2">
                        CellphoneS
                    </h5>
                    <p class="mb-3">
                        Hệ thống bán lẻ điện thoại, laptop, phụ kiện công nghệ chính hãng với giá tốt nhất. 
                        Cam kết 100% hàng chính hãng, bảo hành uy tín.
                    </p>
                    <div class="social-links">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f fa-lg"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-youtube fa-lg"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-tiktok fa-lg"></i></a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="text-danger mb-3">Sản phẩm</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="/category/iphone" class="text-white text-decoration-none">iPhone</a></li>
                        <li class="mb-2"><a href="/category/samsung" class="text-white text-decoration-none">Samsung</a></li>
                        <li class="mb-2"><a href="/category/xiaomi" class="text-white text-decoration-none">Xiaomi</a></li>
                        <li class="mb-2"><a href="/category/macbook" class="text-white text-decoration-none">MacBook</a></li>
                        <li class="mb-2"><a href="/category/tai-nghe" class="text-white text-decoration-none">Tai nghe</a></li>
                    </ul>
                </div>
                
                <!-- Customer Service -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="text-danger mb-3">Hỗ trợ</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="/contact" class="text-white text-decoration-none">Liên hệ</a></li>
                        <li class="mb-2"><a href="/shipping" class="text-white text-decoration-none">Vận chuyển</a></li>
                        <li class="mb-2"><a href="/warranty" class="text-white text-decoration-none">Bảo hành</a></li>
                        <li class="mb-2"><a href="/returns" class="text-white text-decoration-none">Đổi trả</a></li>
                        <li class="mb-2"><a href="/faq" class="text-white text-decoration-none">Câu hỏi thường gặp</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <h6 class="text-danger mb-3">Thông tin liên hệ</h6>
                    <div class="contact-info">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-phone text-danger me-2"></i>
                            <span>Hotline: <strong>1900.2091</strong></span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-envelope text-danger me-2"></i>
                            <span>Email: <strong>support@cellphones.vn</strong></span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-map-marker-alt text-danger me-2"></i>
                            <span>Hệ thống 63 tỉnh thành</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-clock text-danger me-2"></i>
                            <span>8:00 - 22:00 (T2 - CN)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bottom Bar -->
        <div class="bottom-bar bg-danger py-3">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="mb-0 text-white">
                            © <?= date('Y') ?> CellphoneS. Tất cả quyền được bảo lưu.
                        </p>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="payment-methods">
                            <span class="text-white me-3">Chấp nhận thanh toán:</span>
                            <img src="<?= BASE_URL ?>/public/images/payment-methods.png" alt="Payment Methods" height="25">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="backToTop" class="btn btn-danger position-fixed bottom-0 end-0 m-4 rounded-circle" style="display: none; z-index: 1000;">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- Custom JavaScript -->
    <script>
        // Back to top button
        $(document).ready(function() {
            $(window).scroll(function() {
                if ($(this).scrollTop() > 100) {
                    $('#backToTop').fadeIn();
                } else {
                    $('#backToTop').fadeOut();
                }
            });
            
            $('#backToTop').click(function() {
                $('html, body').animate({scrollTop: 0}, 800);
                return false;
            });
        });
        
        // Auto hide alerts
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    </script>
</body>
</html>
