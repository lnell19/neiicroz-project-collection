<?php
session_start();
include '../koneksi.php';

// Cek login
if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit;
}

// Ambil ID dari URL
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: tb_destinasi.php");
    exit;
}

$errors = [];
$success = null;

// Ambil data lama
$stmt = $conn->prepare("SELECT * FROM destinasi WHERE id_destinasi = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$destinasi = $result->fetch_assoc();
$stmt->close();

if (!$destinasi) {
    die("Data tidak ditemukan.");
}

// Proses form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $link = trim($_POST['link__maps'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $lokasi = trim($_POST['lokasi'] ?? '');
    $gambar = $destinasi['gambar']; // default: gambar lama

    // Validasi nama
    if ($nama === '') {
        $errors[] = 'Nama destinasi harus diisi.';
    }

    // Validasi link (jika diisi)
    if ($link !== '' && !filter_var($link, FILTER_VALIDATE_URL)) {
        $errors[] = 'Link tidak valid.';
    }

    // Validasi duplikat nama (kecuali id ini sendiri)
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id_destinasi FROM destinasi WHERE nama = ? AND id_destinasi != ?");
        $stmt->bind_param('si', $nama, $id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = 'Nama destinasi sudah digunakan.';
        }
        $stmt->close();
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
                $uploadDir = 'uploads/destinasi/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $newName = uniqid('dest_', true) . '.' . $ext;
                $dest = $uploadDir . $newName;

                if (move_uploaded_file($file['tmp_name'], $dest)) {
                    // Hapus gambar lama jika bukan default
                    if ($gambar !== 'default.png' && file_exists($uploadDir . $gambar)) {
                        unlink($uploadDir . $gambar);
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

    // Update database jika tidak ada error
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE destinasi SET nama=?, link__maps=?, gambar=?, lokasi=?, deskripsi=?, created_at=NOW() WHERE id_destinasi=?");
        if ($stmt) {
            $stmt->bind_param('sssssi', $nama, $link, $gambar, $lokasi, $deskripsi, $id);
            if ($stmt->execute()) {
                header("Location: tb_destinasi.php");
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
  <title>Edit Destinasi</title>
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

    .form-group {
      margin-bottom: 16px;
    }

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

    .btn:active {
      transform: scale(0.97);
    }

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
    <h1>Edit Destinasi</h1>

    <?php if (!empty($errors)): ?>
      <?php foreach ($errors as $e): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($e) ?></div>
      <?php endforeach; ?>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <div class="form-group">
        <label>Nama Destinasi</label>
        <input type="text" name="nama" class="form-control" required value="<?= htmlspecialchars($nama ?? $destinasi['nama']) ?>">
      </div>

      <div class="form-group">
        <label>Deskripsi</label>
        <textarea name="deskripsi" class="form-control" rows="4" placeholder="Masukkan deskripsi destinasi..."><?= htmlspecialchars($deskripsi ?? $destinasi['deskripsi']) ?></textarea>
      </div>

      <div class="form-group">
        <label>Lokasi</label>
        <input type="text" name="lokasi" class="form-control" placeholder="Masukkan lokasi destinasi..." value="<?= htmlspecialchars($lokasi ?? $destinasi['lokasi']) ?>">
      </div>

      <div class="form-group">
        <label>Link (opsional)</label>
        <input type="url" name="link__maps" class="form-control" placeholder="https://..." value="<?= htmlspecialchars($link ?? $destinasi['link__maps']) ?>">
      </div>

      <div class="form-group">
        <label>Gambar (opsional, max 3MB)</label>
        <input type="file" name="gambar" accept="image/*" class="form-control" onchange="previewImage(event)">
        <img id="preview" class="preview" src="uploads/destinasi/<?= htmlspecialchars($destinasi['gambar']) ?>" alt="preview">
      </div>

      <div class="button-group">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Perbarui</button>
        <a href="tb_destinasi.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Batal</a>
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
  </script>
</body>
</html>
