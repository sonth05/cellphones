# Hệ thống quản lý bán hàng CellphoneS

Hệ thống e-commerce được phát triển dựa trên giao diện Cellphones với hệ thống phân quyền đầy đủ cho khách hàng, nhân viên và quản lý.

## 🚀 Tính năng chính

### 👥 Hệ thống phân quyền
- **Khách hàng**: Mua sản phẩm trên giao diện giống Cellphones
- **Nhân viên**: Quản lý sản phẩm, đơn hàng, khách hàng
- **Quản lý**: Xem báo cáo, thống kê, quản lý toàn bộ hệ thống

### 🛍️ Giao diện khách hàng
- Trang chủ với sản phẩm nổi bật
- Danh mục sản phẩm đầy đủ
- Tìm kiếm và lọc sản phẩm
- Giỏ hàng và thanh toán
- Quản lý tài khoản cá nhân

### ⚙️ Hệ thống quản trị
- Dashboard với thống kê tổng quan
- Quản lý sản phẩm (CRUD)
- Quản lý đơn hàng
- Quản lý khách hàng
- Báo cáo và thống kê (chỉ quản lý)

## 🛠️ Cài đặt

### Yêu cầu hệ thống
- PHP 7.4+
- MySQL 5.7+
- Web server (Apache/Nginx)

### Cài đặt database
```sql
-- Import file database/schema.sql vào MySQL
mysql -u root -p < database/schema.sql
```

### Cấu hình
1. Sao chép file `config.php` và cập nhật thông tin database
2. Cấu hình web server trỏ document root đến thư mục dự án
3. Đảm bảo thư mục `public/` có quyền ghi

### Chạy ứng dụng
```bash
# Sử dụng PHP built-in server (development)
php -S localhost:8000

# Hoặc cấu hình Apache/Nginx
```

## 👤 Tài khoản demo

### Quản lý
- **Email**: admin@cellphones.vn
- **Mật khẩu**: admin123
- **Quyền**: Toàn quyền quản lý hệ thống

### Nhân viên
- **Email**: staff@cellphones.vn  
- **Mật khẩu**: admin123
- **Quyền**: Quản lý sản phẩm, đơn hàng, khách hàng

### Khách hàng
- Đăng ký tài khoản mới tại `/register`
- Hoặc sử dụng tài khoản có sẵn trong database

## 📁 Cấu trúc dự án

```
├── config.php                 # Cấu hình database
├── index.php                  # Entry point chính
├── demo.php                   # Trang demo hệ thống
├── database/
│   └── schema.sql            # Schema database
├── src/
│   ├── Controllers/         # Controllers
│   ├── Models/              # Models
│   ├── Views/               # Views
│   ├── Middleware/          # Middleware (Auth)
│   └── Lib/                 # Libraries
├── public/
│   ├── css/                 # Stylesheets
│   ├── js/                  # JavaScript
│   └── images/              # Images
├── mirror/                  # Giao diện gốc Cellphones
└── scrapers/                # Tools scraping
```

## 🔐 Hệ thống phân quyền

### Khách hàng (Customer)
- Xem sản phẩm, danh mục
- Thêm vào giỏ hàng
- Đặt hàng và thanh toán
- Quản lý tài khoản cá nhân
- Xem lịch sử đơn hàng

### Nhân viên (Staff)
- Tất cả quyền của khách hàng
- Quản lý sản phẩm (CRUD)
- Quản lý đơn hàng
- Quản lý khách hàng
- Xem dashboard

### Quản lý (Manager)
- Tất cả quyền của nhân viên
- Xem báo cáo và thống kê
- Quản lý nhân viên
- Cấu hình hệ thống

## 🎨 Giao diện

### Khách hàng
- Thiết kế dựa trên giao diện Cellphones
- Responsive design
- Tối ưu cho mobile
- Trải nghiệm mua sắm mượt mà

### Quản trị
- Dashboard hiện đại
- Charts và thống kê trực quan
- Quản lý dễ dàng
- Phân quyền rõ ràng

## 🚀 Sử dụng

1. **Truy cập trang demo**: `http://localhost/demo.php`
2. **Giao diện khách hàng**: `http://localhost/`
3. **Hệ thống quản trị**: `http://localhost/admin`
4. **Giao diện gốc**: `http://localhost/mirror`

## 📊 Tính năng nâng cao

- **Phân quyền động**: Kiểm tra quyền truy cập real-time
- **Responsive design**: Tương thích mọi thiết bị
- **Security**: Bảo mật session và CSRF protection
- **Performance**: Tối ưu database queries
- **Scalability**: Kiến trúc MVC dễ mở rộng

## 🔧 Phát triển

### Thêm tính năng mới
1. Tạo Controller trong `src/Controllers/`
2. Tạo Model trong `src/Models/`
3. Tạo View trong `src/Views/`
4. Cập nhật routes trong `index.php`

### Thêm middleware
1. Tạo file trong `src/Middleware/`
2. Import và sử dụng trong Controller

### Customize giao diện
1. Chỉnh sửa CSS trong `public/css/`
2. Cập nhật JavaScript trong `public/js/`
3. Modify templates trong `src/Views/`

## 📝 Ghi chú

- Hệ thống sử dụng PHP thuần, không framework
- Database MySQL với PDO
- Session-based authentication
- MVC architecture
- Responsive Bootstrap-based UI

## 🤝 Đóng góp

1. Fork dự án
2. Tạo feature branch
3. Commit changes
4. Push to branch
5. Tạo Pull Request

## 📄 License

MIT License - Xem file LICENSE để biết thêm chi tiết.