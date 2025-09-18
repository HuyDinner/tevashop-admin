-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3308
-- Thời gian đã tạo: Th9 18, 2025 lúc 01:09 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `du_an_mau_nhom_5_final`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `Email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `VaiTro` int(10) DEFAULT NULL,
  `DiaChi` varchar(50) DEFAULT NULL,
  `HoTen` varchar(50) NOT NULL,
  `Pass` int(10) NOT NULL,
  `phone` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `Email`, `password`, `created_at`, `VaiTro`, `DiaChi`, `HoTen`, `Pass`, `phone`) VALUES
(1, 'admin', 'minhhuy23082005@gmail.com', '$2y$10$PxSoyMMwloRUDnXVbFo0fuwAYiFSeC5CluT.fvm7hPgzPLWwz7PLi', '2025-07-25 08:02:22', NULL, NULL, '', 0, 0),
(2, 'admin2', 'huylvmps40034@gmail.com', '$2y$10$Zcxw2BI5Hb7qxnSwyuVyOO7FhaS7yQhQ7ApgpweiWhHOB7Yl9PHou', '2025-07-25 09:43:47', NULL, NULL, '', 0, 0),
(3, 'admin3', 'tuyuhkoii@gmail.com', '$2y$10$Xysegy3ZXxxmp7YiCPsWA.vExVy758adjj/QJbEdKx0jdsHeDKZ4y', '2025-07-25 10:00:05', NULL, NULL, '', 0, 0),
(4, 'admin113', 'tunguyenhuu1144@gmail.com', '$2y$10$t3RiPx/VNP8cHZWvE1VMu.GzGcpyrcKwo8cMCqtypO2/FSOnuVhGO', '2025-07-25 11:01:48', 0, NULL, '', 0, 0),
(5, '', 'teo@gmail.com', '202cb962ac59075b964b07152d234b70', '2025-08-11 09:35:04', NULL, 'Can Tho', 'Nguyen Thi Lan', 0, 0),
(7, NULL, 'ti@gmail.com', '202cb962ac59075b964b07152d234b70', '2025-08-11 09:40:58', NULL, 'Can Tho', 'Nguyen Thi Lan', 0, 0),
(8, NULL, 'tuan@gmail.com', '', '2025-08-11 09:44:45', 1, 'Ben Tre', 'Le Van Tuan', 202, 961091315),
(9, NULL, 'vietgg10@gmail.com', '', '2025-08-11 09:46:23', NULL, 'Can Tho', 'Nguyen Thi Lan', 202, 0),
(10, NULL, 'abc@gmail.com', '', '2025-08-11 09:50:24', NULL, 'Can Tho', 'Nguyen Thi Lan', 123, 0),
(11, NULL, 'chuvanan1stt@gmail.com', '', '2025-08-13 01:51:56', NULL, 'Tô Ký Q12', 'Chu Van An', 1253, 961091315),
(12, 'admin114', 'minhhuy11323@gmail.com', '$2y$10$B9t/ky8CWkrG01orR8OWsetQh8KfnDRFu4Bpm1IbF18yOeLnVskom', '2025-09-18 11:36:33', NULL, NULL, '', 0, 0),
(13, 'iamadmin', 'tester@gmail.com', '$2y$10$W4hL60nx9IAcafUHhvjKzeQir4quXYXztl7cpPcRtbOEi.GzCueMi', '2025-09-18 18:06:34', NULL, NULL, '', 0, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cuoctrochuyen`
--

