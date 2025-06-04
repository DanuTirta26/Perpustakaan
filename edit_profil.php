<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

include __DIR__ . '/../config/koneksi.php';

$user_id = $_SESSION['user_id'];

$query = "
    SELECT 
        users.nama_lengkap,
        users.email,
        anggota.alamat,
        anggota.no_hp
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

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $email = trim($_POST['email']);
    $alamat = trim($_POST['alamat']);
    $no_hp = trim($_POST['no_hp']);

    if (!$nama_lengkap || !$email) {
        $error = 'Nama lengkap dan email harus diisi.';
    } else {
        // Cek email sudah dipakai user lain (kecuali user ini)
        $emailCheck = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $emailCheck->bind_param("si", $email, $user_id);
        $emailCheck->execute();
        $resEmailCheck = $emailCheck->get_result();

        if ($resEmailCheck->num_rows > 0) {
            $error = "Email sudah digunakan oleh pengguna lain.";
        } else {
            $query1 = "UPDATE users SET nama_lengkap = ?, email = ? WHERE id = ?";
            $stmt1 = $conn->prepare($query1);
            $stmt1->bind_param("ssi", $nama_lengkap, $email, $user_id);
            if (!$stmt1->execute()) {
                $error = "Gagal update data users: " . $stmt1->error;
            } else {
                $cek = $conn->prepare("SELECT id FROM anggota WHERE user_id = ?");
                $cek->bind_param("i", $user_id);
                $cek->execute();
                $resCek = $cek->get_result();

                if ($resCek->num_rows > 0) {
                    $query2 = "UPDATE anggota SET alamat = ?, no_hp = ? WHERE user_id = ?";
                    $stmt2 = $conn->prepare($query2);
                    $stmt2->bind_param("ssi", $alamat, $no_hp, $user_id);
                    if (!$stmt2->execute()) {
                        $error = "Gagal update data anggota: " . $stmt2->error;
                    }
                } else {
                    $tanggal_bergabung = date('Y-m-d');
                    $query2 = "INSERT INTO anggota (user_id, alamat, no_hp, tanggal_bergabung) VALUES (?, ?, ?, ?)";
                    $stmt2 = $conn->prepare($query2);
                    $stmt2->bind_param("isss", $user_id, $alamat, $no_hp, $tanggal_bergabung);
                    if (!$stmt2->execute()) {
                        $error = "Gagal tambah data anggota: " . $stmt2->error;
                    }
                }
            }
        }
    }

    // Jika tidak ada error, redirect dan hentikan script
    if (!$error) {
        header("Location: profil.php");
        exit();
    }

    // Jika error, perbarui $data supaya form menampilkan input user terbaru
    $data['nama_lengkap'] = $nama_lengkap;
    $data['email'] = $email;
    $data['alamat'] = $alamat;
    $data['no_hp'] = $no_hp;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit Profil</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    /* style sama seperti kode kamu sebelumnya */
    body {
      background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      color: #f5f5f5;
      overflow-y: auto;
      padding: 20px;
    }
    .container-edit {
      background: rgba(255, 255, 255, 0.08);
      border-radius: 15px;
      padding: 40px 35px;
      width: 100%;
      max-width: 480px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.25);
      opacity: 0;
      transform: translateY(30px);
      transition: opacity 0.7s ease, transform 0.7s ease;
    }
    .container-edit.visible {
      opacity: 1;
      transform: translateY(0);
    }
    h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #ffd700;
      text-shadow: 0 0 10px #ffd700aa;
      font-weight: 700;
    }
    label {
      font-weight: 600;
      color: #ffd700;
    }
    input.form-control,
    textarea.form-control {
      background-color: rgba(255, 255, 255, 0.15);
      border: none;
      border-bottom: 2px solid #ffd700;
      color: #f5f5f5;
      font-size: 1rem;
      padding-left: 10px;
      transition: border-color 0.3s ease;
      border-radius: 4px 4px 0 0;
    }
    input.form-control::placeholder,
    textarea.form-control::placeholder {
      color: #ddd;
    }
    input.form-control:focus,
    textarea.form-control:focus {
      background-color: rgba(255, 255, 255, 0.25);
      outline: none;
      border-color: #fff700;
      box-shadow: 0 0 8px #ffd700aa;
      color: #fff;
    }
    textarea.form-control {
      resize: vertical;
      min-height: 80px;
      padding-top: 8px;
    }
    .btn-save {
      margin-top: 20px;
      width: 100%;
      background: #ffd700;
      color: #1e3c72;
      font-weight: 700;
      padding: 12px;
      border: none;
      border-radius: 30px;
      font-size: 1.1rem;
      cursor: pointer;
      box-shadow: 0 5px 15px rgba(255, 215, 0, 0.6);
      transition: background 0.3s ease, transform 0.2s ease;
    }
    .btn-save:hover {
      background: #e6c200;
      box-shadow: 0 8px 20px rgba(230, 194, 0, 0.8);
      transform: scale(1.05);
      color: #0b2347;
    }
    .btn-save:active {
      transform: scale(0.95);
    }
    .btn-back {
      display: block;
      margin-bottom: 25px;
      text-align: center;
      color: #ffd700;
      text-decoration: none;
      font-weight: 600;
      letter-spacing: 0.05em;
      transition: color 0.3s ease;
    }
    .btn-back:hover {
      color: #fff700;
      text-decoration: underline;
    }
    .alert-error {
      background: #a32f2f;
      color: #ffdcdc;
      padding: 12px 20px;
      border-radius: 10px;
      margin-bottom: 20px;
      font-weight: 600;
      box-shadow: 0 0 10px #a32f2faa;
      text-align: center;
    }
  </style>
