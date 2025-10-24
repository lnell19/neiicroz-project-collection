<?php
session_start();
//koneksi ke database
include "../koneksi.php";

if (isset($_SESSION['id_admin'])) {
      //simpan session
      $id_admin = $_SESSION['id_admin'];

      //Update status dan tanggal login terakhir
      $conn->query("UPDATE admin SET level = 'on' WHERE id_admin = '$id_admin'");

      //hapus session
      session_destroy();
    }
    
      header("Location: login.php");
      exit;