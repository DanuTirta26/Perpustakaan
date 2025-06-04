<?php
session_start();
include __DIR__ . '/config/koneksi.php';

$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($keyword === '') {
    echo "<p class='text-center mt-5'>Masukkan kata kunci pencarian.</p>";
    exit;
}

$sql = "SELECT id, judul, penulis, tahun_terbit, gambar FROM buku WHERE judul LIKE ? OR penulis LIKE ? ORDER BY judul ASC";
$stmt = $conn->prepare($sql);

$like_keyword = "%{$keyword}%";
$stmt->bind_param("ss", $like_keyword, $like_keyword);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Hasil Pencarian: <?= htmlspecialchars($keyword) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        .book-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-top-left-radius: .5rem;
            border-top-right-radius: .5rem;
        }
        .book-card .card-body {
            padding: 0.75rem;
        }
        .book-card h5 {
            font-size: 1rem;
            margin-bottom: .25rem;
        }
        .book-card p {
            font-size: 0.9rem;
            margin-bottom: 0;
        }
    </style>
</head>
<body class="bg-light">

<div class="container my-5">
    <h2 class="mb-4">Hasil Pencarian untuk: <em><?= htmlspecialchars($keyword) ?></em></h2>
    <hr>

    <?php if ($result->num_rows > 0): ?>
        <div class="row g-4">
            <?php while ($row = $result->fetch_assoc()): 
                $gambar = !empty($row['gambar']) && file_exists("uploads/" . $row['gambar']) 
                    ? "uploads/" . $row['gambar'] 
                    : "uploads/default.jpg";
            ?>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="card shadow-sm h-100 book-card">
                        <a href="detail_buku.php?id=<?= $row['id'] ?>">
                            <img src="<?= $gambar ?>" alt="<?= htmlspecialchars($row['judul']) ?>">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($row['judul']) ?></h5>
                            <p class="text-muted"><?= htmlspecialchars($row['penulis']) ?> • <?= htmlspecialchars($row['tahun_terbit']) ?></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            Buku dengan kata kunci "<strong><?= htmlspecialchars($keyword) ?></strong>" tidak ditemukan.
        </div>
    <?php endif; ?>

    <div class="text-center mt-4">
        <a href="index.php" class="btn btn-outline-primary">Kembali ke Beranda</a>
    </div>
</div>

</body>
</html>
