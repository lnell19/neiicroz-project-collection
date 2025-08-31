<?php
session_start();
include "../koneksi.php";

if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit;
}

// Ambil data admin
$id_admin = $_SESSION['id_admin'];
$query = $conn->query("SELECT username FROM admin WHERE id_admin = '$id_admin'");
$data_admin = $query->fetch_assoc();
$nama_admin = $data_admin['username'];

// Tangani perubahan status aktif/nonaktif
if (isset($_POST['toggle_status'])) {
    $admin_id = $_POST['id_admin'];
    $new_status = $_POST['new_status'];
    
    // Update status di database
    $update_query = $conn->query("UPDATE admin SET level = '$new_status' WHERE id_admin = '$admin_id'");
    
    if ($update_query) {
        echo "success";
    } else {
        echo "error";
    }
    exit;
}

// Tangani hapus admin
if (isset($_POST['delete_admin'])) {
    $admin_id = $_POST['id_admin'];
    $current_admin = $_SESSION['id_admin'];

    if ($admin_id == $current_admin) {
        echo "self_delete_blocked"; // cegah hapus akun diri sendiri
        exit;
    }

    $delete_query = $conn->query("DELETE FROM admin WHERE id_admin = '$admin_id'");
    if ($delete_query) {
        echo "deleted";
    } else {
        echo "error";
    }
    exit;
}

