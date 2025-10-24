<?php
session_start();
include '../koneksi.php';

// Cek login
if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit;
}

// Ambil data admin
$id_admin = $_SESSION['id_admin'];
$query = $conn->query("SELECT username, foto FROM admin WHERE id_admin = '$id_admin'");
$data_admin = $query->fetch_assoc();
$nama_admin = $data_admin['username'];
$foto_admin = !empty($data_admin['foto']) ? $data_admin['foto'] : "default.png"; // fallback

// Hitung total
$total_admin    = $conn->query("SELECT COUNT(*) AS jml FROM admin")->fetch_assoc()['jml'];
$total_kategori = $conn->query("SELECT COUNT(*) AS jml FROM kategori")->fetch_assoc()['jml'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Budaya Sunda</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8f9fa;
      margin: 0;
      padding: 0;
      overflow-x: hidden;
      transition: background 0.3s, color 0.3s;
    }

    /* Sidebar */
    .sidebar {
      position: fixed;
      top: 0;
      left: -250px; /* default TERTUTUP */
      width: 250px;
      height: 100vh;
      background: #2b6cb0;
      color: white;
      padding: 20px 15px;
      transition: 0.3s;
      z-index: 1000;
    }

    .sidebar.active {
      left: 0; /* kebuka */
    }

    .sidebar .nav-link {
      color: white;
      margin: 8px 0;
      border-radius: 8px;
      transition: 0.3s;
      display: block;
    }

    .sidebar .nav-link:hover {
      background: #1a4f8b;
    }

    .profile-img {
      width: 90px;
      height: 90px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #fff;
    }

    .sidebar h6 {
      color: white;
    }

    /* Toggle Button */
    .menu-toggle {
      position: fixed;
      top: 15px;
      left: 15px;
      font-size: 34px;
      cursor: pointer;
      color: #091b2e;
      z-index: 1100;
    }

    /* Content */
    .content {
      margin-left: 30px; /* default tanpa sidebar */
      padding: 30px;
      transition: 0.3s;
    }

    .content.shift {
      margin-left: 250px; /* geser kalau sidebar kebuka */
    }

    .card-stat {
      border-radius: 15px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      transition: 0.3s;
    }
    .card-stat:hover {
      transform: translateY(-5px);
    }

    .box {
      margin-top: 60px;
    }

    .card {
      background-color: #c2cdd9ff;
      border-radius: 12px;
      border: 0px;
      padding: 20px;
      margin-bottom: 30px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
      backdrop-filter: blur(8px);
      animation: fadeIn 0.5s ease;
      transition: background 0.3s color 0.3s;
    }

    .logout {
      margin-top: 128px;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

  </style>
</head>
<body>
  <!-- Toggle Button -->
  <div class="menu-toggle" onclick="toggleSidebar()">
    <i class="bi bi-list"></i>
  </div>

  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <div class="box">
          <h3 class="text-center fw-bold mb-4">Budaya Sunda</h3>
    <div class="text-center mb-4">
      <a href="profile.php">
        <img src="uploads/profiles/<?= htmlspecialchars($foto_admin) ?>" alt="Foto Profil" class="profile-img mb-2">
        <h6 class="mt-2"><?= htmlspecialchars($nama_admin) ?></h6>
      </a>
    </div>
    <nav class="nav flex-column px-2">
      <a class="nav-link" href="dashboard.php"><i class="bi bi-house"></i> Dashboard</a>
      <a class="nav-link" href="tadmin.php"><i class="bi bi-person"></i> Admin</a>
      <a class="nav-link" href="kategori.php"><i class="bi bi-folder"></i> Kategori</a>
      <a class="nav-link" href="budaya.php"><i class="bi bi-mouse"></i> Budaya</a>
    </nav>
    <div class="logout">
      <div class="mt-auto px-2 mb-3">
      <a class="btn btn-danger w-100" href="logout.php"><i class="bi bi-box-arrow-right"></i> Log out</a>
      </div>
    </div>
    </div>
  </div>

  <!-- Content -->
  <div class="content" id="content">
    <div class="card">
      <h1 class="fw-bold mb-4">Budaya Sunda</h1>
      <h2 class="fw-bold mb-4">Selamat Datang, <a href="profile.php"><?php echo htmlspecialchars($nama_admin) ?></h2></a>
    </div>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="card card-stat text-center border-0 bg-primary text-white">
          <div class="card-body">
            <h5>Total Admin</h5>
            <h2><?= $total_admin ?></h2>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card card-stat text-center border-0 bg-success text-white">
          <div class="card-body">
            <h5>Total Kategori</h5>
            <h2><?= $total_kategori ?></h2>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function toggleSidebar() {
      document.getElementById("sidebar").classList.toggle("active");
      document.getElementById("content").classList.toggle("shift");
    }
  </script>
</body>
</html>
