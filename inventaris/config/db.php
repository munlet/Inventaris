<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'inventaris_smk2'; // ganti sesuai nama database Anda

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die('Koneksi database gagal: ' . mysqli_connect_error());
}
?>
