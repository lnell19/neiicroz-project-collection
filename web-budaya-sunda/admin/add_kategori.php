<?php
session_start();
include '../koneksi.php';

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Cek apakah admin sudah login
if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kategori = trim($_POST['nama_kategori'] ?? '');

    // Validasi input kosong
    if ($nama_kategori === '') {
        $error = "Nama kategori harus diisi!";
    } else {
        // 🔍 Cek duplikat nama kategori
        $stmt = $conn->prepare("SELECT id_kategori FROM kategori WHERE nama_kategori = ?");
        $stmt->bind_param("s", $nama_kategori);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Kategori dengan nama tersebut sudah ada.";
        } else {
            // ✅ Handle upload gambar
            $gambar = "default.png"; // default jika tidak upload

            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] !== UPLOAD_ERR_NO_FILE) {
                $file = $_FILES['gambar'];

                if ($file['error'] === UPLOAD_ERR_OK) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

                    if (!in_array($ext, $allowed)) {
                        $error = "Format gambar tidak didukung. Gunakan: jpg, jpeg, png, atau gif.";
                    } elseif ($file['size'] > 2 * 1024 * 1024) { // batas 2MB
                        $error = "Ukuran gambar maksimal 2MB.";
                    } else {
                        $uploadDir = 'uploads/kategori/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }

                        $namaFileBaru = uniqid('kat_', true) . '.' . $ext;
                        $dest = $uploadDir . $namaFileBaru;

                        if (move_uploaded_file($file['tmp_name'], $dest)) {
                            $gambar = $namaFileBaru;
                        } else {
                            $error = "Gagal mengunggah gambar.";
                        }
                    }
                } else {
                    $error = "Error saat mengunggah gambar.";
                }
            }

            // ✅ Jika tidak ada error, simpan ke database
            if (!isset($error)) {
                $stmt = $conn->prepare("INSERT INTO kategori (nama_kategori, gambar, created_at) VALUES (?, ?, NOW())");
                $stmt->bind_param("ss", $nama_kategori, $gambar);

                if ($stmt->execute()) {
                    $success = "Kategori berhasil ditambahkan!";
                } else {
                    $error = "Gagal menambahkan kategori: " . $conn->error;
                }
                $stmt->close();
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah Kategori</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: 'Segoe UI', sans-serif;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        background-image: url('batik_cirebon_megamendung_Nero_AI_Image_Upscaler_Photo_Face.jpeg.jpg');
        background-size: cover;
        background-attachment: fixed;
        flex-direction: column;
    }

    .card {
        background: #ffffff; /* ⬅️ Gak transparan lagi */
        color: #333;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        animation: fadeIn 0.5s ease;
        max-width: 800px;
        margin: 0 auto;
    }

    .card h1 {
        font-size: 1.8rem;
        margin-bottom: 10px;
        color: #2b6cb0;
        text-align: center;
    }

    .form-group { margin-bottom: 1.5rem; }
    .form-label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: #333; }

    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ccc;
        border-radius: 6px;
        background: #f8f9fa; /* ⬅️ solid abu muda */
        color: #000;
        transition: border 0.3s, box-shadow 0.3s;
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
        background: #234e8b;
    }

    .btn-outline {
        background: transparent;
        border: 1px solid #2b6cb0;
        color: #2b6cb0;
    }

    .btn-outline:hover {
        background: rgba(0,217,255,0.1);
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

    input[type="file"] {
        position: relative;
        z-index: 9999;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
</head>
<body>
<div class="content">
    <div class="card">
        <h1>Tambah Kategori</h1>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <script>
                setTimeout(function() {
                    window.location.href = 'kategori.php';
                }, 1500);
            </script>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nama_kategori" class="form-label">Nama Kategori</label>
                <input type="text" name="nama_kategori" id="nama_kategori" class="form-control" required value="<?= isset($_POST['nama_kategori']) ? htmlspecialchars($_POST['nama_kategori']) : '' ?>">
            </div>

            <div class="form-group">
                <label for="gambar" class="form-label">Gambar (opsional, max 2MB)</label>
                <input type="file" name="gambar" id="gambar" class="form-control" accept="image/*">
            </div>

            <button type="submit" class="btn btn-primary">Tambah Kategori</button>
            <a href="kategori.php" class="btn btn-outline">Kembali ke Data Kategori</a>
        </form>
    </div>
</div>
</body>
</html>
