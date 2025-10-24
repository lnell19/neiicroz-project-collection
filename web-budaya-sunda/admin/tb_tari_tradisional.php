<?php
require_once '../koneksi.php';

// Hapus tari
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    if ($id > 0) {
        $stmt = $conn->prepare("SELECT gambar FROM tari WHERE id_tari = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();

        if ($row && !empty($row['gambar'])) {
            $filePath = 'uploads/tari/' . $row['gambar'];
            if (is_file($filePath)) @unlink($filePath);
        }

        $del = $conn->prepare("DELETE FROM tari WHERE id_tari = ?");
        $del->bind_param('i', $id);
        $del->execute();
        $del->close();
    }

    header("Location: tb_tari_tradisional.php");
    exit;
}

// Ambil data tari
$stmt = $conn->prepare("SELECT id_tari, nama, gambar, deskripsi, link_video FROM tari ORDER BY id_tari ASC");
$stmt->execute();
$res = $stmt->get_result();
$tariList = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();

/**
 * Fungsi untuk konversi link YouTube menjadi embed iframe
 */
function getYouTubeEmbed($url) {
    if (preg_match('/(youtu\.be\/|youtube\.com\/(watch\?v=|embed\/|shorts\/))([A-Za-z0-9_-]{11})/', $url, $match)) {
        $videoId = $match[3];
        return '<iframe width="100%" height="180" src="https://www.youtube.com/embed/' . $videoId . '" 
                title="YouTube video player" frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                allowfullscreen></iframe>';
    }
    return null;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Budaya Sunda - Tari Tradisional</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    :root {
        --brand: #1e40af;
        --accent: #2563eb;
        --muted: #6c757d;
    }
    body {
        background: linear-gradient(180deg, #f0f4ff, #e6ecf7);
        font-family: 'Poppins', sans-serif;
        margin: 0;
        padding-bottom: 60px;
    }
    .topbar {
        background: var(--brand);
        color: #fff;
        padding: 14px 18px;
        box-shadow: 0 2px 8px rgba(0,0,0,.1);
    }
    .container-main {
        max-width: 1200px;
        margin: 30px auto;
        padding: 0 16px;
    }
    .btn-back, .btn-add {
        font-weight: 600;
        border-radius: 8px;
        padding: 10px 18px;
        text-decoration: none;
        color: #fff;
        transition: 0.3s;
    }
    .btn-back { background: #0d47a1; }
    .btn-back:hover { background: #2563eb; }
    .btn-add { background: #10b981; }
    .btn-add:hover { background: #059669; }
    h4.section-title {
        color: var(--brand);
        text-align: center;
        margin: 20px 0;
        font-weight: 700;
    }

    .grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
        gap: 20px;
    }

    .card-tari {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 6px 16px rgba(0,0,0,0.1);
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .card-tari:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 24px rgba(0,0,0,0.15);
    }
    .card-tari img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .card-tari img:hover {
        transform: scale(1.05);
    }
    .card-body {
        padding: 14px;
        text-align: center;
    }
    .title {
        font-weight: 700;
        color: #1e293b;
        font-size: 17px;
        margin-bottom: 8px;
    }
    .desc {
        color: #475569;
        font-size: 14px;
        height: 45px;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    iframe {
        border: none;
        margin-top: 8px;
        border-radius: 10px;
    }
    .link-video {
        display: inline-block;
        margin-top: 8px;
        font-size: 13px;
        color: var(--accent);
        text-decoration: none;
    }
    .link-video:hover { text-decoration: underline; }
    .card-actions {
        display: flex;
        justify-content: center;
        gap: 8px;
        padding: 10px;
        border-top: 1px solid #eee;
    }
    .btn-edit {
        background: #3b82f6;
        color: #fff;
        border-radius: 8px;
        padding: 6px 10px;
        text-decoration: none;
    }
    .btn-delete {
        background: #ef4444;
        color: #fff;
        border-radius: 8px;
        padding: 6px 10px;
        text-decoration: none;
    }

    /* === Modal Zoom === */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.85);
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    .modal-overlay img {
        max-width: 90%;
        max-height: 90vh;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(255,255,255,0.2);
    }
    .close-btn {
        position: fixed;
        top: 20px;
        right: 30px;
        font-size: 28px;
        color: white;
        cursor: pointer;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        padding: 6px 12px;
        transition: 0.2s;
    }
    .close-btn:hover {
        background: rgba(255,255,255,0.25);
    }

    @media(max-width:768px){
        .card-tari img, iframe { height: 150px; }
    }
</style>
</head>
<body>
<div class="topbar">
    <div class="container-main d-flex justify-content-between align-items-center">
        <a href="budaya.php" class="btn-back">&larr; Kembali</a>
        <div class="fw-bold fs-5 text-center flex-grow-1">Tari Tradisional Sunda</div>
        <a href="add_tari.php" class="btn-add">+ Tambah Tari</a>
    </div>
</div>

<div class="container-main">
    <h4 class="section-title">Tari Tradisional Jawa Barat (Sunda)</h4>

    <?php if (empty($tariList)): ?>
        <div class="text-center text-muted py-5">Belum ada data tari tradisional.</div>
    <?php else: ?>
        <div class="grid-container">
            <?php foreach ($tariList as $row): 
                $id = (int)$row['id_tari'];
                $name = htmlspecialchars($row['nama']);
                $desc = htmlspecialchars($row['deskripsi']);
                $video = htmlspecialchars($row['link_video']);
                $imgFile = $row['gambar'] ?: 'default.png';
                $imgPath = is_file('uploads/tari/' . $imgFile) ? 'uploads/tari/' . $imgFile : 'uploads/tari/default.png';
                $embedVideo = getYouTubeEmbed($video);
            ?>
            <div class="card-tari">
                <img src="<?= $imgPath ?>" alt="<?= $name ?>" onclick="zoomImage(this.src)">
                <div class="card-body">
                    <div class="title"><?= $name ?></div>
                    <div class="desc"><?= $desc ?: 'Belum ada deskripsi.' ?></div>
                    <?php if ($embedVideo): ?>
                        <?= $embedVideo ?>
                    <?php elseif (!empty($video)): ?>
                        <a class="link-video" href="<?= $video ?>" target="_blank"><i class="fas fa-link"></i> Lihat Video</a>
                    <?php endif; ?>
                </div>
                <div class="card-actions">
                    <a href="edit_tari.php?id=<?= $id ?>" class="btn-edit">Edit</a>
                    <a href="tb_tari_tradisional.php?delete=<?= $id ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus tari ini?')">Hapus</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Zoom -->
<div class="modal-overlay" id="imgModal" onclick="closeZoom()">
    <span class="close-btn" onclick="closeZoom()">&times;</span>
    <img id="modalImg" src="">
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
<script>
function zoomImage(src) {
    const modal = document.getElementById('imgModal');
    const modalImg = document.getElementById('modalImg');
    modalImg.src = src;
    modal.style.display = 'flex';
}
function closeZoom() {
    document.getElementById('imgModal').style.display = 'none';
}
</script>
</body>
</html>
