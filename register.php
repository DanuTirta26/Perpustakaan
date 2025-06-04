<?php
session_start();
require_once 'config/koneksi.php'; // Koneksi ke database
require_once 'config/auth.php';    // Fungsi autentikasi (untuk redirect jika sudah login)

// Redirect jika user sudah login
if (is_logged_in()) {
    if (get_user_role() === 'admin') {
        header('Location: admin/dashboard.php');
        exit;
    } else {
        header('Location: index.php'); // User yang sudah login bisa langsung ke beranda
        exit;
    }
}

$success_message = '';
$error_message = '';

// Proses registrasi jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $nama_lengkap = $_POST['nama_lengkap'] ?? '';
    $email = $_POST['email'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $no_hp = $_POST['no_hp'] ?? '';

    // Validasi input
    if (empty($username) || empty($password) || empty($confirm_password) || empty($nama_lengkap) || empty($email) || empty($alamat) || empty($no_hp)) {
        $error_message = 'Semua field harus diisi.';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Konfirmasi password tidak cocok.';
    } elseif (strlen($password) < 6) {
        $error_message = 'Password minimal 6 karakter.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Format email tidak valid.';
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Cek apakah username atau email sudah terdaftar
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt_check->bind_param("ss", $username, $email);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $error_message = 'Username atau email sudah terdaftar. Silakan gunakan yang lain.';
            $stmt_check->close();
        } else {
            $stmt_check->close(); // Tutup statement cek sebelum statement insert

            // Mulai transaksi untuk memastikan kedua insert berhasil
            $conn->begin_transaction();
            try {
                // Insert ke tabel users
                $stmt_user = $conn->prepare("INSERT INTO users (username, password, nama_lengkap, email, role) VALUES (?, ?, ?, ?, 'user')");
                $stmt_user->bind_param("ssss", $username, $hashed_password, $nama_lengkap, $email);
                
                if (!$stmt_user->execute()) {
                    throw new Exception("Gagal mendaftar user: " . $stmt_user->error);
                }
                $user_id = $stmt_user->insert_id; // Dapatkan ID user yang baru saja dibuat
                $stmt_user->close();

                // Insert ke tabel anggota
                $tanggal_bergabung = date('Y-m-d'); // Tanggal hari ini
                $stmt_anggota = $conn->prepare("INSERT INTO anggota (user_id, alamat, no_hp, tanggal_bergabung) VALUES (?, ?, ?, ?)");
                $stmt_anggota->bind_param("isss", $user_id, $alamat, $no_hp, $tanggal_bergabung);
                
                if (!$stmt_anggota->execute()) {
                    throw new Exception("Gagal mendaftar anggota: " . $stmt_anggota->error);
                }
                $stmt_anggota->close();

                $conn->commit(); // Commit transaksi jika semua berhasil
                
                // REDIRECT KE HALAMAN LOGIN SETELAH REGISTRASI BERHASIL
                $_SESSION['registration_success'] = 'Registrasi berhasil! Silakan login dengan akun Anda.'; // Pesan sukses (opsional)
                header('Location: login.php');
                exit; // Penting untuk menghentikan eksekusi skrip
            } catch (Exception $e) {
                $conn->rollback(); // Rollback transaksi jika ada error
                $error_message = 'Registrasi gagal: ' . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Anggota Perpustakaan</title>
    <style>
        /* ... (CSS Anda yang sudah ada) ... */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .register-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }

        .register-container h2 {
            margin-bottom: 25px;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }

        .form-group input[type="text"],
        .form-group input[type="password"],
        .form-group input[type="email"],
        .form-group textarea {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 60px;
        }

        .btn {
            background-color: #28a745; /* Warna hijau untuk register */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 10px;
            box-sizing: border-box;
        }

        .btn:hover {
            background-color: #218838;
        }

        .btn-link {
            background: none;
            border: none;
            color: #007bff;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            margin-top: 15px;
            display: inline-block;
        }

        .btn-link:hover {
            text-decoration: underline;
        }

        .error-message {
            color: red;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .success-message {
            color: green;
            margin-bottom: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Registrasi Anggota Baru</h2>

        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap:</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="alamat">Alamat:</label>
                <textarea id="alamat" name="alamat" required><?= htmlspecialchars($_POST['alamat'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label for="no_hp">Nomor HP:</label>
                <input type="text" id="no_hp" name="no_hp" value="<?= htmlspecialchars($_POST['no_hp'] ?? '') ?>" required>
            </div>
            <button type="submit" class="btn">DAFTAR</button>
        </form>

        <a href="login.php" class="btn btn-link">Sudah punya akun? Login di sini.</a>
        <a href="index.php" class="btn btn-link">Kembali ke Beranda</a>
    </div>
</body>
</html>