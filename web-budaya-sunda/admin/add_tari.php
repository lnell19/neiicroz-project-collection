<?php
session_start();
include '../koneksi.php';

// Cek login
if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit;
}

$errors = [];
$success = null;

// Proses form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $link_video = trim($_POST['link_video'] ?? '');

    if ($nama === '') {
        $errors[] = 'Nama tari harus diisi.';
    } else {
        // Cek duplikat nama
        $stmt = $conn->prepare("SELECT id_tari FROM tari WHERE nama = ?");
        if ($stmt) {
            $stmt->bind_param('s', $nama);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $errors[] = 'Nama tari sudah ada.';
            }
            $stmt->close();
        }
    }

    // --- Upload gambar (opsional) ---
    $gambar = 'default.png';
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
                    $gambar = $newName;
                } else {
                    $errors[] = 'Gagal mengunggah gambar.';
                }
            }
        }
    }

    // Simpan ke database
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO tari (nama, gambar, deskripsi, link_video, created_at) VALUES (?, ?, ?, ?, NOW())");
        if ($stmt) {
            $stmt->bind_param('ssss', $nama, $gambar, $deskripsi, $link_video);
            if ($stmt->execute()) {
                header("Location: tb_tari_tradisional.php");
                exit;
            } else {
                $errors[] = 'Gagal menyimpan ke database: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $errors[] = 'Query gagal: ' . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Tambah Tari Tradisional</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    body {
      font-family: 'Poppins', sans-serif;
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
      max-width: 650px;
      padding: 30px 40px;
      border-radius: 16px;
      background: rgba(255, 255, 255, 0.92);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
      backdrop-filter: blur(8px);
      animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    h1 {
      margin-bottom: 20px;
      color: #1e4db7;
      text-align: center;
      font-size: 1.8rem;
    }

    .form-group {
      margin-bottom: 16px;
    }

    label {
      display: block;
      font-weight: 600;
      margin-bottom: 6px;
      color: #333;
    }

    .form-control, textarea {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #ccc;
      border-radius: 10px;
      transition: border-color 0.2s;
      font-size: 15px;
      resize: vertical;
    }

    .form-control:focus, textarea:focus {
      border-color: #1e4db7;
      outline: none;
      box-shadow: 0 0 0 3px rgba(30, 77, 183, 0.1);
    }

    .btn {
      padding: 10px 16px;
      border-radius: 10px;
      border: none;
      cursor: pointer;
      font-weight: 600;
      font-size: 15px;
      transition: background 0.2s, transform 0.1s;
    }

    .btn:active { transform: scale(0.97); }

    .btn-primary {
      background: #1e4db7;
      color: #fff;
    }

    .btn-outline {
      background: transparent;
      border: 1px solid #1e4db7;
      color: #1e4db7;
      text-decoration: none;
    }

    .alert {
      padding: 10px 14px;
      border-radius: 8px;
      margin-bottom: 12px;
      font-size: 14px;
    }

    .alert-danger {
      background: #ffe6e6;
      border: 1px solid #f5b7b7;
      color: #8b0000;
    }

    img.preview {
      max-height: 140px;
      border-radius: 8px;
      object-fit: cover;
      border: 1px solid #eee;
      padding: 4px;
      display: block;
      margin: 8px auto 0;
    }

    iframe {
      width: 100%;
      height: 200px;
      border: none;
      border-radius: 10px;
      margin-top: 8px;
      display: none;
    }

    .button-group {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin-top: 16px;
    }
</style>
</head>
<body>
  <div class="card">
    <h1>Tambah Tari Tradisional</h1>

    <?php if (!empty($errors)): foreach ($errors as $e): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($e) ?></div>
    <?php endforeach; endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <div class="form-group">
        <label>Nama Tari</label>
        <input type="text" name="nama" class="form-control" required value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label>Deskripsi Tari</label>
        <textarea name="deskripsi" rows="4" class="form-control" placeholder="Tuliskan deskripsi singkat tentang tari..."><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
      </div>

      <div class="form-group">
        <label>Link Video YouTube (opsional)</label>
        <input type="text" name="link_video" id="link_video" class="form-control" placeholder="Contoh: https://www.youtube.com/watch?v=abc123xyz" value="<?= htmlspecialchars($_POST['link_video'] ?? '') ?>" oninput="previewYouTube()">
        <iframe id="ytPreview"></iframe>
      </div>

      <div class="form-group">
        <label>Gambar (opsional, max 3MB)</label>
        <input type="file" name="gambar" accept="image/*" class="form-control" onchange="previewImage(event)">
        <img id="preview" class="preview" src="uploads/tari/default.png" alt="preview">
      </div>

      <div class="button-group">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
        <a href="tb_tari_tradisional.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Batal</a>
      </div>
    </form>
  </div>

  <script>
    function previewImage(e){
      const file = e.target.files[0];
      if(!file) return;
      const reader = new FileReader();
      reader.onload = ev => document.getElementById('preview').src = ev.target.result;
      reader.readAsDataURL(file);
    }

    function previewYouTube(){
      const url = document.getElementById('link_video').value;
      const iframe = document.getElementById('ytPreview');
      const match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([A-Za-z0-9_-]{11})/);
      if(match){
        iframe.src = "https://www.youtube.com/embed/" + match[1];
        iframe.style.display = "block";
      } else {
        iframe.style.display = "none";
      }
    }
  </script>
</body>
</html>
