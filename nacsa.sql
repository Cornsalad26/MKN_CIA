-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for nacsa
CREATE DATABASE IF NOT EXISTS `nacsa` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `nacsa`;

-- Dumping structure for table nacsa.butiran_kandungan_surat
CREATE TABLE IF NOT EXISTS `butiran_kandungan_surat` (
  `id_butiran` int NOT NULL AUTO_INCREMENT,
  `id_surat` int NOT NULL,
  `nombor_perenggan` int NOT NULL,
  `jenis_kandungan` enum('teks_biasa','jadual_butiran') DEFAULT 'teks_biasa',
  `teks_perenggan` text NOT NULL,
  `butiran_tarikh` varchar(150) DEFAULT NULL,
  `butiran_masa` varchar(150) DEFAULT NULL,
  `butiran_tempat` text,
  `butiran_pengerusi_atau_terlibat` text,
  `butiran_tambahan_json` json DEFAULT NULL,
  PRIMARY KEY (`id_butiran`),
  KEY `id_surat` (`id_surat`),
  CONSTRAINT `butiran_kandungan_surat_ibfk_1` FOREIGN KEY (`id_surat`) REFERENCES `surat_rasmi` (`id_surat`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table nacsa.butiran_kandungan_surat: ~4 rows (approximately)
DELETE FROM `butiran_kandungan_surat`;
INSERT INTO `butiran_kandungan_surat` (`id_butiran`, `id_surat`, `nombor_perenggan`, `jenis_kandungan`, `teks_perenggan`, `butiran_tarikh`, `butiran_masa`, `butiran_tempat`, `butiran_pengerusi_atau_terlibat`, `butiran_tambahan_json`) VALUES
	(1, 1, 2, 'teks_biasa', '2. DENGAN SEGALA HORMATNYA GDSJAHGFJKASGFJHSAGFHJRGEWJHBFHESW', '', '', '', '', NULL),
	(2, 2, 2, 'teks_biasa', '2. DENGAN SEGALA HORMATNYA GDSJAHGFJKASGFJHSAGFHJRGEWJHBFHESW', '', '', '', '', NULL),
	(3, 3, 2, 'teks_biasa', '2. DENGAN SEGALA HORMATNYA GDSJAHGFJKASGFJHSAGFHJRGEWJHBFHESW', '', '', '', '', NULL),
	(4, 4, 2, 'teks_biasa', '2. DENGAN SEGALA HORMATNYA GDSJAHGFJKASGFJHSAGFHJRGEWJHBFHESW', '', '', '', '', NULL),
	(5, 5, 2, 'teks_biasa', 'Kerjasama dan perhatian Tuan/Puan mengenai perkara ini amatlah dihargai.', '', '', '', '', NULL);

-- Dumping structure for table nacsa.pegawai_penandatangan
CREATE TABLE IF NOT EXISTS `pegawai_penandatangan` (
  `id_pegawai` int NOT NULL AUTO_INCREMENT,
  `nama_pegawai` varchar(150) NOT NULL,
  `jawatan` varchar(150) NOT NULL,
  `bahagian_unit` varchar(200) NOT NULL,
  `bp_jawatan` varchar(200) DEFAULT 'b.p. Ketua Pengarah Keselamatan Negara',
  `agensi_induk` varchar(200) DEFAULT 'Majlis Keselamatan Negara\nJabatan Perdana Menteri',
  `no_tel` varchar(50) DEFAULT NULL,
  `emel` varchar(100) DEFAULT NULL,
  `slogan_1` varchar(150) DEFAULT '"MALAYSIA MADANI"',
  `slogan_2` varchar(150) DEFAULT '"BERKHIDMAT UNTUK NEGARA"',
  `slogan_3` varchar(150) DEFAULT '"KESELAMATAN NEGARA TANGGUNGJAWAB BERSAMA"',
  `status_aktif` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pegawai`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table nacsa.pegawai_penandatangan: ~2 rows (approximately)
DELETE FROM `pegawai_penandatangan`;
INSERT INTO `pegawai_penandatangan` (`id_pegawai`, `nama_pegawai`, `jawatan`, `bahagian_unit`, `bp_jawatan`, `agensi_induk`, `no_tel`, `emel`, `slogan_1`, `slogan_2`, `slogan_3`, `status_aktif`, `created_at`) VALUES
	(1, 'NAMA PEGAWAI KANAN 1', 'Ketua Eksekutif', 'Agensi Keselamatan Siber Negara (NACSA)', 'b.p. Ketua Pengarah Keselamatan Negara', 'Majlis Keselamatan Negara\nJabatan Perdana Menteri', NULL, NULL, '"MALAYSIA MADANI"', '"BERKHIDMAT UNTUK NEGARA"', '"KESELAMATAN NEGARA TANGGUNGJAWAB BERSAMA"', 1, '2026-10-16 08:41:53'),
	(2, 'NAMA PEGAWAI KANAN 2', 'Pengarah', 'Bahagian Pematuhan Khas Audit, NACSA', 'b.p. Ketua Pengarah Keselamatan Negara', 'Majlis Keselamatan Negara\nJabatan Perdana Menteri', NULL, NULL, '"MALAYSIA MADANI"', '"BERKHIDMAT UNTUK NEGARA"', '"KESELAMATAN NEGARA TANGGUNGJAWAB BERSAMA"', 1, '2026-10-16 08:41:53');

-- Dumping structure for table nacsa.surat_rasmi
CREATE TABLE IF NOT EXISTS `surat_rasmi` (
  `id_surat` int NOT NULL AUTO_INCREMENT,
  `id_templat` int DEFAULT NULL,
  `no_rujukan_kami` varchar(100) DEFAULT NULL,
  `no_rujukan_tuan` varchar(100) DEFAULT NULL,
  `tarikh_surat` date NOT NULL,
  `tarikh_paparan` varchar(100) NOT NULL,
  `panggilan_hormat_atas` varchar(150) DEFAULT NULL,
  `nama_penerima` varchar(200) NOT NULL,
  `jawatan_penerima` varchar(200) DEFAULT NULL,
  `nama_agensi` varchar(200) DEFAULT NULL,
  `alamat_penerima` text NOT NULL,
  `panggilan_hormat_bawah` varchar(100) NOT NULL,
  `tajuk_surat` text NOT NULL,
  `ada_highlight_tajuk` tinyint(1) DEFAULT '0',
  `mukaddimah` text NOT NULL,
  `ayat_penutup` text,
  `frasa_amanah` varchar(150) DEFAULT 'Saya yang menjalankan amanah,',
  `manual_nama_pegawai` varchar(150) DEFAULT NULL,
  `manual_jawatan_pegawai` varchar(150) DEFAULT NULL,
  `manual_bahagian_unit` varchar(200) DEFAULT NULL,
  `manual_bp_jawatan` varchar(200) DEFAULT 'b.p. Ketua Pengarah Keselamatan Negara',
  `manual_agensi_induk` text,
  `manual_no_tel` varchar(50) DEFAULT NULL,
  `manual_emel` varchar(100) DEFAULT NULL,
  `id_pegawai_penandatangan` int DEFAULT NULL,
  `salinan_kepada` text,
  `dicipta_oleh` varchar(100) DEFAULT NULL,
  `status_surat` enum('Draf','Siap') DEFAULT 'Siap',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_surat`),
  KEY `id_templat` (`id_templat`),
  KEY `id_pegawai_penandatangan` (`id_pegawai_penandatangan`),
  CONSTRAINT `surat_rasmi_ibfk_1` FOREIGN KEY (`id_templat`) REFERENCES `templat_surat` (`id_templat`) ON DELETE SET NULL,
  CONSTRAINT `surat_rasmi_ibfk_2` FOREIGN KEY (`id_pegawai_penandatangan`) REFERENCES `pegawai_penandatangan` (`id_pegawai`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table nacsa.surat_rasmi: ~4 rows (approximately)
DELETE FROM `surat_rasmi`;
INSERT INTO `surat_rasmi` (`id_surat`, `id_templat`, `no_rujukan_kami`, `no_rujukan_tuan`, `tarikh_surat`, `tarikh_paparan`, `panggilan_hormat_atas`, `nama_penerima`, `jawatan_penerima`, `nama_agensi`, `alamat_penerima`, `panggilan_hormat_bawah`, `tajuk_surat`, `ada_highlight_tajuk`, `mukaddimah`, `ayat_penutup`, `frasa_amanah`, `manual_nama_pegawai`, `manual_jawatan_pegawai`, `manual_bahagian_unit`, `manual_bp_jawatan`, `manual_agensi_induk`, `manual_no_tel`, `manual_emel`, `id_pegawai_penandatangan`, `salinan_kepada`, `dicipta_oleh`, `status_surat`, `created_at`, `updated_at`) VALUES
	(1, NULL, 'MKN600/14/5/1', NULL, '2026-10-16', '16 Oktober 2026', '', 'IR.DR. Megat Zuhairy B Megat Tajuddin', 'Ketua Eksekutif', 'Agensi Keselamatan Siber Negara', 'Menara Cyber Axis', 'YBhg. Datuk/Dato\'/Tuan/Puan,', 'JEMPUTAN KE SESI UAT PRKM', 0, 'Dengan hormatnya saya diarah merujuk kepada perkara tersebut di atas.', 'Kerjasama dan perhatian Tuan/Puan mengenai perkara ini amatlah dihargai.', 'Saya yang menjalankan amanah,', NULL, NULL, NULL, 'b.p. Ketua Pengarah Keselamatan Negara', NULL, NULL, NULL, NULL, '', NULL, 'Siap', '2026-10-16 09:11:06', '2026-10-16 09:11:06'),
	(2, NULL, 'MKN600/14/5/1', NULL, '2026-10-16', '16 Oktober 2026', '', 'IR.DR. Megat Zuhairy B Megat Tajuddin', 'Ketua Eksekutif', 'Agensi Keselamatan Siber Negara', 'Menara Cyber Axis', 'YBhg. Datuk/Dato\'/Tuan/Puan,', 'JEMPUTAN KE SESI UAT PRKM', 0, 'Dengan hormatnya saya diarah merujuk kepada perkara tersebut di atas.', 'Kerjasama dan perhatian Tuan/Puan mengenai perkara ini amatlah dihargai.', 'Saya yang menjalankan amanah,', NULL, NULL, NULL, 'b.p. Ketua Pengarah Keselamatan Negara', NULL, NULL, NULL, NULL, '', NULL, 'Siap', '2026-10-16 09:15:23', '2026-10-16 09:15:23'),
	(3, NULL, 'MKN600/14/5/1', NULL, '2026-10-16', '16 Oktober 2026', '', 'IR.DR. Megat Zuhairy B Megat Tajuddin', 'Ketua Eksekutif', 'Agensi Keselamatan Siber Negara', 'Menara Cyber Axis', 'YBhg. Datuk/Dato\'/Tuan/Puan,', 'JEMPUTAN KE SESI UAT PRKM', 0, 'Dengan hormatnya saya diarah merujuk kepada perkara tersebut di atas.', 'Kerjasama dan perhatian Tuan/Puan mengenai perkara ini amatlah dihargai.', 'Saya yang menjalankan amanah,', 'ZURAIN', 'PPTM', 'Unit Pematuhan Khas Audit (BAP)', 'b.p. Ketua Eksekutif NACSA', 'Agensi Keselamatan Siber Negara (NACSA)\r\nMajlis Keselamatan Negara', '0380644901', 'zurain@nacsa.gov.my', NULL, '', NULL, 'Siap', '2026-10-16 09:18:21', '2026-10-16 09:18:21'),
	(4, NULL, 'MKN600/14/5/1', NULL, '2026-10-16', '16 Oktober 2026', '', 'IR.DR. Megat Zuhairy B Megat Tajuddin', 'Ketua Eksekutif', 'Agensi Keselamatan Siber Negara', 'Menara Cyber Axis', 'YBhg. Datuk/Dato\'/Tuan/Puan,', 'JEMPUTAN KE SESI UAT PRKM', 0, 'Dengan hormatnya saya diarah merujuk kepada perkara tersebut di atas.', 'Kerjasama dan perhatian Tuan/Puan mengenai perkara ini amatlah dihargai.', 'Saya yang menjalankan amanah,', 'ZURAIN', 'PPTM', 'Unit Pematuhan Khas Audit (BAP)', 'b.p. Ketua Eksekutif NACSA', 'Agensi Keselamatan Siber Negara (NACSA)\r\nMajlis Keselamatan Negara', '0380644901', 'zurain@nacsa.gov.my', NULL, '', NULL, 'Siap', '2026-10-16 09:19:45', '2026-10-16 09:19:45'),
	(5, NULL, 'MKN600/14/5/1', NULL, '2026-10-19', '19 Oktober 2026', '', 'IR.DR. Megat Zuhairy B Megat Tajuddin', 'Ketua Eksekutif', 'Agensi Keselamatan Siber Negara', 'Menara Cyber Axis', 'YBhg. Datuk/Dato\'/Tuan/Puan,', 'JEMPUTAN KE SESI UAT PRKM', 0, 'Dengan hormatnya saya diarah merujuk kepada perkara tersebut di atas.', 'Kerjasama dan perhatian Tuan/Puan mengenai perkara ini amatlah dihargai.', 'Saya yang menjalankan amanah,', 'ZURAIN', 'PPTM', 'Unit Pematuhan Khas Audit (BAP)', 'b.p. Ketua Eksekutif NACSA', 'Agensi Keselamatan Siber Negara (NACSA)\r\nMajlis Keselamatan Negara', '0380644901', 'zurain@nacsa.gov.my', NULL, '', NULL, 'Siap', '2026-10-19 01:05:51', '2026-10-19 01:05:51');

-- Dumping structure for table nacsa.templat_surat
CREATE TABLE IF NOT EXISTS `templat_surat` (
  `id_templat` int NOT NULL AUTO_INCREMENT,
  `nama_templat` varchar(150) NOT NULL,
  `mukaddimah_default` text,
  `penutup_default` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_templat`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table nacsa.templat_surat: ~2 rows (approximately)
DELETE FROM `templat_surat`;
INSERT INTO `templat_surat` (`id_templat`, `nama_templat`, `mukaddimah_default`, `penutup_default`, `created_at`) VALUES
	(1, 'Surat Rasmi Umum / Makluman', 'Dengan segala hormatnya saya merujuk kepada perkara di atas.', 'Segala perhatian dan kerjasama pihak YBhg. Datuk/Dato\'/Tuan/Puan berhubung perkara ini amatlah dihargai dan didahului dengan ucapan ribuan terima kasih.', '2026-10-16 08:41:53'),
	(2, 'Surat Jemputan Mesyuarat / Sesi Audit', 'Dengan segala hormatnya saya merujuk kepada perkara di atas.', 'Kehadiran dan kerjasama pihak YBhg. Datuk/Dato\'/Tuan/Puan dalam menjayakan sesi ini amatlah dihargai.', '2026-10-16 08:41:53');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
