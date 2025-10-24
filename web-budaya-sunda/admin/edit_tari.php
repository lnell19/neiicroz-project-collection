<?php
session_start();
include '../koneksi.php';

// Cek login
if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit;
}

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if (!$id) {
    header("Location: tb_tari_tradisional.php");
    exit;
}

$errors = [];
$success = null;

// Ambil data lama
$stmt = $conn->prepare("SELECT * FROM tari WHERE id_tari = ? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$tari = $result->fetch_assoc();
$stmt->close();

if (!$tari) {
    die("Data tidak ditemukan.");
}

// pastikan ada nilai default untuk field yang mungkin NULL
$tari['deskripsi'] = $tari['deskripsi'] ?? '';
$tari['link_video'] = $tari['link_video'] ?? '';
$tari['gambar'] = $tari['gambar'] ?? 'default.png';

// Proses form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $link_video = trim($_POST['link_video'] ?? '');
    $gambar = $tari['gambar']; // default: gambar lama

    if ($nama === '') {
        $errors[] = 'Nama tari harus diisi.';
    } else {
        // Cek duplikat nama (kecuali id ini sendiri)
        $stmt = $conn->prepare("SELECT id_tari FROM tari WHERE nama = ? AND id_tari != ?");
        if ($stmt) {
            $stmt->bind_param('si', $nama, $id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $errors[] = 'Nama tari sudah ada.';
            }
            $stmt->close();
        }
    }

    // Handle upload jika ada file baru
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['gambar'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) {
                $errors[] = 'Format gambar tidak didukung (jpg, jpeg, png, gif, webp).';
            } elseif ($file['size'] > 3 * 1024 * 1024) {
                $errors[] = 'Ukuran gambar maksimal 3MB.';
            } else {
                $uploadDir = 'uploads/tari/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $newName = uniqid('tar_', true) . '.' . $ext;
                $dest = $uploadDir . $newName;

                if (move_uploaded_file($file['tmp_name'], $dest)) {
                    // Hapus gambar lama (jika bukan default)
                    if ($gambar !== 'default.png' && file_exists($uploadDir . $gambar)) {
                        @unlink($uploadDir . $gambar);
                    }
                    $gambar = $newName;
                } else {
                    $errors[] = 'Gagal mengunggah gambar baru.';
                }
            }
        } else {
            $errors[] = 'Error saat mengunggah gambar (kode: ' . $file['error'] . ').';
        }
    }

    // Validasi sederhana link youtube (opsional)
    if ($link_video !== '') {
        // hanya cek format dasar, tidak wajib
        $ok = preg_match('/(youtube\.com|youtu\.be)/i', $link_video);
        if (!$ok) {
            // jangan memaksa; hanya beri peringatan kecil
            $errors[] = 'Link video tidak tampak seperti URL YouTube. (boleh dikosongkan)';
        }
    }

    // Update database
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE tari SET nama=?, deskripsi=?, link_video=?, gambar=?, updated_at=NOW() WHERE id_tari=?");
        if ($stmt) {
            $stmt->bind_param('ssssi', $nama, $deskripsi, $link_video, $gambar, $id);
            if ($stmt->execute()) {
                header("Location: tb_tari_tradisional.php");
                exit;
            } else {
                $errors[] = 'Gagal memperbarui data: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $errors[] = 'Query gagal: ' . $conn->error;
        }
    }
}

