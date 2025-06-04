<?php
include('../config/koneksi.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Riwayat Peminjaman</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      display: flex;
    }
    .sidebar {
      width: 250px;
      min-height: 100vh;
      background-color: #343a40;
    }
    .content {
      flex: 1;
      padding: 20px;
      background-color: #f8f9fa;
    }
    .table img {
      max-width: 50px;
      height: auto;
      border-radius: 5px;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <?php include('../includes/sidebar_user.php'); ?>
  </div>

  <!-- Main Content -->
  <div class="content">
    <?php include('../includes/navbar_user.php'); ?>

    <div class="container mt-4">
      <h3 class="mb-4">Riwayat Peminjaman</h3>

      <!-- Tombol Back ke Index -->
      <a href="../index.php" class="btn btn-secondary mb-3">
        &larr; Kembali ke Beranda
      </a>

      <div class="table-responsive bg-white rounded shadow p-3">
        <table class="table table-bordered table-hover">
          <thead class="table-dark">
            <tr>
              <th>No</th>
              <th>Judul Buku</th>
              <th>Penulis</th>
              <th>Gambar</th>
              <th>Tanggal Pinjam</th>
              <th>Tanggal Kembali</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $query = "SELECT tp.*, b.judul, b.penulis, b.gambar 
                      FROM transaksi_peminjaman tp 
                      JOIN buku b ON tp.buku_id = b.id 
                      ORDER BY tp.tanggal_pinjam DESC";
            $result = mysqli_query($conn, $query);
            $no = 1;
            while($row = mysqli_fetch_assoc($result)) {
            ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['judul']) ?></td>
                <td><?= htmlspecialchars($row['penulis']) ?></td>
                <td>
                  <img src="../uploads/<?= htmlspecialchars($row['gambar']) ?>" alt="Gambar Buku" width="60" class="img-thumbnail">
                </td>
                <td><?= $row['tanggal_pinjam'] ?></td>
                <td><?= $row['tanggal_kembali'] ?: '-' ?></td>
                <td>
                  <?php if ($row['status'] == 'dipinjam'): ?>
                    <span class="badge bg-warning text-dark">Dipinjam</span>
                  <?php else: ?>
                    <span class="badge bg-success">Dikembalikan</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</body>
</html>
