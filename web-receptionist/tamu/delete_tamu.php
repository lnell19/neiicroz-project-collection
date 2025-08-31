<?php
session_start();
include "../koneksi.php";

// Cek login admin
if (!isset($_SESSION['id_admin'])) {
    header("Location: ../admin/login.php");
    exit;
}

// Pastikan ada ID
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Hapus data tamu
    $sql = "DELETE FROM tamu WHERE id_tamu = $id";
    if (mysqli_query($conn, $sql)) {
        // Balik ke daftar tamu dengan pesan sukses
        header("Location: tamu.php?msg=deleted");
    } else {
        echo "Gagal menghapus data: " . mysqli_error($conn);
    }
} else {
    header("Location: tamu.php");
    exit;
}
