<?php
session_start();

$username = $_POST['username'];
$password = $_POST['password'];

if ($username === 'admin' && $password === 'admin123') {
    $_SESSION['user'] = [
        'username' => 'admin',
        'role' => 'Admin'
    ];
    header('Location: views/dashboard.php');
    exit;
} elseif ($username === 'user' && $password === 'user123') {
    $_SESSION['user'] = [
        'username' => 'user',
        'role' => 'User'
    ];
    header('Location: views/dashboard.php');
    exit;
} else {
    $_SESSION['error'] = 'Username atau password salah.';
    header('Location: views/login.php');
    exit;
}
