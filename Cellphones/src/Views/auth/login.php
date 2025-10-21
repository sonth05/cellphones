<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - CellphoneS</title>
    <link href="/public/css/styles.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
            margin: 20px;
        }
        
        .login-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .login-header h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
        }
        
        .login-header p {
            margin: 10px 0 0;
            opacity: 0.9;
        }
        
        .login-form {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #007bff;
        }
        
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
        }
        
        .form-links {
            text-align: center;
            margin-top: 20px;
        }
        
        .form-links a {
            color: #007bff;
            text-decoration: none;
            margin: 0 10px;
        }
        
        .form-links a:hover {
            text-decoration: underline;
        }
        
        .divider {
            text-align: center;
            margin: 20px 0;
            position: relative;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e9ecef;
        }
        
        .divider span {
            background: white;
            padding: 0 15px;
            color: #666;
        }
        
        .role-selector {
            display: flex;
            margin-bottom: 20px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .role-option {
            flex: 1;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: white;
            border: none;
        }
        
        .role-option.active {
            background: #007bff;
            color: white;
        }
        
        .role-option:hover {
            background: #f8f9fa;
        }
        
        .role-option.active:hover {
            background: #0056b3;
        }
        
        .flash-message {
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .flash-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .flash-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .flash-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fas fa-mobile-alt"></i> CellphoneS</h1>
            <p>Đăng nhập vào tài khoản của bạn</p>
        </div>
        
        <div class="login-form">
            <?php if (isset($_SESSION['flash'])): ?>
                <?php foreach ($_SESSION['flash'] as $type => $message): ?>
                    <div class="flash-message flash-<?= $type ?>">
                        <?= $message ?>
                    </div>
                <?php endforeach; ?>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>
            
            <!-- Role Selector -->
            <div class="role-selector">
                <button type="button" class="role-option active" data-role="customer">
                    <i class="fas fa-user"></i><br>
                    <small>Khách hàng</small>
                </button>
                <button type="button" class="role-option" data-role="staff">
                    <i class="fas fa-user-tie"></i><br>
                    <small>Nhân viên</small>
                </button>
                <button type="button" class="role-option" data-role="manager">
                    <i class="fas fa-crown"></i><br>
                    <small>Quản lý</small>
                </button>
            </div>
            
            <form method="POST" action="/login">
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input type="email" id="email" name="email" class="form-control" 
                           placeholder="Nhập email của bạn" required
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Mật khẩu
                    </label>
                    <input type="password" id="password" name="password" class="form-control" 
                           placeholder="Nhập mật khẩu" required>
                </div>
                
                <input type="hidden" id="selected-role" name="role" value="customer">
                
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Đăng nhập
                </button>
            </form>
            
            <div class="form-links">
                <a href="/forgot-password">Quên mật khẩu?</a>
                <a href="/register">Đăng ký tài khoản</a>
            </div>
            
            <div class="divider">
                <span>hoặc</span>
            </div>
            
            <div class="text-center">
                <a href="/" class="btn btn-outline" style="display: inline-block; padding: 10px 20px; text-decoration: none;">
                    <i class="fas fa-home"></i> Về trang chủ
                </a>
            </div>
        </div>
    </div>

    <script>
        // Role selector functionality
        document.querySelectorAll('.role-option').forEach(option => {
            option.addEventListener('click', function() {
                // Remove active class from all options
                document.querySelectorAll('.role-option').forEach(opt => opt.classList.remove('active'));
                
                // Add active class to clicked option
                this.classList.add('active');
                
                // Update hidden input
                document.getElementById('selected-role').value = this.dataset.role;
            });
        });
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            
            if (!email || !password) {
                e.preventDefault();
                alert('Vui lòng nhập đầy đủ thông tin');
                return;
            }
            
            if (!email.includes('@')) {
                e.preventDefault();
                alert('Email không hợp lệ');
                return;
            }
        });
    </script>
</body>
</html>