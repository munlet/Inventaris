<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    header('Location: views/login.php');
    exit;
}

require_once 'config/db.php';

$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM barang WHERE ID_barang = '$id'");

header('Location: views/dashboard.php');
exit;
