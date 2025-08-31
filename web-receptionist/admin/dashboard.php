<?php
session_start();
include "../koneksi.php";
// Cek apakah user sudah login
if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit;
}
// Ambil data admin
$id_admin = $_SESSION['id_admin'];
$query = $conn->query("SELECT username FROM admin WHERE id_admin = '$id_admin'");
$data_admin = $query->fetch_assoc();
$nama_admin = $data_admin['username'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Resepsionis</title>
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
    /* Sidebar */
    .sidebar {
      position: fixed;
      left: -250px;
      top: 0;
      width: 250px;
      height: 100%;
      background: #0f3460;
      color: white;
      transition: left 0.3s ease, background 0.3s;
      z-index: 1000;
      box-shadow: 3px 0 10px rgba(0, 0, 0, 0.3);
    }
    body.light .sidebar {
      background: #fff;
      color: #333;
      box-shadow: 3px 0 10px rgba(0,0,0,0.1);
    }
    .sidebar.active {
      left: 0;
    }
    .sidebar h2 {
      text-align: center;
      padding: 1.2rem;
      background: #16213e;
      font-size: 1.2rem;
      letter-spacing: 1px;
      text-transform: uppercase;
      transition: background 0.3s;
    }
    body.light .sidebar h2 {
      background: #eaeaea;
    }
    .sidebar ul {
      list-style: none;
    }
    .sidebar ul li {
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      transition: border 0.3s;
    }
    body.light .sidebar ul li {
      border-bottom: 1px solid #ddd;
    }
    .sidebar ul li a {
      color: inherit;
      text-decoration: none;
      display: flex;
      align-items: center;
      padding: 15px 20px;
      transition: 0.2s;
      font-size: 0.95rem;
    }
    .sidebar ul li a i {
      margin-right: 15px;
      font-size: 1.2rem;
    }
    .sidebar ul li a:hover {
      padding-left: 25px;
      background: rgba(0,0,0,0.1);
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
    /* Content */
    .content {
      padding: 2rem;
      margin-left: 0;
      transition: margin-left 0.3s ease;
    }
    .sidebar.active ~ .content {
      margin-left: 250px;
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
    }
    body.light .card h1 {
      color: #333;
    }
    .card h2 {
      font-weight: normal;
      color: #ddd;
    }
    body.light .card h2 {
      color: #666;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body class="dark">
  <div class="menu-toggle" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
  </div>
  <button class="theme-toggle" onclick="toggleTheme()">🌙 Mode</button>
  <div class="sidebar" id="sidebar">
    <h2>Menu</h2>
    <ul>
      <li><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
      <li><a href="../admin/tadmin.php"><i class="fas fa-user-cog"></i> Admin</a></li>
      <li><a href="../tamu/tamu.php"><i class="fas fa-users"></i> Tamu</a></li>
      <li><a href="../tamu/kehadiran.php"><i class="fas fa-user-check"></i> Kehadiran</a></li>
      <li><a href="../laporan/laporan.php"><i class="fas fa-file-alt"></i> Laporan</a></li>
      <li><a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </div>
  <div class="content">
    <div class="card">
      <h1>Selamat Datang, <a href="profile.php"><?php echo htmlspecialchars($nama_admin) ?></a></h1>
      <h2>SMKN 71 JAKARTA</h2>
    </div>
  </div>
  <script>
    function toggleSidebar() {
      document.getElementById("sidebar").classList.toggle("active");
    }
    function toggleTheme() {
      let body = document.body;
      let currentTheme = body.classList.contains("dark") ? "dark" : "light";
      let newTheme = currentTheme === "dark" ? "light" : "dark";
      body.classList.remove(currentTheme);
      body.classList.add(newTheme);
      localStorage.setItem("theme", newTheme);
    }
    // Load tema dari localStorage
    document.addEventListener("DOMContentLoaded", () => {
      let savedTheme = localStorage.getItem("theme") || "dark";
      document.body.classList.add(savedTheme);
    });
  </script>
</body>
</html>