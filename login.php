<?php
session_start();
require_once 'config/koneksi.php';

// Proses login POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = 'Username dan password harus diisi.';
        header('Location: login.php');
        exit;
    }

    $stmt = $conn->prepare("SELECT id, username, password, nama_lengkap, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['id'];         // PENTING: konsisten user_id
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'];

            // Redirect sesuai role
            if ($user['role'] === 'admin') {
                header('Location: admin/dashboard.php');
                exit;
            } else {
                header('Location: index.php');
                exit;
            }
        } else {
            $_SESSION['login_error'] = 'Username atau password salah.';
            header('Location: login.php');
            exit;
        }
    } else {
        $_SESSION['login_error'] = 'Username atau password salah.';
        header('Location: login.php');
        exit;
    }
    $stmt->close();
}

// Jika sudah login, langsung redirect ke halaman sesuai role
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header('Location: admin/dashboard.php');
        exit;
    } else {
        header('Location: index.php');
        exit;
    }
}

// Ambil pesan error jika ada
$error_message = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login Perpustakaan</title>
    <style>
        /* style tetap sama seperti kode kamu */
    </style>
</head>
<body>
<div class="login-container">
    <h2>Login Perpustakaan</h2>
    <?php if ($error_message): ?>
        <p class="error-message"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>
    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required />
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required />
        </div>
        <button type="submit" class="btn">LOGIN</button>
    </form>
    <a href="register.php" class="btn btn-link">SIGN-UP</a>
    <a href="index.php" class="btn btn-link">Kembali ke Beranda</a>
</div>
</body>
</html>