// helper untuk fallback gambar
$previewImg = is_file('uploads/tari/' . $tari['gambar']) ? 'uploads/tari/' . rawurlencode($tari['gambar']) : 'uploads/tari/default.png';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Edit Tari</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: 'Segoe UI', Roboto, Arial, sans-serif;
      background-image: url('batik_cirebon_megamendung_Nero_AI_Image_Upscaler_Photo_Face.jpeg.jpg');
      background-size: cover;
      background-position: center;
      padding: 24px;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .card {
      width: 100%;
      max-width: 720px;
      padding: 28px 36px;
      border-radius: 16px;
      background: rgba(255, 255, 255, 0.94);
      box-shadow: 0 8px 28px rgba(0, 0, 0, 0.12);
      backdrop-filter: blur(6px);
    }

    h1 {
      margin-bottom: 18px;
      color: #1e4db7;
      text-align: center;
      font-size: 1.6rem;
    }

    .form-group { margin-bottom: 14px; }
    label { display:block; font-weight:600; margin-bottom:6px; color:#333; }
    .form-control, textarea {
      width:100%;
      padding:10px 12px;
      border:1px solid #ccc;
      border-radius:10px;
      font-size:15px;
      transition:0.15s;
    }
    .form-control:focus, textarea:focus {
      border-color:#1e4db7; box-shadow:0 0 0 4px rgba(30,77,183,0.06); outline:none;
    }
    textarea { min-height:90px; resize:vertical; }

    .alert { padding:10px 14px; border-radius:8px; margin-bottom:12px; font-size:14px; }
    .alert-danger { background:#ffe6e6; border:1px solid #f5b7b7; color:#8b0000; }

    img.preview {
      max-height:180px;
      border-radius:10px;
      object-fit:cover;
      border:1px solid #eee;
      padding:4px;
      display:block;
      margin:8px auto 0;
    }

    iframe#ytPreview { width:100%; height:220px; border-radius:10px; border:0; display:none; margin-top:8px; }

    .button-group { display:flex; justify-content:center; gap:10px; margin-top:16px; }
    .btn { padding:10px 16px; border-radius:10px; font-weight:600; font-size:15px; cursor:pointer; border:none; }
    .btn-primary { background:#1e4db7; color:#fff; }
    .btn-outline { background:transparent; border:1px solid #1e4db7; color:#1e4db7; text-decoration:none; display:inline-flex; align-items:center; justify-content:center; }
  </style>
</head>
<body>
  <div class="card">
    <h1>Edit Tari</h1>

    <?php if (!empty($errors)): foreach ($errors as $e): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($e) ?></div>
    <?php endforeach; endif; ?>

    <form method="POST" enctype="multipart/form-data" novalidate>
      <div class="form-group">
        <label>Nama Tari</label>
        <input type="text" name="nama" class="form-control" required value="<?= htmlspecialchars($_POST['nama'] ?? $tari['nama']) ?>">
      </div>

      <div class="form-group">
        <label>Deskripsi</label>
        <textarea name="deskripsi" class="form-control" placeholder="Deskripsi singkat..."><?= htmlspecialchars($_POST['deskripsi'] ?? $tari['deskripsi']) ?></textarea>
      </div>

      <div class="form-group">
        <label>Link Video YouTube (opsional)</label>
        <input type="text" name="link_video" id="link_video" class="form-control" placeholder="https://www.youtube.com/watch?v=..." value="<?= htmlspecialchars($_POST['link_video'] ?? $tari['link_video']) ?>" oninput="previewYouTube()">
        <iframe id="ytPreview" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
      </div>

      <div class="form-group">
        <label>Gambar (opsional, max 3MB)</label>
        <input type="file" name="gambar" accept="image/*" class="form-control" onchange="previewImage(event)">
        <img id="preview" class="preview" src="<?= htmlspecialchars($previewImg) ?>" alt="preview">
      </div>

      <div class="button-group">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>&nbsp; Perbarui</button>
        <a href="tb_tari_tradisional.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i>&nbsp; Batal</a>
      </div>
    </form>
  </div>

  <script>
    // preview image when choose file
    function previewImage(e) {
      const file = e.target.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = function(ev) {
        document.getElementById('preview').src = ev.target.result;
      };
      reader.readAsDataURL(file);
    }

    // preview youtube when user types link
    function previewYouTube() {
      const url = document.getElementById('link_video').value;
      const iframe = document.getElementById('ytPreview');
      const match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([A-Za-z0-9_-]{11})/);
      if (match) {
        iframe.src = "https://www.youtube.com/embed/" + match[1];
        iframe.style.display = "block";
      } else {
        iframe.src = "";
        iframe.style.display = "none";
      }
    }

    // jalankan preview pada load jika ada nilai awal
    (function(){
      previewYouTube();
    })();
  </script>
</body>
</html>
