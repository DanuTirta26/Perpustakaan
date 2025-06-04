<?php
session_start();
include __DIR__ . '/config/koneksi.php';

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    die("Anda harus login terlebih dahulu.");
}

// Ambil data dari form POST
$book_id = $_POST['book_id'] ?? null;
$tanggal_pinjam = $_POST['tanggal_pinjam'] ?? null;
$tanggal_kembali = $_POST['tanggal_kembali'] ?? null;

// Validasi input sederhana
if (!$book_id || !$tanggal_pinjam || !$tanggal_kembali) {
    $error = "Data peminjaman tidak lengkap.";
} else {
    // Cari anggota_id dari tabel anggota berdasarkan user_id
    $stmt = $conn->prepare("SELECT id FROM anggota WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($anggota_id);
    if (!$stmt->fetch()) {
        $error = "Data anggota tidak ditemukan. Silakan lengkapi profil Anda.";
    }
    $stmt->close();

    if (!isset($error)) {
        // Cek stok buku dulu
        $stmt = $conn->prepare("SELECT stok FROM buku WHERE id = ?");
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $stmt->bind_result($stok);
        if (!$stmt->fetch()) {
            $error = "Buku tidak ditemukan.";
        }
        $stmt->close();

        if (!isset($error) && $stok < 1) {
            $error = "Maaf, stok buku habis.";
        }
    }
}

if (!isset($error)) {
    // Insert ke tabel transaksi peminjaman
    $status = 'dipinjam';
    $denda = 0;
    $created_at = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO transaksi_peminjaman (anggota_id, buku_id, tanggal_pinjam, tanggal_kembali, status, denda, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisssis", $anggota_id, $book_id, $tanggal_pinjam, $tanggal_kembali, $status, $denda, $created_at);

    if ($stmt->execute()) {
        // Update stok buku (kurangi 1)
        $stmt->close();
        $stmt = $conn->prepare("UPDATE buku SET stok = stok - 1 WHERE id = ?");
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $stmt->close();

        $success = "Peminjaman berhasil! Terima kasih.";
    } else {
        $error = "Terjadi kesalahan saat memproses peminjaman: " . $stmt->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Proses Peminjaman</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #001f3f;
      color: #fff;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      padding: 1rem;
    }
    .card {
      max-width: 480px;
      width: 100%;
      padding: 2rem;
      border-radius: 12px;
      background-color: #004080;
      box-shadow: 0 0 20px rgba(0, 95, 255, 0.6);
    }
  </style>
</head>
<body>
  <div class="card text-center">
    <?php if (isset($error)): ?>
      <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($error) ?>
      </div>
      <a href="javascript:history.back()" class="btn btn-outline-light mt-3">Kembali</a>

    <?php elseif (isset($success)): ?>
      <div class="alert alert-success" role="alert">
        <?= htmlspecialchars($success) ?>
      </div>
      <a href="index.php" class="btn btn-light mt-3">Kembali ke Beranda</a>

      <script>
        // Redirect otomatis ke halaman beranda setelah 5 detik
        setTimeout(() => {
          window.location.href = "index.php";
        }, 5000);
      </script>

    <?php else: ?>
      <div class="alert alert-warning" role="alert">
        Terjadi sesuatu yang tidak terduga.
      </div>
      <a href="index.php" class="btn btn-outline-light mt-3">Kembali ke Beranda</a>
    <?php endif; ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
