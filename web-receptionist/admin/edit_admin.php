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

$error = '';
$success = '';
$admin_data = null;

// Ambil data admin yang akan diedit
if (isset($_GET['id'])) {
    $id_admin = $_GET['id'];
    
    $stmtSelect = $conn->prepare("SELECT id_admin, email, username, level, foto FROM admin WHERE id_admin = ?");
    $stmtSelect->bind_param("i", $id_admin);
    $stmtSelect->execute();
    $result = $stmtSelect->get_result();
    
    if ($result->num_rows === 1) {
        $admin_data = $result->fetch_assoc();
    } else {
        $error = "Admin tidak ditemukan!";
    }
    $stmtSelect->close();
}

// Proses form update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_admin = $_POST['id_admin'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $level = $_POST['level'] ?? '';

    // Validasi input
    if ($email === '' || $username === '' || $level === '') {
        $error = "Semua field harus diisi!";
    } else {
        // Cek apakah username sudah digunakan oleh admin lain
        $stmtCheck = $conn->prepare("SELECT id_admin FROM admin WHERE username = ? AND id_admin != ?");
        $stmtCheck->bind_param("si", $username, $id_admin);
        $stmtCheck->execute();
        $stmtCheck->store_result();

        if ($stmtCheck->num_rows > 0) {
            $error = "Username sudah digunakan!";
        } else {
            if (!empty($password)) {
                // Update dengan password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmtUpdate = $conn->prepare("UPDATE admin SET email = ?, username = ?, password = ?, level = ? WHERE id_admin = ?");
                $stmtUpdate->bind_param("ssssi", $email, $username, $hashed_password, $level, $id_admin);
            } else {
                // Update tanpa password
                $stmtUpdate = $conn->prepare("UPDATE admin SET email = ?, username = ?, level = ? WHERE id_admin = ?");
                $stmtUpdate->bind_param("sssi", $email, $username, $level, $id_admin);
            }

            if ($stmtUpdate->execute()) {
                $success = "Data admin berhasil diperbarui!";

                // Ambil data terbaru
                $stmtSelect = $conn->prepare("SELECT id_admin, email, username, level, foto FROM admin WHERE id_admin = ?");
                $stmtSelect->bind_param("i", $id_admin);
                $stmtSelect->execute();
                $result = $stmtSelect->get_result();
                $admin_data = $result->fetch_assoc();
                $stmtSelect->close();
            } else {
                $error = "Gagal memperbarui data admin: " . $conn->error;
            }
            $stmtUpdate->close();
        }
        $stmtCheck->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Admin</title>
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
      flex-direction: column;
      justify-content: center;
      align-items: center;
      background: linear-gradient(135deg, #1a1a2e, #16213e);
      color: white;
      padding: 20px;
    }

    /* Light Mode */
    body.light {
      background: #f4f4f4;
      color: #333;
    }

    /* Theme Toggle Button */
    .theme-toggle {
      position: fixed;
      top: 15px;
      right: 15px;
      font-size: 20px;
      background: #00d9ff;
      color: white;
      border: none;
      padding: 8px 12px;
      border-radius: 6px;
      cursor: pointer;
      z-index: 1100;
      transition: background 0.3s;
    }
    .theme-toggle:hover {
      background: #00aacc;
    }

    /* Header */
    .header {
      text-align: center;
      margin-bottom: 30px;
      width: 100%;
      max-width: 450px;
    }
    .header h1 {
      font-size: 2rem;
      margin-bottom: 10px;
      color: #00d9ff;
    }
    .header h2 {
      font-weight: normal;
      color: #ddd;
      font-size: 1.2rem;
    }
    body.light .header h1 {
      color: #333;
    }
    body.light .header h2 {
      color: #666;
    }

    /* Card */
    .card {
      background: rgba(255, 255, 255, 0.05);
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
      backdrop-filter: blur(8px);
      animation: fadeIn 0.5s ease;
      transition: background 0.3s, color 0.3s;
      width: 100%;
      max-width: 450px;
    }
    body.light .card {
      background: white;
      color: #333;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    /* Form styles */
    .form-group {
      margin-bottom: 1.5rem;
    }
    .form-label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
      color: #00d9ff;
    }
    body.light .form-label {
      color: #333;
    }
    .form-control {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: 6px;
      background: rgba(255,255,255,0.1);
      color: inherit;
      transition: border 0.3s, box-shadow 0.3s;
      font-size: 1rem;
    }
    body.light .form-control {
      border: 1px solid #ddd;
      background: white;
    }
    .form-control:focus {
      outline: none;
      border-color: #00d9ff;
      box-shadow: 0 0 0 3px rgba(0,217,255,0.2);
    }
    .form-select {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: 6px;
      background: rgba(255,255,255,0.1);
      color: inherit;
      transition: border 0.3s, box-shadow 0.3s;
      font-size: 1rem;
    }
    body.light .form-select {
      border: 1px solid #ddd;
      background: white;
    }
    .form-select:focus {
      outline: none;
      border-color: #00d9ff;
      box-shadow: 0 0 0 3px rgba(0,217,255,0.2);
    }
    .btn {
      padding: 0.75rem 1.5rem;
      border: none;
      border-radius: 6px;
      font-weight: 500;
      cursor: pointer;
      transition: background 0.3s;
      width: 100%;
      font-size: 1rem;
      margin-top: 10px;
    }
    .btn-primary {
      background: #00d9ff;
      color: white;
    }
    .btn-primary:hover {
      background: #00aacc;
    }
    .btn-outline {
      background: transparent;
      border: 1px solid #00d9ff;
      color: #00d9ff;
      text-decoration: none;
      display: inline-block;
      text-align: center;
    }
    .btn-outline:hover {
      background: rgba(0,217,255,0.1);
    }
    .alert {
      padding: 1rem;
      border-radius: 6px;
      margin-bottom: 1.5rem;
      text-align: center;
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
    .text-center {
      text-align: center;
    }
    .mt-4 {
      margin-top: 1.5rem;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Responsive adjustments */
    @media (max-width: 500px) {
      .card {
        padding: 20px;
      }
      .header h1 {
        font-size: 1.8rem;
      }
      .header h2 {
        font-size: 1rem;
      }
    }
  </style>
</head>
<body class="dark">
  <button class="theme-toggle" onclick="toggleTheme()">🌙 Mode</button>

  <div class="header">
    <h1>Edit Admin</h1>
  </div>

  <div class="content">
    <div class="card">
      <?php if (!empty($success)): ?>
        <div class="alert alert-success">
          <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
          <div class="mt-4">
            <a href="tadmin.php" class="btn btn-outline">Kembali ke Data Admin</a>
          </div>
        </div>
      <?php endif; ?>
      
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
          <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <?php if ($admin_data && empty($success)): ?>
      <form method="POST" action="">
        <input type="hidden" name="id_admin" value="<?= $admin_data['id_admin'] ?>">
        
        <div class="form-group">
          <label for="email" class="form-label">Email</label>
          <input type="email" name="email" id="email" class="form-control" required 
                 value="<?= htmlspecialchars($admin_data['email']) ?>">
        </div>

        <div class="form-group">
          <label for="username" class="form-label">Username</label>
          <input type="text" name="username" id="username" class="form-control" required 
                 value="<?= htmlspecialchars($admin_data['username']) ?>">
        </div>

        <div class="form-group">
          <label for="password" class="form-label">Password (Kosongkan jika tidak ingin mengubah)</label>
          <input type="password" name="password" id="password" class="form-control" 
                 placeholder="Masukkan password baru">
        </div>

        <button type="submit" class="btn btn-primary">Perbarui Data Admin</button>
        <a href="tadmin.php" class="btn btn-outline">Kembali ke Data Admin</a>
      </form>
      <?php elseif (empty($error) && empty($success)): ?>
        <div class="alert alert-danger">
          <i class="fas fa-exclamation-triangle"></i> Data admin tidak ditemukan.
        </div>
        <a href="tadmin.php" class="btn btn-outline">Kembali ke Data Admin</a>
      <?php endif; ?>
    </div>
  </div>

  <script>
    function toggleTheme() {
      const body = document.body;
      const currentTheme = body.classList.contains("dark") ? "dark" : "light";
      const newTheme = currentTheme === "dark" ? "light" : "dark";
      
      body.classList.remove(currentTheme);
      body.classList.add(newTheme);
      localStorage.setItem("theme", newTheme);
    }

    document.addEventListener("DOMContentLoaded", () => {
      const savedTheme = localStorage.getItem("theme") || "dark";
      document.body.classList.add(savedTheme);
    });
  </script>
</body>
</html>