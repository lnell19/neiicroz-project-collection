<?php
require_once '../koneksi.php';

// Hapus kuliner
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    if ($id > 0) {
        $stmt = $conn->prepare("SELECT gambar FROM kuliner WHERE id_kuliner = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();

        if ($row && !empty($row['gambar'])) {
            $filePath = 'uploads/kuliner/' . $row['gambar'];
            if (is_file($filePath)) @unlink($filePath);
        }

        $del = $conn->prepare("DELETE FROM kuliner WHERE id_kuliner = ?");
        $del->bind_param('i', $id);
        $del->execute();
        $del->close();
    }

    header("Location: tb_makanan_tradisional.php");
    exit;
}

// Ambil data kuliner
$stmt = $conn->prepare("SELECT * FROM kuliner ORDER BY id_kuliner ASC");
$stmt->execute();
$res = $stmt->get_result();
$kulinerList = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Budaya Sunda - Makanan Tradisional</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    :root {
        --brand: #1565c0;
        --accent: #0d47a1;
    }
    body {
        background: linear-gradient(180deg, #f8fbff, #eef3f9);
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
    .btn-back:hover { background: #1565c0; }
    .btn-add { background: #10b981; }
    .btn-add:hover { background: #059669; }

    h4.section-title {
        color: var(--accent);
        text-align: center;
        margin: 25px 0;
        font-weight: 700;
    }

    .grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 22px;
    }

    .card-kuliner {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.08);
        overflow: hidden;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .card-kuliner:hover {
        transform: translateY(-6px);
        box-shadow: 0 10px 24px rgba(0,0,0,0.15);
    }

    .card-kuliner img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        cursor: pointer;
        transition: transform 0.3s;
    }
    .card-kuliner img:hover { transform: scale(1.05); }

    .card-body {
        padding: 16px;
    }
    .title {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 8px;
        text-align: center;
    }
    .subtitle {
        font-size: 14px;
        color: #475569;
        margin-bottom: 6px;
        text-align: center;
    }
    .desc {
        font-size: 14px;
        color: #4b5563;
        margin-bottom: 10px;
        text-align: justify;
        line-height: 1.5;
        height: 80px;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .card-actions {
        display: flex;
        justify-content: center;
        gap: 10px;
        padding: 12px;
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

    /* === Modal Zoom Gambar === */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.85);
        justify-content: center;
        align-items: center;
        z-index: 9999;
        animation: fadeIn 0.3s ease;
    }
    .modal-overlay img {
        max-width: 90%;
        max-height: 90vh;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(255,255,255,0.25);
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
    .close-btn:hover { background: rgba(255,255,255,0.25); }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @media(max-width:768px){
        .card-kuliner img { height: 160px; }
    }
</style>
</head>
<body>
<div class="topbar">
    <div class="container-main d-flex justify-content-between align-items-center">
        <a href="budaya.php" class="btn-back">&larr; Kembali</a>
        <div class="fw-bold fs-5 text-center flex-grow-1">Makanan Tradisional Sunda</div>
        <a href="add_kuliner.php" class="btn-add">+ Tambah Makanan</a>
    </div>
</div>

<div class="container-main">
    <h4 class="section-title">Makanan Tradisional Jawa Barat (Sunda)</h4>

    <?php if (empty($kulinerList)): ?>
        <div class="text-center text-muted py-5">Belum ada data kuliner tradisional.</div>
    <?php else: ?>
        <div class="grid-container">
            <?php foreach ($kulinerList as $row): 
                $id = (int)$row['id_kuliner'];
                $nama = htmlspecialchars($row['nama']);
                $asal = htmlspecialchars($row['asal_usul']);
                $bahan = htmlspecialchars($row['bahan']);
                $cara_pembuatan = htmlspecialchars($row['cara_pembuatan']);
                $cara_penyajian = htmlspecialchars($row['cara_penyajian']);
                $deskripsi = htmlspecialchars($row['deskripsi']);
                $imgFile = $row['gambar'] ?: 'default.png';
                $imgPath = is_file('uploads/kuliner/' . $imgFile) ? 'uploads/kuliner/' . $imgFile : 'uploads/kuliner/default.png';
            ?>
            <div class="card-kuliner">
                <img src="<?= $imgPath ?>" alt="<?= $nama ?>" onclick="zoomImage(this.src)">
                <div class="card-body">
                    <div class="title"><?= $nama ?></div>
                    <div class="subtitle"><?= $asal ?></div>
                    <div class="desc"><?= $deskripsi ?: 'Belum ada deskripsi.' ?></div>
                </div>
                <div class="card-actions">
                    <a href="edit_kuliner.php?id=<?= $id ?>" class="btn-edit">Edit</a>
                    <a href="tb_makanan_tradisional.php?delete=<?= $id ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus makanan ini?')">Hapus</a>
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
