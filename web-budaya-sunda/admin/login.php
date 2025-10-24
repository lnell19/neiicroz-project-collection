<?php
session_start();
include "../koneksi.php";

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Ambil data user
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($data = $result->fetch_assoc()) {
        $db_password = $data['password'];

        // Cek apakah password di database sudah di-hash
        if (password_get_info($db_password)['algo'] !== 0) {
            // Password sudah di-hash
            $valid = password_verify($password, $db_password);
        } else {
            // Password masih plain text
            $valid = ($password === $db_password);
        }

        if ($valid) {
            // Login berhasil
            $_SESSION['id_admin'] = $data['id_admin'];
            $_SESSION['username'] = $data['username'];
            
            $id_admin = $data['id_admin'];

            // Update status dan tanggal login terakhir
            $now = date("Y-m-d H:i:s");
            $update = $conn->prepare("UPDATE admin SET level = 'on', created_at = ? WHERE id_admin = ?");
            $update->bind_param("si", $now, $id_admin);
            $update->execute();

            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Password salah. Silakan coba lagi.";
        }
    } else {
        $error = "Username tidak ditemukan.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        /* Background pattern */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-image: url(batik_cirebon_megamendung_Nero_AI_Image_Upscaler_Photo_Face.jpeg.jpg);
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-position: center center;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Container form */
        .login-box {
            width: 320px;
            background: #c2c2c2;
            padding: 30px 40px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
            z-index: 10;
        }

        /* Title "Login" */
        .login-title {
            font-size: 36px;
            color: white;
            margin-bottom: 20px;
            font-weight: normal;
            text-shadow: 0 0 8px black;
            text-align: center;
        }

        /* Icon */
        .icon {
            width: 80px;
            height: 80px;
            background: black;
            border-radius: 50%;
            margin: 0 auto 20px auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon svg {
            width: 50px;
            height: 50px;
        }

        /* Lupa password */
        .forgot {
            color: red;
            font-size: 12px;
            margin: 15px 0;
        }

        .forgot a {
            color: red;
            text-decoration: none;
            font-weight: bold;
        }

        .forgot a:hover {
            text-decoration: underline;
        }

        /* Input */
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px 8px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #999;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }

        label {
            font-weight: bold;
            font-size: 14px;
            display: block;
            margin-top: 10px;
            text-align: left;
            color: #333;
        }

        /* Button */
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
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #27ae38;
        }

        /* Music controls container */
        .music-controls {
            margin-top: 40px;
            display: flex;
            gap: 15px;
            justify-content: center;
            z-index: 10;
        }

        .music-controls button {
            width: auto;
            padding: 10px 20px;
            font-size: 14px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .music-controls button:hover {
            background-color: #2980b9;
        }

        /* Responsive adjustment */
        @media (max-width: 360px) {
            .login-box {
                width: 90%;
                padding: 25px;
            }
            .login-title {
                font-size: 30px;
            }
        }
    </style>
</head>
<body>

    <!-- Login Box -->
    <div class="login-title">Login</div>
    <div class="login-box">
        <div class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="white" height="50" viewBox="0 0 24 24" width="50">
                <path d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z"/>
            </svg>
        </div>

        <div class="forgot"><a href="lupapw.php">Lupa sandi Klik Disini</a></div>

        <!-- Tampilkan pesan error jika ada -->
        <?php if (!empty($error)) : ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>

            <button type="submit">Login</button>
        </form>
    </div>

    <!-- Music Controls (Below Login Box) -->
    <div class="music-controls">
        <button onclick="playMusic()">▶ Play</button>
        <button onclick="pauseMusic()">⏸ Pause</button>
    </div>

    <!-- Audio Element -->
    <audio id="bgm" loop>
        <source src="audio/[SABILULUNGAN] SUNDANESE INSTRUMENTALIA _ DEGUNG SUNDA _ INDONESIAN TRADITIONAL MUSIC [PeJ-vJNw5JA].mp3" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>

    <script>
        let bgm = document.getElementById("bgm");

        function playMusic() {
            bgm.play().catch(e => console.log("Play failed:", e));
        }

        function pauseMusic() {
            bgm.pause();
        }
    </script>

</body>
</html>