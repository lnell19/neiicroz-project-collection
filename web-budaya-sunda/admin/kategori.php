<?php
session_start();
include '../koneksi.php';

// Cek login
if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit;
}

// Ambil data admin dengan prepared statement
$id_admin = (int) ($_SESSION['id_admin'] ?? 0);
$foto_admin = 'default.png';
if ($id_admin > 0) {
    $stmt = $conn->prepare("SELECT username, foto FROM admin WHERE id_admin = ?");
    if ($stmt) {
        $stmt->bind_param('i', $id_admin);
        $stmt->execute();
        $res = $stmt->get_result();
        $data_admin = $res->fetch_assoc();
        if (!empty($data_admin['foto'])) $foto_admin = $data_admin['foto'];
        $stmt->close();
    }
}

// Hapus kategori (prepared)
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM kategori WHERE id_kategori = ?");
        if ($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
        }
    }
    header("Location: kategori.php");
    exit;
}

// Update kategori (prepared)
if (isset($_POST['edit'])) {
    $id = intval($_POST['id'] ?? 0);
    $nama_baru = trim($_POST['nama_kategori'] ?? '');
    if ($id > 0 && $nama_baru !== '') {
        $stmt = $conn->prepare("UPDATE kategori SET nama_kategori = ? WHERE id_kategori = ?");
        if ($stmt) {
            $stmt->bind_param('si', $nama_baru, $id);
            $stmt->execute();
            $stmt->close();
        }
        header("Location: kategori.php");
        exit;
    }
}

// Ambil data kategori (prepared for search)
$search = "";
if (!empty($_GET['search'])) {
    $search = $_GET['search'];
    $like = '%' . $search . '%';
    $stmt = $conn->prepare("SELECT * FROM kategori WHERE nama_kategori LIKE ? ORDER BY id_kategori ASC");
    if ($stmt) {
        $stmt->bind_param('s', $like);
        $stmt->execute();
        $kategori = $stmt->get_result();
        // don't close here; we'll iterate and then close
    } else {
        // fallback
        $kategori = $conn->query("SELECT * FROM kategori WHERE nama_kategori LIKE '$like' ORDER BY id_kategori ASC");
    }
} else {
    $kategori = $conn->query("SELECT * FROM kategori ORDER BY id_kategori ASC");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }
        body {
            background-color: #e8eef4;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .header {
            background: #2b64f1;
            color: white;
            padding: 15px 25px;
            font-size: 22px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .header i {
            cursor: pointer;
        }
        .content {
            flex: 1;
            padding: 30px 50px;
        }
        .profile {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
        }
        .profile img {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            background: white;
            object-fit: cover;
        }
        .search-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        .search-bar input {
            flex: 1;
            padding: 10px 15px;
            border-radius: 25px;
            border: 2px solid #aaa;
            outline: none;
        }
        .search-bar button {
            background: #555;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }
        th {
            background: #3366ff;
            color: white;
            padding: 12px;
            font-size: 16px;
        }
        td {
            text-align: center;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            font-size: 15px;
        }
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            color: white;
            cursor: pointer;
        }
        .btn-akses { background-color: #3ec5f3; color:#fff; text-decoration:none; display:inline-block; padding:6px 12px; border-radius:6px; }
        .btn-edit { background-color: #27ae60; }
        .btn-hapus { background-color: #e74c3c; }
        .btn:hover { opacity: 0.85; }
        .edit-form {
            display: none;
            background: #f7f7f7;
        }
        .edit-form input {
            padding: 6px;
            border-radius: 5px;
            border: 1px solid #aaa;
        }
        .btn-simpan { background-color: #27ae60; color:white; border:none; padding:6px 12px; border-radius:6px; }
        .btn-batal { background-color: #999; color:white; border:none; padding:6px 12px; border-radius:6px; }
        a { color: white; text-decoration: none; }
        .headkate { flex: 1; text-align: center; font-size: 24px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <a href="dashboard.php" style="color:white; text-decoration:none;">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <div class="headkate">Kategori</div>
    </div>

    <div class="content">
        <div class="profile" style="margin-bottom:20px;">
            <img src="uploads/profiles/<?php echo htmlspecialchars($foto_admin, ENT_QUOTES); ?>" alt="Foto Admin">
        </div>

        <!-- Tombol menuju tambah kategori -->
        <div style="margin-bottom: 20px;">
            <a href="add_kategori.php" class="btn-akses">+ Tambah Kategori</a>
        </div>

        <!-- Pencarian -->
        <form method="GET" class="search-bar">
            <input type="text" name="search" placeholder="Cari kategori..." value="<?php echo htmlspecialchars($search, ENT_QUOTES); ?>">
            <button type="submit">Tampilkan</button>
        </form>

        <!-- Tabel kategori -->
        <table>
            <tr>
                <th>NO</th>
                <th>Nama Kategori</th>
                <th>Aksi</th>
            </tr>
            <?php
            $no = 1;
            if ($kategori && $kategori->num_rows > 0) {
                while ($row = $kategori->fetch_assoc()) {
                    $id = (int) $row['id_kategori'];
                    $nama = htmlspecialchars($row['nama_kategori'], ENT_QUOTES);
                    $table_name = "tb_" . preg_replace('/[^a-zA-Z0-9_]/', '_', strtolower($row['nama_kategori']));
                    echo "<tr>
                            <td>{$no}</td>
                            <td id='nama-{$id}'>{$nama}</td>
                            <td>
                                <a href='{$table_name}.php' class='btn-akses'>Akses</a>
                                <a href='edit_kategori.php?id={$id}' class='btn btn-edit' style='display:inline-block;padding:6px 12px;border-radius:6px;text-decoration:none;color:#fff'>Edit</a>
                                <button class='btn btn-hapus' onclick='konfirmasiHapus({$id})'>Hapus</button>
                            </td>
                          </tr>";
                    $no++;
                }
            } else {
                echo "<tr><td colspan='3'>Tidak ada data kategori</td></tr>";
            }
            ?>
        </table>
    </div>

    <script>
        function konfirmasiHapus(id) {
            if (confirm("Yakin ingin menghapus kategori ini?")) {
                window.location.href = "kategori.php?hapus=" + id;
            }
        }

        function toggleEdit(id) {
            var el = document.getElementById('form-edit-' + id);
            if (!el) return;
            el.style.display = (el.style.display === 'table-row' ? 'none' : 'table-row');
        }

        function batalEdit(id) {
            var el = document.getElementById('form-edit-' + id);
            if (el) el.style.display = 'none';
        }

        function validateEdit(form) {
            var v = form.nama_kategori.value.trim();
            if (!v) { alert('Nama kategori tidak boleh kosong'); return false; }
            return true;
        }
    </script>
</body>
</html>