</head>
<body>

<div class="container-edit" id="containerEdit">
  <h2>Edit Profil</h2>

  <?php if ($error): ?>
    <div class="alert-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <a href="../index.php" class="btn-back">← Kembali ke Beranda</a>

  <form method="post" novalidate>
    <div class="mb-3">
      <label for="nama_lengkap">Nama Lengkap</label>
      <input
        type="text"
        id="nama_lengkap"
        name="nama_lengkap"
        class="form-control"
        required
        value="<?= htmlspecialchars($data['nama_lengkap']) ?>"
        placeholder="Masukkan nama lengkap"
      />
    </div>
    <div class="mb-3">
      <label for="email">Email</label>
      <input
        type="email"
        id="email"
        name="email"
        class="form-control"
        required
        value="<?= htmlspecialchars($data['email']) ?>"
        placeholder="Masukkan email aktif"
      />
    </div>
    <div class="mb-3">
      <label for="alamat">Alamat</label>
      <textarea
        id="alamat"
        name="alamat"
        class="form-control"
        placeholder="Masukkan alamat lengkap"
      ><?= htmlspecialchars($data['alamat'] ?? '') ?></textarea>
    </div>
    <div class="mb-3">
      <label for="no_hp">No HP</label>
      <input
        type="tel"
        id="no_hp"
        name="no_hp"
        class="form-control"
        pattern="[0-9+]+"
        value="<?= htmlspecialchars($data['no_hp'] ?? '') ?>"
        placeholder="Contoh: 08123456789"
      />
    </div>

    <button type="submit" class="btn-save">Simpan</button>
  </form>
</div>

<script>
  document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById('containerEdit');
    setTimeout(() => {
      container.classList.add('visible');
    }, 150);

    const btnSave = document.querySelector('.btn-save');
    btnSave.addEventListener('mousedown', () => {
      btnSave.style.transform = 'scale(0.95)';
    });
    btnSave.addEventListener('mouseup', () => {
      btnSave.style.transform = 'scale(1)';
    });
    btnSave.addEventListener('mouseout', () => {
      btnSave.style.transform = 'scale(1)';
    });
  });
</script>

</body>
</html>
