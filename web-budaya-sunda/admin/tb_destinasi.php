<?php
require_once '../koneksi.php';

// Hapus destinasi
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    if ($id > 0) {
        $stmt = $conn->prepare("SELECT gambar FROM destinasi WHERE id_destinasi = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();
            $stmt->close();

            if ($row && !empty($row['gambar'])) {
                $filePath = 'uploads/destinasi/' . $row['gambar'];
                if (is_file($filePath)) {
                    @unlink($filePath);
                }
            }

            $del = $conn->prepare("DELETE FROM destinasi WHERE id_destinasi = ?");
            if ($del) {
                $del->bind_param('i', $id);
                $del->execute();
                $del->close();
            }
        }
    }

    header("Location: tb_destinasi.php");
    exit;
}

// Ambil data destinasi
$stmt = $conn->prepare("SELECT id_destinasi, nama, gambar, lokasi, link__maps, deskripsi FROM destinasi ORDER BY id_destinasi ASC");
$destinasiList = [];
if ($stmt) {
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $destinasiList[] = $r;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Budaya Sunda - Destinasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --brand: #1565c0;
            --card-bg: #1e40af;
            --muted: #6c757d;
        }
        body {
            background: #f9f9fb;
            font-family: Poppins, system-ui, Segoe UI, Roboto, Arial;
            margin: 0;
            padding-bottom: 40px;
        }

        .topbar {
            background: var(--brand);
            color: #fff;
            padding: 14px 18px;
            box-shadow: 0 2px 8px rgba(0,0,0,.06);
        }
        .container-main {
            max-width: 1180px;
            margin: 20px auto;
            padding: 0 16px;
        }

        .btn-back {
            background: #0d47a1;
            color: #fff;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px 18px;
            text-decoration: none;
            transition: background 0.3s;
        }
        .btn-back:hover { background: #1565c0; color: #fff; }

        .btn-add {
            background: #2ecc71;
            color: #fff;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px 18px;
            text-decoration: none;
            transition: background 0.3s;
        }
        .btn-add:hover { background: #27ae60; color: #fff; }

        h4.section-title {
            color: var(--brand);
            text-align: center;
            margin: 18px 0;
            font-weight: 700;
        }

        .scroll-container {
            display: flex;
            gap: 18px;
            overflow-x: auto;
            padding: 18px 10px;
            scroll-behavior: smooth;
        }
        .scroll-container::-webkit-scrollbar { height: 10px; }
        .scroll-container::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.12);
            border-radius: 10px;
        }

        .card-destination {
            flex: 0 0 auto;
            width: 280px;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 8px 22px rgba(0,0,0,0.08);
            background: linear-gradient(180deg, #fff, #f8fbff);
            display: flex;
            flex-direction: column;
            align-items: stretch;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }
        .card-destination:hover {
            transform: scale(1.04);
            box-shadow: 0 12px 28px rgba(0,0,0,0.1);
        }
        .card-destination .img-wrap {
            height: 170px;
            overflow: hidden;
            background: #eee;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-destination img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card-body {
            padding: 12px 14px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            text-align: center;
        }
        .card-body .title {
            font-weight: 700;
            color: #0b254e;
            font-size: 16px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .card-body .link {
            color: var(--muted);
            font-size: 13px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .card-actions {
            display: flex;
            gap: 8px;
            justify-content: center;
            padding: 12px;
            border-top: 1px solid rgba(0,0,0,0.04);
        }
        .btn-edit { background: #2ecc71; color: #fff; border-radius: 8px; padding: 8px 12px; text-decoration: none; }
        .btn-delete { background: #e74c3c; color: #fff; border-radius: 8px; padding: 8px 12px; text-decoration: none; }

        @media (max-width:720px){
            .card-destination { width: 220px; }
            .card-destination .img-wrap { height: 140px; }
        }

        /* Modal style */
        .modal-lg {
            max-width: 850px;
        }
        .modal-body img {
            width: 100%;
            border-radius: 10px;
            margin-bottom: 15px;
            transition: transform 0.2s ease;
        }
        .modal-body img:hover {
            transform: scale(1.02);
            filter: brightness(1.05);
        }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="container-main d-flex justify-content-between align-items-center">
            <a href="budaya.php" class="btn-back">&larr; Kembali</a>
            <div class="fw-bold fs-5 text-center flex-grow-1">Destinasi Wisata</div>
            <a href="add_destinasi.php" class="btn-add">+ Tambah Destinasi</a>
        </div>
    </div>

    <div class="container-main">
        <h4 class="section-title">Destinasi Wisata di Jawa Barat (Sunda)</h4>

        <div class="scroll-container" role="list">
            <?php if (empty($destinasiList)): ?>
                <div class="text-center w-100 py-5 text-muted">Belum ada destinasi.</div>
            <?php else: foreach ($destinasiList as $row):
                $id = (int)$row['id_destinasi'];
                $name = htmlspecialchars($row['nama']);
                $desc = htmlspecialchars($row['deskripsi'] ?? '');
                $lokasi = htmlspecialchars($row['lokasi'] ?? 'Tidak ada lokasi');
                $link = htmlspecialchars($row['link__maps'] ?: '#');
                $imgFile = $row['gambar'] ?: 'default.png';
                $serverPath = 'uploads/destinasi/' . $imgFile;
                $webPath = 'uploads/destinasi/' . rawurlencode($imgFile);
                if (!is_file($serverPath)) $webPath = 'uploads/destinasi/default.png';
            ?>
                <div class="card-destination" data-bs-toggle="modal" data-bs-target="#destModal<?= $id ?>">
                    <div class="img-wrap">
                        <img src="<?= $webPath ?>" alt="<?= $name ?>" loading="lazy">
                    </div>
                    <div class="card-body">
                        <div class="title"><?= $name ?></div>
                        <div class="desc text-muted" style="font-size:13px; max-height:70px; overflow:auto;"><?= nl2br($desc) ?></div>
                        <div class="link"><?= $lokasi ?></div>
                    </div>
                    <div class="card-actions">
                        <a class="btn-edit" href="edit_destinasi.php?id=<?= $id ?>">Edit</a>
                        <a class="btn-delete" href="tb_destinasi.php?delete=<?= $id ?>" onclick="return confirm('Yakin ingin menghapus destinasi ini?')">Hapus</a>
                    </div>
                </div>

                <!-- Modal Detail -->
                <div class="modal fade" id="destModal<?= $id ?>" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title fw-bold"><?= $name ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <?php if ($link !== '#'): ?>
                            <a href="<?= $link ?>" target="_blank" title="Buka di Google Maps">
                                <img src="<?= $webPath ?>" alt="<?= $name ?>">
                            </a>
                        <?php else: ?>
                            <img src="<?= $webPath ?>" alt="<?= $name ?>">
                        <?php endif; ?>
                        <p class="mb-2"><strong>Lokasi:</strong> <?= $lokasi ?></p>
                        <p><?= nl2br($desc) ?></p>
                        <?php if ($link !== '#'): ?>
                            <a href="<?= $link ?>" target="_blank" class="btn btn-primary mt-3">Lihat di Google Maps</a>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
