<?php
session_start();
include '../koneksi.php';

// Cek login
if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit;
}

// Ambil data admin login
$id_admin = $_SESSION['id_admin'];
$query = $conn->query("SELECT username, foto FROM admin WHERE id_admin = '$id_admin'");
$data_admin = $query->fetch_assoc();
$nama_admin = $data_admin['username'];
$foto_admin = !empty($data_admin['foto']) ? $data_admin['foto'] : "default.png";

// Hitung total
$total_admin    = $conn->query("SELECT COUNT(*) AS jml FROM admin")->fetch_assoc()['jml'];
$total_kategori = $conn->query("SELECT COUNT(*) AS jml FROM kategori")->fetch_assoc()['jml'];

// =====================
// 🔍 Fitur Search Admin
// =====================
$data_table_admin = [];
$search = "";

if (!empty($_GET['search'])) {
    $search = trim($_GET['search']);
    $like = "%" . $search . "%";
    $stmt = $conn->prepare("SELECT id_admin, email, username, level FROM admin 
                            WHERE username LIKE ? OR email LIKE ?
                            ORDER BY id_admin ASC");
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT id_admin, email, username, level FROM admin ORDER BY id_admin ASC");
}

while ($row = $result->fetch_assoc()) {
    $data_table_admin[] = $row;
}

// Handle toggle status
if (isset($_POST['toggle_status'])) {
    $id_admin_toggle = $_POST['id_admin'];
    $new_status = $_POST['new_status'];
    
    if ($id_admin_toggle == $id_admin) {
        $_SESSION['error'] = "Tidak dapat menonaktifkan akun sendiri!";
    } else {
        $conn->query("UPDATE admin SET level = '$new_status' WHERE id_admin = '$id_admin_toggle'");
        if ($new_status == 'off') {
            $_SESSION['delete_timer'][$id_admin_toggle] = time() + 10;
        } else {
            unset($_SESSION['delete_timer'][$id_admin_toggle]);
        }
        $_SESSION['success'] = "Status admin berhasil diubah!";
    }
    header("Location: tadmin.php");
    exit;
}

// Proses penghapusan admin yang nonaktif >10 detik
if (isset($_SESSION['delete_timer'])) {
    $current_time = time();
    foreach ($_SESSION['delete_timer'] as $id_to_delete => $delete_time) {
        if ($current_time >= $delete_time) {
            $cek_admin = $conn->query("SELECT id_admin FROM admin WHERE id_admin = '$id_to_delete' AND id_admin != '$id_admin'");
            if ($cek_admin->num_rows > 0) {
                $conn->query("DELETE FROM admin WHERE id_admin = '$id_to_delete'");
            }
            unset($_SESSION['delete_timer'][$id_to_delete]);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Table Admin - Budaya Sunda</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
      left: -250px;
      width: 250px;
      height: 100vh;
      background: #2b6cb0;
      color: white;
      padding: 20px 15px;
      transition: left 0.3s ease;
      z-index: 1000;
    }  

    .sidebar.active {
      left: 0;
    }

    .sidebar .nav-link {
      color: white;
      margin: 8px 0;
      border-radius: 8px;
      transition: 0.3s;
      display: block;
    }

    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
      background: #1a4f8b;
      font-weight: bold;
    }

    .profile-img {
      width: 90px;
      height: 90px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #fff;
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
      margin-left: 30px;
      padding: 30px;
      transition: margin-left 0.3s ease;
    }

    .content.shift {
      margin-left: 250px;
    }

    .card {
      background-color: #c2cdd9ff;
      border-radius: 12px;
      border: 0;
      padding: 20px;
      margin-bottom: 30px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
      backdrop-filter: blur(8px);
      animation: fadeIn 0.5s ease;
      transition: background 0.3s, color 0.3s;
    }

    .logout {
      margin-top: 128px;
    }

    /* Search Bar */
    .search-bar .input-group {
      max-width: 400px;
    }
    .search-bar .input-group-text {
      background: #1a4f8b;
      color: white;
      border: none;
      border-radius: 8px 0 0 8px;
    }
    .search-bar .form-control {
      border: none;
      box-shadow: none;
    }
    .search-bar .btn {
      background: #1a4f8b;
      color: white;
      border-radius: 0 8px 8px 0;
      transition: background 0.3s;
    }
    .search-bar .btn:hover {
      background: #14407c;
    }

    .table-container {
      background-color: white;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .table thead.custom-thead th {
      background-color: #1a4f8b;
      color: #fff;
    }

    .timer-badge {
      background-color: #dc3545;
      color: white;
      padding: 2px 8px;
      border-radius: 10px;
      font-size: 0.8rem;
    }
  </style>
</head>
<body>
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
        <a class="nav-link active" href="tadmin.php"><i class="bi bi-person"></i> Admin</a>
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
    <?php if (isset($_SESSION['success'])): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['success'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $_SESSION['error'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="table-container">
      <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="fw-bold">Data Admin</h3>
        <a href="add_admin.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tambah Admin</a>
      </div>

      <!-- 🔍 Search Bar -->
      <form method="GET" class="search-bar d-flex align-items-center mb-3">
        <div class="input-group shadow-sm">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input type="text" name="search" class="form-control" placeholder="Cari admin berdasarkan nama atau email..." value="<?= htmlspecialchars($search, ENT_QUOTES); ?>">
          <button class="btn" type="submit">Cari</button>
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead class="custom-thead">
            <tr>
              <th>ID</th>
              <th>Email</th>
              <th>Username</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($data_table_admin) > 0): ?>
              <?php foreach ($data_table_admin as $admin): ?>
                <tr>
                  <td><?= $admin['id_admin'] ?></td>
                  <td><?= htmlspecialchars($admin['email']) ?></td>
                  <td><?= htmlspecialchars($admin['username']) ?></td>
                  <td>
                    <?php if ($admin['id_admin'] == $id_admin): ?>
                      <span class="badge bg-success">ON</span>
                    <?php else: ?>
                      <?php if ($admin['level'] == 'on'): ?>
                        <span class="badge bg-success">ON</span>
                      <?php else: ?>
                        <span class="badge bg-danger">OFF</span>
                        <?php 
                        if (isset($_SESSION['delete_timer'][$admin['id_admin']])) {
                          $time_left = $_SESSION['delete_timer'][$admin['id_admin']] - time();
                          if ($time_left > 0) {
                            echo '<span class="timer-badge ms-2">Hapus: ' . $time_left . 's</span>';
                          }
                        }
                        ?>
                      <?php endif; ?>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if ($admin['id_admin'] != $id_admin): ?>
                      <form method="post" class="d-inline">
                        <input type="hidden" name="id_admin" value="<?= $admin['id_admin'] ?>">
                        <input type="hidden" name="toggle_status" value="1">
                        <input type="hidden" name="new_status" value="off">
                        <label class="switch">
                          <input type="checkbox" name="new_status" value="on" <?= $admin['level'] == 'on' ? 'checked' : '' ?> onchange="this.form.submit();">
                          <span class="slider"></span>
                        </label>
                      </form>
                    <?php else: ?>
                      <span class="text-muted">Akun sendiri</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="5" class="text-center">Tidak ada data admin ditemukan.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script>
    function toggleSidebar() {
      document.getElementById("sidebar").classList.toggle("active");
      document.getElementById("content").classList.toggle("shift");
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
