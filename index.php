<?php
session_start();
include __DIR__ . '/config/koneksi.php';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar_user.php';
include __DIR__ . '/includes/sidebar_user.php';

$sql = "SELECT buku.id, judul, penulis, penerbit, tahun_terbit, gambar, kategori.nama_kategori, buku.stok
        FROM buku 
        LEFT JOIN kategori ON buku.kategori_id = kategori.id 
        ORDER BY buku.created_at DESC";
$result = $conn->query($sql);

$books = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
}

$ads = [
    ['image' => 'uploads/diskon1.jpg', 'caption' => 'Belajar JavaScript - Diskon 50%'],
    ['image' => 'uploads/diskon2.jpg', 'caption' => 'UI/UX Design untuk Pemula - Hanya Rp 49.000!'],
    ['image' => 'uploads/diskon3.jpg', 'caption' => 'Python Data Science - Gratis eBook!'],
];
?>

<!-- Animate.css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<style>
  /* Background biru dongker */
  body {
    background-color: #001f3f;
    /* Hapus background image */
    background-image: none;
  }

  /* Hilangkan margin bawah navbar (pastikan navbar tidak beri margin bawah) */
  nav.navbar {
    margin-bottom: 0 !important;
  }

  #iklan-gambar {
    width: 100%;
    max-width: 500px;
    height: auto;
    object-fit: contain;
    transition: opacity 0.5s ease;
    border-radius: 10px;
    margin: 0 auto;
    display: block;
  }

  .stok-habis {
    color: #ff4c4c;
    font-weight: 600;
  }

  /* Card Buku putih */
  .book-card {
    background-color: #ffffff; /* putih */
    color: #212529; /* teks gelap */
    border: 1px solid #ddd;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.2s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
  }

  .book-card:hover {
    transform: scale(1.03);
  }

  .book-img {
    height: 150px;
    object-fit: cover;
    border-bottom: 1px solid #ddd;
  }

  .book-info {
    font-size: 0.9rem;
    color: #212529;
    padding: 0.75rem 1rem;
    flex-grow: 1;
  }

  .book-title {
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: #001f3f;
  }

  .btn-pinjam {
    font-size: 0.9rem;
    padding: 8px 16px;
    margin-top: auto;
    border-radius: 6px;
  }

  /* Hilangkan margin top pada wrapper agar banner nempel navbar */
  .dashboard-wrapper {
    margin-top: 0 !important;
  }

  /* Kontrol margin banner iklan */
  #banner-iklan {
    margin-top: 0;
    margin-bottom: 1.5rem;
  }
</style>

