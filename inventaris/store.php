<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    header('Location: views/login.php');
    exit;
}

require_once 'config/db.php';

mysqli_query($conn, "INSERT INTO barang 
    (nama_barang, kode_barang, jumlah, tanggal_masuk, ID_kategori, ID_ruangan, status_barang, created_at, updated_at)
    VALUES (
        '" . $_POST['nama_barang'] . "',
        '" . $_POST['kode_barang'] . "',
        '" . $_POST['jumlah'] . "',
        '" . $_POST['tanggal_masuk'] . "',
        '" . $_POST['ID_kategori'] . "',
        '" . $_POST['ID_ruangan'] . "',
        '" . $_POST['status_barang'] . "',
        NOW(),
        NOW()
    )
");

header('Location: views/dashboard.php');
exit;
