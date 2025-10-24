<?php
session_start();
include '../koneksi.php';

// Cek koneksi
if (isset($conn) && isset($conn->connect_error) && $conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Cek login
if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit;
}

// Ambil ID dari query
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header("Location: kategori.php");
    exit;
}

// Ambil data kategori
$stmt = $conn->prepare("SELECT id_kategori, nama_kategori, gambar FROM kategori WHERE id_kategori = ? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$kategori = $res->fetch_assoc();
$stmt->close();

if (!$kategori) {
    header("Location: kategori.php");
    exit;
}

$success = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kategori = trim($_POST['nama_kategori'] ?? '');
    $gambar = $kategori['gambar']; // default gambar lama

    // Validasi input
    if ($nama_kategori === '') {
        $error = "Nama kategori harus diisi!";
    } else {
        // Cek duplikat (kecuali kategori ini sendiri)
        $stmt = $conn->prepare("SELECT id_kategori FROM kategori WHERE nama_kategori = ? AND id_kategori != ?");
        $stmt->bind_param("si", $nama_kategori, $id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Kategori dengan nama tersebut sudah ada.";
        } else {
            // Handle upload gambar baru
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] !== UPLOAD_ERR_NO_FILE) {
                $file = $_FILES['gambar'];

                if ($file['error'] === UPLOAD_ERR_OK) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

                    if (!in_array($ext, $allowed)) {
                        $error = "Format gambar tidak didukung. Gunakan: jpg, jpeg, png, atau gif.";
                    } elseif ($file['size'] > 2 * 1024 * 1024) {
                        $error = "Ukuran gambar maksimal 2MB.";
                    } else {
                        $uploadDir = 'uploads/kategori/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }

                        $namaFileBaru = uniqid('kat_', true) . '.' . $ext;
                        $dest = $uploadDir . $namaFileBaru;

                        if (move_uploaded_file($file['tmp_name'], $dest)) {
                            // Hapus file lama jika bukan default
                            if ($kategori['gambar'] !== 'default.png' && file_exists($uploadDir . $kategori['gambar'])) {
                                unlink($uploadDir . $kategori['gambar']);
                            }
                            $gambar = $namaFileBaru;
                        } else {
                            $error = "Gagal mengunggah gambar.";
                        }
                    }
                } else {
                    $error = "Error saat mengunggah gambar.";
                }
            }

            // Jika tidak ada error → update database
            if (!$error) {
                $stmt = $conn->prepare("UPDATE kategori SET nama_kategori = ?, gambar = ? WHERE id_kategori = ?");
                $stmt->bind_param("ssi", $nama_kategori, $gambar, $id);

                if ($stmt->execute()) {
                    $success = "Kategori berhasil diperbarui!";
                    // Refresh data setelah update
                    $kategori['nama_kategori'] = $nama_kategori;
                    $kategori['gambar'] = $gambar;
                } else {
                    $error = "Gagal menyimpan perubahan: " . $conn->error;
                }
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Kategori</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    /* CSS tidak diubah sama sekali */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      transition: background 0.3s, color 0.3s;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background-image: url('batik_cirebon_megamendung_Nero_AI_Image_Upscaler_Photo_Face.jpeg.jpg');
      background-repeat: no-repeat;
      background-attachment: fixed;
      background-position: center center;
      background-size: cover;
      flex-direction: column;
    }

    .card {
      background: rgba(255, 255, 255, 0.05);
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
      backdrop-filter: blur(8px);
      animation: fadeIn 0.5s ease;
      transition: background 0.3s, color 0.3s;
      max-width: 800px;
      margin: 0 auto;
    }

    body.light .card {
      background: #c2c2c2;
      color: #333;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .card h1 {
      font-size: 1.8rem;
      margin-bottom: 10px;
      color: #2b6cb0;
      text-align: center;
    }

    body.light .card h1 {
      color: #333;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
    }

    .form-control {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: 6px;
      background: rgba(255,255,255,0.1);
      color: inherit;
      transition: border 0.3s, box-shadow 0.3s;
    }

    body.light .form-control {
      border: 1px solid #ddd;
      background: white;
    }

    .form-control:focus {
      outline: none;
      border-color: #2b6cb0;
      box-shadow: 0 0 0 3px rgba(0,217,255,0.2);
    }

    .btn {
      padding: 0.75rem 1.5rem;
      border: none;
      border-radius: 6px;
      font-weight: 500;
      cursor: pointer;
      transition: background 0.3s;
    }

    .btn-primary {
      background: #2b6cb0;
      color: white;
    }

    .btn-primary:hover {
      background: #2b6cb0;
    }

    .btn-outline {
      background: transparent;
      border: 1px solid #2b6cb0;
      color: #2b6cb0;
    }

    .alert {
      padding: 1rem;
      border-radius: 6px;
      margin-bottom: 1.5rem;
    }

    .alert-success {
      background: rgba(0,200,83,0.2);
      border: 1px solid rgba(0,200,83,0.3);
      color: #00c853;
    }

    .alert-danger {
      background: rgba(255,0,0,0.1);
      border: 1px solid rgba(255,0,0,0.2);
      color: #ff5252;
    }

    .profile-picture-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .profile-picture {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid #2b6cb0;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    .text-center {
      text-align: center;
    }

    .mt-4 {
      margin-top: 1.5rem;
    }

    .d-flex {
      display: flex;
    }

    .gap-2 {
      gap: 0.5rem;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body class="light">
  <div class="content">
    <div class="card">
      <h1>Edit Kategori</h1>

      <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <script>
          setTimeout(() => window.location.href = 'kategori.php', 1200);
        </script>
      <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data">
        <div class="form-group profile-picture-container">
          <?php
            $uploadPath = 'uploads/kategori/';
            $imgFile = $kategori['gambar'] ?? 'default.png';
            $imgSrc = file_exists($uploadPath . $imgFile)
                ? $uploadPath . rawurlencode($imgFile)
                : $uploadPath . 'default.png';
          ?>
          <img src="<?= htmlspecialchars($imgSrc) ?>" alt="gambar" class="profile-picture" id="preview">
        </div>

        <div class="form-group">
          <label for="nama_kategori" class="form-label">Nama Kategori</label>
          <input type="text" name="nama_kategori" id="nama_kategori" class="form-control"
                 required value="<?= htmlspecialchars($kategori['nama_kategori'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label for="gambar" class="form-label">Ganti Gambar (opsional, max 2MB)</label>
          <input type="file" name="gambar" id="gambar" class="form-control"
                 accept="image/*" onchange="previewImage(event)">
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
          <a href="kategori.php" class="btn btn-outline">Kembali ke Data Kategori</a>
        </div>
      </form>
    </div>
  </div>

  <script>
    function previewImage(e) {
      const file = e.target.files[0];
      if (!file) return;
      const img = document.getElementById('preview');
      const reader = new FileReader();
      reader.onload = ev => img.src = ev.target.result;
      reader.readAsDataURL(file);
    }
  </script>
</body>
</html>
