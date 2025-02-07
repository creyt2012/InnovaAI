# LM Studio Manager

<p align="center">
  <img src="public/logo.png" alt="LM Studio Manager Logo" width="200"/>
</p>

<p align="center">
  <a href="#features">Tính năng</a> •
  <a href="#installation">Cài đặt</a> •
  <a href="#usage">Sử dụng</a> •
  <a href="#development">Phát triển</a> •
  <a href="#contributing">Đóng góp</a>
</p>

LM Studio Manager là một giao diện web hiện đại cho LM Studio, cho phép bạn tương tác với các mô hình AI thông qua giao diện thân thiện. Dự án được xây dựng trên Laravel và tối ưu cho hiệu năng cao.

## ✨ Tính năng

- 🤖 Tích hợp với LM Studio API
- 💬 Chat với AI qua giao diện trực quan
- 🎨 Giao diện người dùng hiện đại với Tailwind CSS
- 🌙 Hỗ trợ Dark Mode
- 🎤 Voice Input (trên trình duyệt hỗ trợ)
- 📝 Markdown & Code Highlighting
- 💾 Lưu trữ lịch sử chat
- 📊 Theo dõi hiệu năng hệ thống
- 🔒 Bảo mật và xác thực người dùng

## 🚀 Cài đặt

### Yêu cầu hệ thống

- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL/MariaDB
- Redis
- LM Studio (đã cài đặt và chạy)

### Cài đặt trên aaPanel

bash
Clone repository
git clone https://github.com/your-username/lm-studio-manager.git
cd lm-studio-manager
Cấp quyền thực thi cho script cài đặt
chmod +x init.sh
Chạy script cài đặt
sudo ./init.sh



### Cài đặt thủ công

1. **Cài đặt dependencies**
bash
composer install
npm install

2. **Cấu hình môi trường**
bash
cp .env.example .env
php artisan key:generate

3. **Cấu hình database trong .env**
bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lm_studio_manager
DB_USERNAME=root
DB_PASSWORD=your_password

4. **Chạy migrations và seeders**
bash
php artisan migrate --seed

5. **Build assets**
bash
npm run build

6. **Khởi động server**
bash
php artisan serve

## 💻 Sử dụng

1. Truy cập http://localhost:8000
2. Đăng nhập với tài khoản mặc định:
   - Email: admin@example.com
   - Password: password
3. Bắt đầu chat với AI!

## 🛠 Phát triển

### Cấu trúc project
lm-studio-manager/
├── app/
│ ├── Http/Controllers/ # Controllers
│ ├── Services/ # Business logic
│ └── Models/ # Database models
├── resources/
│ └── views/ # Blade templates
├── routes/ # Route definitions
└── tests/ # Test cases


### Chạy tests
bash
php artisan test

### Style Guide

Project tuân thủ PSR-12 và sử dụng Laravel Pint cho code style:

bash
./vendor/bin/pint

## 🤝 Đóng góp

1. Fork project
2. Tạo branch mới (`git checkout -b feature/amazing-feature`)
3. Commit thay đổi (`git commit -m 'Add amazing feature'`)
4. Push lên branch (`git push origin feature/amazing-feature`)
5. Mở Pull Request

## 📝 License

Distributed under the MIT License. See `LICENSE` for more information.

## 🙏 Credits

- [Laravel](https://laravel.com)
- [LM Studio](https://lmstudio.ai)
- [Tailwind CSS](https://tailwindcss.com)

## 📞 Hỗ trợ

Nếu bạn gặp vấn đề, vui lòng:
1. Kiểm tra [Issues](https://github.com/your-username/lm-studio-manager/issues)
2. Tạo issue mới nếu vấn đề chưa được báo cáo
3. Liên hệ qua email: support@example.com

---

<p align="center">Made with ❤️ by Your Team</p>