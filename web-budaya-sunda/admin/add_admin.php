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
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validasi input
    if ($email === '' || $username === '' || $password === '') {
        $error = "Semua field harus diisi!";
    } else {
        // Cek apakah username dan email sudah ada
        $stmt = $conn->prepare("SELECT id_admin FROM admin WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Email atau Username sudah digunakan!";
        } else {
            $level = 'off';
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $foto = "default.png"; // file default

            $stmt = $conn->prepare("INSERT INTO admin (email, username, password, level, foto, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("sssss", $email, $username, $hashed_password, $level, $foto);

            if ($stmt->execute()) {
                $success = "Admin berhasil ditambahkan!";
            } else {
                $error = "Gagal menambahkan admin: " . $conn->error;
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
  <title>Tambah Admin</title>
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
      <h1>Tambah Admin</h1>
      <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <script>
          setTimeout(function() {
            window.location.href = 'tadmin.php';
          }, 1500);
        </script>
      <?php endif; ?>
      
      <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="form-group">
          <label for="email" class="form-label">Email</label>
          <input type="email" name="email" id="email" class="form-control" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
        </div>

        <div class="form-group">
          <label for="username" class="form-label">Username</label>
          <input type="text" name="username" id="username" class="form-control" required value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
        </div>

        <div class="form-group">
          <label for="password" class="form-label">Password</label>
          <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Tambah Admin</button>
        <a href="tadmin.php" class="btn btn-outline">Kembali ke Data Admin</a>
      </form>
    </div>
  </div>
</body>
</html>