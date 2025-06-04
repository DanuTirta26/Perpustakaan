<?php
$koneksi = new mysqli("localhost", "root", "", "db_perpustakaan");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'];
    $pengarang = $_POST['pengarang'];
    $tahun = $_POST['tahun'];

    $foto_tmp = $_FILES['foto']['tmp_name'];
    $foto_name = $_FILES['foto']['name'];
    $foto_type = $_FILES['foto']['type'];

    $allowed_types = ['image/jpg', 'image/png'];

    // Validasi tipe file
    if (!in_array($foto_type, $allowed_types)) {
        die('File harus berupa JPG atau PNG.');
    }

    // Folder upload
    $folder = "uploads/";

    // Buat nama file unik
    $ext = pathinfo($foto_name, PATHINFO_EXTENSION);
    $new_filename = time() . '_' . uniqid() . '.' . $ext;

    // Pastikan folder ada
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    $upload_path = $folder . $new_filename;

    if (move_uploaded_file($foto_tmp, $upload_path)) {
        // Simpan ke database dengan prepared statement
        $stmt = $koneksi->prepare("INSERT INTO buku (judul, pengarang, tahun, foto) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $judul, $pengarang, $tahun, $new_filename);
        $stmt->execute();
        $stmt->close();

        header("Location: index.php");
        exit;
    } else {
        die('Gagal mengupload gambar.');
    }
} else {
    die('Metode request tidak valid.');
}
?>
