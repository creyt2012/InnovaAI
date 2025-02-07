# InnovaAI Studio Manager

<p align="center">
  <img src="https://github.com/creyt2012/InnovaAI/blob/main/logo-search-grid.png" alt="InnovaAI Studio Manager Logo" width="200"/>
</p>

<p align="center">
  <a href="#features">Tính năng</a> •
  <a href="#installation">Cài đặt</a> •
  <a href="#usage">Hướng dẫn sử dụng</a> •
  <a href="#development">Phát triển</a> •
  <a href="#contributing">Đóng góp</a>
</p>

**InnovaAI Studio Manager** là một giao diện web hiện đại cho phép người dùng tương tác với các mô hình AI thông qua một nền tảng thân thiện với người dùng. Dự án này được xây dựng trên Laravel và tối ưu cho hiệu suất cao, giúp bạn dễ dàng sử dụng và khai thác các khả năng của LM Studio.

## ✨ Tính năng

- 🤖 **Tích hợp với LM Studio API** – Tương tác trực tiếp với các mô hình AI của LM Studio.
- 💬 **Chat với AI** – Tương tác với AI thông qua giao diện trò chuyện dễ sử dụng.
- 🎨 **Giao diện người dùng hiện đại** – Giao diện đẹp mắt, đáp ứng mọi nhu cầu với Tailwind CSS.
- 🌙 **Hỗ trợ chế độ tối** – Tùy chỉnh giữa chế độ sáng và tối để phù hợp với sở thích cá nhân.
- 🎤 **Nhập liệu bằng giọng nói** – Sử dụng giọng nói để giao tiếp với AI (tùy thuộc vào trình duyệt hỗ trợ).
- 📝 **Hỗ trợ Markdown & Highlighting mã** – Hiển thị nội dung với định dạng Markdown và nổi bật cú pháp mã.
- 💾 **Lưu trữ lịch sử chat** – Lưu lại lịch sử các cuộc trò chuyện với AI.
- 📊 **Giám sát hiệu suất hệ thống** – Theo dõi và hiển thị tình trạng hệ thống.
- 🔒 **Bảo mật và xác thực người dùng** – Đảm bảo quyền truy cập và bảo mật người dùng.

## 🚀 Cài đặt

### Yêu cầu hệ thống

- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL/MariaDB
- Redis
- LM Studio (đã cài đặt và chạy)

### Cài đặt trên aaPanel

Để cài đặt trên aaPanel, làm theo các bước sau:

```bash
# Clone repository
git clone https://github.com/creyt2012/InnovaAI.git
cd InnovaAI

# Cấp quyền thực thi cho script cài đặt
chmod +x init.sh

# Chạy script cài đặt
sudo ./init.sh

```
### Cài đặt thủ công
# Cài đặt dependencies
```bash 
composer install
npm install
```
# Cấu hình môi trường
```bash
cp .env.example .env
php artisan key:generate
```
# Cấu hình database trong .env
## Chỉnh sửa file .env và cấu hình kết nối với cơ sở dữ liệu:
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=innovaai
DB_USERNAME=root
DB_PASSWORD=your_password
```

# Chạy migrations và seeders
```bash
php artisan migrate --seed
```

# Build assets
```bash
npm run build
```

# Khởi động server
```bash
php artisan serve
```

## 💻 Hướng dẫn sử dụng
- Truy cập vào http://localhost:8000.
- Đăng nhập với tài khoản mặc định:
- Email: admin@example.com
- Mật khẩu: password
- Bắt đầu trò chuyện với AI!


## 🛠 Phát triển

# Cấu trúc dự án
```bash
innovaai/
├── app/
│ ├── Http/Controllers/        # Controllers xử lý các yêu cầu HTTP
│ ├── Services/                # Logic và dịch vụ xử lý nghiệp vụ
│ └── Models/                  # Các model tương tác với cơ sở dữ liệu
├── resources/
│ └── views/                   # Các template Blade
├── routes/                     # Định nghĩa các route
└── tests/                      # Các bài kiểm tra
```

## Chạy các bài kiểm tra
# Để chạy các bài kiểm tra, sử dụng lệnh sau:
```bash
php artisan test
```

## Quy chuẩn mã nguồn
- Dự án này tuân thủ chuẩn mã nguồn PSR-12 và sử dụng Laravel Pint để kiểm tra và làm đẹp mã nguồn:
```bash
  ./vendor/bin/pint
```

# 🤝 Đóng góp

#Chúng tôi hoan nghênh mọi đóng góp để phát triển InnovaAI Studio Manager! Đây là các bước bạn có thể thực hiện để đóng góp vào dự án:
- Fork repository.
- Tạo một branch mới (git checkout -b feature/amazing-feature).
- Commit thay đổi của bạn (git commit -m 'Add amazing feature').
- Push lên branch (git push origin feature/amazing-feature).
- Mở Pull Request.
# 📝 License

## Dự án này là mã nguồn mở và được phát hành dưới giấy phép MIT. Bạn có thể tham khảo thêm thông tin trong file LICENSE.


#🙏 Credits

- Laravel
- LM Studio
- Tailwind CSS
- 📞 Hỗ trợ : 0375001297

## Nếu bạn gặp bất kỳ vấn đề gì hoặc cần hỗ trợ, vui lòng liên hệ: thông qua link fb ở dưới
- Kiểm tra phần Issues để xem các vấn đề đã được báo cáo.
- Nếu vấn đề của bạn chưa có trong đó, hãy tạo một Issue mới.
- Liên hệ với chúng tôi qua email: mortarcloud@gmail.com
<p align="center">Made with ❤️ by Nguyễn Thành Biên</p> <p align="center"> Theo dõi tôi trên Facebook: <a href="https://www.facebook.com/Creyt.deptrai/" target="_blank">Creyt.deptrai</a> </p> ```






