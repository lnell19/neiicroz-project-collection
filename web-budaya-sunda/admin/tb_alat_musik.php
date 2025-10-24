<?php
require_once '../koneksi.php';

// Hapus alatmusik
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    if ($id > 0) {
        $stmt = $conn->prepare("SELECT gambar FROM alatmusik WHERE id_alatmusik = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();
            $stmt->close();

            if ($row && !empty($row['gambar'])) {
                $filePath = 'uploads/alatmusik/' . $row['gambar'];
                if (is_file($filePath)) {
                    @unlink($filePath);
                }
            }

            $del = $conn->prepare("DELETE FROM alatmusik WHERE id_alatmusik = ?");
            if ($del) {
                $del->bind_param('i', $id);
                $del->execute();
                $del->close();
            }
        }
    }

    header("Location: tb_alat_musik.php");
    exit;
}

// Ambil data alat musik lengkap
$stmt = $conn->prepare("SELECT id_alatmusik, nama, gambar, deskripsi, asal_usul2, cara_pembuatan, cara_permainan FROM alatmusik ORDER BY id_alatmusik ASC");
$alatmusikList = [];
if ($stmt) {
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $alatmusikList[] = $r;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Budaya Sunda - Alat Musik</title>
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

        .card-alat {
            border-radius: 14px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 8px 16px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }
        .card-alat:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 24px rgba(0,0,0,0.12);
        }

        .card-alat img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .card-alat .info {
            padding: 12px 14px;
            text-align: center;
        }
        .card-alat .info .title {
            font-weight: 700;
            font-size: 16px;
            color: #1e293b;
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

        /* Modal style */
        .modal-lg { max-width: 850px; }
        .modal-body img {
            width: 100%;
            border-radius: 10px;
            margin-bottom: 16px;
        }
        .modal-body p { margin-bottom: 8px; }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="container-main d-flex justify-content-between align-items-center">
            <a href="budaya.php" class="btn-back">&larr; Kembali</a>
            <div class="fw-bold fs-5 text-center flex-grow-1">Alat Musik Tradisional</div>
            <a href="add_alatmusik.php" class="btn-add">+ Tambah Alat Musik</a>
        </div>
    </div>

    <div class="container-main">
        <h4 class="section-title">Alat Musik Tradisional Jawa Barat (Sunda)</h4>

        <?php if (empty($alatmusikList)): ?>
            <div class="text-center text-muted py-5">Belum ada data alat musik.</div>
        <?php else: ?>
            <div class="grid-container">
                <?php foreach ($alatmusikList as $row): 
                    $id = (int)$row['id_alatmusik'];
                    $name = htmlspecialchars($row['nama']);
                    $desc = htmlspecialchars($row['deskripsi']);
                    $asal = htmlspecialchars($row['asal_usul2']);
                    $buat = htmlspecialchars($row['cara_pembuatan']);
                    $main = htmlspecialchars($row['cara_permainan']);
                    $imgFile = $row['gambar'] ?: 'default.png';
                    $serverPath = 'uploads/alatmusik/' . $imgFile;
                    $webPath = file_exists($serverPath) ? $serverPath : 'uploads/alatmusik/default.png';
                ?>
                <div class="card-alat" data-bs-toggle="modal" data-bs-target="#modal<?= $id ?>">
                    <img src="<?= $webPath ?>" alt="<?= $name ?>" loading="lazy">
                    <div class="info">
                        <div class="title"><?= $name ?></div>
                    </div>
                    <div class="card-actions">
                        <a href="edit_alatmusik.php?id=<?= $id ?>" class="btn-edit">Edit</a>
                        <a href="tb_alat_musik.php?delete=<?= $id ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus alat musik ini?')">Hapus</a>
                    </div>
                </div>

                <!-- Modal Detail -->
                <div class="modal fade" id="modal<?= $id ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold"><?= $name ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <img src="<?= $webPath ?>" alt="<?= $name ?>">
                                <p><strong>Asal Usul:</strong> <?= nl2br($asal) ?></p>
                                <p><strong>Deskripsi:</strong> <?= nl2br($desc) ?></p>
                                <p><strong>Cara Pembuatan:</strong> <?= nl2br($buat) ?></p>
                                <p><strong>Cara Permainan:</strong> <?= nl2br($main) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
