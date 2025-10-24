<?php
include '../koneksi.php';

$success = '';
$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$email = trim($_POST['email'] ?? '');


if (empty($email)) {
$error = "Email harus diisi!";
} else {
$stmt = $conn->prepare("SELECT username FROM admin WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();


if ($stmt->num_rows > 0) {
$stmt->bind_result($username);
$stmt->fetch();


// Generate password sementara (plaintext untuk ditampilkan ke user)
$temp_password = substr(md5(uniqid(rand(), true)), 0, 8);


// Hash password sementara sebelum disimpan
$hashed_temp_password = password_hash($temp_password, PASSWORD_DEFAULT);


// Update password di database
$update_stmt = $conn->prepare("UPDATE admin SET password = ? WHERE email = ?");
$update_stmt->bind_param('ss', $hashed_temp_password, $email);
$update_stmt->execute();
$update_stmt->close();


// Karena ini sistem dummy (email "boongan"), langsung tampilkan password sementara ke layar
$success = "Gunakan password sementara ini: <strong>" . htmlspecialchars($temp_password) . "</strong>. Silakan login dan segera ganti password Anda.";
} else {
$error = "Email tidak ditemukan!";
}
$stmt->close();
}
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: url('batik_cirebon_megamendung_Nero_AI_Image_Upscaler_Photo_Face.jpeg.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .login-box {
            width: 320px;
            background: #c2c2c2;
            padding: 30px 40px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 6px 15px rgba(0,0,0,0.3);
        }
        .login-title {
            font-size: 32px;
            color: white;
            margin-bottom: 20px;
            font-weight: bold;
            text-shadow: 0 0 8px black;
        }
        .icon {
            width: 80px;
            height: 80px;
            background: black;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        input[type="email"] {
            width: 100%;
            padding: 10px 8px;
            margin: 8px 0 15px;
            border: 1px solid #999;
            border-radius: 4px;
            font-size: 14px;
        }
        label {
            font-weight: bold;
            font-size: 14px;
            display: block;
            text-align: left;
            margin-top: 10px;
            color: #333;
        }
        button {
            width: 100%;
            background-color: #2ecc40;
            color: white;
            border: none;
            padding: 10px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #27ae38;
        }
        .back-link {
            margin-top: 20px;
            font-size: 14px;
        }
        .back-link a {
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
        .alert {
            padding: 12px;
            margin: 15px 0;
            border-radius: 4px;
            font-size: 14px;
            text-align: center;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .music-controls {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        .music-controls button {
            background: #3498db;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
            font-size: 14px;
        }
        .music-controls button:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>
    <div>
        <div class="login-title">Lupa Password</div>
        <div class="login-box">
            <div class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="white" width="50" height="50" viewBox="0 0 24 24">
                    <path d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z"/>
                </svg>
            </div>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php elseif ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="POST">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required placeholder="Masukkan email Anda" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                <button type="submit">Kirim Reset Password</button>
            </form>
            <div class="back-link">
                <a href="login.php">← Kembali ke Halaman Login</a>
            </div>
        </div>
        <div class="music-controls">
            <button onclick="playMusic()">▶ Play</button>
            <button onclick="pauseMusic()">⏸ Pause</button>
        </div>
    </div>
    <audio id="bgm" loop>
        <source src="[SABILULUNGAN] SUNDANESE INSTRUMENTALIA _ DEGUNG SUNDA _ INDONESIAN TRADITIONAL MUSIC [PeJ-vJNw5JA].mp3" type="audio/mpeg">
    </audio>
    <script>
        const bgm = document.getElementById("bgm");
        function playMusic() { bgm.play().catch(e => console.log("Play failed:", e)); }
        function pauseMusic() { bgm.pause(); }
    </script>
</body>
</html>
