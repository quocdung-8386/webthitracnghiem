-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th3 14, 2026 lúc 01:52 PM
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
-- Cơ sở dữ liệu: `he_thong_thi_trac_nghiem`
--
CREATE DATABASE IF NOT EXISTS `he_thong_thi_trac_nghiem` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `he_thong_thi_trac_nghiem`;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bai_lam`
--

CREATE TABLE IF NOT EXISTS `bai_lam` (
  `ma_bai_lam` int(11) NOT NULL AUTO_INCREMENT,
  `ma_nguoi_dung` int(11) NOT NULL,
  `ma_ca_thi` int(11) NOT NULL,
  `thoi_diem_bat_dau` datetime DEFAULT NULL,
  `thoi_diem_nop` datetime DEFAULT NULL,
  `tong_diem` float DEFAULT NULL,
  `trang_thai` enum('dang_lam','da_nop','da_cham') DEFAULT 'dang_lam',
  PRIMARY KEY (`ma_bai_lam`),
  KEY `ma_nguoi_dung` (`ma_nguoi_dung`),
  KEY `ma_ca_thi` (`ma_ca_thi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bao_cao_xuat`
--

CREATE TABLE IF NOT EXISTS `bao_cao_xuat` (
  `ma_bao_cao` int(11) NOT NULL AUTO_INCREMENT,
  `ten_file` varchar(255) NOT NULL,
  `loai_bao_cao` varchar(100) NOT NULL,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `dinh_dang` enum('PDF','XLSX','CSV') NOT NULL,
  `dung_luong` varchar(20) DEFAULT NULL,
  `duong_dan_file` varchar(255) NOT NULL,
  PRIMARY KEY (`ma_bao_cao`),
  KEY `idx_ngay_tao` (`ngay_tao`),
  KEY `idx_ten_file` (`ten_file`(100))
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `bao_cao_xuat`
--

INSERT INTO `bao_cao_xuat` (`ma_bao_cao`, `ten_file`, `loai_bao_cao`, `ngay_tao`, `dinh_dang`, `dung_luong`, `duong_dan_file`) VALUES
(1, 'Bao_cao_Hoc_ky_1_DCCNTT14.8_2026.xlsx', 'Báo cáo theo lớp', '2025-12-30 08:30:00', 'XLSX', '2.4 MB', '/storage/reports/Bao_cao_Hoc_ky_1_Khoi_12_2023.xlsx'),
(2, 'Ket_qua_Thi_Toan_Cao_Cap_A1.pdf', 'Báo cáo theo môn', '2024-05-11 15:45:00', 'PDF', '4.8 MB', '/storage/reports/Ket_qua_Thi_Toan_Cao_Cap_A1.pdf'),
(3, 'Danh_sach_Sinh_vien_Nganh_IT.csv', 'Báo cáo cá nhân', '2024-05-10 10:20:00', 'CSV', '850 KB', '/storage/reports/Danh_sach_Sinh_vien_Nganh_IT.csv'),
(4, 'Tong_hop_Ky_thi_Gia_dinh_2024.xlsx', 'Báo cáo tổng hợp', '2024-05-09 14:15:00', 'XLSX', '1.2 MB', '/storage/reports/Tong_hop_Ky_thi_Gia_dinh_2024.xlsx');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cau_hinh_he_thong`
--

CREATE TABLE IF NOT EXISTS `cau_hinh_he_thong` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ten_he_thong` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `quy_dinh_thi` text DEFAULT NULL,
  `smtp_server` varchar(255) DEFAULT NULL,
  `smtp_port` int(11) DEFAULT NULL,
  `smtp_email` varchar(255) DEFAULT NULL,
  `smtp_password` varchar(255) DEFAULT NULL,
  `ngay_cap_nhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `cau_hinh_he_thong`
--

