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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['nama'] ?? '');

    if ($judul === '') {
        $errors[] = 'Judul lagu harus diisi.';
    } else {
        // Cek duplikat judul
        $stmt = $conn->prepare("SELECT id_lagu FROM lagu WHERE nama = ?");
        if ($stmt) {
            $stmt->bind_param('s', $judul);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $errors[] = 'Judul lagu sudah ada.';
            }
            $stmt->close();
        }
    }

    // === Upload Gambar ===
    $gambar = 'default.png';
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
                    $gambar = $newName;
                } else {
                    $errors[] = 'Gagal mengunggah gambar.';
                }
            }
        }
    }

    // === Upload Audio ===
    $audio = null;
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
                    $audio = $newName;
                } else {
                    $errors[] = 'Gagal mengunggah file audio.';
                }
            }
        }
    } else {
        $errors[] = 'File audio wajib diunggah.';
    }

    // === Simpan ke Database ===
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO lagu (nama, gambar, audio, created_at) VALUES (?, ?, ?, NOW())");
        if ($stmt) {
            $stmt->bind_param('sss', $judul, $gambar, $audio);
            if ($stmt->execute()) {
                header("Location: tb_lagu_tradisional.php");
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
<title>Tambah Lagu Tradisional</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: url('batik_cirebon_megamendung_Nero_AI_Image_Upscaler_Photo_Face.jpeg.jpg') no-repeat center center/cover;
        margin: 0;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }
    .card {
        width: 100%;
        max-width: 700px;
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(8px);
        padding: 28px 36px;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        animation: fadeIn 0.4s ease-in-out;
    }
    @keyframes fadeIn { from {opacity:0; transform:translateY(10px);} to {opacity:1; transform:none;} }

    h1 {
        text-align: center;
        color: #1e40af;
        margin-bottom: 22px;
        font-weight: 700;
    }
    .form-group { margin-bottom: 16px; }
    label {
        font-weight: 600;
        color: #333;
        margin-bottom: 6px;
        display: block;
    }
    .form-control {
        width: 100%;
        border: 1px solid #ccc;
        border-radius: 10px;
        padding: 10px;
        font-size: 15px;
        transition: 0.2s;
    }
    .form-control:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        outline: none;
    }
    .alert {
        padding: 10px 14px;
        border-radius: 8px;
        background: #ffe6e6;
        border: 1px solid #f5b7b7;
        color: #8b0000;
        margin-bottom: 14px;
    }
    img.preview {
        max-height: 150px;
        border-radius: 8px;
        object-fit: cover;
        border: 1px solid #eee;
        margin-top: 6px;
        display: block;
    }
    audio {
        width: 100%;
        margin-top: 8px;
        border-radius: 8px;
    }
    .button-group {
        text-align: center;
        margin-top: 18px;
    }
    .btn {
        border: none;
        border-radius: 10px;
        padding: 10px 16px;
        font-weight: 600;
        cursor: pointer;
        font-size: 15px;
        transition: all 0.2s;
    }
    .btn-primary {
        background: #2563eb;
        color: #fff;
    }
    .btn-primary:hover { background: #1d4ed8; }
    .btn-outline {
        border: 1px solid #2563eb;
        background: transparent;
        color: #2563eb;
        text-decoration: none;
    }
    .btn-outline:hover { background: #2563eb; color: #fff; }
</style>
</head>
<body>
<div class="card">
    <h1>Tambah Lagu Tradisional</h1>

    <?php if (!empty($errors)): foreach ($errors as $e): ?>
        <div class="alert"><?= htmlspecialchars($e) ?></div>
    <?php endforeach; endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Judul Lagu</label>
            <input type="text" name="nama" class="form-control" required value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Gambar Sampul (opsional, max 3MB)</label>
            <input type="file" name="gambar" accept="image/*" class="form-control" onchange="previewImage(event)">
            <img id="preview" class="preview" src="uploads/lagu/gambar/default.png" alt="preview">
        </div>

        <div class="form-group">
            <label>File Audio (mp3, wav, ogg, m4a - max 20MB)</label>
            <input type="file" name="audio" accept="audio/*" class="form-control" onchange="previewAudio(event)">
            <audio id="previewAudio" controls style="display:none;"></audio>
        </div>

        <div class="button-group">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
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
