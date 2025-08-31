<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header('Location:../admin/login.php');
    }
    $username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="id">
<meta charset="UTF-8">
<title>Dashboard Tamu</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
body {
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', sans-serif;
    background: white;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}
.header {
    padding: 20px;
    text-align: center;
}
.content {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    text-align: center;
    padding: 20px;
}
.content h2 {
    color: #2c3e50;
    margin-bottom: 10px;
}
.content p {
    font-size: 18px;
    color: #34495e;
}
.btn-isi {
    padding: 12px 20px;
    font-size: 16px;
    text-decoration: none;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: background 0.3s ease;
}
.btn-isi:hover {
    background-color: #16a085;
}
.footer {
    background: #2c3e50;
    color: white;
    text-align: center;
    padding: 10px;
    font-size: 14px;
}
</style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-building"></i> Buku Tamu Digital</h1>
        <img src="Logo 71.png" width="300px" height="300px">
    </div>
    <div class="content">
        <h2>Selamat Datang di SMKN 71 Jakarta</h2>
        <p>Silakan isi form buku tamu di bawah ini untuk melakukan kunjungan.</p>
        <a href="form_tamu.php" class="btn-isi"><i class="fas fa-pen"></i> Isi Buku Tamu</a>
    </div>
    <div class="footer">
        &copy; <?= date('Y') ?> SMKN 71 Jakarta – Sistem Buku Tamu Digital
    </div>
</body>
</html>