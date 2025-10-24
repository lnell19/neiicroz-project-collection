<?php
include '../koneksi.php'; // ganti sesuai lokasi koneksi kamu

// Ambil data kategori dari database
$query = "SELECT * FROM kategori ORDER BY created_at DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budaya Sunda</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        :root{
            --bg:#e6edf3;
            --card-bg:#ffffff;
            --accent:#1E5CC4;
            --muted:#6c757d;
        }
        body {
            background-color: var(--bg);
            font-family: 'Poppins', sans-serif;
            margin:0;
            padding-bottom:40px;
        }

        /* navbar */
        .navbar {
            background-color: var(--accent);
            color: white;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }
        .navbar a { color: white; text-decoration:none; }

        .page-wrap { max-width:1200px; margin:24px auto; padding:0 16px; }

        /* profile */
        .profile-container {
            display:flex;
            gap:20px;
            align-items:center;
            background:var(--card-bg);
            border-radius:12px;
            padding:18px;
            box-shadow:0 4px 20px rgba(0,0,0,0.06);
        }
        .profile-container img {
            width:96px;
            height:96px;
            border-radius:12px;
            object-fit:cover;
            flex:0 0 96px;
        }
        .profile-meta h6 { margin:0 0 6px 0; font-weight:700; }
        .description {
            background:#fffbe7;
            border:1px solid #f0e6b8;
            border-radius:8px;
            padding:12px;
            color:#333;
            margin-top:8px;
            line-height:1.6;
        }

        /* controls */
        .controls { display:flex; justify-content:space-between; align-items:center; margin:18px 0; gap:12px; }
        .btn-tambah {
            background-color: #00C853;
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 8px;
            padding: 8px 14px;
            transition: background .18s ease;
            text-decoration:none;
        }
        .btn-tambah:hover { background:#009624; }

        /* kategori horizontal scroll */
        .kategori-scroll {
            overflow-x:auto;
            padding:12px 6px;
            -webkit-overflow-scrolling:touch;
        }
        .kategori-row{
            display:flex;
            gap:18px;
            align-items:stretch;
            padding-bottom:6px;
        }

        .kategori-card {
            background:var(--card-bg);
            width: 300px;
            min-width:300px;
            border-radius:12px;
            overflow:hidden;
            text-align:center;
            box-shadow:0 6px 18px rgba(0,0,0,0.08);
            transition:transform .18s ease, box-shadow .18s ease;
            cursor:pointer;
            display:flex;
            flex-direction:column;
            justify-content:flex-start;
        }
        .kategori-card:hover{
            transform:translateY(-6px);
            box-shadow:0 10px 30px rgba(0,0,0,0.12);
        }
        .kategori-card a{ color:inherit; text-decoration:none; display:block; height:100%; }

        .kategori-card img {
            width:100%;
            height:180px;
            object-fit:cover;
            display:block;
            flex-shrink:0;
        }
        .kategori-card .card-body {
            padding:12px 14px;
            display:flex;
            flex-direction:column;
            gap:8px;
            flex:1 1 auto;
        }
        .kategori-card .title {
            font-weight:600;
            color:#222;
            font-size:1rem;
            white-space:nowrap;
            overflow:hidden;
            text-overflow:ellipsis;
        }
        .kategori-card .meta {
            color:var(--muted);
            font-size:0.87rem;
        }

        /* scrollbar styling */
        .kategori-scroll::-webkit-scrollbar { height:10px; }
        .kategori-scroll::-webkit-scrollbar-thumb {
            background: rgba(0,0,0,0.12);
            border-radius:8px;
        }
        .kategori-scroll::-webkit-scrollbar-track { background: transparent; }

        /* responsive */
        @media (max-width:720px){
            .kategori-card { width:230px; min-width:230px; }
            .kategori-card img{ height:140px; }
            .profile-container { flex-direction:column; align-items:flex-start; gap:12px; }
            .controls { flex-direction:column; align-items:stretch; }
        }
    </style>
</head>
<body>
<nav class="navbar p-3">
    <div class="container page-wrap d-flex justify-content-between align-items-center">
        <div><a href="dashboard.php" class="fw-bold"><i class="fas fa-arrow-left"></i> Kembali</a></div>
        <div class="fw-bold fs-5 text-white">Budaya Sunda</div>
        <div style="width:1px"></div>
    </div>
</nav>

<div class="page-wrap">
    <div class="profile-container mt-3">
        <img src="dedi.png" alt="Kang Dedi Mulyadi (KDM)">
        <div class="profile-meta">
            <h6 class="mb-1 fw-bold">H. Dedi Mulyadi, S.H. (KDM)</h6>
            <div class="description">
                <strong>Kang Dedi Mulyadi</strong> adalah seorang politisi, budayawan, dan tokoh publik asal Jawa Barat
                yang dikenal karena kiprahnya dalam mengangkat dan melestarikan <strong>budaya Sunda</strong>.
                <br><br>
                Ia lahir di <strong>Subang, 11 April 1971</strong>, dan pernah menjabat sebagai 
                <strong>Bupati Purwakarta dua periode (2008–2018)</strong>. 
                Dalam masa kepemimpinannya, Purwakarta dikenal luas sebagai kota dengan 
                <em>identitas budaya Sunda yang kuat</em> — terlihat dari arsitektur kota, 
                taman tematik, dan kegiatan masyarakat yang bernuansa lokal.
                <br><br>
                Filosofi kepemimpinannya berpegang pada nilai <strong>"Ngajaga Karakter Sunda"</strong> — 
                menjaga keseimbangan antara manusia, alam, dan budaya. 
                Setelah menjabat bupati, ia aktif sebagai anggota DPR RI dan kerap terjun langsung ke masyarakat
                melalui kegiatan sosial yang humanis dan inspiratif di berbagai media.
            </div>
        </div>
    </div>

    <!-- Horizontal scroll list -->
    <div class="kategori-scroll" role="region" aria-label="Daftar kategori">
        <div class="kategori-row">
            <?php while($row = $result->fetch_assoc()): ?>
                <?php
                    $safeName = htmlspecialchars($row['nama_kategori']);
                    $table_name = "tb_" . preg_replace('/[^a-zA-Z0-9_]/', '_', strtolower($row['nama_kategori']));
                    $img = !empty($row['gambar']) ? $row['gambar'] : 'default.png';
                    $imgPath = 'uploads/kategori/' . $img;
                    if (!is_file('uploads/kategori/' . $img)) {
                        $imgPath = 'uploads/kategori/default.png';
                    }
                ?>
                <div class="kategori-card" onclick="location.href='<?=$table_name?>.php'">
                    <a href="<?=$table_name?>.php" aria-label="<?= $safeName ?>">
                        <img src="<?= htmlspecialchars($imgPath) ?>" alt="<?= $safeName ?>">
                        <div class="card-body">
                            <div class="title"><?= $safeName ?></div>
                            <div class="meta">Lihat detail &rarr;</div>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
</body>
</html>
