<?php
session_start();
include "../koneksi.php";

// Cek apakah admin sudah login
if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit;
}

$admin_id = $_SESSION['id_admin'];
$error = '';
$success = '';

// Ambil data admin dari database
$stmt = $conn->prepare("SELECT id_admin, email, username, password, level, foto FROM admin WHERE id_admin = ?");
$stmt->bind_param('i', $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

$current_email = $admin['email'];
$current_profile = $admin['foto'] ?? 'default.png';

// Path untuk direktori upload (menggunakan path relatif)
$upload_dir = 'uploads/profiles/';

// Proses form jika dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_email = trim($_POST['email'] ?? '');
    $new_username = trim($_POST['username'] ?? '');
    $new_password = trim($_POST['password'] ?? '');
    $current_password = trim($_POST['current_password'] ?? '');

    // Validasi input
    if (empty($new_email) || empty($new_username)) {
        $error = "Email dan Username tidak boleh kosong!";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid!";
    } else {
        // Cek apakah email sudah digunakan admin lain
        $check_email = $conn->prepare("SELECT id_admin FROM admin WHERE email = ? AND id_admin != ?");
        $check_email->bind_param('si', $new_email, $admin_id);
        $check_email->execute();
        $check_email->store_result();
        
        if ($check_email->num_rows > 0) {
            $error = "Email sudah digunakan oleh admin lain!";
        } else {
            // Validasi password jika ingin diubah
            $password_error = false;
            if (!empty($new_password)) {
                if (empty($current_password)) {
                    $error = "Harap masukkan password saat ini untuk mengubah password!";
                    $password_error = true;
                } elseif (!password_verify($current_password, $admin['password'])) {
                    $error = "Password saat ini salah!";
                    $password_error = true;
                }
            }

            // Handle upload gambar hanya jika tidak ada error password
            if (!$password_error && isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                $file_type = $_FILES['foto']['type'];
                
                // Dapatkan ekstensi file
                $file_name = $_FILES['foto']['name'];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                
                if (!in_array($file_type, $allowed_types)) {
                    $error = "Hanya file JPG, PNG, atau GIF yang diperbolehkan! Diterima: " . $file_type;
                } elseif ($_FILES['foto']['size'] > 2 * 1024 * 1024) { // 2MB max
                    $error = "Ukuran file terlalu besar (maksimal 2MB)!";
                } else {
                    // Pastikan direktori upload ada
                    if (!file_exists($upload_dir)) {
                        if (!mkdir($upload_dir, 0755, true)) {
                            $error = "Gagal membuat direktori upload! Pastikan folder uploads dapat ditulisi.";
                        }
                    }
                    
                    if (empty($error)) {
                        // Generate nama file unik
                        $filename = 'profile_' . $admin_id . '_' . time() . '.' . $file_ext;
                        $upload_path = $upload_dir . $filename;

                        if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_path)) {
                            // Hapus foto lama jika bukan default
                            if ($current_profile && $current_profile !== 'default.png' && file_exists($upload_dir . $current_profile)) {
                                unlink($upload_dir . $current_profile);
                            }
                            $current_profile = $filename;
                        } else {
                            $error = "Gagal mengupload gambar! Error: " . $_FILES['foto']['error'];
                        }
                    }
                }
            }

            if (empty($error)) {
                // Update data admin
                if (empty($new_password)) {
                    $update = $conn->prepare("UPDATE admin SET email = ?, username = ?, foto = ? WHERE id_admin = ?");
                    $update->bind_param('sssi', $new_email, $new_username, $current_profile, $admin_id);
                } else {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $update = $conn->prepare("UPDATE admin SET email = ?, username = ?, password = ?, foto = ? WHERE id_admin = ?");
                    $update->bind_param('ssssi', $new_email, $new_username, $hashed_password, $current_profile, $admin_id);
                }
                
                if ($update->execute()) {
                    $success = "Profil berhasil diperbarui!";
                    $_SESSION['username'] = $new_username;
                    // Refresh data admin
                    $admin['email'] = $new_email;
                    $admin['username'] = $new_username;
                    $admin['foto'] = $current_profile;
                    $current_email = $new_email;
                } else {
                    $error = "Gagal memperbarui profil: " . $conn->error;
                }
                $update->close();
            }
        }
        $check_email->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Profil Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      transition: background 0.3s, color 0.3s;
      background-image: url('batik_cirebon_megamendung_Nero_AI_Image_Upscaler_Photo_Face.jpeg.jpg');
      background-repeat: no-repeat;
      background-attachment: fixed;
      background-position: center center;
      background-size: cover;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 40px;
    }

    /* Content */
    .content {
      padding: 2rem;
      margin-left: 0;
      transition: margin-left 0.3s ease;
    }


    /* Card */
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

    .card h2 {
      font-weight: normal;
      color: #ddd;
      text-align: center;
      margin-bottom: 30px;
    }

    body.light .card h2 {
      color: #666;
    }

    /* Form styles */
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

    .input-group {
      display: flex;
    }

    .input-group .form-control {
      border-top-right-radius: 0;
      border-bottom-right-radius: 0;
    }

    .input-group-btn {
      display: flex;
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

    .btn-outline:hover {
      background: rgba(0,217,255,0.1);
    }

    .btn-secondary {
      padding: 0.5rem 1rem;
      background: rgba(255,255,255,0.1);
      border: 1px solid rgba(255,255,255,0.2);
    }

    .btn-secondary:hover {
      background: rgba(255,255,255,0.2);
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

    body.light .profile-picture {
      border-color: #2b6cb0;
    }

    #profile_picture {
      max-width: 300px;
      padding: 0.5rem;
      cursor: pointer;
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
      <div class="text-center">
        <div class="profile-picture-container">
          <img src="uploads/profiles/<?= htmlspecialchars($current_profile) ?>" 
               alt="Foto Profil" 
               class="profile-picture"
               onerror="this.src='uploads/profiles/default.png'">
        </div>
        <h1><?= htmlspecialchars($admin['username']) ?></h1>
        <h2>Email: <?= htmlspecialchars($current_email) ?></h2>
      </div>

      <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" class="mt-4" enctype="multipart/form-data">
        <div class="form-group">
          <label for="foto" class="form-label">Foto Profil</label>
          <input type="file" name="foto" id="foto" 
                 accept=".jpg,.jpeg,.png,.gif" 
                 class="form-control">
          <small class="form-text">Format: JPG, PNG, atau GIF (maks. 2MB)</small>
        </div>

        <div class="form-group">
          <label for="email" class="form-label">Email</label>
          <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($current_email) ?>" required>
        </div>

        <div class="form-group">
          <label for="username" class="form-label">Username</label>
          <input type="text" name="username" id="username" class="form-control" value="<?= htmlspecialchars($admin['username']) ?>" required>
        </div>

        <div class="form-group">
          <label for="password" class="form-label">Password Baru (opsional)</label>
          <div class="input-group">
            <input type="password" id="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengganti">
            <button type="button" id="togglePass" class="btn btn-secondary">👁</button>
          </div>
        </div>

        <div class="form-group">
          <label for="current_password" class="form-label">Konfirmasi Password Saat Ini</label>
          <input type="password" id="current_password" name="current_password" class="form-control" required>
          <small class="form-text">Harus diisi untuk menyimpan perubahan</small>
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
          <a href="dashboard.php" class="btn btn-outline">Kembali ke Dashboard</a>
        </div>
      </form>
    </div>
  </div>

  <script>
    function toggleSidebar() {
      document.getElementById("sidebar").classList.toggle("active");
    }

    // Load tema dari localStorage saat halaman dimuat
    document.addEventListener("DOMContentLoaded", () => {
      const savedTheme = localStorage.getItem("theme") || "dark";
      document.body.classList.add(savedTheme);

      // Toggle password visibility
      const toggleBtn = document.getElementById('togglePass');
      const passInput = document.getElementById('password');
      const currentPassInput = document.getElementById('current_password');
      
      toggleBtn.addEventListener('click', () => {
        const type = passInput.type === 'password' ? 'text' : 'password';
        passInput.type = type;
        toggleBtn.textContent = type === 'password' ? '👁' : '🙈';
      });

      // Preview gambar sebelum upload
      const profilePicInput = document.getElementById('foto');
      const profilePicPreview = document.querySelector('.profile-picture');

      profilePicInput.addEventListener('change', function(e) {
          const file = e.target.files[0];
          if (file) {
              const reader = new FileReader();
              reader.onload = function(event) {
                  profilePicPreview.src = event.target.result;
              }
              reader.readAsDataURL(file);
          }
      });

      // Validasi ukuran file sebelum upload
      profilePicInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file && file.size > 2 * 1024 * 1024) {
          alert('File terlalu besar! Maksimal 2MB.');
          this.value = '';
        }
      });
    });
  </script>
</body>
</html>