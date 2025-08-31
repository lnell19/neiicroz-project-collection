<?php
session_start();
include "../koneksi.php";

if (!isset($_SESSION['id_admin'])) {
    header("Location: ../admin/login.php");
    exit;
}

// Ambil data admin
$id_admin = $_SESSION['id_admin'];
$query = $conn->query("SELECT username FROM admin WHERE id_admin = '$id_admin'");
$data_admin = $query->fetch_assoc();
$nama_admin = $data_admin['username'];

// Ambil input filter tanggal
$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : '';
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : '';

// Pagination
$batas = 10;
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$mulai = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

// Filter query
$filter_sql = "";
if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
    $filter_sql = " WHERE tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
}

// Query data tamu
$sql = "SELECT * FROM tamu $filter_sql ORDER BY tanggal DESC LIMIT $mulai, $batas";
$data = mysqli_query($conn, $sql);

// Hitung total data
$total_data = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tamu $filter_sql"));
$total_halaman = ceil($total_data / $batas);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Tamu</title>
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
      padding-top: 60px;
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
      margin-bottom: 2rem;
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

    /* Filter Section */
    .filter {
      background: rgba(255, 255, 255, 0.05);
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
      backdrop-filter: blur(8px);
      margin-bottom: 25px;
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      align-items: center;
      transition: background 0.3s;
    }
    
    body.light .filter {
      background: white;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .filter label {
      font-weight: 600;
      color: #00d9ff;
    }
    
    body.light .filter label {
      color: #2c3e50;
    }
    
    .filter input[type="date"] {
      padding: 10px 15px;
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 6px;
      font-size: 14px;
      background: rgba(0, 0, 0, 0.1);
      color: white;
      transition: all 0.3s;
    }
    
    body.light .filter input[type="date"] {
      background: #f8f9fa;
      color: #333;
      border: 1px solid #ddd;
    }
    
    .filter button {
      background-color: #00d9ff;
      color: white;
      border: none;
      padding: 10px 18px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 14px;
      transition: background-color 0.3s;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .filter button:hover {
      background-color: #00aacc;
    }
    
    .filter .reset-btn {
      background-color: #95a5a6;
      color: white;
      text-decoration: none;
      padding: 10px 18px;
      border-radius: 6px;
      font-size: 14px;
      transition: background-color 0.3s;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .filter .reset-btn:hover {
      background-color: #7f8c8d;
    }
    
    /* Table */
    .table-container {
      overflow-x: auto;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
      backdrop-filter: blur(8px);
      margin-bottom: 20px;
    }
    
    body.light .table-container {
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
      background: rgba(255, 255, 255, 0.05);
      transition: background 0.3s;
    }
    
    body.light table {
      background: white;
    }
    
    table th {
      background-color: #00d9ff;
      color: white;
      padding: 15px;
      text-align: left;
      font-weight: 600;
    }
    
    body.light table th {
      background-color: #00aacc;
    }
    
    table td {
      padding: 12px 15px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      color: #ddd;
    }
    
    body.light table td {
      color: #666;
      border-bottom: 1px solid #eee;
    }
    
    table tr:nth-child(even) {
      background-color: rgba(255, 255, 255, 0.02);
    }
    
    body.light table tr:nth-child(even) {
      background-color: #f8f9fa;
    }
    
    table tr:hover {
      background-color: rgba(0, 217, 255, 0.1);
    }
    
    body.light table tr:hover {
      background-color: #e8f4fc;
    }
    
    /* Pagination */
    .pagination {
      display: flex;
      justify-content: center;
      margin: 20px 0;
      gap: 8px;
    }
    
    .pagination a {
      padding: 10px 15px;
      text-decoration: none;
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: #00d9ff;
      border-radius: 6px;
      transition: all 0.3s;
      background: rgba(255, 255, 255, 0.05);
    }
    
    body.light .pagination a {
      border: 1px solid #ddd;
      background: white;
    }
    
    .pagination a.active {
      background-color: #00d9ff;
      color: white;
      border-color: #00d9ff;
    }
    
    .pagination a:hover:not(.active) {
      background-color: rgba(0, 217, 255, 0.2);
    }
    
    body.light .pagination a:hover:not(.active) {
      background-color: #f1f1f1;
    }
    
    /* Empty State */
    .empty-state {
      text-align: center;
      padding: 40px 20px;
      color: #aaa;
    }
    
    body.light .empty-state {
      color: #666;
    }
    
    .empty-state i {
      font-size: 3rem;
      margin-bottom: 15px;
      color: #00d9ff;
    }
    
    .empty-state p {
      font-size: 1.1rem;
    }

    /* Animations */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Responsive */
    @media (max-width: 768px) {
      .filter {
        flex-direction: column;
        align-items: flex-start;
      }
      
      .filter > * {
        width: 100%;
      }
      
      .filter input[type="date"] {
        width: 100%;
      }
      
      table {
        font-size: 14px;
      }
      
      table th, table td {
        padding: 10px;
      }
    }
  </style>
</head>
<body class="dark">
  <div class="menu-toggle" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
  </div>
  <button class="theme-toggle" onclick="toggleTheme()">🌙 Mode</button>
  
  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <h2>Menu</h2>
    <ul>
      <li><a href="../admin/dashboard.php"><i class="fas fa-home"></i> Home</a></li>
      <li><a href="../admin/tadmin.php"><i class="fas fa-user-cog"></i> Admin</a></li>
      <li><a href="tamu.php"><i class="fas fa-users"></i> Tamu</a></li>
      <li><a href="kehadiran.php"><i class="fas fa-user-check"></i> Kehadiran</a></li>
      <li><a href="../laporan/laporan.php"><i class="fas fa-file-alt"></i> Laporan</a></li>
      <li><a href="../admin/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </div>
  
  <!-- Content -->
  <div class="content">
    <div class="card">
      <h1 align="center">Daftar Tamu</h1>
      <h2 align="center">SMKN 71 JAKARTA</h2>
    </div>

    <form class="filter" method="GET" action="">
      <label><i class="fas fa-calendar-alt"></i> Dari:</label>
      <input type="date" name="tanggal_awal" value="<?= htmlspecialchars($tanggal_awal) ?>">

      <label>Sampai:</label>
      <input type="date" name="tanggal_akhir" value="<?= htmlspecialchars($tanggal_akhir) ?>">

      <button type="submit"><i class="fas fa-search"></i> Tampilkan</button>
      <a href="tamu.php" class="reset-btn"><i class="fas fa-sync"></i> Reset</a>
    </form>

    <div class="table-container">
      <table>
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>Instansi</th>
          <th>Keperluan</th>
          <th>Tanggal/Waktu</th>
          <th>Aksi</th>
        </tr>
        
        <?php 
        if (mysqli_num_rows($data) > 0) {
          $no = $mulai + 1;
          while ($row = mysqli_fetch_assoc($data)) {
            echo "<tr>
              <td>$no</td>
              <td>{$row['nama']}</td>
              <td>{$row['instansi']}</td>
              <td>{$row['keperluan']}</td>
              <td>{$row['tanggal']}</td>
              <td>
              <a href='delete_tamu.php?id={$row['id_tamu']}' 
                 onclick=\"return confirm('Yakin ingin menghapus tamu ini?');\" 
                 style='color:red; text-decoration:none;'>
                 <i class='fas fa-trash'></i> Hapus
             </a>
             </td>
            </tr>";
            $no++;
          }
        } else {
          echo "<tr><td colspan='5' style='text-align:center;'>";
          echo '<div class="empty-state">';
          echo '  <i class="fas fa-user-slash"></i>';
          echo '  <p>Tidak ada data tamu</p>';
          echo '</div>';
          echo "</td></tr>";
        }
        ?>
      </table>
    </div>

    <div class="pagination">
      <?php for ($i = 1; $i <= $total_halaman; $i++) : ?>
        <a class="<?= ($i == $halaman) ? 'active' : '' ?>" href="?halaman=<?= $i ?>&tanggal_awal=<?= urlencode($tanggal_awal) ?>&tanggal_akhir=<?= urlencode($tanggal_akhir) ?>">
          <?= $i ?>
        </a>
      <?php endfor; ?>
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
      
      // Update button text
      const themeButton = document.querySelector('.theme-toggle');
      themeButton.textContent = newTheme === 'dark' ? '☀️ Mode' : '🌙 Mode';
    }

    document.addEventListener("DOMContentLoaded", () => {
      let savedTheme = localStorage.getItem("theme") || "dark";
      document.body.classList.add(savedTheme);
      
      // Set button text based on saved theme
      const themeButton = document.querySelector('.theme-toggle');
      themeButton.textContent = savedTheme === 'dark' ? '☀️ Mode' : '🌙 Mode';
    });
  </script>
</body>
</html>