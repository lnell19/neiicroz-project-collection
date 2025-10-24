<?php
require_once '../koneksi.php';

// Hapus lagu
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    if ($id > 0) {
        $stmt = $conn->prepare("SELECT audio, gambar FROM lagu WHERE id_lagu = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();

        if ($row) {
            if (!empty($row['audio'])) {
                $audioPath = 'uploads/lagu/audio/' . $row['audio'];
                if (is_file($audioPath)) @unlink($audioPath);
            }
            if (!empty($row['gambar'])) {
                $imgPath = 'uploads/lagu/gambar/' . $row['gambar'];
                if (is_file($imgPath)) @unlink($imgPath);
            }
        }

        $del = $conn->prepare("DELETE FROM lagu WHERE id_lagu = ?");
        $del->bind_param('i', $id);
        $del->execute();
        $del->close();
    }

    header("Location: tb_lagu_tradisional.php");
    exit;
}

// Ambil data lagu
$stmt = $conn->prepare("SELECT id_lagu, nama, gambar, audio, created_at FROM lagu ORDER BY id_lagu ASC");
$stmt->execute();
$res = $stmt->get_result();
$laguList = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Budaya Sunda - Lagu Tradisional</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    :root {
        --brand: #1e40af;
        --accent: #2563eb;
        --muted: #6c757d;
    }
    body {
        background: linear-gradient(180deg, #f1f5ff, #e2e8f0);
        font-family: 'Poppins', system-ui, sans-serif;
        margin: 0;
        padding-bottom: 60px;
    }
    .topbar {
        background: var(--brand);
        color: #fff;
        padding: 14px 18px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.15);
    }
    .container-main {
        max-width: 1180px;
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
    .btn-add { background: #10b981; }
    .btn-back:hover { background: #2563eb; }
    .btn-add:hover { background: #059669; }

    h4.section-title {
        color: var(--brand);
        text-align: center;
        margin-bottom: 24px;
        font-weight: 700;
    }

    .grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
    }

    .card-lagu {
        border-radius: 14px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 8px 16px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
    }
    .card-lagu:hover {
        transform: translateY(-6px);
        box-shadow: 0 10px 24px rgba(0,0,0,0.12);
    }

    .card-lagu img {
        width: 100%;
        height: 180px;
        object-fit: cover;
    }

    .card-lagu .info {
        padding: 12px 14px;
        text-align: center;
    }
    .card-lagu .title {
        font-weight: 700;
        font-size: 16px;
        color: #1e293b;
    }
    .card-lagu audio {
        width: 100%;
        margin-top: 8px;
        border-radius: 8px;
    }

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
        border-radius: 6px;
        padding: 6px 10px;
        text-decoration: none;
    }
    .btn-delete {
        background: #ef4444;
        color: #fff;
        border-radius: 6px;
        padding: 6px 10px;
        text-decoration: none;
    }
</style>
</head>
<body>
<div class="topbar">
    <div class="container-main d-flex justify-content-between align-items-center">
        <a href="budaya.php" class="btn-back">&larr; Kembali</a>
        <div class="fw-bold fs-5 text-center flex-grow-1">Lagu Tradisional Sunda</div>
        <a href="add_lagu.php" class="btn-add">+ Tambah Lagu</a>
    </div>
</div>

<div class="container-main">
    <h4 class="section-title">Lagu Tradisional Jawa Barat (Sunda)</h4>

    <?php if (empty($laguList)): ?>
        <div class="text-center text-muted py-5">Belum ada lagu tradisional.</div>
    <?php else: ?>
        <div class="grid-container">
            <?php foreach ($laguList as $row): 
                $id = (int)$row['id_lagu'];
                $name = htmlspecialchars($row['nama']);
                $imgFile = $row['gambar'] ?: 'default.png';
                $imgPath = file_exists('uploads/lagu/gambar/' . $imgFile)
                    ? 'uploads/lagu/gambar/' . $imgFile
                    : 'uploads/lagu/gambar/default.png';
                $audioFile = 'uploads/lagu/audio/' . htmlspecialchars($row['audio']);
            ?>
            <div class="card-lagu">
                <img src="<?= $imgPath ?>" alt="<?= $name ?>" loading="lazy">
                <div class="info">
                    <div class="title"><?= $name ?></div>
                    <audio controls>
                        <source src="<?= $audioFile ?>" type="audio/mpeg">
                        Browser kamu tidak mendukung pemutar audio.
                    </audio>
                </div>
                <div class="card-actions">
                    <a href="edit_lagu.php?id=<?= $id ?>" class="btn-edit">Edit</a>
                    <a href="tb_lagu_tradisional.php?delete=<?= $id ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus lagu ini?')">Hapus</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
