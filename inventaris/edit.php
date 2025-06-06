<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    header('Location: views/login.php');
    exit;
}

require_once 'config/db.php';

$id = $_GET['id'];
$barang = mysqli_query($conn, "SELECT * FROM barang WHERE ID_barang = '$id'");
$data = mysqli_fetch_assoc($barang);

$kategori = mysqli_query($conn, "SELECT * FROM kategori");
$ruangan = mysqli_query($conn, "SELECT * FROM ruangan");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Barang</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Edit Barang</h2>
    <form method="POST" action="update.php" class="needs-validation" novalidate>
        <input type="hidden" name="id" value="<?= htmlspecialchars($data['ID_barang']) ?>">

        <div class="mb-3">
            <label for="nama_barang" class="form-label">Nama Barang</label>
            <input type="text" class="form-control" id="nama_barang" name="nama_barang" value="<?= htmlspecialchars($data['nama_barang']) ?>" required>
            <div class="invalid-feedback">
                Mohon isi Nama Barang.
            </div>
        </div>

        <div class="mb-3">
            <label for="kode_barang" class="form-label">Kode Barang</label>
            <input type="text" class="form-control" id="kode_barang" name="kode_barang" value="<?= htmlspecialchars($data['kode_barang']) ?>" required>
            <div class="invalid-feedback">
                Mohon isi Kode Barang.
            </div>
        </div>

        <div class="mb-3">
            <label for="jumlah" class="form-label">Jumlah</label>
            <input type="number" class="form-control" id="jumlah" name="jumlah" value="<?= htmlspecialchars($data['jumlah']) ?>" required min="0">
            <div class="invalid-feedback">
                Mohon isi Jumlah dengan angka yang valid.
            </div>
        </div>

        <div class="mb-3">
            <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
            <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk" value="<?= htmlspecialchars($data['tanggal_masuk']) ?>" required>
            <div class="invalid-feedback">
                Mohon isi Tanggal Masuk.
            </div>
        </div>

        <div class="mb-3">
            <label for="ID_kategori" class="form-label">Kategori</label>
            <select class="form-select" id="ID_kategori" name="ID_kategori" required>
                <option value="" disabled>-- Pilih Kategori --</option>
                <?php 
                mysqli_data_seek($kategori, 0); // reset pointer jika diperlukan
                while ($k = mysqli_fetch_assoc($kategori)): ?>
                    <option value="<?= htmlspecialchars($k['ID_kategori']) ?>" <?= $data['ID_kategori'] == $k['ID_kategori'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($k['nama_kategori']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <div class="invalid-feedback">
                Mohon pilih Kategori.
            </div>
        </div>

        <div class="mb-4">
            <label for="ID_ruangan" class="form-label">Ruangan</label>
            <select class="form-select" id="ID_ruangan" name="ID_ruangan" required>
                <option value="" disabled>-- Pilih Ruangan --</option>
                <?php 
                mysqli_data_seek($ruangan, 0); // reset pointer jika diperlukan
                while ($r = mysqli_fetch_assoc($ruangan)): ?>
                    <option value="<?= htmlspecialchars($r['ID_ruangan']) ?>" <?= $data['ID_ruangan'] == $r['ID_ruangan'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($r['nama_ruangan']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <div class="invalid-feedback">
                Mohon pilih Ruangan.
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="barang_list.php" class="btn btn-secondary ms-2">Batal</a>
    </form>
</div>

<!-- Bootstrap JS & Popper (optional for validation) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Contoh validasi bootstrap
(() => {
    'use strict'
    const forms = document.querySelectorAll('.needs-validation')
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>
</body>
</html>