INSERT INTO `cau_hinh_he_thong` (`id`, `ten_he_thong`, `logo`, `quy_dinh_thi`, `smtp_server`, `smtp_port`, `smtp_email`, `smtp_password`, `ngay_cap_nhat`) VALUES
(1, 'Hệ thống thi trắc nghiệm trực tuyến', '1772716172_vl.jpg', '1. Không sử dụng tài liệu\r\n2. Không sử dụng thiết bị thông minh', 'smtp.gmail.com', 587, 'notification@exam.edu', 'admin@123', '2026-03-05 13:09:32');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cau_hoi`
--

CREATE TABLE IF NOT EXISTS `cau_hoi` (
  `ma_cau_hoi` int(11) NOT NULL AUTO_INCREMENT,
  `ma_danh_muc` int(11) DEFAULT NULL,
  `ma_giao_vien` int(11) DEFAULT NULL,
  `noi_dung` text NOT NULL,
  `muc_do` enum('de','trung_binh','kho') DEFAULT 'trung_binh',
  `loai_cau_hoi` enum('trac_nghiem','dung_sai','tu_luan') NOT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  `trang_thai_duyet` enum('cho_duyet','da_duyet','tu_choi') DEFAULT 'cho_duyet',
  PRIMARY KEY (`ma_cau_hoi`),
  KEY `ma_danh_muc` (`ma_danh_muc`),
  KEY `ma_giao_vien` (`ma_giao_vien`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `cau_hoi`
--

INSERT INTO `cau_hoi` (`ma_cau_hoi`, `ma_danh_muc`, `ma_giao_vien`, `noi_dung`, `muc_do`, `loai_cau_hoi`, `ngay_tao`, `trang_thai_duyet`) VALUES
(1, 1, 2, 'SQL là viết tắt của gì?', 'de', 'trac_nghiem', '2026-03-05 08:30:48', 'da_duyet'),
(2, 2, 2, 'PHP là ngôn ngữ phía server?', 'de', 'dung_sai', '2026-03-05 08:30:48', 'cho_duyet'),
(3, 2, 2, 'Hàm dùng để kết nối MySQL trong PHP là gì?', 'trung_binh', 'trac_nghiem', '2026-03-05 08:30:48', 'cho_duyet');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ca_thi`
--

