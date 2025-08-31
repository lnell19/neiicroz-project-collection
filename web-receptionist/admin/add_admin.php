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
        // Cek apakah username sudah ada
        $stmt = $conn->prepare("SELECT id_admin FROM admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username sudah digunakan!";
        } else {
            $level = 'Non Aktif';
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $foto = "default.png"; // file default

            $stmt = $conn->prepare("INSERT INTO admin (email, username, password, level, foto) VALUES (?, ?, ?, ?, ?)");
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
    }

    /* Dark Mode */
    body.dark {
      background: linear-gradient(135deg, #1a1a2e, #16213e);
      color: white;
    }
    /* Light Mode */
    body.light {
      background: #f4f4f4;
      color: #333;
    }

    /* Toggle Button */
    .menu-toggle {
      position: fixed;
      top: 15px;
      left: 15px;
      font-size: 26px;
      color: #00d9ff;
      cursor: pointer;
      z-index: 1100;
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
    .card h1 {
      font-size: 1.8rem;
      margin-bottom: 10px;
      color: #00d9ff;
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
      margin-top: 10px;
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
  </style>
</head>
<body class="dark">
  <button class="theme-toggle" onclick="toggleTheme()">🌙 Mode</button>

  <div class="content">
    <div class="card">
      <h1>Tambah Admin</h1>
      <h2>SMKN 71 JAKARTA</h2>

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

  <script>
    function toggleTheme() {
      const body = document.body;
      const currentTheme = body.classList.contains("dark") ? "dark" : "light";
      const newTheme = currentTheme === "dark" ? "light" : "dark";
      
      body.classList.remove(currentTheme);
      body.classList.add(newTheme);
      localStorage.setItem("theme", newTheme);
    }

    // Load tema dari localStorage saat halaman dimuat
    document.addEventListener("DOMContentLoaded", () => {
      const savedTheme = localStorage.getItem("theme") || "dark";
      document.body.classList.add(savedTheme);
    });
  </script>
</body>
</html>