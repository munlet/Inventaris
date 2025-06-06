<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    header('Location: views/login.php');
    exit;
}

require_once 'config/db.php';

$id = $_POST['id'];

mysqli_query($conn, "UPDATE barang SET 
    nama_barang = '" . $_POST['nama_barang'] . "',
    kode_barang = '" . $_POST['kode_barang'] . "',
    jumlah = '" . $_POST['jumlah'] . "',
    tanggal_masuk = '" . $_POST['tanggal_masuk'] . "',
    ID_kategori = '" . $_POST['ID_kategori'] . "',
    ID_ruangan = '" . $_POST['ID_ruangan'] . "',
    status_barang = '" . $_POST['status_barang'] . "',
    updated_at = NOW()
    WHERE ID_barang = '$id'
");

header('Location: views/dashboard.php');
exit;
