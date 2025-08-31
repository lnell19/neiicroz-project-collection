<?php
include "koneksi.php";

// Ambil semua data user
$query = "SELECT id_admin, password FROM admin";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['id_admin'];
        $plainPassword = $row['password'];

        // Cek apakah sudah di-hash (bcrypt biasanya panjang 60 karakter & diawali $2y$)
        if (strlen($plainPassword) < 60 || substr($plainPassword, 0, 4) !== '$2y$') {
            $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

            // Update password di database
            $update = "UPDATE admin SET password='$hashedPassword' WHERE id_admin=$id";
            if (mysqli_query($conn, $update)) {
                echo "Password untuk user ID $id berhasil di-hash.<br>";
            } else {
                echo "Gagal update password untuk user ID $id: " . mysqli_error($conn) . "<br>";
            }
        } else {
            echo "User ID $id sudah di-hash, dilewati.<br>";
        }
    }
} else {
    echo "Tidak ada data user.";
}
?>
