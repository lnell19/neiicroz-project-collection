<?php
include '../koneksi.php'; // Pastikan path ini benar

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $instansi = $_POST['instansi'];
    $keperluan = $_POST['keperluan'];
    date_default_timezone_set('Asia/Jakarta'); 
    $tanggal = date('Y-m-d H:i:s');

    if (!empty($nama) && !empty($instansi) && !empty($keperluan)) {
        $stmt = $conn->prepare("INSERT INTO tamu (nama, instansi, keperluan, tanggal) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nama, $instansi, $keperluan, $tanggal);

            if ($stmt->execute()) {
                $success = "Data tamu berhasil disimpan.";
            } else {
                $error = "Gagal menyimpan data tamu.";
            }
            $stmt->close();
    } else {
        $error = "Semua field wajib diisi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Form Buku Tamu</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
body {
  background-color: #ecf0f1;
  font-family: "Segoe UI", sans-serif;
  padding: 20px;
}

.form-container {
  max-width: 600px;
  margin: 0 auto;
  background: white;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

h2 {
  text-align: center;
  color: #2c3e50;
  margin-bottom: 20px;
}

.form-group {
  margin-bottom: 15px;
}

label {
  display: block;
  margin-bottom: 5px;
  color: #34495e;
}

input[type="text"],
textarea {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 6px;
}

textarea {
  resize: vertical;
  height: 90px;
}

.message {
  margin-bottom: 15px;
  padding: 10px;
  border-radius: 6px;
}

.success {
  background-color: #dff0d8;
  color: #2f8f36;
}

.error {
  background-color: #f2dede;
  color: #a94442;
}

.button-group {
  display: flex;
  justify-content: space-between;
  margin-top: 20px;
}

.btn-kembali, .btn-kirim {
  padding: 10px 20px;
  font-size: 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: all 0.3s ease;
}

.btn-kembali {
  background: #2c3e50;
  color: white;
}

.btn-kembali:hover {
  background: #1a252f;
}

.btn-kirim {
  background: #1abc9c;
  color: white;
}

.btn-kirim:hover {
  background: #16a085;
}
</style>
</head>
<body>

<div class="form-container">
  <h2><i class="fas fa-edit"></i> Form Buku Tamu</h2>

  <?php if ($success): ?>
    <div class="message success"><?= $success ?></div>
  <?php elseif ($error): ?>
    <div class="message error"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    <div class="form-group">
      <label for="nama">Nama Lengkap:</label>
      <input type="text" id="nama" name="nama" required>
    </div>

    <div class="form-group">
      <label for="instansi">Instansi / Asal:</label>
      <input type="text" id="instansi" name="instansi" required >
    </div>

    <div class="form-group">
      <label for="keperluan">Keperluan:</label>
      <textarea id="keperluan" name="keperluan" required></textarea>
    </div>

    <div class="button-group">
      <a href="dashboard_tamu.php" class="btn-kembali"><i class="fas fa-arrow-left"></i> Kembali</a>
      <button type="button" class="btn-kirim" onclick="konfirmasiData()">
        kirim <i class="fas fa-paper-plane"></i>
      </button>
    </div>
  </form>
</div>

<script>
  function konfirmasiData() {
    const nama = document.getElementById('nama').value.trim();
    const instansi = document.getElementById('instansi').value.trim();
    const keperluan = document.getElementById('keperluan').value.trim();

    if (nama === "" || instansi === ""|| keperluan === "") {
    Swal.fire({
        icon: 'warning',
        title: 'lengkapi form!',
        text: 'semua field wajib diisi'
    });
      return;
    }
    Swal.fire({
        title: 'Konfirmasi Data',
        html: `
        <strong>Nama:</strong> ${nama}<br>
        <strong>Instansi:</strong> ${instansi}<br>
        <strong>Keperluan:</strong> ${keperluan}<br><br>
        Pastikan data sudah benar `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sudah Benar & Kirim',
        cancelButtonText: 'Edit Kembali'    
    }).then((result) => {
        if (result.isConfirmed) {
            document.querySelector("form").submit();
        }
    });

  }
</script>

</body>
</html>