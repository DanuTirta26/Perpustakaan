<?php
require 'config/db.php'; // Pastikan ini sesuai koneksi DB-mu

$q = isset($_GET['q']) ? $_GET['q'] : '';

if ($q === '') {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT id, judul, penulis, gambar FROM buku WHERE judul LIKE ? LIMIT 5");
$searchTerm = "%" . $q . "%";
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $gambar = !empty($row['gambar']) ? 'uploads/' . $row['gambar'] : 'uploads/default.jpg';

    $data[] = [
        'id' => $row['id'],
        'judul' => $row['judul'],
        'penulis' => $row['penulis'],
        'gambar' => $gambar
    ];
}

echo json_encode($data);
