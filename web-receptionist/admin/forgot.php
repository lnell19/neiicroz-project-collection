<?php
include '../koneksi.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $error = "Email harus diisi!";
    } else {
        $stmt = $conn->prepare("SELECT username, password FROM admin WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($username, $hashed_password);
            $stmt->fetch();

            // Generate password sementara (hanya untuk demo)
            // Dalam implementasi nyata, Anda harus membuat sistem reset password yang aman
            $temp_password = substr(md5(uniqid(rand(), true)), 0, 8);
            
            // Hash password sementara sebelum menyimpan ke database
            $hashed_temp_password = password_hash($temp_password, PASSWORD_DEFAULT);
            
            // Update password di database dengan yang baru
            $update_stmt = $conn->prepare("UPDATE admin SET password = ? WHERE email = ?");
            $update_stmt->bind_param('ss', $hashed_temp_password, $email);
            $update_stmt->execute();
            $update_stmt->close();

            // Kirim email (gunakan mail() bawaan PHP)
            $to = $email;
            $subject = "Reset Password Admin";
            $message = "Halo $username,\n\nPassword sementara Anda adalah: $temp_password\n\nSilakan login menggunakan password ini dan ubah password Anda setelah login.\n\nHormat kami,\nTim Admin";
            $headers = "From: noreply@" . $_SERVER['HTTP_HOST'] . "\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

            if (mail($to, $subject, $message, $headers)) {
                $success = "Password sementara berhasil dikirim ke email Anda.";
            } else {
                // Fallback: Tampilkan password sementara di halaman
                $success = "Sistem email sedang mengalami masalah. Silakan gunakan password sementara ini: <strong>$temp_password</strong>";
                
                // Catat error untuk debugging
                error_log("Gagal mengirim email ke: $email", 0);
            }
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            color: white;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: background 0.3s, color 0.3s;
        }

        body.light {
            background: #f4f4f4;
            color: #333;
        }

        .forgot-container {
            background: rgba(255, 255, 255, 0.05);
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(8px);
            width: 100%;
            max-width: 450px;
            animation: fadeIn 0.5s ease;
        }

        body.light .forgot-container {
            background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .forgot-container h2 {
            text-align: center;
            margin-bottom: 1rem;
            color: #00d9ff;
            font-size: 1.8rem;
        }

        body.light .forgot-container h2 {
            color: #333;
        }

        .forgot-icon {
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 3rem;
            color: #00d9ff;
        }

        .forgot-description {
            text-align: left;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            opacity: 0.8;
            line-height: 1.5;
        }

        .input-group {
            margin-bottom: 1.5rem;
        }

        .input-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .input-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 6px;
            background: rgba(255,255,255,0.1);
            color: white;
            font-size: 1rem;
            transition: all 0.3s;
        }

        body.light .input-group input {
            border: 1px solid #ddd;
            background: white;
            color: #333;
        }

        .input-group input:focus {
            outline: none;
            border-color: #00d9ff;
            box-shadow: 0 0 0 3px rgba(0,217,255,0.2);
        }

        button[type="submit"] {
            width: 100%;
            padding: 0.75rem;
            background: #00d9ff;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s;
            margin-bottom: 1rem;
        }

        button[type="submit"]:hover {
            background: #00aacc;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            color: #00d9ff;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: #00aacc;
            text-decoration: underline;
        }

        body.light .back-link {
            color: #007bff;
        }

        body.light .back-link:hover {
            color: #0056b3;
        }

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

        .alert {
            padding: 0.75rem;
            margin-bottom: 1rem;
            border-radius: 6px;
            text-align: center;
        }

        .alert-success {
            background: rgba(0, 200, 83, 0.2);
            border: 1px solid rgba(0, 200, 83, 0.3);
            color: #00c853;
        }

        .alert-danger {
            background: rgba(255, 82, 82, 0.2);
            border: 1px solid rgba(255, 82, 82, 0.3);
            color: #ff5252;
        }

        .admin-contact {
            margin-top: 1.5rem;
            padding: 1rem;
            background: rgba(0, 217, 255, 0.1);
            border-radius: 8px;
            text-align: center;
            font-size: 0.9rem;
        }

        body.light .admin-contact {
            background: rgba(0, 217, 255, 0.05);
        }

        .password-display {
            margin-top: 1rem;
            padding: 1rem;
            background: rgba(0, 217, 255, 0.1);
            border-radius: 8px;
            text-align: center;
            font-size: 1.1rem;
            word-break: break-all;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="dark">
    <button class="theme-toggle" onclick="toggleTheme()">🌙 Mode</button>

    <div class="forgot-container">
        <div class="forgot-icon">
            <i class="fas fa-unlock-alt"></i>
        </div>
        <h2>Lupa Password</h2>
        <p class="forgot-description">Masukkan email admin Anda yang terdaftar. Kami akan mengirimkan password sementara ke email Anda.</p>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> 
                <?= $success ?>
            </div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> 
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label for="email"><i class="fas fa-envelope"></i> Email Admin</label>
                <input type="email" name="email" id="email" required placeholder="Masukkan email Anda" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            </div>
            
            <button type="submit">
                <i class="fas fa-paper-plane"></i> Kirim Password Sementara
            </button>
        </form>
        
        <a href="login.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Kembali ke Login
        </a>
    </div>

    <script>
        // Toggle theme
        function toggleTheme() {
            const body = document.body;
            body.classList.toggle('light');
            localStorage.setItem('theme', body.classList.contains('light') ? 'light' : 'dark');
        }

        // Load saved theme
        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.body.classList.add(savedTheme);
        });
    </script>
</body>
</html>