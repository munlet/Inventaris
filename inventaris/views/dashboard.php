<?php
session_start();
if (!isset($_SESSION['user'])) {
  header('Location: login.php');
  exit;
}

require_once '../config/db.php';

$user = $_SESSION['user'];
$barang = mysqli_query($conn, "SELECT b.*, k.nama_kategori, r.nama_ruangan FROM barang b
    JOIN kategori k ON b.ID_kategori = k.ID_kategori
    JOIN ruangan r ON b.ID_ruangan = r.ID_ruangan");
$kategori = mysqli_query($conn, "SELECT * FROM kategori");
$ruangan = mysqli_query($conn, "SELECT * FROM ruangan");

// Count statistics
$total_barang = mysqli_num_rows($barang);
$barang_baik = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM barang WHERE status_barang = 'Baik'"));
$barang_rusak = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM barang WHERE status_barang = 'Rusak'"));
$total_kategori = mysqli_num_rows($kategori);

// Handle section navigation
$current_section = isset($_GET['section']) ? $_GET['section'] : 'dashboard';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Stok Inventaris SMKN 2 Bandung</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      min-height: 100vh;
    }

    .dashboard-container {
      display: flex;
      min-height: 100vh;
    }

    /* Sidebar Styling */
    .sidebar {
      width: 280px;
      background: linear-gradient(180deg, #4A90E2 0%, #2E5BBA 100%);
      color: white;
      padding: 0;
      box-shadow: 4px 0 20px rgba(0,0,0,0.1);
      position: relative;
      overflow: hidden;
    }

    .sidebar::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-image: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
      background-size: 20px 20px;
      opacity: 0.3;
    }

    .sidebar-header {
      padding: 30px 25px;
      text-align: center;
      border-bottom: 1px solid rgba(255,255,255,0.1);
      position: relative;
      z-index: 1;
    }

    .sidebar-title {
      font-size: 1.4rem;
      font-weight: bold;
      margin-bottom: 5px;
      color: #87CEEB;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }

    .sidebar-subtitle {
      font-size: 0.9rem;
      opacity: 0.9;
      color: #E6F3FF;
    }

    .user-info {
      padding: 20px 25px;
      background: rgba(255,255,255,0.1);
      margin: 20px;
      border-radius: 15px;
      text-align: center;
      position: relative;
      z-index: 1;
    }

    .user-avatar {
      width: 60px;
      height: 60px;
      background: #87CEEB;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 15px;
      font-size: 1.5rem;
      color: #2E5BBA;
      font-weight: bold;
    }

    .user-name {
      font-weight: bold;
      font-size: 1.1rem;
      margin-bottom: 5px;
    }

    .user-role {
      font-size: 0.9rem;
      opacity: 0.8;
      background: rgba(255,255,255,0.2);
      padding: 4px 12px;
      border-radius: 20px;
      display: inline-block;
    }

    .sidebar-nav {
      padding: 20px 0;
      position: relative;
      z-index: 1;
    }

    .nav-item {
      margin: 5px 20px;
    }

    .nav-link {
      display: flex;
      align-items: center;
      padding: 15px 20px;
      color: white;
      text-decoration: none;
      border-radius: 12px;
      transition: all 0.3s ease;
      font-weight: 500;
    }

    .nav-link:hover {
      background: rgba(255,255,255,0.15);
      transform: translateX(5px);
      color: #87CEEB;
    }

    .nav-link.active {
      background: rgba(255,255,255,0.2);
      color: #87CEEB;
    }

    .nav-link i {
      margin-right: 12px;
      font-size: 1.1rem;
      width: 20px;
    }

    /* Main Content Wrapper */
    .content-wrapper {
      flex: 1;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    /* Main Content */
    .main-content {
      flex: 1;
      padding: 30px;
      overflow-y: auto;
    }

    .header {
      background: white;
      padding: 25px 30px;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      margin-bottom: 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .header-title {
      font-size: 2rem;
      font-weight: bold;
      color: #2E5BBA;
      margin: 0;
    }

    .header-time {
      color: #666;
      font-size: 0.9rem;
    }

    /* Stats Cards */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 25px;
      margin-bottom: 30px;
    }

    .stat-card {
      background: white;
      padding: 30px;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      text-align: center;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #4A90E2, #2E5BBA);
    }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }

    .stat-icon {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
      font-size: 2rem;
      color: white;
    }

    .stat-icon.total { background: linear-gradient(135deg, #4A90E2, #2E5BBA); }
    .stat-icon.good { background: linear-gradient(135deg, #28a745, #20c997); }
    .stat-icon.damaged { background: linear-gradient(135deg, #dc3545, #fd7e14); }
    .stat-icon.category { background: linear-gradient(135deg, #6f42c1, #e83e8c); }

    .stat-number {
      font-size: 2.5rem;
      font-weight: bold;
      color: #2E5BBA;
      margin-bottom: 10px;
    }

    .stat-label {
      color: #666;
      font-weight: 500;
      font-size: 1.1rem;
    }

    /* Form Card */
    .form-card {
      background: white;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      margin-bottom: 30px;
      overflow: hidden;
    }

    .form-header {
      background: linear-gradient(135deg, #4A90E2, #2E5BBA);
      color: white;
      padding: 25px 30px;
      font-size: 1.3rem;
      font-weight: bold;
    }

    .form-body {
      padding: 30px;
    }

    .form-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 20px;
      margin-bottom: 25px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #2E5BBA;
    }

    .form-control {
      width: 100%;
      padding: 15px 20px;
      border: 2px solid #e1e8ed;
      border-radius: 12px;
      font-size: 1rem;
      transition: all 0.3s ease;
      background: #f8f9fa;
    }

    .form-control:focus {
      outline: none;
      border-color: #4A90E2;
      box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
      background: white;
    }

    .btn {
      padding: 15px 30px;
      border: none;
      border-radius: 12px;
      font-weight: 600;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-block;
      text-align: center;
    }

    .btn-primary {
      background: linear-gradient(135deg, #4A90E2, #2E5BBA);
      color: white;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(74, 144, 226, 0.3);
    }

    .btn-success {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white;
    }

    .btn-warning {
      background: linear-gradient(135deg, #ffc107, #fd7e14);
      color: white;
    }

    .btn-danger {
      background: linear-gradient(135deg, #dc3545, #fd7e14);
      color: white;
    }

    .btn-sm {
      padding: 8px 16px;
      font-size: 0.85rem;
      margin: 0 2px;
    }

    /* Table Styling */
    .table-card {
      background: white;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      overflow: hidden;
    }

    .table-header {
      background: linear-gradient(135deg, #4A90E2, #2E5BBA);
      color: white;
      padding: 25px 30px;
      font-size: 1.3rem;
      font-weight: bold;
    }

    .table-container {
      overflow-x: auto;
    }

    .table {
      width: 100%;
      margin: 0;
      border-collapse: collapse;
    }

    .table th {
      background: #f8f9fa;
      padding: 20px 15px;
      font-weight: 600;
      color: #2E5BBA;
      text-align: left;
      border-bottom: 2px solid #e1e8ed;
    }

    .table td {
      padding: 18px 15px;
      border-bottom: 1px solid #e1e8ed;
      vertical-align: middle;
    }

    .table tr:hover {
      background: #f8f9fa;
    }

    .table tr:last-child td {
      border-bottom: none;
    }

    /* Footer Styling */
    .footer {
      margin-top: auto;
      background: linear-gradient(135deg, #2E5BBA 0%, #4A90E2 100%);
      color: white;
      padding: 40px 30px 20px;
      border-radius: 20px 20px 0 0;
      box-shadow: 0 -10px 30px rgba(0,0,0,0.1);
    }

    .footer-content {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 40px;
      margin-bottom: 30px;
    }

    .footer-section h3 {
      font-size: 1.4rem;
      font-weight: bold;
      margin-bottom: 15px;
      color: #87CEEB;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .footer-section p {
      margin-bottom: 8px;
      line-height: 1.6;
      opacity: 0.9;
    }

    .footer-section a {
      color: #87CEEB;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .footer-section a:hover {
      color: #E6F3FF;
      text-decoration: underline;
    }

    .footer-divider {
      height: 1px;
      background: rgba(255,255,255,0.2);
      margin: 20px 0;
    }

    .footer-bottom {
      text-align: center;
      font-size: 0.9rem;
      opacity: 0.8;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .dashboard-container {
        flex-direction: column;
      }
      
      .sidebar {
        width: 100%;
        height: auto;
      }
      
      .main-content {
        padding: 20px;
      }
      
      .header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
      }
      
      .stats-grid {
        grid-template-columns: 1fr;
      }
      
      .form-grid {
        grid-template-columns: 1fr;
      }

      .footer-content {
        grid-template-columns: 1fr;
        gap: 30px;
      }
      
      .footer {
        padding: 30px 20px 15px;
      }
    }

    /* Loading Animation */
    .loading {
      display: none;
      text-align: center;
      padding: 20px;
      color: #4A90E2;
    }

    .spinner {
      border: 3px solid #f3f3f3;
      border-top: 3px solid #4A90E2;
      border-radius: 50%;
      width: 30px;
      height: 30px;
      animation: spin 1s linear infinite;
      margin: 0 auto 15px;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  </style>
</head>
<body>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <nav class="sidebar">
      <div class="sidebar-header">
        <div class="sidebar-title">STOK INVENTARIS</div>
        <div class="sidebar-subtitle">SMKN 2 BANDUNG</div>
      </div>

      <div class="user-info">
        <div class="user-avatar">
          <?= strtoupper(substr($user['username'], 0, 1)) ?>
        </div>
        <div class="user-name"><?= htmlspecialchars($user['username']) ?></div>
        <div class="user-role"><?= $user['role'] ?></div>
      </div>

      <div class="sidebar-nav">
        <div class="nav-item">
          <a href="?section=dashboard" class="nav-link <?= $current_section == 'dashboard' ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt"></i>
            Dashboard
          </a>
        </div>
        <?php if ($user['role'] === 'Admin'): ?>
        <div class="nav-item">
          <a href="?section=tambah-barang" class="nav-link <?= $current_section == 'tambah-barang' ? 'active' : '' ?>">
            <i class="fas fa-plus-circle"></i>
            Tambah Barang
          </a>
        </div>
        <?php endif; ?>
        <div class="nav-item">
          <a href="?section=daftar-barang" class="nav-link <?= $current_section == 'daftar-barang' ? 'active' : '' ?>">
            <i class="fas fa-list"></i>
            Daftar Barang
          </a>
        </div>
        <div class="nav-item">
          <a href="../logout.php" class="nav-link" onclick="return confirm('Yakin ingin logout?')">
            <i class="fas fa-sign-out-alt"></i>
            Logout
          </a>
        </div>
      </div>
    </nav>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
      <!-- Main Content -->
      <main class="main-content">
        <!-- Header -->
        <div class="header">
          <h1 class="header-title">Dashboard Inventaris</h1>
          <div class="header-time">
            <i class="fas fa-clock"></i>
            <span id="current-time"></span>
          </div>
        </div>

        <!-- Dashboard Section -->
        <?php if ($current_section == 'dashboard'): ?>
        <section id="dashboard-section">
          <div class="stats-grid">
            <div class="stat-card">
              <div class="stat-icon total">
                <i class="fas fa-boxes"></i>
              </div>
              <div class="stat-number"><?= $total_barang ?></div>
              <div class="stat-label">Total Barang</div>
            </div>

            <div class="stat-card">
              <div class="stat-icon good">
                <i class="fas fa-check-circle"></i>
              </div>
              <div class="stat-number"><?= $barang_baik ?></div>
              <div class="stat-label">Barang Baik</div>
            </div>

            <div class="stat-card">
              <div class="stat-icon damaged">
                <i class="fas fa-exclamation-triangle"></i>
              </div>
              <div class="stat-number"><?= $barang_rusak ?></div>
              <div class="stat-label">Barang Rusak</div>
            </div>

            <div class="stat-card">
              <div class="stat-icon category">
                <i class="fas fa-tags"></i>
              </div>
              <div class="stat-number"><?= $total_kategori ?></div>
              <div class="stat-label">Kategori</div>
            </div>
          </div>
        </section>
        <?php endif; ?>

        <!-- Add Item Form -->
        <?php if ($user['role'] === 'Admin' && $current_section == 'tambah-barang'): ?>
        <section id="tambah-barang-section">
          <div class="form-card">
            <div class="form-header">
              <i class="fas fa-plus-circle"></i>
              Tambah Barang Baru
            </div>
            <div class="form-body">
              <form method="POST" action="../store.php">
                <div class="form-grid">
                  <div class="form-group">
                    <label class="form-label">Nama Barang</label>
                    <input type="text" name="nama_barang" class="form-control" placeholder="Masukkan nama barang" required>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Kode Barang</label>
                    <input type="text" name="kode_barang" class="form-control" placeholder="Masukkan kode barang" required>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Jumlah</label>
                    <input type="number" name="jumlah" class="form-control" placeholder="Masukkan jumlah" required>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" class="form-control" required>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Status Barang</label>
                    <select name="status_barang" class="form-control" required>
                      <option value="">-- Pilih Status --</option>
                      <option value="Baik">Baik</option>
                      <option value="Rusak">Rusak</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Kategori</label>
                    <select name="ID_kategori" class="form-control" required>
                      <option value="">-- Pilih Kategori --</option>
                      <?php 
                      mysqli_data_seek($kategori, 0);
                      while ($k = mysqli_fetch_assoc($kategori)): ?>
                        <option value="<?= $k['ID_kategori'] ?>"><?= $k['nama_kategori'] ?></option>
                      <?php endwhile; ?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Ruangan</label>
                    <select name="ID_ruangan" class="form-control" required>
                      <option value="">-- Pilih Ruangan --</option>
                      <?php 
                      mysqli_data_seek($ruangan, 0);
                      while ($r = mysqli_fetch_assoc($ruangan)): ?>
                        <option value="<?= $r['ID_ruangan'] ?>"><?= $r['nama_ruangan'] ?></option>
                      <?php endwhile; ?>
                    </select>
                  </div>
                </div>
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-save"></i>
                  Simpan Barang
                </button>
              </form>
            </div>
          </div>
        </section>
        <?php endif; ?>

        <!-- Items List -->
        <?php if ($current_section == 'daftar-barang'): ?>
        <section id="daftar-barang-section">
          <div class="table-card">
            <div class="table-header">
              <i class="fas fa-list"></i>
              Daftar Barang Inventaris
            </div>
            <div class="table-container">
              <table class="table">
                <thead>
                  <tr>
                    <th><i class="fas fa-box"></i> Nama Barang</th>
                    <th><i class="fas fa-barcode"></i> Kode</th>
                    <th><i class="fas fa-sort-numeric-up"></i> Jumlah</th>
                    <th><i class="fas fa-calendar"></i> Tanggal Masuk</th>
                    <th><i class="fas fa-tag"></i> Kategori</th>
                    <th><i class="fas fa-map-marker-alt"></i> Ruangan</th>
                    <th><i class="fas fa-info-circle"></i> Status</th>
                    <?php if ($user['role'] === 'Admin'): ?><th><i class="fas fa-cogs"></i> Aksi</th><?php endif; ?>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  mysqli_data_seek($barang, 0);
                  while ($b = mysqli_fetch_assoc($barang)): ?>
                    <tr>
                      <td><strong><?= htmlspecialchars($b['nama_barang']) ?></strong></td>
                      <td><code><?= htmlspecialchars($b['kode_barang']) ?></code></td>
                      <td><span class="badge" style="background: #4A90E2; color: white; padding: 5px 10px; border-radius: 15px;"><?= $b['jumlah'] ?></span></td>
                      <td><?= date('d/m/Y', strtotime($b['tanggal_masuk'])) ?></td>
                      <td><?= htmlspecialchars($b['nama_kategori']) ?></td>
                      <td><?= htmlspecialchars($b['nama_ruangan']) ?></td>
                      <td>
                        <span class="badge" style="background: <?= $b['status_barang'] == 'Baik' ? '#28a745' : '#dc3545' ?>; color: white; padding: 5px 10px; border-radius: 15px;">
                          <?= $b['status_barang'] ?>
                        </span>
                      </td>
                      <?php if ($user['role'] === 'Admin'): ?>
                      <td>
                        <a href="../edit.php?id=<?= $b['ID_barang'] ?>" class="btn btn-warning btn-sm">
                          <i class="fas fa-edit"></i>
                        </a>
                        <a href="../delete.php?id=<?= $b['ID_barang'] ?>" onclick="return confirm('Yakin ingin hapus?')" class="btn btn-danger btn-sm">
                          <i class="fas fa-trash"></i>
                        </a>
                      </td>
                      <?php endif; ?>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          </div>
        </section>
        <?php endif; ?>
      </main>

      <!-- Footer -->
      <footer class="footer">
        <div class="footer-content">
          <div class="footer-section">
            <h3><i class="fas fa-school"></i> SMKN 2</h3>
            <p><strong>SMK NEGERI 2 BANDUNG</strong></p>
            <p>Jl. Ciliwung No.4 Kelurahan Cihapit</p>
            <p>Kecamatan Bandung Wetan, Kota Bandung</p>
            <p><i class="fas fa-envelope"></i> <a href="mailto:humas@smkn2bandung.sch.id">humas@smkn2bandung.sch.id</a></p>
          </div>
          <div class="footer-section">
            <h3><i class="fas fa-user"></i> Personal Contact</h3>
            <p><i class="fas fa-envelope"></i> <a href="mailto:khalishaagmy1805@gmail.com">khalishaagmy1805@gmail.com</a></p>
            <p><i class="fas fa-envelope"></i> <a href="mailto:munletsesat@gmail.com">munletsesat@gmail.com</a></p>
            <p><i class="fab fa-instagram"></i> <a href="https://www.instagram.com/munletsesat/" target="_blank">@munletsesat</a></p>
          </div>
        </div>
        <div class="footer-divider"></div>
        <div class="footer-bottom">
          <p>&copy; 2024 SMK Negeri 2 Bandung - Sistem Inventaris Barang</p>
        </div>
      </footer>
    </div>
  </div>

  <script>
    // Update time
    function updateTime() {
      const now = new Date();
      const timeString = now.toLocaleString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
      document.getElementById('current-time').textContent = timeString;
    }

    // Initialize
    updateTime();
    setInterval(updateTime, 1000);

    // Add hover effects to stat cards
    document.querySelectorAll('.stat-card').forEach(card => {
      card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-10px) scale(1.02)';
      });
      
      card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) scale(1)';
      });
    });

    // Form validation
    document.querySelectorAll('form').forEach(form => {
      form.addEventListener('submit', function(e) {
        const inputs = this.querySelectorAll('input[required], select[required]');
        let isValid = true;
        
        inputs.forEach(input => {
          if (!input.value.trim()) {
            isValid = false;
            input.style.borderColor = '#dc3545';
          } else {
            input.style.borderColor = '#28a745';
          }
        });
        
        if (!isValid) {
          e.preventDefault();
          alert('Mohon lengkapi semua field yang diperlukan!');
        }
      });
    });
  </script>
</body>
</html>
