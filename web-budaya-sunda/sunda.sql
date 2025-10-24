-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: mysql:3306
-- Waktu pembuatan: 24 Okt 2025 pada 18.11
-- Versi server: 8.4.6
-- Versi PHP: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sunda`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id_admin` int NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `level` enum('on','off') COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `foto` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `email`, `username`, `password`, `level`, `created_at`, `foto`) VALUES
(3, 'tipleng@gmail.com', 'lionel', '$2y$10$imeai3PkieU5NuNlltdm4Ozx3TIU0aGurxVszjkerOk4RGOUTFl0C', 'on', '2025-10-24 03:23:02', 'profile_3_1761040455.png'),
(7, 'tesaja@gmail.com', 'lionsoerjadi', '$2y$10$yTyHzqf6pkAto7VwOqFXgeaQ.up8OfYq4VUwdNZgW7VBdTh2db6Ce', 'on', '2025-10-22 06:02:49', 'default.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `alatmusik`
--

CREATE TABLE `alatmusik` (
  `id_alatmusik` int NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `gambar` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_general_ci NOT NULL,
  `asal_usul2` text COLLATE utf8mb4_general_ci NOT NULL,
  `cara_pembuatan` text COLLATE utf8mb4_general_ci NOT NULL,
  `cara_permainan` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `alatmusik`
--

INSERT INTO `alatmusik` (`id_alatmusik`, `nama`, `gambar`, `deskripsi`, `asal_usul2`, `cara_pembuatan`, `cara_permainan`, `created_at`) VALUES
(1, 'Angklung', 'alat_68f7986c92ef51.89361864.png', 'Angklung adalah alat musik tradisional yang berasal dari Jawa Barat, terbuat dari bambu, dan dimainkan dengan cara digoyangkan. Setiap angklung hanya menghasilkan satu nada, sehingga dimainkan berkelompok untuk menciptakan melodi. Angklung diakui sebagai warisan budaya takbenda dunia oleh UNESCO sejak tahun 2010.', 'Catatan mengenai angklung yang baru muncul merujuk pada masa Kerajaan Sunda (abad ke-12 sampai abad ke-16). Asal-usul terciptanya musik bambu seperti angklung berdasar pada pandangan hidup masyarakat Sunda yang agraris dengan sumber kehidupan dari padi (paré) sebagai makanan pokoknya. Hal ini melahirkan mitos kepercayaan terhadap Nyai Sri Pohaci sebagai lambang Dewi Padi pemberi kehidupan (hirup-hurip). Masyarakat Badui, yang dianggap sebagai sisa-sisa masyarakat Sunda asli, menerapkan angklung sebagai bagian dari ritual mengawali penanaman padi. Permainan angklung gubrag di Jasinga, Bogor adalah salah satu yang masih hidup sejak lebih dari 400 tahun lampau. Kemunculannya berawal dari ritus padi. Angklung diciptakan dan dimainkan untuk memikat Dewi Sri turun ke bumi agar tanaman padi rakyat tumbuh subur.', 'Pembuatan angklung melibatkan proses rumit mulai dari pemilihan bambu berusia 4-6 tahun, pengeringan dan pengawetan, pembuatan tabung suara dan rangka, hingga penyeteman nada yang presisi. Bambu kemudian dipotong, dikeringkan, diawetkan, lalu diolah menjadi tabung-tabung dengan ukuran berbeda untuk menghasilkan nada-nada spesifik yang akan dipasang ke kerangka bambu menggunakan tali rotan.', 'Cara memainkan angklung adalah dengan memegang rangkanya dengan satu tangan, lalu tangan lainnya digunakan untuk menggoyangkan atau menggetarkan angklung sehingga tabung bambunya bergetar dan menghasilkan suara. Ada tiga teknik utama: Kurulung (getar/Getar) dengan mengayunkan secara lembut dan terus-menerus, Cetok (Sentak) dengan menarik tabung dasar secara cepat untuk nada tunggal, dan Tangkep dengan menahan salah satu tabung agar tidak bergetar untuk nada yang berbeda.', '2025-10-21 14:27:56');

-- --------------------------------------------------------

--
-- Struktur dari tabel `destinasi`
--

CREATE TABLE `destinasi` (
  `id_destinasi` int NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_general_ci NOT NULL,
  `gambar` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `lokasi` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `link__maps` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `destinasi`
--

INSERT INTO `destinasi` (`id_destinasi`, `nama`, `deskripsi`, `gambar`, `lokasi`, `link__maps`, `created_at`) VALUES
(6, 'Kawah Putih', 'Kawah Putih adalah sebuah tempat wisata di Jawa Barat yang terletak di Desa Alam Endah, Kecamatan Rancabali, Kabupaten Bandung, Jawa Barat yang terletak di kaki Gunung Patuha. Kawah putih merupakan sebuah danau yang terbentuk dari letusan Gunung Patuha.', 'dest_68f78c41aa12e0.84245944.jpg', 'Sugihmukti, Kec. Pasirjambu, Kabupaten Bandung, Jawa Barat', 'https://maps.app.goo.gl/VLSJ6CZ1wDGZepUA7', '2025-10-21 13:46:50'),
(7, 'Tangkuban Perahu', 'Gunung Tangkuban Parahu adalah salah satu gunung yang terletak di antara Kabupaten Subang dan Kabupaten Bandung Barat, Provinsi Jawa Barat, Indonesia.', 'dest_68f78f8838ecc4.26419945.jpg', 'Cikahuripan, Kec. Lembang, Kabupaten Bandung Barat, Jawa Barat', 'https://maps.app.goo.gl/8fTpCyMucmLJUaGu5', '2025-10-21 13:50:36');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int NOT NULL,
  `nama_kategori` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `gambar` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`, `gambar`, `created_at`) VALUES
(9, 'Destinasi', 'kat_68f78174e69fe9.68029192.png', '2025-10-21 12:49:56'),
(10, 'Alat Musik', 'kat_68f7939befe5a2.73027163.jpeg', '2025-10-21 14:07:23'),
(11, 'Lagu Tradisional', 'kat_68f798f5e621c2.36686935.png', '2025-10-21 14:30:13'),
(14, 'Tari Tradisional', 'kat_68f7a1192ec0f3.55991665.png', '2025-10-21 15:04:57'),
(15, 'Makanan Tradisional', 'kat_68f7ac5e6a3ac4.63501256.png', '2025-10-21 15:53:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kuliner`
--

CREATE TABLE `kuliner` (
  `id_kuliner` int NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `gambar` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `asal_usul` text COLLATE utf8mb4_general_ci NOT NULL,
  `bahan` text COLLATE utf8mb4_general_ci NOT NULL,
  `cara_pembuatan` text COLLATE utf8mb4_general_ci NOT NULL,
  `cara_penyajian` text COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kuliner`
--

INSERT INTO `kuliner` (`id_kuliner`, `nama`, `gambar`, `asal_usul`, `bahan`, `cara_pembuatan`, `cara_penyajian`, `deskripsi`, `created_at`) VALUES
(1, 'Bakso Cuanki', 'kul_68f7b49939f536.85000868.png', 'Bandung, Jawa Barat', '400 gr paha ayam fillet\r\n2 sdt bawang putih bubuk\r\n2 sm bawang goreng\r\n1 sdt garam\r\n2 sdt kaldu jamur\r\n1/2 sdt merica\r\n1 sdm kecap ikan\r\n2 sdt baking powder\r\n2 butir telur\r\n130 grm air es\r\n275 grm tapioka\r\n1 pak kulit pangsit\r\n10 bh tahu\r\nBahan kuah baso\r\n3000 ml air\r\n2 sdm minyak bawang putih bersama bawang putih goreng\r\n1 sdm kaldu ayam bubuk\r\n1/2 sdt lada bubuk\r\n1 sdt garam\r\n1/2 sdt penyedap\r\n1 sdm kecap asin\r\nW batang daun bawang, iris kasar\r\nBahan Pelengkap:\r\nSecukupnya Kuah Baso, Sambal baso dan soto, kecap manis, bawang goreng, daun bawang', '1. Masukkan semua bahan kecuali Tepung ke dalam choper. Giling hingga halus.\r\n2. Setelah halus masukkan tepung tapioka perlahan, lalu aduk kembali hingga adonan tercampur rata.\r\n3. Pangsit: ambil kulit pangsit, lalu isi dengan adonan.\r\n4. Goreng dalam minyak panas hingga kuning kecoklatan, angkat dan sisihkan\r\n5. Tahu baso, korek bagian tengah tahu, isi dengan adonan. Masukkan ke dalam air panas, masak dengan api kecil selama 7 menit, angkat dan sisihkan. Kalo pake tahu goreng lakukan hal yg sama\r\n6. Panaskan air. Bentuk adonan menjadi bulat. masukkan ke dalam air panas, masak dengan api kecil selama 7 - 10 menit, hingga bakso mengambang. angkat dan sisihkan\r\n7. Susun Baso, baso tahu dan pangsit dalam mangkok. Siram dengan kuah bakso. Tambahkan bawang goreng dan daun bawang. Sajikan dengan sambal dan kecap\r\n8. Cara membuat kuah baso. Panaskan air beserta kaldu ayam. Masukkan bawang putih goreng dan seasoning. Koreksi rasa\r\n9. Tambahkan kecap asin dan daun bawang, angkat. Jadi kuah utk baso cuanki', '1. Siapkan kuah. Panaskan kuah kaldu sapi atau ayam yang telah dimasak bersama bumbu halus (bawang putih, bawang merah, merica), seledri, dan daun bawang.\r\n2. Susun isian. Atur aneka isian bakso cuanki, seperti bakso sapi atau ayam, tahu goreng, tahu putih, siomay goreng, siomay kukus, dan pangsit goreng dalam mangkuk.\r\n3. Tuang kuah panas. Siram kuah kaldu yang masih panas ke dalam mangkuk hingga semua isian terendam.\r\n4. Tambahkan taburan. Beri taburan bawang goreng dan potongan seledri di atasnya.\r\n5. Sajikan selagi hangat. Sajikan cuanki segera dalam keadaan hangat untuk cita rasa yang maksimal.', 'Cuanki adalah salah satu jajanan yang populer dari kota Bandung yang berbahan dasar ikan, daging sapi, tepung tapioka, dan bumbu penyedap lainnya yang disajikan dengan kuah kaldu yang kuat berisi bakso, siomay kukus, siomay goreng, tahu goreng, dan tahu rebus dengan taburan bawang goreng dan daun seledri.', '2025-10-21 16:28:09');

-- --------------------------------------------------------

--
-- Struktur dari tabel `lagu`
--

CREATE TABLE `lagu` (
  `id_lagu` int NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `gambar` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `audio` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `lagu`
--

INSERT INTO `lagu` (`id_lagu`, `nama`, `gambar`, `audio`, `created_at`) VALUES
(1, 'Manuk Dadali', 'img_68f79e891155e4.86195219.png', 'lagu_68f79e89117a13.80369527.mp3', '2025-10-21 14:54:01');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tari`
--

CREATE TABLE `tari` (
  `id_tari` int NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `gambar` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_general_ci NOT NULL,
  `link_video` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tari`
--

INSERT INTO `tari` (`id_tari`, `nama`, `gambar`, `deskripsi`, `link_video`, `created_at`) VALUES
(6, 'Tari Jaipong', 'tar_68f7a9a83ae967.77071080.png', 'Tari Jaipong adalah tari kreasi tradisional dari Jawa Barat yang diciptakan oleh Gugum Gumbira pada tahun 1970-an. Tarian ini lahir dari gabungan kesenian seperti pencak silat, Tayuban, dan Ketuk Tilu, serta dikenal dengan gerakan yang energik, unik, dan dinamis. Gerakannya menggambarkan semangat, keceriaan, keanggunan, serta keberanian perempuan Sunda dan sering ditampilkan dalam acara adat maupun hiburan.', 'https://www.youtube.com/embed/iE-YsfxH3pc?si=O_zaICiPJUrVrCJX', '2025-10-21 15:41:28');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indeks untuk tabel `alatmusik`
--
ALTER TABLE `alatmusik`
  ADD PRIMARY KEY (`id_alatmusik`);

--
-- Indeks untuk tabel `destinasi`
--
ALTER TABLE `destinasi`
  ADD PRIMARY KEY (`id_destinasi`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indeks untuk tabel `kuliner`
--
ALTER TABLE `kuliner`
  ADD PRIMARY KEY (`id_kuliner`);

--
-- Indeks untuk tabel `lagu`
--
ALTER TABLE `lagu`
  ADD PRIMARY KEY (`id_lagu`);

--
-- Indeks untuk tabel `tari`
--
ALTER TABLE `tari`
  ADD PRIMARY KEY (`id_tari`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `alatmusik`
--
ALTER TABLE `alatmusik`
  MODIFY `id_alatmusik` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `destinasi`
--
ALTER TABLE `destinasi`
  MODIFY `id_destinasi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `kuliner`
--
ALTER TABLE `kuliner`
  MODIFY `id_kuliner` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `lagu`
--
ALTER TABLE `lagu`
  MODIFY `id_lagu` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tari`
--
ALTER TABLE `tari`
  MODIFY `id_tari` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
