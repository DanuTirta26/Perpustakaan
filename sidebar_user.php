<!-- includes/sidebar_user.php -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

<style>
  body {
    margin: 0;
    font-family: Arial, sans-serif;
  }

  /* NAVBAR */
  .navbar-custom {
    background-color: #2c3e50;
    color: white;
    height: 60px;
    display: flex;
    align-items: center;
    padding: 0 20px;
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1000;
    box-shadow: 0 2px 5px rgba(0,0,0,0.3);
  }

  /* SIDEBAR */
  #userSidebarWrapper {
    width: 260px;
    background-color: #1e1e2f;
    color: white;
    height: calc(100vh - 60px); /* sisakan ruang navbar */
    position: fixed;
    top: 60px; /* mulai dari bawah navbar */
    left: 0;
    transform: translateX(-100%);
    transition: transform 0.3s ease;
    z-index: 999; /* di bawah navbar */
    overflow-y: auto;
    box-shadow: 2px 0 8px rgba(0, 0, 0, 0.5);
  }

  #userSidebarWrapper.open {
    transform: translateX(0);
  }

  .nav-link {
    color: white;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    text-decoration: none;
    transition: background-color 0.2s ease;
  }

  .nav-link:hover {
    background-color: #343454;
  }

  .nav-link i {
    margin-right: 10px;
  }

  .cart-badge {
    background-color: #e74c3c;
    color: white;
    font-size: 0.75rem;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 12px;
    position: absolute;
    top: 10px;
    right: 25px;
  }

  .cart-link {
    position: relative;
  }

  #sidebarToggleBtn {
    position: fixed;
    top: 12px;
    left: 12px;
    background-color: #1e1e2f;
    color: white;
    border: none;
    padding: 10px;
    border-radius: 5px;
    z-index: 1100;
    cursor: pointer;
  }

  /* Konten halaman */
  .main-content {
    padding: 80px 20px 20px 20px;
    margin-left: 0;
  }

  @media (min-width: 992px) {
    .main-content {
      margin-left: 260px;
    }
  }
</style>

<?php
session_start();
if (!isset($_SESSION['cart'])) {
  $_SESSION['cart'] = [];
}
$cartCount = count($_SESSION['cart']);
?>

<!-- NAVBAR -->
<div class="navbar-custom">
  <h1 style="font-size: 18px;">Aplikasi Perpustakaan</h1>
</div>

<!-- TOGGLE BUTTON -->
<button id="sidebarToggleBtn"><i class="fas fa-bars"></i></button>

<!-- SIDEBAR -->
<div id="userSidebarWrapper">
  <nav>
    <div class="nav-divider" style="padding: 15px; color: #bbb;">Menu User</div>

    <a class="nav-link" href="./user/riwayat_peminjaman.php">
      <i class="fa fa-book"></i> Riwayat Peminjaman
    </a>

    <a class="nav-link" href="logout.php">
      <i class="fa fa-sign-out-alt"></i> Logout
    </a>
  </nav>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">
</div>

<!-- SCRIPT SIDEBAR -->
<script>
  const sidebar = document.getElementById('userSidebarWrapper');
  const toggleBtn = document.getElementById('sidebarToggleBtn');

  toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('open');
  });

  // Optional: Geser kiri/kanan untuk buka/tutup (gesture)
  let startX = 0, isDragging = false;
  document.addEventListener('touchstart', e => { startX = e.touches[0].clientX; isDragging = true; });
  document.addEventListener('touchmove', e => {
    if (!isDragging) return;
    let diff = e.touches[0].clientX - startX;
    if (diff > 80) sidebar.classList.add('open');
    if (diff < -80) sidebar.classList.remove('open');
  });
  document.addEventListener('touchend', () => isDragging = false);
</script>