CREATE TABLE `cuoctrochuyen` (
  `IDCuocTroChuyen` int(11) NOT NULL,
  `IDKhachHang` int(11) NOT NULL,
  `IDAdmin` int(11) DEFAULT NULL,
  `TrangThai` enum('mo','dong','cho_xu_ly') DEFAULT 'cho_xu_ly',
  `ThoiGianCapNhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `NoiDungTinNhanCuoi` text DEFAULT NULL,
  `ThoiGianTao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danhgia`
--

CREATE TABLE `danhgia` (
  `Id` int(11) NOT NULL,
  `MaSanPham` int(11) DEFAULT NULL,
  `MaKhachHang` int(11) DEFAULT NULL,
  `NgayTao` datetime DEFAULT current_timestamp(),
  `BinhLuan` text DEFAULT NULL,
  `SoLuong` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danhmuc`
--

CREATE TABLE `danhmuc` (
  `Id` int(11) NOT NULL,
  `TenDanhMuc` varchar(255) NOT NULL,
  `TrangThai` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `danhmuc`
--

INSERT INTO `danhmuc` (`Id`, `TenDanhMuc`, `TrangThai`) VALUES
(5, 'Nam', 1),
(6, 'Nữ', 1),
(7, 'Trẻ Em', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `doanhthuchitiet`
--

CREATE TABLE `doanhthuchitiet` (
  `id` int(11) NOT NULL,
  `MaDonHang` int(11) DEFAULT NULL,
  `NgayHoanThanh` date DEFAULT NULL,
  `TongTien` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `doanhthuchitiet`
--

INSERT INTO `doanhthuchitiet` (`id`, `MaDonHang`, `NgayHoanThanh`, `TongTien`) VALUES
(1, 6572, '2025-08-14', 1030000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `donhang`
--

CREATE TABLE `donhang` (
  `Id` int(11) NOT NULL,
  `MaKhachHang` int(11) DEFAULT NULL,
  `NgayDatHang` datetime NOT NULL DEFAULT current_timestamp(),
  `TongTien` decimal(10,2) DEFAULT NULL,
  `TrangThai` varchar(50) DEFAULT NULL,
  `TenNguoiNhan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DiaChiNguoiNhan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `SDTNguoiNhan` varchar(20) DEFAULT NULL,
  `EmailNguoiNhan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `donhang`
--

INSERT INTO `donhang` (`Id`, `MaKhachHang`, `NgayDatHang`, `TongTien`, `TrangThai`, `TenNguoiNhan`, `DiaChiNguoiNhan`, `SDTNguoiNhan`, `EmailNguoiNhan`) VALUES
(6572, 11, '2025-08-14 11:27:16', 1030000.00, 'Đã giao hàng', 'Minh Huy', 'Tô Ký', '0354632555', 'minhhuy23082004@gmail.com');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `donhangchitiet`
--

CREATE TABLE `donhangchitiet` (
  `Id` int(11) NOT NULL,
  `MaDonHang` int(11) DEFAULT NULL,
  `MaSanPham` int(11) DEFAULT NULL,
  `SoLuong` int(11) DEFAULT NULL,
  `Gia` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khachhang`
--

CREATE TABLE `khachhang` (
  `Id` int(11) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `TenKhachHang` varchar(255) DEFAULT NULL,
  `SoDienThoai` varchar(20) DEFAULT NULL,
  `DiaChi` text DEFAULT NULL,
  `TrangThai` tinyint(1) DEFAULT NULL,
  `VaiTro` int(11) NOT NULL,
  `NgayDangKy` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `khachhang`
--

INSERT INTO `khachhang` (`Id`, `Email`, `Password`, `TenKhachHang`, `SoDienThoai`, `DiaChi`, `TrangThai`, `VaiTro`, `NgayDangKy`) VALUES
(7, 'chuvanan2stt@gmail.com', '1253', 'Trước Khi Đặt', '0358318625', 'Trước đặt địa chỉ', 0, 1, '2025-08-13 12:04:48'),
(9, 'chuvanan3stt@gmail.com', '1253', 'Chu Van An nam', '0988736345', 'Q12 mới nè', NULL, 0, '2025-08-13 12:32:46'),
(10, 'giuselevantuan1253@gmail.com', '123456789', 'Vũ Quốc Việt', '0388736245', 'Tô Ký Trung Mỹ Tây Gò Vấp', NULL, 0, '2025-08-13 14:21:16'),
(11, 'minhhuy23082004@gmail.com', '123456', 'Minh Huy', '0354632555', 'Tô Ký', NULL, 0, '2025-08-14 11:10:34');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khuyenmai`
--

CREATE TABLE `khuyenmai` (
  `Id` int(11) NOT NULL,
  `MaKhuyenMai` varchar(50) NOT NULL,
  `NgayBatDau` date DEFAULT NULL,
  `NgayKetThuc` date DEFAULT NULL,
  `SoLuong` int(11) DEFAULT NULL,
  `PhanTramGiam` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sanpham`
--

CREATE TABLE `sanpham` (
  `Id` int(11) NOT NULL,
  `TenSanPham` varchar(255) NOT NULL,
  `MaDanhMuc` int(11) DEFAULT NULL,
  `GiaGoc` decimal(10,2) DEFAULT NULL,
  `HinhAnh` varchar(255) DEFAULT NULL,
  `GiaKhuyenMai` decimal(10,2) DEFAULT NULL,
  `SoLuong` int(11) DEFAULT NULL,
  `LuotXem` int(11) DEFAULT NULL,
  `MoTa` text DEFAULT NULL,
  `TrangThai` tinyint(1) DEFAULT NULL,
  `ThuongHieu` varchar(50) NOT NULL,
  `MaStyle` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `sanpham`
--

INSERT INTO `sanpham` (`Id`, `TenSanPham`, `MaDanhMuc`, `GiaGoc`, `HinhAnh`, `GiaKhuyenMai`, `SoLuong`, `LuotXem`, `MoTa`, `TrangThai`, `ThuongHieu`, `MaStyle`) VALUES
(54, 'Giày sneakers unisex cổ thấp Pronto V2', 5, 2149000.00, 'sneakers unisex cổ thấp Pronto V2.jpg', 2000.00, 10, 35, 'không đpẹ không lấy tiền', 0, 'FILA', 1),
(55, 'unisex quai ngang Arizona Big Bucket', 6, 9990000.00, 'unisex quai ngang Arizona Big Bucket.jpg', 1000000.00, 10, 36, 'không đẹp mới lạ', NULL, 'BIRKENSTOCK', 2),
(56, 'unisex quai ngang Arizona Big 2', 6, 9990000.00, 'unisex quai ngang Arizona Big Bucket.jpg', 1000000.00, 10, 36, 'không đẹp mới lạ', NULL, 'BIRKENSTOCK', 2),
(57, 'Giày Clog Unix Boston', 5, 4490000.00, 'Giày clog unisex Boston.jpg', 2090000.00, 5, 20, NULL, 0, 'BIRKENSTOCK', 2),
(59, 'Giày chạy bộ nam Nike Air Zoom', 5, 2500000.00, 'nam1.jpg', 1999000.00, 500, 1200, 'Giày thể thao nam siêu nhẹ', 1, 'NIKE', 1),
(60, 'Dép unisex quai ngang Arizona', 6, 2200000.00, 'nu1.jpg', 1799000.00, 300, 950, 'Phong cách thời trang nữ cá tính', 1, 'BIRKENSTOCK', 0),
(61, 'Giày tập gym nam Puma Flex', 5, 1800000.00, 'nam2.jpg', 1599000.00, 400, 870, 'Giày tập luyện đa năng nam', 1, 'PUMA', 1),
(62, 'Giày thể thao nữ Converse Chuck', 6, 1500000.00, 'nu2.jpg', 1299000.00, 250, 760, 'Giày vải nữ cổ điển', 1, 'CONVERSE', 3),
(63, 'Giày sandal bé gái đáng yêu', 7, 800000.00, 'anhtreem2.jpg', 699000.00, 600, 540, 'Sandal nhẹ nhàng cho bé gái', 1, 'BUBBLEGUM', 4),
(64, 'Giày sneaker nam Vans Old Skool', 5, 1700000.00, 'nam3.jpg', 1499000.00, 350, 1110, 'Giày skate nam phong cách', 1, 'VANS', 0),
(65, 'Giày thể thao nữ Puma Runner', 6, 2000000.00, 'nu3.jpg', 1790000.00, 420, 890, 'Giày chạy bộ nữ nhẹ và bền', 1, 'PUMA', 1),
(66, 'Giày thể thao trẻ em Adidas Kids', 7, 1200000.00, 'anhtreem3.jpg', 999000.00, 500, 610, 'Giày thể thao cho bé trai và bé gái', 1, 'ADIDAS', 0),
(67, 'Giày sneaker nữ Nike Court Vision', 6, 2300000.00, 'nu4.jpg', 1899000.00, 280, 980, 'Giày sneaker nữ phong cách cổ điển', 1, 'NIKE', 0),
(68, 'Giày Thể Thao Biti\'s Nam Màu Trắng BSM004400TRG', 5, 620000.00, 'giay_bitis.png', 520000.00, 20, NULL, 'Với kiểu dáng đơn giản, lịch sự và dễ ứng dụng, Biti’s BSM004400TRG là mẫu giày thể thao thông dụng dành cho nam giới yêu thích phong cách tối giản – linh hoạt – bền chắc. Tông màu trắng phối đen tạo cảm giác sáng gọn, năng động, thích hợp cho cả môi trường học đường, công sở hay dạo phố.\r\n\r\nChất liệu da tổng hợp được sử dụng cho phần upper giúp giữ phom giày tốt, bền theo thời gian, lại dễ vệ sinh sau khi sử dụng. Đường may chỉn chu, form gọn ôm chân, lót trong mềm và thoáng khí, mang lại cảm giác thoải mái khi đi lại nhiều.\r\n\r\nPhần đế cao su nguyên khối với rãnh chống trượt giúp tăng độ ma sát, bám sàn hiệu quả, hỗ trợ an toàn khi di chuyển nhanh, nhất là trên bề mặt phẳng như gạch, xi măng. Dây giày truyền thống mang lại độ fit linh hoạt và tăng vẻ thể thao cho tổng thể sản phẩm.\r\n\r\nĐặc điểm nổi bật (Bullet Points):\r\nPhối màu trắng – đen thanh lịch, hiện đại, dễ phối quần áo\r\nChất liệu da tổng hợp cao cấp, giữ phom tốt, ít nhăn gãy\r\nLót trong êm, khô thoáng, hỗ trợ đi lại thoải mái suốt ngày dài\r\nForm gọn, ôm chân, cổ thấp, phù hợp dáng nam giới Việt\r\nĐế cao su nguyên khối bền bỉ, rãnh chống trượt an toàn\r\nThích hợp cho học sinh – sinh viên, nhân viên văn phòng, người vận động nhẹ\r\nBiti’s – thương hiệu Việt tin cậy và bền bỉ\r\nThông số kỹ thuật (Technical Specs):\r\nMã sản phẩm: BSM004400TRG\r\nMàu sắc: Trắng phối đen\r\nĐối tượng: Nam\r\nLoại giày: Giày thể thao thông dụng\r\nChất liệu upper: Da tổng hợp\r\nChất liệu đế: Cao su nguyên khối\r\nForm: Cổ thấp, ôm chân, thiết kế gọn\r\nThương hiệu: Biti’s\r\nXuất xứ: Việt Nam', 1, '', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thongbao`
--

CREATE TABLE `thongbao` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `tieu_de` varchar(255) NOT NULL,
  `noi_dung` text NOT NULL,
  `loai_thong_bao` varchar(50) DEFAULT 'general',
  `link_url` varchar(255) DEFAULT NULL,
  `da_doc` tinyint(1) DEFAULT 0,
  `ngay_tao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tinnhan`
--

CREATE TABLE `tinnhan` (
  `IDTinNhan` int(11) NOT NULL,
  `IDCuocTroChuyen` int(11) NOT NULL,
  `LoaiNguoiGui` enum('khach_hang','admin') NOT NULL,
  `IDNguoiGui` int(11) NOT NULL,
  `NoiDung` text NOT NULL,
  `DaDoc` tinyint(1) DEFAULT 0,
  `ThoiGianGui` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`Email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Chỉ mục cho bảng `cuoctrochuyen`
--
ALTER TABLE `cuoctrochuyen`
  ADD PRIMARY KEY (`IDCuocTroChuyen`),
  ADD KEY `IDKhachHang` (`IDKhachHang`),
  ADD KEY `IDAdmin` (`IDAdmin`);

--
-- Chỉ mục cho bảng `danhgia`
--
ALTER TABLE `danhgia`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `danhgia_ibfk_1` (`MaKhachHang`),
  ADD KEY `danhgia_ibfk_2` (`MaSanPham`);

--
-- Chỉ mục cho bảng `danhmuc`
--
ALTER TABLE `danhmuc`
  ADD PRIMARY KEY (`Id`);

--
-- Chỉ mục cho bảng `doanhthuchitiet`
--
ALTER TABLE `doanhthuchitiet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `MaDonHang` (`MaDonHang`);

--
-- Chỉ mục cho bảng `donhang`
--
ALTER TABLE `donhang`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `donhang_ibfk_1` (`MaKhachHang`);

--
-- Chỉ mục cho bảng `donhangchitiet`
--
ALTER TABLE `donhangchitiet`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `donhangchitiet_ibfk_1` (`MaDonHang`),
  ADD KEY `donhangchitiet_ibfk_2` (`MaSanPham`);

--
-- Chỉ mục cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  ADD PRIMARY KEY (`Id`);

--
-- Chỉ mục cho bảng `khuyenmai`
--
ALTER TABLE `khuyenmai`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `CodeKhuyenMai` (`MaKhuyenMai`);

--
-- Chỉ mục cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `MaDanhMuc` (`MaDanhMuc`);

--
-- Chỉ mục cho bảng `thongbao`
--
ALTER TABLE `thongbao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Chỉ mục cho bảng `tinnhan`
--
ALTER TABLE `tinnhan`
  ADD PRIMARY KEY (`IDTinNhan`),
  ADD KEY `IDCuocTroChuyen` (`IDCuocTroChuyen`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `cuoctrochuyen`
--
ALTER TABLE `cuoctrochuyen`
  MODIFY `IDCuocTroChuyen` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `danhgia`
--
ALTER TABLE `danhgia`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `danhmuc`
--
ALTER TABLE `danhmuc`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `doanhthuchitiet`
--
ALTER TABLE `doanhthuchitiet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `donhang`
--
ALTER TABLE `donhang`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6573;

--
-- AUTO_INCREMENT cho bảng `donhangchitiet`
--
ALTER TABLE `donhangchitiet`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `khuyenmai`
--
ALTER TABLE `khuyenmai`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT cho bảng `thongbao`
--
ALTER TABLE `thongbao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `tinnhan`
--
ALTER TABLE `tinnhan`
  MODIFY `IDTinNhan` int(11) NOT NULL AUTO_INCREMENT;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `cuoctrochuyen`
--
ALTER TABLE `cuoctrochuyen`
  ADD CONSTRAINT `cuoctrochuyen_ibfk_1` FOREIGN KEY (`IDKhachHang`) REFERENCES `khachhang` (`Id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cuoctrochuyen_ibfk_2` FOREIGN KEY (`IDAdmin`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `danhgia`
--
ALTER TABLE `danhgia`
  ADD CONSTRAINT `danhgia_ibfk_1` FOREIGN KEY (`MaKhachHang`) REFERENCES `khachhang` (`Id`),
  ADD CONSTRAINT `danhgia_ibfk_2` FOREIGN KEY (`MaSanPham`) REFERENCES `sanpham` (`Id`);

--
-- Các ràng buộc cho bảng `doanhthuchitiet`
--
ALTER TABLE `doanhthuchitiet`
  ADD CONSTRAINT `doanhthuchitiet_ibfk_1` FOREIGN KEY (`MaDonHang`) REFERENCES `donhang` (`Id`);

--
-- Các ràng buộc cho bảng `donhang`
--
ALTER TABLE `donhang`
  ADD CONSTRAINT `donhang_ibfk_1` FOREIGN KEY (`MaKhachHang`) REFERENCES `khachhang` (`Id`);

--
-- Các ràng buộc cho bảng `donhangchitiet`
--
ALTER TABLE `donhangchitiet`
  ADD CONSTRAINT `donhangchitiet_ibfk_1` FOREIGN KEY (`MaDonHang`) REFERENCES `donhang` (`Id`),
  ADD CONSTRAINT `donhangchitiet_ibfk_2` FOREIGN KEY (`MaSanPham`) REFERENCES `sanpham` (`Id`);

--
-- Các ràng buộc cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  ADD CONSTRAINT `sanpham_ibfk_1` FOREIGN KEY (`MaDanhMuc`) REFERENCES `danhmuc` (`Id`);

--
-- Các ràng buộc cho bảng `tinnhan`
--
ALTER TABLE `tinnhan`
  ADD CONSTRAINT `tinnhan_ibfk_1` FOREIGN KEY (`IDCuocTroChuyen`) REFERENCES `cuoctrochuyen` (`IDCuocTroChuyen`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