CREATE TABLE IF NOT EXISTS `ca_thi` (
  `ma_ca_thi` int(11) NOT NULL AUTO_INCREMENT,
  `ma_de_thi` int(11) NOT NULL,
  `thoi_gian_bat_dau` datetime NOT NULL,
  `thoi_gian_ket_thuc` datetime NOT NULL,
  `ma_phong` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ma_ca_thi`),
  KEY `ma_de_thi` (`ma_de_thi`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `ca_thi`
--

INSERT INTO `ca_thi` (`ma_ca_thi`, `ma_de_thi`, `thoi_gian_bat_dau`, `thoi_gian_ket_thuc`, `ma_phong`) VALUES
(10, 1, '2026-03-01 08:00:00', '2026-03-01 09:00:00', 'P101');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_tiet_bai_lam`
--

CREATE TABLE IF NOT EXISTS `chi_tiet_bai_lam` (
  `ma_chi_tiet` int(11) NOT NULL AUTO_INCREMENT,
  `ma_bai_lam` int(11) NOT NULL,
  `ma_cau_hoi` int(11) NOT NULL,
  `ma_dap_an_chon` int(11) DEFAULT NULL,
  `noi_dung_tu_luan` text DEFAULT NULL,
  `diem` float DEFAULT 0,
  PRIMARY KEY (`ma_chi_tiet`),
  KEY `ma_bai_lam` (`ma_bai_lam`),
  KEY `ma_cau_hoi` (`ma_cau_hoi`),
  KEY `ma_dap_an_chon` (`ma_dap_an_chon`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_tiet_de_thi`
--

CREATE TABLE IF NOT EXISTS `chi_tiet_de_thi` (
  `ma_chi_tiet` int(11) NOT NULL AUTO_INCREMENT,
  `ma_de_thi` int(11) NOT NULL,
  `ma_cau_hoi` int(11) NOT NULL,
  `diem` float DEFAULT 1,
  PRIMARY KEY (`ma_chi_tiet`),
  KEY `ma_de_thi` (`ma_de_thi`),
  KEY `ma_cau_hoi` (`ma_cau_hoi`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chi_tiet_de_thi`
--

INSERT INTO `chi_tiet_de_thi` (`ma_chi_tiet`, `ma_de_thi`, `ma_cau_hoi`, `diem`) VALUES
(1, 1, 1, 3),
(2, 1, 2, 3),
(3, 1, 3, 4);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dang_ky_thi`
--

CREATE TABLE IF NOT EXISTS `dang_ky_thi` (
  `ma_dang_ky` int(11) NOT NULL AUTO_INCREMENT,
  `ma_nguoi_dung` int(11) NOT NULL,
  `ma_ca_thi` int(11) NOT NULL,
  `thoi_diem_dang_ky` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ma_dang_ky`),
  KEY `ma_nguoi_dung` (`ma_nguoi_dung`),
  KEY `ma_ca_thi` (`ma_ca_thi`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_muc`
--

CREATE TABLE IF NOT EXISTS `danh_muc` (
  `ma_danh_muc` int(11) NOT NULL AUTO_INCREMENT,
  `ten_danh_muc` varchar(100) NOT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ma_danh_muc`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `danh_muc`
--

INSERT INTO `danh_muc` (`ma_danh_muc`, `ten_danh_muc`, `ngay_tao`) VALUES
(1, 'Cơ sở dữ liệu', '2026-03-05 08:30:35'),
(2, 'Lập trình PHP', '2026-03-05 08:30:35'),
(3, 'Mạng máy tính', '2026-03-05 08:30:35'),
(4, 'Hướng đối tượng', '2026-03-09 15:32:11');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dap_an`
--

CREATE TABLE IF NOT EXISTS `dap_an` (
  `ma_dap_an` int(11) NOT NULL AUTO_INCREMENT,
  `ma_cau_hoi` int(11) NOT NULL,
  `noi_dung` text NOT NULL,
  `la_dap_an_dung` tinyint(1) DEFAULT 0,
  `thu_tu` int(11) DEFAULT 1,
  PRIMARY KEY (`ma_dap_an`),
  KEY `ma_cau_hoi` (`ma_cau_hoi`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `dap_an`
--

INSERT INTO `dap_an` (`ma_dap_an`, `ma_cau_hoi`, `noi_dung`, `la_dap_an_dung`, `thu_tu`) VALUES
(1, 1, 'Structured Query Language', 1, 1),
(2, 1, 'Simple Query Language', 0, 2),
(3, 1, 'System Query Logic', 0, 3),
(4, 1, 'None', 0, 4),
(5, 2, 'Đúng', 1, 1),
(6, 2, 'Sai', 0, 2),
(7, 3, 'mysqli_connect()', 1, 1),
(8, 3, 'connect_mysql()', 0, 2),
(9, 3, 'mysql_open()', 0, 3),
(10, 3, 'db_connect()', 0, 4);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dat_lai_mat_khau`
--

CREATE TABLE IF NOT EXISTS `dat_lai_mat_khau` (
  `ma_yeu_cau` int(11) NOT NULL AUTO_INCREMENT,
  `ma_nguoi_dung` int(11) DEFAULT NULL,
  `ma_token` varchar(255) DEFAULT NULL,
  `thoi_gian_het_han` datetime DEFAULT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ma_yeu_cau`),
  KEY `ma_nguoi_dung` (`ma_nguoi_dung`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `de_thi`
--

CREATE TABLE IF NOT EXISTS `de_thi` (
  `ma_de_thi` int(11) NOT NULL AUTO_INCREMENT,
  `ma_giao_vien` int(11) DEFAULT NULL,
  `tieu_de` varchar(255) NOT NULL,
  `thoi_gian_lam` int(11) NOT NULL COMMENT 'phút',
  `tong_diem` float DEFAULT 10,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ma_de_thi`),
  KEY `ma_giao_vien` (`ma_giao_vien`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `de_thi`
--

INSERT INTO `de_thi` (`ma_de_thi`, `ma_giao_vien`, `tieu_de`, `thoi_gian_lam`, `tong_diem`, `ngay_tao`) VALUES
(1, 2, 'Đề thi Cơ sở dữ liệu cơ bản', 30, 10, '2026-03-05 08:31:16');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khieu_nai`
--

CREATE TABLE IF NOT EXISTS `khieu_nai` (
  `ma_khieu_nai` int(11) NOT NULL AUTO_INCREMENT,
  `ma_nguoi_dung` int(11) NOT NULL,
  `ma_de_thi` int(11) NOT NULL,
  `ma_cau_hoi` int(11) NOT NULL,
  `noi_dung` text NOT NULL,
  `minh_chung` varchar(255) DEFAULT NULL,
  `trang_thai` enum('dang_cho','da_duyet','tu_choi') DEFAULT 'dang_cho',
  `ngay_tao` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`ma_khieu_nai`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lich_su_dang_nhap`
--

CREATE TABLE IF NOT EXISTS `lich_su_dang_nhap` (
  `ma_lich_su` int(11) NOT NULL AUTO_INCREMENT,
  `ma_nguoi_dung` int(11) DEFAULT NULL,
  `dia_chi_ip` varchar(50) DEFAULT NULL,
  `thiet_bi` text DEFAULT NULL,
  `thoi_diem_dang_nhap` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ma_lich_su`),
  KEY `ma_nguoi_dung` (`ma_nguoi_dung`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoi_dung`
--

CREATE TABLE IF NOT EXISTS `nguoi_dung` (
  `ma_nguoi_dung` int(11) NOT NULL AUTO_INCREMENT,
  `ma_vai_tro` int(11) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `ten_dang_nhap` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mat_khau` varchar(255) NOT NULL,
  `trang_thai` enum('hoat_dong','ngung_hoat_dong','bi_khoa') DEFAULT 'hoat_dong',
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  `ngay_cap_nhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`ma_nguoi_dung`),
  UNIQUE KEY `ten_dang_nhap` (`ten_dang_nhap`),
  UNIQUE KEY `email` (`email`),
  KEY `ma_vai_tro` (`ma_vai_tro`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoi_dung`
--

INSERT INTO `nguoi_dung` (`ma_nguoi_dung`, `ma_vai_tro`, `ho_ten`, `ten_dang_nhap`, `email`, `mat_khau`, `trang_thai`, `ngay_tao`, `ngay_cap_nhat`) VALUES
(1, 1, 'Nông Quốc Dũng', 'admin', 'admin@gmail.com', '123456', 'hoat_dong', '2026-03-04 13:32:48', '2026-03-09 13:40:02'),
(2, 2, 'Trương Văn Long', 'giangvien1', 'gv1@gmail.com', '123456', 'hoat_dong', '2026-03-04 13:32:48', '2026-03-04 13:32:48'),
(3, 3, 'Vũ Hồng Quang', 'thisinh1', 'ts1@gmail.com', '123456', 'hoat_dong', '2026-03-04 13:32:48', '2026-03-04 13:32:48'),
(8, 2, 'Nguyễn Lữ Bố', 'dungnong03052005', 'dungnong03052005@gmail.com', '$2y$10$3SgYiEJVz3AUD1KZ08t2dO/CGIAH9yvjqqYX1veMKluKsBMFRLsRu', 'hoat_dong', '2026-03-10 07:50:15', '2026-03-10 08:10:04'),
(9, 3, 'Nong Quoc Dung', '20232890', '20232890@eaut.edu.vn', '$2y$10$qjYJpoOB1r7qU1DQ1KluieSTlxriqjby4/OFmtr6vuH4doyoK6MVW', 'hoat_dong', '2026-03-10 07:50:41', '2026-03-10 07:50:41'),
(10, 2, 'Nong Quoc Dung', 'longtruong123', 'longtruong123@gmail.com', '$2y$10$WaBj2dtj/4rNIVuF11vTK.PMzqtFpbAUsfeso63cb8vBTSkPVCb6e', 'hoat_dong', '2026-03-10 08:10:18', '2026-03-10 08:10:18');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhat_ky_he_thong`
--

CREATE TABLE IF NOT EXISTS `nhat_ky_he_thong` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ten_nguoi_dung` varchar(100) DEFAULT NULL,
  `vai_tro` varchar(100) DEFAULT NULL,
  `hanh_dong` varchar(50) DEFAULT NULL,
  `noi_dung` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `thoi_gian` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nhat_ky_he_thong`
--

INSERT INTO `nhat_ky_he_thong` (`id`, `ten_nguoi_dung`, `vai_tro`, `hanh_dong`, `noi_dung`, `ip_address`, `thoi_gian`) VALUES
(1, 'Nông Quốc Dũng', 'Quản trị viên', 'ĐĂNG NHẬP', 'Đăng nhập thành công', '192.168.1.15', '2026-03-06 11:43:29'),
(2, 'Trương Văn Long', 'Giảng viên', 'THÊM MỚI', 'Tạo đề thi VLDC_01', '172.16.0.42', '2026-03-06 11:43:29'),
(3, 'Nông Quốc Dũng', 'Quản trị viên', 'CHỈNH SỬA', 'Cập nhật quyền user student_02', '192.168.1.15', '2026-03-06 11:43:29'),
(4, 'Nông Quốc Dũng', 'Quản trị viên', 'XÓA', 'Xóa 12 câu hỏi trùng', '113.161.4.1', '2026-03-06 11:43:29');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phien_ban_cau_hoi`
--

CREATE TABLE IF NOT EXISTS `phien_ban_cau_hoi` (
  `ma_phien_ban` int(11) NOT NULL AUTO_INCREMENT,
  `ma_cau_hoi` int(11) NOT NULL,
  `ma_nguoi_cap_nhat` int(11) DEFAULT NULL,
  `noi_dung` text NOT NULL,
  `version` varchar(10) DEFAULT 'v1.0',
  `ghi_chu` text DEFAULT NULL,
  `trang_thai` enum('current','old') DEFAULT 'current',
  `thoi_gian_cap_nhat` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ma_phien_ban`),
  KEY `ma_cau_hoi` (`ma_cau_hoi`),
  KEY `ma_nguoi_cap_nhat` (`ma_nguoi_cap_nhat`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `phien_ban_cau_hoi`
--

INSERT INTO `phien_ban_cau_hoi` (`ma_phien_ban`, `ma_cau_hoi`, `ma_nguoi_cap_nhat`, `noi_dung`, `version`, `ghi_chu`, `trang_thai`, `thoi_gian_cap_nhat`) VALUES
(1, 1, 1, 'Cho hàm số y=ax^2+bx+c. Tìm điều kiện...', 'v1.0', 'Tạo câu hỏi', 'old', '2026-03-09 15:45:24'),
(2, 1, 1, 'Cho hàm số y=ax^2+bx+c. Tìm nghiệm của phương trình...', 'v1.1', 'Sửa nội dung', 'current', '2026-03-09 15:45:24');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phong_thi`
--

CREATE TABLE IF NOT EXISTS `phong_thi` (
  `ma_phong` int(11) NOT NULL AUTO_INCREMENT,
  `ten_phong` varchar(100) NOT NULL,
  `suc_chua` int(11) DEFAULT 50,
  `dia_diem` varchar(255) DEFAULT NULL,
  `trang_thai` enum('mo','dong','bao_tri') DEFAULT 'mo',
  `mo_ta` text DEFAULT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  `ngay_cap_nhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`ma_phong`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `phong_thi`
--

INSERT INTO `phong_thi` (`ma_phong`, `ten_phong`, `suc_chua`, `dia_diem`, `trang_thai`, `mo_ta`, `ngay_tao`, `ngay_cap_nhat`) VALUES
(1, 'P101', 50, 'Tòa A', 'mo', NULL, '2026-03-10 17:36:28', '2026-03-10 17:36:28'),
(2, 'P102', 40, 'Tòa A', 'mo', NULL, '2026-03-10 17:36:28', '2026-03-10 17:36:28'),
(3, 'LAB01', 60, 'Tòa B', 'mo', NULL, '2026-03-10 17:36:28', '2026-03-10 17:36:28'),
(4, 'LAB02', 35, 'Tòa B', 'mo', NULL, '2026-03-10 17:36:28', '2026-03-10 17:36:28'),
(5, 'HALL01', 120, 'Hội trường', 'mo', NULL, '2026-03-10 17:36:28', '2026-03-10 17:36:28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phuc_khao`
--

CREATE TABLE IF NOT EXISTS `phuc_khao` (
  `ma_phuc_khao` int(11) NOT NULL AUTO_INCREMENT,
  `ma_bai_lam` int(11) NOT NULL,
  `noi_dung_yeu_cau` text DEFAULT NULL,
  `noi_dung_phan_hoi` text DEFAULT NULL,
  `trang_thai` enum('cho_duyet','chap_nhan','tu_choi') DEFAULT 'cho_duyet',
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ma_phuc_khao`),
  KEY `ma_bai_lam` (`ma_bai_lam`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thong_bao`
--

CREATE TABLE IF NOT EXISTS `thong_bao` (
  `ma_thong_bao` int(11) NOT NULL AUTO_INCREMENT,
  `ma_nguoi_dung` int(11) DEFAULT NULL,
  `tieu_de` varchar(255) DEFAULT NULL,
  `noi_dung` text DEFAULT NULL,
  `da_doc` tinyint(1) DEFAULT 0,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ma_thong_bao`),
  KEY `ma_nguoi_dung` (`ma_nguoi_dung`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vai_tro`
--

CREATE TABLE IF NOT EXISTS `vai_tro` (
  `ma_vai_tro` int(11) NOT NULL AUTO_INCREMENT,
  `ten_vai_tro` varchar(50) NOT NULL,
  `mo_ta` varchar(255) DEFAULT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ma_vai_tro`),
  UNIQUE KEY `ten_vai_tro` (`ten_vai_tro`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `vai_tro`
--

INSERT INTO `vai_tro` (`ma_vai_tro`, `ten_vai_tro`, `mo_ta`, `ngay_tao`) VALUES
(1, 'admin', 'Quản trị hệ thống', '2026-03-04 13:31:03'),
(2, 'giang_vien', 'Giảng viên ra đề', '2026-03-04 13:31:03'),
(3, 'thi_sinh', 'Thí sinh tham gia thi', '2026-03-04 13:31:03');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vi_pham_thi`
--

CREATE TABLE IF NOT EXISTS `vi_pham_thi` (
  `id_vi_pham` int(11) NOT NULL AUTO_INCREMENT,
  `ma_bai_lam` int(11) NOT NULL,
  `loai_vi_pham` varchar(255) NOT NULL,
  `thoi_gian` datetime DEFAULT current_timestamp(),
  `ghi_chu` text DEFAULT NULL,
  PRIMARY KEY (`id_vi_pham`),
  KEY `ma_bai_lam` (`ma_bai_lam`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `bai_lam`
--
ALTER TABLE `bai_lam`
  ADD CONSTRAINT `bai_lam_ibfk_1` FOREIGN KEY (`ma_nguoi_dung`) REFERENCES `nguoi_dung` (`ma_nguoi_dung`),
  ADD CONSTRAINT `bai_lam_ibfk_2` FOREIGN KEY (`ma_ca_thi`) REFERENCES `ca_thi` (`ma_ca_thi`);

--
-- Các ràng buộc cho bảng `cau_hoi`
--
ALTER TABLE `cau_hoi`
  ADD CONSTRAINT `cau_hoi_ibfk_1` FOREIGN KEY (`ma_danh_muc`) REFERENCES `danh_muc` (`ma_danh_muc`),
  ADD CONSTRAINT `cau_hoi_ibfk_2` FOREIGN KEY (`ma_giao_vien`) REFERENCES `nguoi_dung` (`ma_nguoi_dung`);

--
-- Các ràng buộc cho bảng `ca_thi`
--
ALTER TABLE `ca_thi`
  ADD CONSTRAINT `ca_thi_ibfk_1` FOREIGN KEY (`ma_de_thi`) REFERENCES `de_thi` (`ma_de_thi`);

--
-- Các ràng buộc cho bảng `chi_tiet_bai_lam`
--
ALTER TABLE `chi_tiet_bai_lam`
  ADD CONSTRAINT `chi_tiet_bai_lam_ibfk_1` FOREIGN KEY (`ma_bai_lam`) REFERENCES `bai_lam` (`ma_bai_lam`) ON DELETE CASCADE,
  ADD CONSTRAINT `chi_tiet_bai_lam_ibfk_2` FOREIGN KEY (`ma_cau_hoi`) REFERENCES `cau_hoi` (`ma_cau_hoi`),
  ADD CONSTRAINT `chi_tiet_bai_lam_ibfk_3` FOREIGN KEY (`ma_dap_an_chon`) REFERENCES `dap_an` (`ma_dap_an`);

--
-- Các ràng buộc cho bảng `chi_tiet_de_thi`
--
ALTER TABLE `chi_tiet_de_thi`
  ADD CONSTRAINT `chi_tiet_de_thi_ibfk_1` FOREIGN KEY (`ma_de_thi`) REFERENCES `de_thi` (`ma_de_thi`) ON DELETE CASCADE,
  ADD CONSTRAINT `chi_tiet_de_thi_ibfk_2` FOREIGN KEY (`ma_cau_hoi`) REFERENCES `cau_hoi` (`ma_cau_hoi`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `dang_ky_thi`
--
ALTER TABLE `dang_ky_thi`
  ADD CONSTRAINT `dang_ky_thi_ibfk_1` FOREIGN KEY (`ma_nguoi_dung`) REFERENCES `nguoi_dung` (`ma_nguoi_dung`),
  ADD CONSTRAINT `dang_ky_thi_ibfk_2` FOREIGN KEY (`ma_ca_thi`) REFERENCES `ca_thi` (`ma_ca_thi`);

--
-- Các ràng buộc cho bảng `dap_an`
--
ALTER TABLE `dap_an`
  ADD CONSTRAINT `dap_an_ibfk_1` FOREIGN KEY (`ma_cau_hoi`) REFERENCES `cau_hoi` (`ma_cau_hoi`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `dat_lai_mat_khau`
--
ALTER TABLE `dat_lai_mat_khau`
  ADD CONSTRAINT `dat_lai_mat_khau_ibfk_1` FOREIGN KEY (`ma_nguoi_dung`) REFERENCES `nguoi_dung` (`ma_nguoi_dung`);

--
-- Các ràng buộc cho bảng `de_thi`
--
ALTER TABLE `de_thi`
  ADD CONSTRAINT `de_thi_ibfk_1` FOREIGN KEY (`ma_giao_vien`) REFERENCES `nguoi_dung` (`ma_nguoi_dung`);

--
-- Các ràng buộc cho bảng `lich_su_dang_nhap`
--
ALTER TABLE `lich_su_dang_nhap`
  ADD CONSTRAINT `lich_su_dang_nhap_ibfk_1` FOREIGN KEY (`ma_nguoi_dung`) REFERENCES `nguoi_dung` (`ma_nguoi_dung`);

--
-- Các ràng buộc cho bảng `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  ADD CONSTRAINT `nguoi_dung_ibfk_1` FOREIGN KEY (`ma_vai_tro`) REFERENCES `vai_tro` (`ma_vai_tro`);

--
-- Các ràng buộc cho bảng `phien_ban_cau_hoi`
--
ALTER TABLE `phien_ban_cau_hoi`
  ADD CONSTRAINT `phien_ban_cau_hoi_ibfk_1` FOREIGN KEY (`ma_cau_hoi`) REFERENCES `cau_hoi` (`ma_cau_hoi`) ON DELETE CASCADE,
  ADD CONSTRAINT `phien_ban_cau_hoi_ibfk_2` FOREIGN KEY (`ma_nguoi_cap_nhat`) REFERENCES `nguoi_dung` (`ma_nguoi_dung`);

--
-- Các ràng buộc cho bảng `phuc_khao`
--
ALTER TABLE `phuc_khao`
  ADD CONSTRAINT `phuc_khao_ibfk_1` FOREIGN KEY (`ma_bai_lam`) REFERENCES `bai_lam` (`ma_bai_lam`);

--
-- Các ràng buộc cho bảng `thong_bao`
--
ALTER TABLE `thong_bao`
  ADD CONSTRAINT `thong_bao_ibfk_1` FOREIGN KEY (`ma_nguoi_dung`) REFERENCES `nguoi_dung` (`ma_nguoi_dung`);

--
-- Các ràng buộc cho bảng `vi_pham_thi`
--
ALTER TABLE `vi_pham_thi`
  ADD CONSTRAINT `vi_pham_thi_ibfk_1` FOREIGN KEY (`ma_bai_lam`) REFERENCES `bai_lam` (`ma_bai_lam`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
