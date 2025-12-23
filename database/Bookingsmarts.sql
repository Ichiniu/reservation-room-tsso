-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 19 Des 2025 pada 03.06
-- Versi Server: 10.1.21-MariaDB
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `warsito`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `catering`
--

CREATE TABLE `catering` (
  `ID_CATERING` int(11) NOT NULL,
  `NAMA_PAKET` varchar(225) NOT NULL,
  `MENU_PEMBUKA` varchar(225) NOT NULL,
  `MENU_UTAMA` varchar(225) NOT NULL,
  `MENU_PENUTUP` varchar(225) NOT NULL,
  `HARGA` bigint(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `catering`
--

INSERT INTO `catering` (`ID_CATERING`, `NAMA_PAKET`, `MENU_PEMBUKA`, `MENU_UTAMA`, `MENU_PENUTUP`, `HARGA`) VALUES
(1, 'Paket Hemat 1', 'Rendang kuda dengan saus ikan tuna basi', 'Ikan pari', 'Otak Kera', 150000),
(2, 'Paket Hemat 2', 'Anggur Merah', 'Ayam Hitam Putih ', 'Otak sapi dengan saus tiram', 175000),
(3, 'Paket Hemat 3', 'Puding Lumpur Lapindo', 'Abu Vulkanik Merapi dengan saus tiram', 'Lahar dingin gunung krakatau yang meletus 400 tahun yang lalu', 135000),
(4, 'Paket Hemat 4', 'Coklat bekas becekan hujan', 'Nasi ama kecap dan garem ditambah kerupuk pasir', 'Coca cola rasa yang pernah ada', 156000),
(5, 'Paket Hemat 5', 'Hot Chocolate With Hot Mama', 'Nasi Lemak Jahat', 'Fresh Fruit from the open', 75000),
(6, 'Premium Indian Package', 'Crispy Vagetable Pakoras', 'Sweet Curry With Garlic And Tomatoes', 'Spicy Lenti Dip', 250000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `gedung`
--

CREATE TABLE `gedung` (
  `ID_GEDUNG` int(11) NOT NULL,
  `NAMA_GEDUNG` varchar(255) NOT NULL,
  `KAPASITAS` int(11) NOT NULL,
  `ALAMAT` varchar(255) NOT NULL,
  `HARGA_SEWA` bigint(11) NOT NULL,
  `DESKRIPSI_GEDUNG` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `gedung`
--

INSERT INTO `gedung` (`ID_GEDUNG`, `NAMA_GEDUNG`, `KAPASITAS`, `ALAMAT`, `HARGA_SEWA`, `DESKRIPSI_GEDUNG`) VALUES
(1, 'Smart Office Amphiater', 100, 'Jl. Prof. DR. Supomo No.23, Sriwedari, Kec. Laweyan, Kota Surakarta, Jawa Tengah ', 500000000, 'Wifi Gratis, Lokasi Pusat Kota, Gedung dan Fasilitas Baru, LCD 70\', AC, Parkir Luas'),
(2, 'Smart Office Meeting Room ', 30, 'Jl. Prof. DR. Supomo No.23, Sriwedari, Kec. Laweyan, Kota Surakarta, Jawa Tengah ', 500000000, 'Wifi Gratis, Lokasi Pusat Kota, Gedung dan Fasilitas Baru, LCD 70\', AC, Parkir Luas'),
(3, 'Smart Office Studio Photo', 20, 'Jl. Prof. DR. Supomo No.23, Sriwedari, Kec. Laweyan, Kota Surakarta, Jawa Tengah ', 552000000, 'Wifi Gratis, Lokasi Pusat Kota, Gedung dan Fasilitas Baru, LCD 70\', AC, Parkir Luas'),
(4, 'Smart Office Studio Podcast', 15, 'Jl. KH Mas Mansyur 2 Tanah Abang, Thamrin Jakarta PusatJl. Prof. DR. Supomo No.23, Sriwedari, Kec. Laweyan, KotJl. Prof. DR. Supomo No.23, Sriwedari, Kec. Laweyan, Kota Surakarta, Jawa Tengah a Surakarta, Jawa Tengah ', 765000000, 'Wifi Gratis, Lokasi Pusat Kota, Gedung dan Fasilitas Baru, LCD 70\', AC, Parkir Luas');

-- --------------------------------------------------------

--
-- Struktur dari tabel `gedung_img`
--

CREATE TABLE `gedung_img` (
  `ID_IMG` int(11) NOT NULL,
  `ID_GEDUNG` int(11) NOT NULL,
  `NAMA_GEDUNG` varchar(225) NOT NULL,
  `PATH` varchar(225) NOT NULL,
  `IMG_NAME` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `gedung_img`
--

INSERT INTO `gedung_img` (`ID_IMG`, `ID_GEDUNG`, `NAMA_GEDUNG`, `PATH`, `IMG_NAME`) VALUES
(1, 1, 'The Ritz Carlton', 'http://localhost/Warsito/assets/images/gedung/', 'am2.jpeg'),
(2, 2, 'Pullman Hotel Jakarta', 'http://localhost/Warsito/assets/images/gedung/', 'ast3.jpeg'),
(3, 3, 'Hotel Santika Bintaro', 'http://localhost/Warsito/assets/images/gedung/', 'ast2.jpeg'),
(4, 4, 'Milennium Hotel Jakarta', 'http://localhost/Warsito/assets/images/gedung/', 'am5.jpeg'),
(7, 1, 'The Ritz Carlton', 'http://localhost/Warsito/assets/images/gedung/', 'am2.jpeg'),
(8, 1, 'The Ritz Carlton', 'http://localhost/Warsito/assets/images/gedung/', 'am3.jpeg'),
(9, 1, 'The Ritz Carlton', 'http://localhost/Warsito/assets/images/gedung/', 'am4.jpeg');

-- --------------------------------------------------------

--
-- Stand-in structure for view `home_data`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `home_data` (
`ID_GEDUNG` int(11)
,`HARGA_SEWA` bigint(11)
,`NAMA_GEDUNG` varchar(255)
,`KAPASITAS` int(11)
,`PATH` varchar(225)
,`IMG_NAME` varchar(225)
);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran`
--

CREATE TABLE `pembayaran` (
  `KODE_PEMBAYARAN` varchar(225) NOT NULL DEFAULT 'TRS000',
  `ID_PEMBAYARAN` int(11) NOT NULL,
  `KODE_PEMESANAN` varchar(225) NOT NULL DEFAULT 'PMSN000',
  `ID_PEMESANAN` int(11) NOT NULL,
  `ATAS_NAMA` varchar(225) NOT NULL,
  `NOMINAL_TRANSFER` bigint(20) NOT NULL,
  `BANK_PENGIRIM` varchar(225) NOT NULL,
  `TANGGAL_TRANSFER` date NOT NULL,
  `FLAG` int(11) NOT NULL,
  `PATH` varchar(225) NOT NULL,
  `IMG_NAME` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `pembayaran`
--

INSERT INTO `pembayaran` (`KODE_PEMBAYARAN`, `ID_PEMBAYARAN`, `KODE_PEMESANAN`, `ID_PEMESANAN`, `ATAS_NAMA`, `NOMINAL_TRANSFER`, `BANK_PENGIRIM`, `TANGGAL_TRANSFER`, `FLAG`, `PATH`, `IMG_NAME`) VALUES
('TRS000', 1, 'PMSN000', 6, 'Anton Prio Hutomo', 5826250, 'BCA', '2016-12-25', 1, 'http://localhost/Warsito/assets/images/client-bukti-pembayaran/', 'client-trf_15012017_062106.jpg'),
('TRS000', 2, 'PMSN000', 7, 'Muhammad Dzakwan', 5270000, 'Mandiri', '2016-12-26', 1, 'http://localhost/Warsito/assets/images/client-bukti-pembayaran/', 'client-trf_15012017_062515.jpg'),
('TRS000', 3, 'PMSN000', 9, 'Karina Novilda', 5722500, 'BNI', '2016-12-26', 1, 'http://localhost/Warsito/assets/images/client-bukti-pembayaran/', 'client-trf_15012017_062838.jpg'),
('TRS000', 4, 'PMSN000', 22, 'Muhammad Dzakwan', 5225000, 'BCA', '2017-01-04', 1, 'http://localhost/Warsito/assets/images/client-bukti-pembayaran/', 'client-trf_15012017_063041.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemesanan`
--

CREATE TABLE `pemesanan` (
  `ID_PEMESANAN` int(11) NOT NULL,
  `USERNAME` varchar(255) NOT NULL,
  `EMAIL` varchar(100) NOT NULL,
  `TANGGAL_PEMESANAN` date NOT NULL,
  `JAM_PEMESANAN` time DEFAULT NULL,
  `TIPE_JAM` enum('CUSTOM','HALF_DAY','FULL_DAY') NOT NULL DEFAULT 'CUSTOM',
  `JAM_SELESAI` time DEFAULT NULL,
  `ID_CATERING` int(11) DEFAULT NULL,
  `ID_GEDUNG` int(11) NOT NULL,
  `JUMLAH_CATERING` int(11) DEFAULT NULL,
  `STATUS` int(1) NOT NULL,
  `REMARKS` varchar(225) DEFAULT NULL,
  `FLAG` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `pemesanan`
--

INSERT INTO `pemesanan` (`ID_PEMESANAN`, `USERNAME`, `EMAIL`, `TANGGAL_PEMESANAN`, `JAM_PEMESANAN`, `TIPE_JAM`, `JAM_SELESAI`, `ID_CATERING`, `ID_GEDUNG`, `JUMLAH_CATERING`, `STATUS`, `REMARKS`, `FLAG`) VALUES
(2, 'antonprio', 'antonpriohutomo@gmail.com', '2016-12-16', NULL, 'CUSTOM', NULL, 3, 1, 500, 2, NULL, 0),
(6, 'antonprio', 'antonpriohutomo@gmail.com', '2017-01-07', NULL, 'CUSTOM', NULL, 2, 3, 175, 1, NULL, 0),
(7, 'warsiwan', 'antonprio22@gmail.com', '2016-12-31', NULL, 'CUSTOM', NULL, 3, 1, 200, 1, NULL, 0),
(9, 'awkarin', 'awakarin_gendut@gmail.com', '2016-12-31', NULL, 'CUSTOM', NULL, 3, 3, 150, 1, NULL, 0),
(22, 'warsiwan', 'warsito.rakhman@gmail.com', '2017-01-07', NULL, 'CUSTOM', NULL, 1, 1, 150, 1, NULL, 2),
(23, 'pogbay', 'antonprio22@gmail.com', '2017-02-04', NULL, 'CUSTOM', NULL, 3, 4, 100, 3, NULL, 2),
(24, 'awkarin', 'awakarin_gendut@gmail.com', '2025-12-30', '08:00:00', 'HALF_DAY', '12:00:00', NULL, 1, NULL, 2, 'tidak berguna', 2),
(25, 'awkarin', 'awakarin_gendut@gmail.com', '2025-12-30', '08:00:00', 'HALF_DAY', '12:00:00', NULL, 1, NULL, 1, NULL, 1),
(26, 'awkarin', 'awakarin_gendut@gmail.com', '2025-12-30', '08:00:00', 'HALF_DAY', '12:00:00', NULL, 1, NULL, 0, NULL, 0),
(27, 'awkarin', 'awakarin_gendut@gmail.com', '2025-12-30', '08:00:00', 'HALF_DAY', '12:00:00', NULL, 1, NULL, 0, NULL, 0),
(28, 'awkarin', 'awakarin_gendut@gmail.com', '2025-12-30', '08:00:00', 'HALF_DAY', '12:00:00', NULL, 1, NULL, 3, NULL, 2),
(29, 'awkarin', 'awakarin_gendut@gmail.com', '2025-12-31', '10:00:00', 'CUSTOM', '18:00:00', 4, 1, 10, 3, NULL, 2),
(30, 'awkarin', 'awakarin_gendut@gmail.com', '2025-12-31', '08:00:00', 'FULL_DAY', '17:00:00', 1, 1, 10, 1, NULL, 2),
(31, 'pogbay', 'antonprio22@gmail.com', '2025-12-27', '08:00:00', 'HALF_DAY', '12:00:00', NULL, 4, NULL, 0, NULL, 0),
(32, 'pogbay', 'antonprio22@gmail.com', '2025-12-27', '08:00:00', 'HALF_DAY', '12:00:00', NULL, 4, NULL, 1, NULL, 2),
(33, 'pogbay', 'antonprio22@gmail.com', '2026-01-01', '14:40:00', 'CUSTOM', '16:00:00', NULL, 2, NULL, 1, NULL, 2),
(34, 'pogbay', 'antonprio22@gmail.com', '2025-12-27', '15:00:00', 'CUSTOM', '17:00:00', NULL, 2, NULL, 1, NULL, 2),
(35, 'awkarin', 'awakarin_gendut@gmail.com', '2026-02-02', '08:00:00', 'HALF_DAY', '12:00:00', NULL, 1, NULL, 1, NULL, 2),
(36, 'Wahyu', 'WAHYU@GMAIL.COM', '2026-02-17', '13:00:00', '', '16:00:00', NULL, 3, NULL, 1, NULL, 2),
(37, 'Wahyu', 'WAHYU@GMAIL.COM', '2026-01-22', '13:00:00', '', '16:00:00', NULL, 1, NULL, 1, NULL, 1),
(38, 'Wahyu', 'WAHYU@GMAIL.COM', '2026-06-30', '08:00:00', 'FULL_DAY', '17:00:00', NULL, 1, NULL, 0, NULL, 0),
(39, 'Wahyu', 'WAHYU@GMAIL.COM', '2026-06-30', '08:00:00', 'FULL_DAY', '17:00:00', NULL, 1, NULL, 1, NULL, 2),
(40, 'Wahyu', 'WAHYU@GMAIL.COM', '2026-04-09', '10:00:00', 'CUSTOM', '15:00:00', NULL, 3, NULL, 0, NULL, 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemesanan_details`
--

CREATE TABLE `pemesanan_details` (
  `ID_DETAILS` int(11) NOT NULL,
  `ID_PEMESANAN` int(11) NOT NULL,
  `PATH` varchar(225) NOT NULL,
  `FILE_NAME` varchar(225) NOT NULL,
  `DESKRIPSI_ACARA` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `pemesanan_details`
--

INSERT INTO `pemesanan_details` (`ID_DETAILS`, `ID_PEMESANAN`, `PATH`, `FILE_NAME`, `DESKRIPSI_ACARA`) VALUES
(13, 30, 'http://localhost/Warsito/assets/user-proposal/', 'awkarin_16122025_043131.pdf', 'MENYANYII DENGAN SENANG'),
(14, 32, 'http://localhost/Warsito/assets/user-proposal/', 'pogbay_16122025_043945.pdf', 'mancing'),
(15, 33, 'http://localhost/Warsito/assets/user-proposal/', 'pogbay_16122025_073739.pdf', 'SOK SIBUK AJA SIH'),
(16, 34, 'http://localhost/Warsito/assets/user-proposal/', 'pogbay_16122025_074235.pdf', 'BELAJAR AJA SI'),
(17, 35, 'http://localhost/Warsito/assets/user-proposal/', 'awkarin_16122025_082619.pdf', 'bagi bagi sembakoo'),
(18, 36, 'http://localhost/Warsito/assets/user-proposal/', 'Wahyu_17122025_075737.pdf', 'TESTING TERBARU'),
(19, 39, 'http://localhost/Warsito/assets/user-proposal/', 'Wahyu_18122025_032453.pdf', 'memandu untuk paymen');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemesanan_fix_detail`
--

CREATE TABLE `pemesanan_fix_detail` (
  `ID_FIX_DETAIL` int(11) NOT NULL,
  `ID_PEMESANAN` int(11) NOT NULL,
  `USERNAME` varchar(225) NOT NULL,
  `TANGGAL_APPROVAL` date NOT NULL,
  `TANGGAL_FINAL_PEMESANAN` date NOT NULL,
  `TANGGAL_DEADLINE` date NOT NULL,
  `FINAL_STATUS` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `pemesanan_fix_detail`
--

INSERT INTO `pemesanan_fix_detail` (`ID_FIX_DETAIL`, `ID_PEMESANAN`, `USERNAME`, `TANGGAL_APPROVAL`, `TANGGAL_FINAL_PEMESANAN`, `TANGGAL_DEADLINE`, `FINAL_STATUS`) VALUES
(6, 6, 'antonprio', '2016-12-24', '2017-01-07', '2016-12-26', 1),
(7, 7, 'warsiwan', '2016-12-24', '2016-12-31', '2016-12-26', 1),
(8, 9, 'awkarin', '2016-12-25', '2016-12-31', '2016-12-27', 1),
(27, 22, 'warsiwan', '2017-01-22', '2017-01-07', '2017-01-24', 1),
(47, 23, 'pogbay', '2017-01-24', '2017-02-04', '2017-01-26', 2),
(48, 30, 'awkarin', '2025-12-16', '2025-12-31', '2025-12-18', 1),
(49, 32, 'pogbay', '2025-12-16', '2025-12-27', '2025-12-18', 1),
(50, 33, 'pogbay', '2025-12-16', '2026-01-01', '2025-12-18', 1),
(51, 34, 'pogbay', '2025-12-16', '2025-12-27', '2025-12-18', 1),
(52, 35, 'awkarin', '2025-12-16', '2026-02-02', '2025-12-18', 1),
(53, 25, 'awkarin', '2025-12-17', '2025-12-30', '2025-12-19', 1),
(54, 36, 'Wahyu', '2025-12-17', '2026-02-17', '2025-12-19', 1),
(55, 37, 'Wahyu', '2025-12-18', '2026-01-22', '2025-12-20', 1),
(56, 39, 'Wahyu', '2025-12-18', '2026-06-30', '2025-12-20', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `perawatan`
--

CREATE TABLE `perawatan` (
  `ID_PERAWATAN` int(11) NOT NULL,
  `NO_ID` varchar(225) NOT NULL,
  `NAMA_PERAWATAN` varchar(225) NOT NULL,
  `NAMA_GEDUNG` varchar(225) NOT NULL,
  `TANGGAL_PEMBAYARAN` date NOT NULL,
  `BIAYA` bigint(15) NOT NULL,
  `PATH` varchar(225) NOT NULL,
  `IMG_NAME` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `perawatan`
--

INSERT INTO `perawatan` (`ID_PERAWATAN`, `NO_ID`, `NAMA_PERAWATAN`, `NAMA_GEDUNG`, `TANGGAL_PEMBAYARAN`, `BIAYA`, `PATH`, `IMG_NAME`) VALUES
(2, 'REFFF1123344212FF332', 'Pembayaran Listrik', 'Millenium Hotel Jakarta', '2016-12-13', 2500000, 'http://localhost/Warsito/assets/images/bukti-pembayaran/', 'Listrik_10122016_075521'),
(3, 'RFF009II887244', 'Pembayaran Air', 'Hotel Santika Bintaro', '2016-12-20', 6500000, 'http://localhost/Warsito/assets/images/bukti-pembayaran/', 'Air_10122016_075943'),
(6, 'KBRSHN1109231113', 'Pembayaran Kebersihan', 'Pullman Hotel Jakarta', '2016-12-24', 500000, 'http://localhost/Warsito/assets/images/bukti-pembayaran/', 'Kebersihan_10122016_080518'),
(7, 'REF1442123321', 'Pembayaran Air', 'The Ritz Carlton Jakarta', '2017-01-19', 150000000, 'http://localhost/Warsito/assets/images/bukti-pembayaran/', 'Air_29012017_063355');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `USERNAME` varchar(225) NOT NULL,
  `NAMA_LENGKAP` varchar(225) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `EMAIL` varchar(255) NOT NULL,
  `NO_TELEPON` varchar(15) NOT NULL,
  `ALAMAT` varchar(225) NOT NULL,
  `TANGGAL_LAHIR` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`USERNAME`, `NAMA_LENGKAP`, `PASSWORD`, `EMAIL`, `NO_TELEPON`, `ALAMAT`, `TANGGAL_LAHIR`) VALUES
('antonprio', 'Anton Prio Hutomo', '123321', 'antonpriohutomo@gmail.com', '087877878215', 'Jl. Bintaro Raya 6 No 18 RT 011 RW 12 Tangerang Selatan Banten Indonesia 11232', '1995-01-22'),
('awkarin', 'Karin Novilda', 'awkarin', 'awakarin_gendut@gmail.com', '08562443213', 'Jl. Pembangunan Jaya no 11 Bintaro Tangerang Selatan', '1997-06-04'),
('pogbay', 'Paul Pogbay Anapi', 'qwerty', 'antonprio22@gmail.com', '0217810626', 'Jl. Tegalrotan 3 No 11 RT 03 RW 01 Kelurahan Pondok Aren Kecamatan Jurangmangu Tangerang Selatan', '1992-01-03'),
('Wahyu', 'WAHYUUUUU', 'Wahyu', 'WAHYU@GMAIL.COM', '2899999999999', 'CENTER AFRIKA', '2009-02-12'),
('warsiwan', 'Muhammad Warsito Dzakwan Valdiansyah Rakhman', 'password123', 'antonprio22@gmail.com', '08134457833', 'Jl. Sepatan Utara No 11 Sepatan Barat Kabupaten Tangerang, Provinsi Banten ', '1992-06-14');

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_pemesanan`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_pemesanan` (
`ID_PEMESANAN` varchar(18)
,`USERNAME` varchar(255)
,`TANGGAL_PEMESANAN` date
,`JAM_PEMESANAN` time
,`JAM_SELESAI` time
,`EMAIL` varchar(100)
,`JUMLAH_CATERING` varchar(11)
,`NAMA_PAKET` varchar(225)
,`NAMA_GEDUNG` varchar(255)
,`HARGA_SATUAN` bigint(15)
,`TOTAL_HARGA` bigint(30)
,`STATUS` varchar(23)
,`HARGA_SEWA` bigint(11)
,`TOTAL_KESELURUHAN` bigint(31)
,`DESKRIPSI_ACARA` varchar(225)
,`REMARKS` varchar(225)
);

-- --------------------------------------------------------

--
-- Struktur untuk view `home_data`
--
DROP TABLE IF EXISTS `home_data`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `home_data`  AS  select `gedung`.`ID_GEDUNG` AS `ID_GEDUNG`,`gedung`.`HARGA_SEWA` AS `HARGA_SEWA`,`gedung`.`NAMA_GEDUNG` AS `NAMA_GEDUNG`,`gedung`.`KAPASITAS` AS `KAPASITAS`,`gedung_img`.`PATH` AS `PATH`,`gedung_img`.`IMG_NAME` AS `IMG_NAME` from (`gedung` join `gedung_img` on((`gedung_img`.`ID_GEDUNG` = `gedung`.`ID_GEDUNG`))) group by `gedung`.`ID_GEDUNG` ;

-- --------------------------------------------------------

--
-- Struktur untuk view `v_pemesanan`
--
DROP TABLE IF EXISTS `v_pemesanan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_pemesanan`  AS  select concat('PMSN000',`p`.`ID_PEMESANAN`) AS `ID_PEMESANAN`,`p`.`USERNAME` AS `USERNAME`,`p`.`TANGGAL_PEMESANAN` AS `TANGGAL_PEMESANAN`,`p`.`JAM_PEMESANAN` AS `JAM_PEMESANAN`,`p`.`JAM_SELESAI` AS `JAM_SELESAI`,`p`.`EMAIL` AS `EMAIL`,coalesce(`p`.`JUMLAH_CATERING`,'Tidak Ada') AS `JUMLAH_CATERING`,coalesce(`c`.`NAMA_PAKET`,'Tidak Ada') AS `NAMA_PAKET`,`g`.`NAMA_GEDUNG` AS `NAMA_GEDUNG`,`c`.`HARGA` AS `HARGA_SATUAN`,coalesce((`c`.`HARGA` * `p`.`JUMLAH_CATERING`),0) AS `TOTAL_HARGA`,(case `p`.`STATUS` when 0 then 'PENDING' when 1 then 'DISETUJUI' when 2 then 'DITOLAK' when 3 then 'CANCELED WITH REFUND' when 4 then 'CANCELED WITHOUT REFUND' end) AS `STATUS`,`g`.`HARGA_SEWA` AS `HARGA_SEWA`,(`g`.`HARGA_SEWA` + coalesce((`c`.`HARGA` * `p`.`JUMLAH_CATERING`),0)) AS `TOTAL_KESELURUHAN`,`pemesanan_details`.`DESKRIPSI_ACARA` AS `DESKRIPSI_ACARA`,`p`.`REMARKS` AS `REMARKS` from (((`pemesanan` `p` left join `catering` `c` on((`c`.`ID_CATERING` = `p`.`ID_CATERING`))) left join `gedung` `g` on((`g`.`ID_GEDUNG` = `p`.`ID_GEDUNG`))) left join `pemesanan_details` on((`pemesanan_details`.`ID_PEMESANAN` = `p`.`ID_PEMESANAN`))) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `catering`
--
ALTER TABLE `catering`
  ADD PRIMARY KEY (`ID_CATERING`);

--
-- Indexes for table `gedung`
--
ALTER TABLE `gedung`
  ADD PRIMARY KEY (`ID_GEDUNG`);

--
-- Indexes for table `gedung_img`
--
ALTER TABLE `gedung_img`
  ADD PRIMARY KEY (`ID_IMG`),
  ADD KEY `gedung_img_ibfk_1` (`ID_GEDUNG`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`ID_PEMBAYARAN`);

--
-- Indexes for table `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD PRIMARY KEY (`ID_PEMESANAN`),
  ADD KEY `USERNAME` (`USERNAME`),
  ADD KEY `ID_GEDUNG` (`ID_GEDUNG`),
  ADD KEY `ID_CATERING` (`ID_CATERING`);

--
-- Indexes for table `pemesanan_details`
--
ALTER TABLE `pemesanan_details`
  ADD PRIMARY KEY (`ID_DETAILS`),
  ADD KEY `ID_PEMESANAN` (`ID_PEMESANAN`);

--
-- Indexes for table `pemesanan_fix_detail`
--
ALTER TABLE `pemesanan_fix_detail`
  ADD PRIMARY KEY (`ID_FIX_DETAIL`),
  ADD KEY `ID_PEMESANAN` (`ID_PEMESANAN`);

--
-- Indexes for table `perawatan`
--
ALTER TABLE `perawatan`
  ADD PRIMARY KEY (`ID_PERAWATAN`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`USERNAME`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `catering`
--
ALTER TABLE `catering`
  MODIFY `ID_CATERING` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `gedung`
--
ALTER TABLE `gedung`
  MODIFY `ID_GEDUNG` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `gedung_img`
--
ALTER TABLE `gedung_img`
  MODIFY `ID_IMG` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `ID_PEMBAYARAN` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `pemesanan`
--
ALTER TABLE `pemesanan`
  MODIFY `ID_PEMESANAN` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;
--
-- AUTO_INCREMENT for table `pemesanan_details`
--
ALTER TABLE `pemesanan_details`
  MODIFY `ID_DETAILS` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `pemesanan_fix_detail`
--
ALTER TABLE `pemesanan_fix_detail`
  MODIFY `ID_FIX_DETAIL` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;
--
-- AUTO_INCREMENT for table `perawatan`
--
ALTER TABLE `perawatan`
  MODIFY `ID_PERAWATAN` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `gedung_img`
--
ALTER TABLE `gedung_img`
  ADD CONSTRAINT `gedung_img_ibfk_1` FOREIGN KEY (`ID_GEDUNG`) REFERENCES `gedung` (`ID_GEDUNG`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD CONSTRAINT `pemesanan_ibfk_1` FOREIGN KEY (`USERNAME`) REFERENCES `user` (`USERNAME`),
  ADD CONSTRAINT `pemesanan_ibfk_2` FOREIGN KEY (`ID_GEDUNG`) REFERENCES `gedung` (`ID_GEDUNG`),
  ADD CONSTRAINT `pemesanan_ibfk_3` FOREIGN KEY (`ID_CATERING`) REFERENCES `catering` (`ID_CATERING`);

--
-- Ketidakleluasaan untuk tabel `pemesanan_details`
--
ALTER TABLE `pemesanan_details`
  ADD CONSTRAINT `pemesanan_details_ibfk_1` FOREIGN KEY (`ID_PEMESANAN`) REFERENCES `pemesanan` (`ID_PEMESANAN`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pemesanan_fix_detail`
--
ALTER TABLE `pemesanan_fix_detail`
  ADD CONSTRAINT `pemesanan_fix_detail_ibfk_1` FOREIGN KEY (`ID_PEMESANAN`) REFERENCES `pemesanan` (`ID_PEMESANAN`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
