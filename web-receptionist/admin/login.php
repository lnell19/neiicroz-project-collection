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
            $update = $conn->prepare("UPDATE admin SET level = 'Aktif', created_at = ? WHERE id_admin = ?");
            $update->bind_param("si", $now, $id_admin);
            $update->execute();

            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Password salah.";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
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

        .login-container {
            background: rgba(255, 255, 255, 0.05);
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(8px);
            width: 100%;
            max-width: 400px;
            animation: fadeIn 0.5s ease;
        }

        body.light .login-container {
            background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #00d9ff;
            font-size: 1.8rem;
        }

        body.light .login-container h2 {
            color: #333;
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
        }

        button[type="submit"]:hover {
            background: #00aacc;
        }

        .error-message {
            color: #ff5252;
            text-align: center;
            margin-bottom: 1rem;
            padding: 0.5rem;
            background: rgba(255,0,0,0.1);
            border: 1px solid rgba(255,0,0,0.2);
            border-radius: 6px;
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

        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #00d9ff;
            cursor: pointer;
            font-size: 1rem;
        }

        /* Gaya baru untuk tautan Lupa Password */
        .forgot-password {
            text-align: right;
            margin: -10px 0 20px 0;
        }

        .forgot-password a {
            color: #00d9ff;
            text-decoration: none;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            transition: color 0.3s, transform 0.2s;
        }

        .forgot-password a:hover {
            color: #00aacc;
            transform: translateX(3px);
        }

        .forgot-password i {
            margin-right: 5px;
            font-size: 0.8rem;
        }

        body.light .forgot-password a {
            color: #007bff;
        }

        body.light .forgot-password a:hover {
            color: #0056b3;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="dark">
    <button class="theme-toggle" onclick="toggleTheme()">🌙 Mode</button>

    <div class="login-container">
        <h2>Login Admin</h2>
        
        <?php if (!empty($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="input-group">
                <label for="username"><i class="fas fa-user"></i> Username</label>
                <input type="text" name="username" id="username" required placeholder="Masukkan username">
            </div>
            
            <div class="input-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="password" required placeholder="Masukkan password">
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="forgot-password">
                <a href="forgot.php">
                    <i class="fas fa-key"></i> Lupa Password?
                </a>
            </div>
            
            <button type="submit">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
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

        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.password-toggle i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>