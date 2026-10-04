# Chạy dự án quản lý lớp học và Menu

Môi trường cục bộ đã chuẩn bị: XAMPP tại `C:\xampp` (PHP 8.2.12), PHP 8.4.24 riêng tại `.tools/php-8.4.24`, Composer tại `.tools/composer.phar`. Dự án giữ Laravel 13 theo `composer.lock`; PHP của XAMPP chưa đủ phiên bản để chạy dự án này. Dùng các lệnh bên dưới để luôn chọn đúng PHP.

## Khởi động

Mở terminal trong thư mục dự án và chạy:

```powershell
.\start.cmd
```

Truy cập:

- Lớp học: http://127.0.0.1:8000/lop-hocs
- Menu: http://127.0.0.1:8000/menus
- Sinh viên: http://127.0.0.1:8000/sinhvien
- Tổng quan: http://127.0.0.1:8000

Cơ sở dữ liệu cục bộ dùng SQLite tại `database/database.sqlite`, theo cấu hình mặc định của mã nguồn. Không cần bật MySQL/Apache trong XAMPP khi chạy bằng `start.cmd`.

## Lệnh thường dùng

```powershell
.\php.cmd artisan migrate
.\php.cmd artisan test
.\composer.cmd install
.\npm.cmd run build
```

File `.env`, cơ sở dữ liệu, thư viện `vendor`, `node_modules` và công cụ `.tools` được bỏ qua trong Git. Khi chuyển sang máy khác, cần cài PHP >= 8.3 cùng Composer, chạy `composer install`, sao chép `.env.example` thành `.env`, chạy `php artisan key:generate` và `php artisan migrate`. Các file `.cmd` tại đây dành cho môi trường đã chuẩn bị trên máy này.

## Các chức năng

- Lớp học: thêm/sửa/xóa theo luồng cũ; tìm tên lớp, mã lớp, giáo viên, số điện thoại; lọc trạng thái và khoảng sĩ số; sắp xếp tăng/giảm; phân trang 5/10/20 bản ghi và giữ bộ lọc.
- Menu: thêm/sửa/xóa; slug duy nhất, tên hiển thị và trạng thái; tìm kiếm, lọc trạng thái, sắp xếp và phân trang. Slug dùng chữ thường không dấu, số và dấu gạch ngang giữa các từ.
- Bảng `menus` có đúng bốn cột `id`, `slug`, `tenhienthi`, `trangthai`; model tắt timestamps tương ứng.
- Quản lý Menu mở qua mục "Quản lý Menu" trong thanh bên. Các bản ghi Menu được quản lý riêng; thanh điều hướng sẵn có được giữ nguyên.

Kiểm thử tự động dùng SQLite trong bộ nhớ, không thay đổi dữ liệu đang sử dụng.

## Phần hoàn thiện bổ sung

- Sinh viên lưu trong bảng `sinh_viens` gồm ID tự tăng, tên (`name`), tuổi (`age`), lớp (`lop_hoc_id`) và timestamps. Giữ tên trường `name`, `age` của form bài học cũ; thay ô nhập lớp tự do bằng danh sách lớp đã có để liên kết đúng dữ liệu.
- Thêm, sửa, xóa, xem chi tiết sinh viên; chuyển sinh viên sang lớp khác bằng form sửa. Validation và `old()` giữ thông tin đã nhập khi có lỗi.
- Tìm sinh viên theo tên, tên/mã lớp; lọc lớp và khoảng tuổi; sắp xếp tăng/giảm và phân trang 5/10/20 bản ghi.
- Trang chi tiết lớp hiển thị thông tin lớp và danh sách sinh viên phân trang; có nút thêm sinh viên với lớp được chọn sẵn.
- Không cho xóa lớp còn sinh viên. Chuyển sinh viên sang lớp khác hoặc xóa sinh viên trước; khóa ngoại cũng bảo vệ liên kết trong cơ sở dữ liệu.
- Giữ `si_so` là sĩ số khai báo trong luồng lớp học cũ; số sinh viên đã nhập được hiển thị riêng, không tự sửa sĩ số khai báo.
- Trang chủ là tổng quan số lớp, lớp hoạt động, sinh viên và Menu. Thanh bên chỉ dẫn đến các chức năng hiện có, đánh dấu mục đang mở.
- Giữ đường dẫn `/sinhvien/add`, `/sinhvien/show/{id}` và ví dụ `/sinhvien/show2/{name}/{tuoi}`. Không dùng dữ liệu mẫu trong mảng hoặc `dd()` để lưu sinh viên.

Luồng chính: Route → Controller → `$request->validate()` → Eloquent → Blade hoặc `redirect()->route()->with('success', ...)`. Form thêm/sửa dùng chung như phần lớp học; form xóa dùng CSRF và `@method('DELETE')`. Liên kết dữ liệu dùng `belongsTo` / `hasMany`, tải kèm quan hệ bằng `with()` và đếm bằng `withCount()`.
