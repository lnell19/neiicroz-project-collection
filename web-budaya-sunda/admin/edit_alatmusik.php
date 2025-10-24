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
    header("Location: tb_alat_musik.php");
    exit;
}

$errors = [];

// Ambil data lama
$stmt = $conn->prepare("SELECT * FROM alatmusik WHERE id_alatmusik = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$alatmusik = $result->fetch_assoc();
$stmt->close();

if (!$alatmusik) {
    die("Data tidak ditemukan.");
}

// Proses form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $asal = trim($_POST['asal_usul2'] ?? '');
    $pembuatan = trim($_POST['cara_pembuatan'] ?? '');
    $permainan = trim($_POST['cara_permainan'] ?? '');
    $gambar = $alatmusik['gambar'];

    if ($nama === '') {
        $errors[] = 'Nama alat musik harus diisi.';
    } else {
        // Validasi duplikat nama (kecuali id ini sendiri)
        $stmt = $conn->prepare("SELECT id_alatmusik FROM alatmusik WHERE nama = ? AND id_alatmusik != ?");
        $stmt->bind_param('si', $nama, $id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = 'Nama alat musik sudah digunakan.';
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
                $errors[] = 'Format gambar tidak didukung.';
            } elseif ($file['size'] > 3 * 1024 * 1024) {
                $errors[] = 'Ukuran gambar maksimal 3MB.';
            } else {
                $uploadDir = 'uploads/alatmusik/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $newName = uniqid('alat_', true) . '.' . $ext;
                $dest = $uploadDir . $newName;

                if (move_uploaded_file($file['tmp_name'], $dest)) {
                    if ($gambar !== 'default.png' && file_exists($uploadDir . $gambar)) {
                        unlink($uploadDir . $gambar);
                    }
                    $gambar = $newName;
                } else {
                    $errors[] = 'Gagal mengunggah gambar.';
                }
            }
        }
    }

    // Update ke database
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE alatmusik 
            SET nama=?, deskripsi=?, asal_usul2=?, cara_pembuatan=?, cara_permainan=?, gambar=?, updated_at=NOW() 
            WHERE id_alatmusik=?");
        $stmt->bind_param('ssssssi', $nama, $deskripsi, $asal, $pembuatan, $permainan, $gambar, $id);
        if ($stmt->execute()) {
            header("Location: tb_alat_musik.php");
            exit;
        } else {
            $errors[] = "Gagal memperbarui data: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Edit Alat Musik</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: url('batik_cirebon_megamendung_Nero_AI_Image_Upscaler_Photo_Face.jpeg.jpg') no-repeat center center/cover;
        margin: 0;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .card {
        width: 100%;
        max-width: 700px;
        background: rgba(255,255,255,0.92);
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
    .form-group {
        margin-bottom: 15px;
    }
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
    textarea.form-control {
        resize: vertical;
        min-height: 80px;
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
    <h1>Edit Alat Musik</h1>

    <?php if (!empty($errors)): foreach ($errors as $e): ?>
        <div class="alert"><?= htmlspecialchars($e) ?></div>
    <?php endforeach; endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Nama Alat Musik</label>
            <input type="text" name="nama" class="form-control" required value="<?= htmlspecialchars($alatmusik['nama']) ?>">
        </div>

        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control"><?= htmlspecialchars($alatmusik['deskripsi']) ?></textarea>
        </div>

        <div class="form-group">
            <label>Asal Usul</label>
            <textarea name="asal_usul2" class="form-control"><?= htmlspecialchars($alatmusik['asal_usul2']) ?></textarea>
        </div>

        <div class="form-group">
            <label>Cara Pembuatan</label>
            <textarea name="cara_pembuatan" class="form-control"><?= htmlspecialchars($alatmusik['cara_pembuatan']) ?></textarea>
        </div>

        <div class="form-group">
            <label>Cara Permainan</label>
            <textarea name="cara_permainan" class="form-control"><?= htmlspecialchars($alatmusik['cara_permainan']) ?></textarea>
        </div>

        <div class="form-group">
            <label>Gambar (opsional, max 3MB)</label>
            <input type="file" name="gambar" accept="image/*" class="form-control" onchange="previewImage(event)">
            <img id="preview" class="preview" src="uploads/alatmusik/<?= htmlspecialchars($alatmusik['gambar']) ?>" alt="preview">
        </div>

        <div class="button-group">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
            <a href="tb_alat_musik.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Batal</a>
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
