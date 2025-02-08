# InnovaAI Studio Manager

## 🚀 Giới thiệu

**InnovaAI Studio Manager** là một giao diện web hiện đại cho phép người dùng tương tác với các mô hình AI thông qua một nền tảng thân thiện với người dùng. Dự án này được xây dựng trên Laravel và tối ưu cho hiệu suất cao, giúp bạn dễ dàng sử dụng và khai thác các khả năng của LM Studio.

---

## ✨ [Tính năng](#-tính-năng)

- 🤖 **Tích hợp với LM Studio API** – Tương tác trực tiếp với các mô hình AI của LM Studio.
- 💬 **Chat với AI** – Giao tiếp với AI thông qua giao diện trò chuyện dễ sử dụng.
- 🎨 **Giao diện người dùng hiện đại** – Thiết kế đẹp mắt với Tailwind CSS.
- 🌙 **Hỗ trợ chế độ tối** – Tuỳ chỉnh giữa chế độ sáng và tối.
- 🎤 **Nhập liệu bằng giọng nói** – Sử dụng giọng nói để giao tiếp với AI.
- 📝 **Hỗ trợ Markdown & Highlighting mã** – Hiển thị nội dung với Markdown và nổi bật cú pháp mã.
- 💾 **Lưu trữ lịch sử chat** – Giữ lại lịch sử các cuộc trò chuyện với AI.
- 📊 **Giám sát hiệu suất hệ thống** – Theo dõi và hiển thị tình trạng hệ thống.
- 🔒 **Bảo mật và xác thực người dùng** – Đảm bảo quyền truy cập và bảo mật dữ liệu.

---

## 📥 [Cài đặt](#-cài-đặt)

### Yêu cầu hệ thống

- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL/MariaDB
- Redis
- LM Studio (đã cài đặt và chạy)

### Cài đặt trên aaPanel

Thực hiện các bước sau để cài đặt trên aaPanel:

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

Cài đặt các dependencies:

```bash
composer install
npm install
```

Cấu hình môi trường:

```bash
cp .env.example .env
php artisan key:generate
```

Cấu hình database trong `.env`:

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=innovaai
DB_USERNAME=root
DB_PASSWORD=your_password
```

Chạy migrations và seeders:

```bash
php artisan migrate --seed
```

Build assets:

```bash
npm run build
```

Khởi động server:

```bash
php artisan serve
```

### Cài đặt với Docker Compose

Nếu bạn muốn chạy ứng dụng bằng Docker, hãy sử dụng `docker-compose`:

```bash
# Khởi động dịch vụ
docker-compose up -d
```

Cấu trúc file `docker-compose.yml`:

```yaml
version: '3.8'

services:
  app:
    build: .
    ports:
      - "8000:8000"
    depends_on:
      - db
    environment:
      DB_HOST: db
      DB_DATABASE: innovaai
      DB_USERNAME: root
      DB_PASSWORD: your_password
  
  db:
    image: mysql:latest
    restart: always
    environment:
      MYSQL_ROOT_PASSWORD: your_password
      MYSQL_DATABASE: innovaai
    ports:
      - "3306:3306"
  
  redis:
    image: redis:latest
    restart: always
    ports:
      - "6379:6379"
```

---

## 💻 [Hướng dẫn sử dụng](#-hướng-dẫn-sử-dụng)

- Truy cập vào [http://localhost:8000](http://localhost:8000).
- Đăng nhập với tài khoản mặc định:
  - **Email:** [admin@example.com](mailto\:admin@example.com)
  - **Mật khẩu:** password
- Bắt đầu trò chuyện với AI!

---

## 🛠 [Phát triển](#-phát-triển)

### Cấu trúc dự án

```bash
innovaai/
├── app/
│   ├── Http/Controllers/        # Controllers xử lý các yêu cầu HTTP
│   ├── Services/                # Logic và dịch vụ xử lý nghiệp vụ
│   └── Models/                  # Các model tương tác với cơ sở dữ liệu
├── resources/
│   └── views/                   # Các template Blade
├── routes/                      # Định nghĩa các route
└── tests/                       # Các bài kiểm tra
```

### Chạy các bài kiểm tra

```bash
php artisan test
```

### Quy chuẩn mã nguồn

Dự án tuân thủ chuẩn mã nguồn **PSR-12** và sử dụng Laravel Pint để kiểm tra và làm đẹp mã nguồn:

```bash
./vendor/bin/pint
```

---

## 🤝 [Đóng góp](#-đóng-góp)

Chúng tôi hoan nghênh mọi đóng góp để phát triển **InnovaAI Studio Manager**! Để đóng góp:

1. **Fork repository**.
2. **Tạo branch mới**: `git checkout -b feature/amazing-feature`.
3. **Commit thay đổi**: `git commit -m 'Add amazing feature'`.
4. **Push lên branch**: `git push origin feature/amazing-feature`.
5. **Mở Pull Request**.

---

## 📝 License

Dự án này là mã nguồn mở và được phát hành dưới giấy phép **MIT**. Tham khảo thêm thông tin trong file `LICENSE`.

---

## 🔥 Khuyến khích sử dụng các model

Chúng tôi khuyến nghị sử dụng các model sau để có trải nghiệm AI tốt nhất:

- **Qwen2.5 7B Instruct 1M**: [Tải xuống](https://huggingface.co/lmstudio-community/Qwen2.5-7B-Instruct-1M-GGUF)
- **DeepSeek R1 Distill (Llama 8B)**: [Tải xuống](https://huggingface.co/lmstudio-community/DeepSeek-R1-Distill-Llama-8B-GGUF)
- **Llama 3.3 70B Instruct**: [Tải xuống](https://huggingface.co/lmstudio-community/Llama-3.3-70B-Instruct-GGUF)

**Khuyến cáo:** Các node LM Studio nên sử dụng **CPU 16 core - 32GB RAM, khoảng 2GB RAM GPU** để đạt hiệu suất tối ưu.

---

## 🙏 Credits

- **Laravel**
- **LM Studio**
- **Tailwind CSS**

📞 Hỗ trợ: **0375001297**

Nếu bạn gặp bất kỳ vấn đề gì hoặc cần hỗ trợ, vui lòng:

- Kiểm tra phần [Issues](https://github.com/creyt2012/InnovaAI/issues) để xem các vấn đề đã được báo cáo.
- Nếu vấn đề của bạn chưa có trong đó, hãy tạo một **Issue** mới.
- Liên hệ qua email: **[mortarcloud@gmail.com](mailto\:mortarcloud@gmail.com)**


---

## 👉 Hướng dẫn mở API LM Studio ra ngoài

Mặc định, LM Studio chỉ cho phép truy cập API trên local (localhost). Để mở API ra ngoài, hãy làm theo hướng dẫn sau:

1. **Mở LM Studio** và vào "Settings".
2. **Chọn "API"** và bật "Enable API".
3. **Thiết lập Bind Address**: 
   - Nhậu TCP host là `0.0.0.0` để cho phép truy cập từ bên ngoài.
   - Cấp nhật "API Port" (vd: `8080`).
4. **Khởi động lại LM Studio** để áp dụng thay đổi.

Bây giờ, bạn có thể truy cập API từ máy khác qua `http://<IP>:8080/v1/chat/completions`.

### 🛠️ Thêm API LM Studio vào dự án
Mở setting admin project Lavarel và bạn đã có thể thêm các nodes API LM STUDIO

Dự án đã sẵn sàng tích hợp với LM Studio API! 🚀
![GitHub All Releases](https://img.shields.io/github/downloads/{creyt2012}/{InnovaAI}/total)


