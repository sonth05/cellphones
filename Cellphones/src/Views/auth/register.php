<?php
$title = 'Đăng ký - CellphoneS';
$content = ob_get_clean();
ob_start();
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <img src="<?= BASE_URL ?>/public/images/logo.png" alt="CellphoneS" height="50" class="mb-3">
                        <h3 class="fw-bold">Đăng ký tài khoản</h3>
                        <p class="text-muted">Tạo tài khoản để mua sắm dễ dàng hơn</p>
                    </div>

                    <form method="POST" action="/register" id="registerForm">
                        <div class="mb-3">
                            <label for="name" class="form-label">Họ và tên *</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" class="form-control" id="name" name="name" 
                                       placeholder="Nhập họ và tên" required 
                                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="Nhập email của bạn" required 
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-phone"></i>
                                </span>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       placeholder="Nhập số điện thoại" 
                                       value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu *</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Nhập mật khẩu" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">
                                Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ cái và số
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Xác nhận mật khẩu *</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" 
                                       placeholder="Nhập lại mật khẩu" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    Tôi đồng ý với <a href="/terms" target="_blank">Điều khoản sử dụng</a> 
                                    và <a href="/privacy" target="_blank">Chính sách bảo mật</a>
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="newsletter">
                                <label class="form-check-label" for="newsletter">
                                    Nhận thông báo về sản phẩm mới và ưu đãi
                                </label>
                            </div>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-danger btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Đăng ký
                            </button>
                        </div>

                        <div class="text-center">
                            <p class="mb-0">Đã có tài khoản? 
                                <a href="/login" class="text-decoration-none fw-bold">Đăng nhập ngay</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle password visibility
document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordField = document.getElementById('password');
    const icon = this.querySelector('i');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});

document.getElementById('togglePasswordConfirm').addEventListener('click', function() {
    const passwordField = document.getElementById('password_confirm');
    const icon = this.querySelector('i');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});

// Form validation
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password_confirm').value;
    
    if (password !== passwordConfirm) {
        e.preventDefault();
        alert('Mật khẩu xác nhận không khớp!');
        return false;
    }
    
    if (password.length < 8) {
        e.preventDefault();
        alert('Mật khẩu phải có ít nhất 8 ký tự!');
        return false;
    }
    
    if (!/(?=.*[A-Za-z])(?=.*\d)/.test(password)) {
        e.preventDefault();
        alert('Mật khẩu phải bao gồm chữ cái và số!');
        return false;
    }
});
</script>

<?php require __DIR__ . '/../../layouts/main.php'; ?>