-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 22 Des 2024 pada 11.52
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vitamin_chatbot`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `chat_history`
--

CREATE TABLE `chat_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `response` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `keywords`
--

CREATE TABLE `keywords` (
  `id` int(11) NOT NULL,
  `keyword` varchar(100) NOT NULL,
  `response` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `live_chats`
--

CREATE TABLE `live_chats` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `sender` enum('user','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `shopee_link` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image_path`, `shopee_link`, `created_at`) VALUES
(5, 'ULTIGAR VITAMIN E 400 IU', 'POM SD. 212374361', 1.00, '../uploads/products/vitamin_e_400.png', 'https://collshp.com/apylotaa', '2024-12-21 13:45:40'),
(6, 'ULTIGAR VITAMIN D3 1000 IU', 'POM SD. 212385371\r\n', 1.00, '../uploads/products/vitamin_d3_1000.png', 'https://collshp.com/apylotaa', '2024-12-21 13:48:50'),
(8, 'ULTIGAR VITAMIN K2 D3', 'POM SD. 212387041', 1.00, '../uploads/products/vitamin_k2d3.png', 'https://collshp.com/apylotaa', '2024-12-21 13:51:14'),
(9, 'ULTIGAR VITAMIN D3 4000 IU', 'POM SD. 223037351', 1.00, '../uploads/products/vitamin_d3_4000.png', 'https://collshp.com/apylotaa', '2024-12-21 13:52:10'),
(10, 'ULTIGAR VITAMIN FISH OIL OMEGA 1000 MG', 'POM SD 223034961\r\n', 1.00, '../uploads/products/vitamin_omega3.png', 'https://collshp.com/apylotaa', '2024-12-21 13:53:34'),
(11, 'ULTIGAR VITAMIN HABBATUSSAUDA PROPOLIS', 'POM TR 223032751\r\n', 1.00, '../uploads/products/vitamin_habbatussauda.png', 'https://collshp.com/apylotaa', '2024-12-21 13:54:59'),
(12, 'ULTIGAR VITAMIN EKSTRAK GAMAT', 'POM TR 223036311\r\n', 1.00, '../uploads/products/vitamin_gamat.png', 'https://collshp.com/apylotaa', '2024-12-21 13:55:53'),
(13, 'ULTIGAR VITAMIN C 1000 MG', 'POM SD. 225022311\r\n', 1.00, '../uploads/products/vitamin_c_1000.png', 'https://collshp.com/apylotaa', '2024-12-21 14:05:43'),
(14, 'ULTIGAR VITAMIN MAGNESIUM 250 MG', 'POM SD. 225062561\r\n', 1.00, '../uploads/products/vitamin_magnesium.png', 'https://collshp.com/apylotaa', '2024-12-21 14:06:49'),
(15, 'ULTIGAR VITAMIN WHITE WILLOW BARK', 'POM TR 223005611\r\n', 1.00, '../uploads/products/vitamin_white_willow.png', 'https://collshp.com/apylotaa', '2024-12-21 14:11:35'),
(16, 'ULTIGAR VITAMIN CITICOLINE 500MG', 'POM SD 223044111', 1.00, '../uploads/products/vitamin_citicoline.png', 'https://collshp.com/apylotaa', '2024-12-21 14:14:34'),
(17, 'ULTIGAR VITAMIN C 500 MG', 'POM SD.225041841', 1.00, '../uploads/products/vitamin_c_500.png', 'https://collshp.com/apylotaa', '2024-12-21 14:15:27'),
(18, 'ULTIGAR VITAMIN GLUCOSAMINE FISH OIL', 'POM SD 223000161', 1.00, '../uploads/products/vitamin_glucosamine.png', 'https://collshp.com/apylotaa', '2024-12-21 14:17:40'),
(19, 'ULTIGAR VITAMIN EKSTRAK IKAN GABUS', 'POM TR 223027721\r\n', 1.00, '../uploads/products/vitamin_ikan_gabus.png', 'https://collshp.com/apylotaa', '2024-12-21 14:18:28'),
(20, 'ULTIGAR VITAMIN EKSTRAK CACING TANAH 500MG', 'POM TR 223027741\r\n', 1.00, '../uploads/products/vitamin_cacing_tanah.png', 'https://collshp.com/apylotaa', '2024-12-21 14:19:41'),
(21, 'ULTIGAR VITAMIN ECHINACEA 500 MG', 'POM TR. 212300581\r\n', 1.00, '../uploads/products/vitamin_echinacea.png', 'https://collshp.com/apylotaa', '2024-12-21 14:21:08'),
(28, 'ULTIGAR HABBATUSSAUDA 500 MG', 'POM TR 223027731', 1.00, '../uploads/products/vitamin_habbatussauda.png', 'https://collshp.com/apylotaa', '2024-12-22 07:12:36'),
(29, 'ULTIGAR VITAMIN B-COMPLEX', 'POM SD 225054981\r\n', 1.00, '../uploads/products/vitamin_b_complex.png', 'https://collshp.com/apylotaa', '2024-12-22 07:15:29'),
(30, 'ULTIGAR VITAMIN B12 50MCG', 'BPOM RI MD 270331145219', 1.00, '../uploads/products/Screenshot 2024-12-22 141723.png', 'https://collshp.com/apylotaa', '2024-12-22 07:17:45'),
(31, 'VITAMIN HILBAT', 'BPOM RI MD 270331145219\r\n', 1.00, '../uploads/products/vitamin_hilbat.png', 'https://collshp.com/apylotaa', '2024-12-22 07:19:33'),
(32, 'VITAMIN FRUGGIEZ', 'BPOM RI MD 867531071219', 1.00, '../uploads/products/vitamin_fruggiez.png', 'https://collshp.com/apylotaa', '2024-12-22 07:20:06'),
(33, 'VITAMIN GUNABUMIN', 'BPOM RI MD 270331185219', 1.00, '../uploads/products/Screenshot 2024-12-22 142034.png', 'https://collshp.com/apylotaa', '2024-12-22 07:21:26'),
(34, 'ULTIGAR VITAMIN COQ10', '-', 1.00, '../uploads/products/vitamin_coq10.png', 'https://collshp.com/apylotaa', '2024-12-22 07:23:21'),
(35, 'ULTIGAR VITAMIN B-COMPLEX GOLD', 'POM SD 225054981\r\n', 1.00, '../uploads/products/vitamin_b_complex_gold.png', 'https://collshp.com/apylotaa', '2024-12-22 07:24:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'salak', '$2y$10$lGfqY4OkOxZYUoJjeD5IZuh/wedjT634ZhMIxr4GKPQKZ8IKOoEAS', 'admin'),
(3, 'sule', '$2y$10$.5feFLUsbC7qBtWVs2crJOpW/KQds8ZIqLvEVezsP7RWvl5OGN75y', 'user'),
(5, 'zena', '$2y$10$Wba1PdYcK/8eQJnjxc3F3OiAHJbOxOWSYxoJ/KSKVnosfN.A3yiwu', 'admin'),
(9, 'yanto', '$2y$10$juKOLgUR2QEQtOmRpw0KjuG4K8SxfX.q6jrVGhl4MquuQ2isOTMyC', 'user'),
(10, 'admin001', '$2y$10$h27Oz7G8oafqB.kH9D/E4uObY8//6XDxJUfM3zFP0uhJyivd8Qv5C', 'admin'),
(11, 'user001', '$2y$10$htGJBWhlmNQgZzmpnCcgfeM9Y26qFwXdFT0BbsRLnABG432YZtPiK', 'user');

-- --------------------------------------------------------

--
-- Struktur dari tabel `vitamins`
--

CREATE TABLE `vitamins` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `keywords` text NOT NULL,
  `description` text NOT NULL,
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `vitamins`
--

INSERT INTO `vitamins` (`id`, `name`, `keywords`, `description`, `image_url`) VALUES
(1, 'Vitamin D3 1000 IU', 'mudah lelah, kecapean', 'Membantu mengatasi mudah lelah dan kecapean.', 'vitamin_d3_1000.png'),
(2, 'Vitamin D3 4000 IU', 'badan sering terasa sakit, autoimun', 'Mendukung kesehatan tulang dan kekebalan tubuh.', 'vitamin_d3_4000.png'),
(3, 'Vitamin C 500 MG', 'imun badan turun, mudah terserang virus, kulit tidak sehat', 'Meningkatkan daya tahan tubuh dan mendukung kesehatan kulit.', 'vitamin_c_500.png'),
(4, 'Vitamin C 1000 MG', 'imun tubuh menurun, sering sakit', 'Meningkatkan sistem kekebalan tubuh dan mencegah penyakit.', 'vitamin_c_1000.png'),
(5, 'Vitamin E 400 IU', 'permasalahan kulit, kesehatan mata, kekurangan antioksidan', 'Mendukung kesehatan kulit, mata, dan kaya antioksidan.', 'vitamin_e_400.png'),
(6, 'Vitamin Magnesium', 'hipertensi, sulit tidur, kolesterol', 'Membantu mengatur tekanan darah, meningkatkan kualitas tidur, dan mengontrol kolesterol.', 'vitamin_magnesium.png'),
(7, 'Vitamin Glucosamine', 'nyeri, sakit sendi dan otot', 'Mengurangi nyeri dan mendukung kesehatan sendi.', 'vitamin_glucosamine.png'),
(8, 'Vitamin Omega 3', 'daya ingat, fungsi otak, pertumbuhan janin', 'Mendukung fungsi otak, daya ingat, dan kesehatan janin.', 'vitamin_omega3.png'),
(9, 'Vitamin K2D3', 'rambut rontok, pertumbuhan tulang dan gigi', 'Membantu pertumbuhan tulang dan gigi serta mengurangi rambut rontok.', 'vitamin_k2d3.png'),
(10, 'Vitamin Ekstrak ikan gabus', 'kekurangan protein, penyembuhan luka', 'Meningkatkan kadar protein/albumin dan membantu penyembuhan luka.', 'vitamin_ikan_gabus.png'),
(11, 'Vitamin B12', 'kesemutan, sering lelah, anemia', 'Meningkatkan energi, mengurangi kesemutan, dan mengatasi anemia.', 'vitamin_b12.png'),
(12, 'Vitamin COQ10', 'promil, meningkatkan gairah seks, kesuburan sperma', 'Mendukung program kehamilan dan meningkatkan kesuburan.', 'vitamin_coq10.png'),
(13, 'Vitamin Fruggiez', 'multivitamin anak-anak', 'Mendukung kesehatan dan pertumbuhan anak-anak.', 'vitamin_fruggiez.png'),
(14, 'Vitamin B Complex', 'nafsu makan, perbaikan sel rusak, stress', 'Meningkatkan nafsu makan, memperbaiki sel, dan mengurangi stress.', 'vitamin_b_complex.png'),
(15, 'Vitamin B Complex Gold', 'nafsu makan, syaraf, energi, stress', 'Mendukung kesehatan saraf, energi, dan mengurangi stress.', 'vitamin_b_complex_gold.png'),
(16, 'Vitamin Habbatussauda', 'mengontrol gula darah, kolesterol, antioksidan', 'Membantu mengontrol gula darah dan kolesterol, serta kaya antioksidan.', 'vitamin_habbatussauda.png'),
(17, 'Vitamin Gamat', 'gerd, maag, kista, osteoporosis, penyembuhan luka', 'Mengatasi masalah lambung, tulang, dan mempercepat penyembuhan luka.', 'vitamin_gamat.png'),
(18, 'Vitamin Cacing Tanah', 'demam, dbd, diare, cacingan', 'Mengatasi demam, DBD, dan infeksi parasit.', 'vitamin_cacing_tanah.png'),
(19, 'Vitamin Echinacea', 'alergi, kekebalan daya tahan tubuh', 'Meningkatkan kekebalan tubuh dan mengurangi alergi.', 'vitamin_echinacea.png'),
(20, 'Vitamin Ashwaganda', 'kecemasan, sulit tidur, energi fisik', 'Mengurangi kecemasan, meningkatkan energi, dan kualitas tidur.', 'vitamin_ashwaganda.png'),
(21, 'Vitamin Citicoline', 'stroke, hilang ingatan', 'Membantu pemulihan stroke dan memperbaiki daya ingat.', 'vitamin_citicoline.png'),
(22, 'Vitamin White Willow Bark', 'nyeri, peradangan', 'Mengurangi nyeri dan peradangan.', 'vitamin_white_willow.png'),
(23, 'Vitamin Hilbat', 'batuk, flu', 'Meredakan batuk dan flu.', 'vitamin_hilbat.png'),
(24, 'Vitamin Zinc', 'peradangan tubuh, jerawat', 'Mengurangi peradangan tubuh dan membantu mengatasi jerawat.', 'vitamin_zinc.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `vitamin_images`
--

CREATE TABLE `vitamin_images` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `vitamin_images`
--

INSERT INTO `vitamin_images` (`id`, `image_path`, `created_at`) VALUES
(1, '../uploads/hitzewallungen.jpg', '2024-12-20 18:47:31'),
(2, '../uploads/hitzewallungen.jpg', '2024-12-20 18:47:42'),
(3, '../uploads/productsThe Ultimate Shopping Guide For Sustainable Vitamins — Sustainably Chic.jpg', '2024-12-20 19:05:45'),
(4, '../uploads/products/vitamin_coq10.png', '2024-12-22 05:44:07');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `chat_history`
--
ALTER TABLE `chat_history`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `keywords`
--
ALTER TABLE `keywords`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `live_chats`
--
ALTER TABLE `live_chats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_id` (`session_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `vitamins`
--
ALTER TABLE `vitamins`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `vitamin_images`
--
ALTER TABLE `vitamin_images`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `chat_history`
--
ALTER TABLE `chat_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `keywords`
--
ALTER TABLE `keywords`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `live_chats`
--
ALTER TABLE `live_chats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT untuk tabel `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `vitamins`
--
ALTER TABLE `vitamins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `vitamin_images`
--
ALTER TABLE `vitamin_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `live_chats`
--
ALTER TABLE `live_chats`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