<div class="dashboard-wrapper container">
  <h3 class="mb-4 text-white">📚 Jual Buku</h3>

  <!-- Banner Iklan -->
  <div id="banner-iklan" class="text-center">
    <img id="iklan-gambar" src="<?= $ads[0]['image'] ?>" alt="Iklan Buku" class="img-fluid rounded shadow animate__animated animate__fadeInDown">
    <div id="iklan-caption" class="mt-2 fw-bold text-white"><?= htmlspecialchars($ads[0]['caption']) ?></div>
  </div>

  <!-- Daftar Buku -->
  <div class="row g-3">
    <?php if (count($books) > 0): ?>
      <?php 
        $delay = 0;
        foreach ($books as $book): 
      ?>
        <div class="col-6 col-sm-4 col-md-3 animate__animated animate__fadeInUp" style="animation-delay: <?= $delay ?>s;">
          <div class="book-card">
            <img src="<?= htmlspecialchars($book['gambar']) ?>" class="book-img w-100" alt="Sampul Buku <?= htmlspecialchars($book['judul']) ?>">
            <div class="book-info">
              <div class="book-title"><?= htmlspecialchars($book['judul']) ?></div>
              <div><strong>Penulis:</strong> <?= htmlspecialchars($book['penulis']) ?></div>
              <div><strong>Tahun:</strong> <?= htmlspecialchars($book['tahun_terbit']) ?></div>
              <div><strong>Kategori:</strong> <?= htmlspecialchars($book['nama_kategori']) ?: 'Tidak ada' ?></div>

              <?php if ((int)$book['stok'] > 0): ?>
                <div><strong>Stok:</strong> <?= (int)$book['stok'] ?></div>
              <?php else: ?>
                <div class="stok-habis"><strong>Stok:</strong> Habis</div>
              <?php endif; ?>
            </div>

            <button
              class="btn btn-primary btn-pinjam"
              data-bs-toggle="modal"
              data-bs-target="#pinjamModal"
              data-bookid="<?= $book['id'] ?>"
              data-judul="<?= htmlspecialchars($book['judul']) ?>"
              <?= ((int)$book['stok'] === 0) ? 'disabled' : '' ?>
            >
              Pinjam
            </button>
          </div>
        </div>
      <?php 
        $delay += 0.1;
        endforeach; 
      ?>
    <?php else: ?>
      <div class="col-12">
        <div class="alert alert-info">Belum ada buku tersedia.</div>
      </div>
    <?php endif; ?>
  </div>

  <!-- Pagination Dummy -->
  <nav aria-label="Navigasi halaman buku">
    <ul class="pagination justify-content-center mt-4">
      <li class="page-item disabled"><a class="page-link" href="#">Sebelumnya</a></li>
      <li class="page-item active"><a class="page-link" href="#">1</a></li>
      <li class="page-item"><a class="page-link" href="#">2</a></li>
      <li class="page-item"><a class="page-link" href="#">3</a></li>
      <li class="page-item"><a class="page-link" href="#">Berikutnya</a></li>
    </ul>
  </nav>
</div>

<!-- Modal Form Pinjam Buku -->
<div class="modal fade" id="pinjamModal" tabindex="-1" aria-labelledby="pinjamModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="pinjam_proses.php" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pinjamModalLabel">Form Pinjam Buku</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="book_id" id="modalBookId" value="">

        <div class="mb-3">
          <label for="modalBookTitle" class="form-label">Judul Buku</label>
          <input type="text" class="form-control" id="modalBookTitle" readonly>
        </div>

        <div class="mb-3">
          <label for="peminjamNama" class="form-label">Nama Peminjam</label>
          <input type="text" class="form-control" id="peminjamNama" name="peminjam_nama" required>
        </div>

        <div class="mb-3">
          <label for="tanggalPinjam" class="form-label">Tanggal Pinjam</label>
          <input type="date" class="form-control" id="tanggalPinjam" name="tanggal_pinjam" required value="<?= date('Y-m-d') ?>">
        </div>

        <div class="mb-3">
          <label for="tanggalKembali" class="form-label">Tanggal Kembali</label>
          <input type="date" class="form-control" id="tanggalKembali" name="tanggal_kembali" required value="<?= date('Y-m-d', strtotime('+7 days')) ?>">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Pinjam</button>
      </div>
    </form>
  </div>
</div>

<!-- Script untuk banner iklan -->
<script>
  const ads = <?= json_encode($ads) ?>;
  let idx = 0;
  const imgElement = document.getElementById("iklan-gambar");
  const captionElement = document.getElementById("iklan-caption");

  setInterval(() => {
    idx = (idx + 1) % ads.length;
    imgElement.classList.remove('animate__fadeIn');
    void imgElement.offsetWidth;
    imgElement.classList.add('animate__animated', 'animate__fadeIn');
    imgElement.src = ads[idx].image;
    captionElement.textContent = ads[idx].caption;
  }, 4000);
</script>

<!-- Script untuk isi data modal -->
<script>
  var pinjamModal = document.getElementById('pinjamModal');
  pinjamModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var bookId = button.getAttribute('data-bookid');
    var judul = button.getAttribute('data-judul');

    var modalBookId = pinjamModal.querySelector('#modalBookId');
    var modalBookTitle = pinjamModal.querySelector('#modalBookTitle');

    modalBookId.value = bookId;
    modalBookTitle.value = judul;
  });
</script>

<!-- Bootstrap 5 JS (pastikan sudah ada) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
