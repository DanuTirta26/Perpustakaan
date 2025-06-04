<?php
session_start();
require_once 'config/koneksi.php'; // koneksi ke database
require_once 'config/auth.php';    // fungsi is_logged_in(), get_user_role()

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
            // Login sukses, set session
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['id'];
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
} else {
    // Akses langsung tanpa POST
    header('Location: login.php');
    exit;
}
