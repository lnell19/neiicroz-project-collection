<?php
session_start();
include '../koneksi.php';

// Cek login
if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit;
}

// --- Ambil data lagu berdasarkan ID ---
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: tb_lagu_tradisional.php");
    exit;
}

$id = (int) $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM lagu WHERE id_lagu = ? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$lagu = $result->fetch_assoc();
$stmt->close();

if (!$lagu) {
    die("Data lagu tidak ditemukan.");
}

$errors = [];
$success = null;

// --- Proses Update ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['nama'] ?? '');

    if ($judul === '') {
        $errors[] = 'Judul lagu harus diisi.';
    } else {
        // Cek duplikat judul kecuali diri sendiri
        $stmt = $conn->prepare("SELECT id_lagu FROM lagu WHERE nama = ? AND id_lagu != ?");
        if ($stmt) {
            $stmt->bind_param('si', $judul, $id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $errors[] = 'Judul lagu sudah ada.';
            }
            $stmt->close();
        }
    }

    // Default data lama
    $gambar = $lagu['gambar'];
    $audio = $lagu['audio'];

    // --- Upload Gambar (Opsional) ---
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['gambar'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            $allowed_img = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $ext_img = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (!in_array($ext_img, $allowed_img)) {
                $errors[] = 'Format gambar tidak didukung (jpg, jpeg, png, gif, webp).';
            } elseif ($file['size'] > 3 * 1024 * 1024) {
                $errors[] = 'Ukuran gambar maksimal 3MB.';
            } else {
                $uploadDir = 'uploads/lagu/gambar/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $newName = uniqid('img_', true) . '.' . $ext_img;
                $dest = $uploadDir . $newName;

                if (move_uploaded_file($file['tmp_name'], $dest)) {
                    // Hapus gambar lama jika bukan default
                    if ($gambar && $gambar !== 'default.png' && file_exists($uploadDir . $gambar)) {
                        unlink($uploadDir . $gambar);
                    }
                    $gambar = $newName;
                } else {
                    $errors[] = 'Gagal mengunggah gambar.';
                }
            }
        } else {
            $errors[] = 'Error saat mengunggah gambar (kode: ' . $file['error'] . ').';
        }
    }

    // --- Upload Audio (Opsional) ---
    if (isset($_FILES['audio']) && $_FILES['audio']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['audio'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            $allowed_audio = ['mp3', 'wav', 'ogg', 'm4a'];
            $ext_audio = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (!in_array($ext_audio, $allowed_audio)) {
                $errors[] = 'Format audio tidak didukung (mp3, wav, ogg, m4a).';
            } elseif ($file['size'] > 20 * 1024 * 1024) {
                $errors[] = 'Ukuran audio maksimal 20MB.';
            } else {
                $uploadDir = 'uploads/lagu/audio/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $newName = uniqid('lagu_', true) . '.' . $ext_audio;
                $dest = $uploadDir . $newName;

                if (move_uploaded_file($file['tmp_name'], $dest)) {
                    // Hapus audio lama
                    if ($audio && file_exists($uploadDir . $audio)) {
                        unlink($uploadDir . $audio);
                    }
                    $audio = $newName;
                } else {
                    $errors[] = 'Gagal mengunggah file audio.';
                }
            }
        } else {
            $errors[] = 'Error saat mengunggah audio (kode: ' . $file['error'] . ').';
        }
    }

    // --- Simpan Perubahan ke Database ---
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE lagu SET nama = ?, gambar = ?, audio = ?, created_at = NOW() WHERE id_lagu = ?");
        if ($stmt) {
            $stmt->bind_param('sssi', $judul, $gambar, $audio, $id);
            if ($stmt->execute()) {
                header("Location: tb_lagu_tradisional.php");
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
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Edit Lagu</title>
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
      max-width: 600px;
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

    .form-group { margin-bottom: 16px; }

    label {
      display: block;
      font-weight: 600;
      margin-bottom: 6px;
      color: #333;
    }

    .form-control {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #ccc;
      border-radius: 10px;
      transition: border-color 0.2s;
      font-size: 15px;
    }

    .form-control:focus {
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

    .btn-primary { background: #1e4db7; color: #fff; }
    .btn-outline { background: transparent; border: 1px solid #1e4db7; color: #1e4db7; text-decoration: none; }

    .alert {
      padding: 10px 14px;
      border-radius: 8px;
      margin-bottom: 12px;
      font-size: 14px;
    }

    .alert-danger {
      background: #ffe6e6; border: 1px solid #f5b7b7; color: #8b0000;
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

    audio {
      display: block;
      width: 100%;
      margin-top: 10px;
      border-radius: 8px;
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
    <h1>Edit Lagu</h1>

    <?php if (!empty($errors)): foreach ($errors as $e): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($e) ?></div>
    <?php endforeach; endif; ?>

    <form method="POST" enctype="multipart/form-data" action="">
      <div class="form-group">
        <label>Judul Lagu</label>
        <input type="text" name="nama" class="form-control" required value="<?= htmlspecialchars($_POST['nama'] ?? $lagu['nama']) ?>">
      </div>

      <div class="form-group">
        <label>Gambar (kosongkan jika tidak ingin mengganti)</label>
        <input type="file" name="gambar" accept="image/*" class="form-control" onchange="previewImage(event)">
        <img id="preview" class="preview" src="uploads/lagu/gambar/<?= htmlspecialchars($lagu['gambar'] ?: 'default.png') ?>" alt="preview">
      </div>

      <div class="form-group">
        <label>File Audio (kosongkan jika tidak ingin mengganti)</label>
        <input type="file" name="audio" accept="audio/*" class="form-control" onchange="previewAudio(event)">
        <audio id="previewAudio" controls src="uploads/lagu/audio/<?= htmlspecialchars($lagu['audio']) ?>"></audio>
      </div>

      <div class="button-group">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Perbarui</button>
        <a href="tb_lagu_tradisional.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Batal</a>
      </div>
    </form>
  </div>

  <script>
    function previewImage(e){
      const file = e.target.files[0];
      if(!file) return;
      const reader = new FileReader();
      reader.onload = function(ev){
        document.getElementById('preview').src = ev.target.result;
      };
      reader.readAsDataURL(file);
    }

    function previewAudio(e){
      const file = e.target.files[0];
      if(!file) return;
      const audio = document.getElementById('previewAudio');
      audio.src = URL.createObjectURL(file);
      audio.style.display = 'block';
    }
  </script>
</body>
</html>