$query = $conn->query("SELECT * FROM admin");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Admin</title>
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

    /* Admin Cards Container */
    .admin-cards {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 20px;
      margin-top: 20px;
    }

    /* Admin Card */
    .admin-card {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
      backdrop-filter: blur(8px);
      transition: transform 0.3s, box-shadow 0.3s;
      animation: fadeIn 0.5s ease;
      position: relative;
      overflow: hidden;
    }
    
    body.light .admin-card {
      background: white;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .admin-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 25px rgba(0, 0, 0, 0.5);
    }
    
    body.light .admin-card:hover {
      box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }
    
    .admin-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 4px;
      background: linear-gradient(90deg, #00d9ff, #00aacc);
    }
    
    .admin-header {
      display: flex;
      align-items: center;
      margin-bottom: 15px;
    }
    
    .admin-avatar {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: linear-gradient(135deg, #00d9ff, #00aacc);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 24px;
      font-weight: bold;
      margin-right: 15px;
      overflow: hidden;
      position: relative;
    }
    
    .admin-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    
    .admin-avatar .avatar-fallback {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      height: 100%;
    }
    
    .admin-info h3 {
      font-size: 1.2rem;
      margin-bottom: 5px;
      color: #00d9ff;
    }
    
    body.light .admin-info h3 {
      color: #2c3e50;
    }
    
    .admin-info p {
      font-size: 0.9rem;
      color: #aaa;
    }
    
    body.light .admin-info p {
      color: #666;
    }
    
    .admin-details {
      margin-bottom: 15px;
    }
    
    .detail-item {
      display: flex;
      margin-bottom: 8px;
      font-size: 0.9rem;
    }
    
    .detail-label {
      font-weight: 500;
      min-width: 80px;
      color: #00d9ff;
    }
    
    body.light .detail-label {
      color: #2c3e50;
    }
    
    .detail-value {
      color: #ddd;
    }
    
    body.light .detail-value {
      color: #666;
    }
    
    .admin-status {
      display: inline-block;
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 500;
      margin-top: -3px;
      margin-left: -10px;
    }
    
    .status-active {
      color: #00c853;
    }
    
    .status-inactive {
      color: #ff5252;
    }
    
    .admin-actions {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 15px;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      padding-top: 15px;
    }
    
    body.light .admin-actions {
      border-top: 1px solid #eee;
    }
    
    .btn-action {
      padding: 8px 12px;
      border-radius: 6px;
      font-size: 0.85rem;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 5px;
      transition: all 0.3s;
      text-decoration: none;
    }
    
    .btn-delete {
      background: rgba(255, 82, 82, 0.2);
      color: #ff5252;
      border: 1px solid rgba(255, 82, 82, 0.3);
    }
    
    .btn-delete:hover {
      background: rgba(255, 82, 82, 0.3);
    }
    
    .btn-edit {
      background: rgba(0, 217, 255, 0.2);
      color: #00d9ff;
      border: 1px solid rgba(0, 217, 255, 0.3);
    }
    
    .btn-edit:hover {
      background: rgba(0, 217, 255, 0.3);
    }

    /* Add Admin Button */
    .btn-add {
      background: #00d9ff;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 500;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: background 0.3s;
      text-decoration: none;
      margin-bottom: 20px;
    }
    
    .btn-add:hover {
      background: #00aacc;
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

    /* Toggle Switch Styles */
    .toggle-switch {
      position: relative;
      display: inline-block;
      width: 50px;
      height: 24px;
    }
    
    .toggle-switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }
    
    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      transition: .4s;
      border-radius: 24px;
    }
    
    .slider:before {
      position: absolute;
      content: "";
      height: 16px;
      width: 16px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      transition: .4s;
      border-radius: 50%;
    }
    
    input:checked + .slider {
      background-color: #00c853;
    }
    
    input:checked + .slider:before {
      transform: translateX(26px);
    }
    
    .toggle-label {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 0.9rem;
    }
    
    .status-text {
      font-weight: 500;
    }
    
    .status-active {
      color: #00c853;
    }
    
    .status-inactive {
      color: #ff5252;
    }

    /* Animations */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Responsive */
    @media (max-width: 768px) {
      .admin-cards {
        grid-template-columns: 1fr;
      }
      
      .admin-actions {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
      }
      
      .toggle-label {
        margin-bottom: 10px;
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
      <li><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
      <li><a href="../admin/tadmin.php"><i class="fas fa-user-cog"></i> Admin</a></li>
      <li><a href="../tamu/tamu.php"><i class="fas fa-users"></i> Tamu</a></li>
      <li><a href="../tamu/kehadiran.php"><i class="fas fa-user-check"></i> Kehadiran</a></li>
      <li><a href="../laporan/laporan.php"><i class="fas fa-file-alt"></i> Laporan</a></li>
      <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </div>
  
  <!-- Content -->
  <div class="content">
    <div class="card">
      <h1 align="center">Data Admin</h1>
      <h2 align="center">SMKN 71 JAKARTA</h2>
    </div>
    
    <a href="add_admin.php" class="btn-add"><i class="fas fa-plus"></i> Tambah Admin</a>
    
    <div class="admin-cards">
      <?php
        $sql = "SELECT id_admin, email, username, level, foto FROM admin";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
            $initial = strtoupper(substr($row['username'], 0, 1));
            $statusClass = ($row['level'] == 'Aktif') ? 'status-active' : 'status-inactive';
            $isChecked = ($row['level'] == 'Aktif') ? 'checked' : '';
            $statusText = ($row['level'] == 'Aktif') ? 'Aktif' : 'Non Aktif';

            // Path foto fix sesuai container
            $fotoPath = "uploads/profiles/" . $row['foto'];

            echo '<div class="admin-card">';
            echo '  <div class="admin-header">';
            echo '    <div class="admin-avatar">';

            // Cek file foto beneran ada atau nggak
            if (!empty($row['foto']) && file_exists($fotoPath)) {
                echo '<img src="' . htmlspecialchars($fotoPath) . '" alt="' . htmlspecialchars($row['username']) . '" onerror="this.onerror=null; this.style.display=\'none\'; this.nextElementSibling.style.display=\'flex\';">';
                echo '<div class="avatar-fallback" style="display: none;">' . $initial . '</div>';
            } else {
                echo '<div class="avatar-fallback">' . $initial . '</div>';
            }

            echo '    </div>';
            echo '    <div class="admin-info">';
            echo '      <h3>' . htmlspecialchars($row['username']) . '</h3>';
            echo '      <p>ID: ' . htmlspecialchars($row['id_admin']) . '</p>';
            echo '    </div>';
            echo '  </div>';

            echo '  <div class="admin-details">';
            echo '    <div class="detail-item">';
            echo '      <span class="detail-label">Email:</span>';
            echo '      <span class="detail-value">' . htmlspecialchars($row['email']) . '</span>';
            echo '    </div>';
            echo '    <div class="detail-item">';
            echo '      <span class="detail-label">Status:</span>';
            echo '      <span class="admin-status ' . $statusClass . '">' . htmlspecialchars($row['level']) . '</span>';
            echo '    </div>';
            echo '  </div>';

            echo '  <div class="admin-actions">';
            echo '    <div class="toggle-label">';
            echo '      <span class="status-text ' . $statusClass . '">' . $statusText . '</span>';
            echo '      <label class="toggle-switch">';
            echo '        <input type="checkbox" ' . $isChecked . ' onchange="toggleAdminStatus(this, ' . $row['id_admin'] . ')">';
            echo '        <span class="slider"></span>';
            echo '      </label>';
            echo '    </div>';
            echo '    <a href="edit_admin.php?id=' . urlencode($row['id_admin']) . '" class="btn-action btn-edit"><i class="fas fa-edit"></i> Edit</a>';
            echo '  </div>';

            echo '</div>';
          }
        } else {
          echo '<div class="empty-state">';
          echo '  <i class="fas fa-user-slash"></i>';
          echo '  <p>Belum ada data admin</p>';
          echo '</div>';
        }

        $conn->close();
      ?>
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

    function toggleAdminStatus(checkbox, adminId) {
      const statusElement = checkbox.closest('.admin-card').querySelector('.status-text');
      const statusBadge = checkbox.closest('.admin-card').querySelector('.admin-status');
      let newStatus = checkbox.checked ? 'Aktif' : 'Non Aktif';

      let formData = new FormData();
      formData.append('toggle_status', true);
      formData.append('id_admin', adminId);
      formData.append('new_status', newStatus);

      fetch('<?php echo $_SERVER['PHP_SELF']; ?>', {
        method: 'POST',
        body: formData
      })
      .then(response => response.text())
      .then(data => {
        if (data === 'success') {
          statusElement.textContent = newStatus;
          statusElement.className = 'status-text ' + (newStatus === 'Aktif' ? 'status-active' : 'status-inactive');
          statusBadge.textContent = newStatus;
          statusBadge.className = 'admin-status ' + (newStatus === 'Aktif' ? 'status-active-badge' : 'status-inactive-badge');

          if (newStatus === 'Non Aktif') {
            setTimeout(() => {
              const deleteData = new FormData();
              deleteData.append('delete_admin', true);
              deleteData.append('id_admin', adminId);

              fetch('<?php echo $_SERVER['PHP_SELF']; ?>', {
                method: 'POST',
                body: deleteData
              })
              .then(r => r.text())
              .then(res => {
                if (res === 'deleted') {
                  const card = checkbox.closest('.admin-card');
                  card.remove();
                  alert('Akun admin berhasil dihapus otomatis');
                } else if (res === 'self_delete_blocked') {
                  alert('❌ Tidak bisa menghapus akun diri sendiri');
                  checkbox.checked = true;
                  statusElement.textContent = 'Aktif';
                  statusElement.className = 'status-text status-active';
                  statusBadge.textContent = 'Aktif';
                  statusBadge.className = 'admin-status status-active-badge';
                }
              });
            }, 10000); // 10 detik
          }

        } else {
          alert('Gagal mengubah status');
          checkbox.checked = !checkbox.checked;
        }
      });
    }

    document.addEventListener("DOMContentLoaded", () => {
      let savedTheme = localStorage.getItem("theme") || "dark";
      document.body.classList.add(savedTheme);

      // Handle error gambar
      document.querySelectorAll('.admin-avatar img').forEach(img => {
        img.onerror = function() {
          this.style.display = 'none';
          const fallback = this.nextElementSibling;
          if (fallback && fallback.classList.contains('avatar-fallback')) {
            fallback.style.display = 'flex';
          }
        };
      });
    });
  </script>
</body>
</html>