<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Include koneksi database (sesuaikan path ini jika profil.php di folder 'user')
include __DIR__ . '/../config/koneksi.php';

$user_id = $_SESSION['user_id'];

// Query join users dan anggota
$query = "
    SELECT 
        users.id AS user_id,
        users.username,
        users.nama_lengkap,
        users.email,
        users.role,
        users.created_at,
        anggota.alamat,
        anggota.no_hp,
        anggota.tanggal_bergabung
    FROM users
    LEFT JOIN anggota ON users.id = anggota.user_id
    WHERE users.id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='alert alert-danger'>Data profil tidak ditemukan.</div>";
    exit();
}

$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Profil Saya</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #0d1b2a;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #fff;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }
    .profile-container {
      background: #ffffff;
      color: #212529;
      width: 100%;
      max-width: 700px;
      padding: 40px 30px;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.25);
      position: relative;
      overflow: hidden;
    }
    .profile-title {
      text-align: center;
      font-weight: 700;
      margin-bottom: 30px;
      color: #0d1b2a;
      font-size: 2rem;
      letter-spacing: 1px;
    }
    .profile-info p {
      margin-bottom: 15px;
      font-size: 1.1rem;
    }
    .profile-info span {
      display: inline-block;
      width: 180px;
      font-weight: 600;
      color: #0d1b2a;
    }
    .btn-custom {
      background-color: #0d1b2a;
      color: #fff;
      border: none;
      padding: 10px 25px;
      font-weight: 600;
      border-radius: 8px;
      transition: background-color 0.3s ease;
      cursor: pointer;
      margin: 0 10px;
      min-width: 120px;
      box-shadow: 0 3px 10px rgba(13,27,42,0.6);
      text-decoration: none;
      display: inline-flex;
      justify-content: center;
      align-items: center;
    }
    .btn-custom:hover {
      background-color: #1a2e46;
      box-shadow: 0 6px 15px rgba(13,27,42,0.8);
      text-decoration: none;
      color: #fff;
    }
    .btn-danger {
      background-color: #dc3545;
      border: none;
      padding: 10px 25px;
      font-weight: 600;
      border-radius: 8px;
      min-width: 120px;
      cursor: pointer;
      box-shadow: 0 3px 10px rgba(220,53,69,0.6);
      transition: background-color 0.3s ease;
      text-decoration: none;
      display: inline-flex;
      justify-content: center;
      align-items: center;
      color: #fff;
    }
    .btn-danger:hover {
      background-color: #b02a37;
      box-shadow: 0 6px 15px rgba(176,42,55,0.8);
      text-decoration: none;
      color: #fff;
    }
    .btn-group {
      display: flex;
      justify-content: center;
      margin-top: 30px;
      gap: 10px;
      flex-wrap: wrap;
    }
  </style>
</head>
<body>

  <div class="profile-container animate__animated animate__fadeInDown">
    <h3 class="profile-title">Profil Pengguna</h3>

    <div class="profile-info">
      <p><span>Username:</span> <?= htmlspecialchars($data['username']) ?></p>
      <p><span>Nama Lengkap:</span> <?= htmlspecialchars($data['nama_lengkap']) ?></p>
      <p><span>Email:</span> <?= htmlspecialchars($data['email']) ?></p>
      <p><span>Role:</span> <?= htmlspecialchars($data['role']) ?></p>
      <p><span>Terdaftar Sejak:</span> <?= date('d M Y', strtotime($data['created_at'])) ?></p>
      <hr>
      <p><span>Alamat:</span> <?= htmlspecialchars($data['alamat'] ?? '-') ?></p>
      <p><span>No HP:</span> <?= htmlspecialchars($data['no_hp'] ?? '-') ?></p>
      <p><span>Tanggal Bergabung:</span> <?= htmlspecialchars($data['tanggal_bergabung'] ?? '-') ?></p>
    </div>

    <div class="btn-group">
      <a href="edit_profil.php" class="btn-custom">✏️ Edit Profil</a>
      <a href="../index.php" class="btn-custom">🔙 Kembali</a>
      <a href="../logout.php" class="btn-danger">Logout</a>
    </div>
  </div>

  <!-- Animate.css CDN -->
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
  />

</body>
</html>
