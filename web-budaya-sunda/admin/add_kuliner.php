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
    $asal_usul = trim($_POST['asal_usul'] ?? '');
    $bahan = trim($_POST['bahan'] ?? '');
    $cara_pembuatan = trim($_POST['cara_pembuatan'] ?? '');
    $cara_penyajian = trim($_POST['cara_penyajian'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');

    if ($nama === '') {
        $errors[] = 'Nama kuliner harus diisi.';
    } else {
        // Cek duplikat nama
        $stmt = $conn->prepare("SELECT id_kuliner FROM kuliner WHERE nama = ?");
        $stmt->bind_param("s", $nama);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = "Nama kuliner sudah ada!";
        }
        $stmt->close();
    }

    // Handle upload
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
                $uploadDir = 'uploads/kuliner/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $newName = uniqid('kul_', true) . '.' . $ext;
                $dest = $uploadDir . $newName;

                if (move_uploaded_file($file['tmp_name'], $dest)) {
                    $gambar = $newName;
                } else {
                    $errors[] = 'Gagal mengunggah gambar.';
                }
            }
        } else {
            $errors[] = 'Error saat mengunggah gambar (kode: ' . $file['error'] . ').';
        }
    }

    // Simpan ke database
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO kuliner 
        (nama, gambar, asal_usul, bahan, cara_pembuatan, cara_penyajian, deskripsi, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");

        if ($stmt) {
            $stmt->bind_param('sssssss', $nama, $gambar, $asal_usul, $bahan, $cara_pembuatan, $cara_penyajian, $deskripsi);
            if ($stmt->execute()) {
                header("Location: tb_kuliner_tradisional.php");
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
  <title>Tambah Makanan Tradisional</title>
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
      max-width: 750px;
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
    }

    .form-control:focus, textarea:focus {
      border-color: #1e4db7;
      outline: none;
      box-shadow: 0 0 0 3px rgba(30, 77, 183, 0.1);
    }

    textarea {
      resize: vertical;
      min-height: 80px;
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
      max-height: 150px;
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
    <h1>Tambah Makanan Tradisional</h1>

    <?php if (!empty($errors)): foreach ($errors as $e): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($e) ?></div>
    <?php endforeach; endif; ?>

    <form method="POST" enctype="multipart/form-data" action="">
      <div class="form-group">
        <label>Nama Kuliner</label>
        <input type="text" name="nama" class="form-control" required value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label>Asal Usul</label>
        <input type="text" name="asal_usul" class="form-control" placeholder="Contoh: Bandung, Jawa Barat" value="<?= htmlspecialchars($_POST['asal_usul'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label>Bahan-bahan</label>
        <textarea name="bahan" class="form-control" placeholder="Tulis bahan-bahannya"><?= htmlspecialchars($_POST['bahan'] ?? '') ?></textarea>
      </div>

      <div class="form-group">
        <label>Cara Pembuatan</label>
        <textarea name="cara_pembuatan" class="form-control" placeholder="Langkah-langkah pembuatan"><?= htmlspecialchars($_POST['cara_pembuatan'] ?? '') ?></textarea>
      </div>

      <div class="form-group">
        <label>Cara Penyajian</label>
        <textarea name="cara_penyajian" class="form-control" placeholder="Cara menyajikan makanan ini"><?= htmlspecialchars($_POST['cara_penyajian'] ?? '') ?></textarea>
      </div>

      <div class="form-group">
        <label>Deskripsi</label>
        <textarea name="deskripsi" class="form-control" rows="4" placeholder="Deskripsi singkat"><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
      </div>

      <div class="form-group">
        <label>Gambar (opsional, max 3MB)</label>
        <input type="file" name="gambar" accept="image/*" class="form-control" onchange="previewImage(event)">
        <img id="preview" class="preview" src="uploads/kuliner/default.png" alt="preview">
      </div>

      <div class="button-group">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
        <a href="tb_makanan_tradisional.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Batal</a>
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
