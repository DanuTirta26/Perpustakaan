<?php
// Pastikan session sudah dimulai di awal setiap file yang membutuhkan session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Fungsi untuk memeriksa apakah pengguna sudah login.
 * @return bool True jika sudah login, False jika belum.
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Fungsi untuk memeriksa role pengguna yang sedang login.
 * @return string|null Role pengguna (e.g., 'admin', 'user') atau null jika belum login.
 */
function get_user_role() {
    return $_SESSION['role'] ?? null;
}

/**
 * Fungsi untuk mengarahkan pengguna ke halaman login jika belum login.
 * Opsional: bisa ditambahkan parameter untuk mengarahkan ke halaman tertentu.
 */
function require_login() {
    if (!is_logged_in()) {
        header('Location: ../login.php'); // Sesuaikan path jika dipanggil dari subfolder
        exit;
    }
}

/**
 * Fungsi untuk memeriksa role dan mengarahkan jika tidak sesuai.
 * @param string $required_role Role yang dibutuhkan (e.g., 'admin', 'user').
 * @param string $redirect_page Halaman untuk dialihkan jika role tidak sesuai.
 */
function require_role($required_role, $redirect_page = '../index.php') {
    if (!is_logged_in() || get_user_role() !== $required_role) {
        header('Location: ' . $redirect_page);
        exit;
    }
}

/**
 * Fungsi untuk melakukan proses logout.
 */
function logout() {
    session_unset();   // Hapus semua variabel sesi
    session_destroy(); // Hancurkan sesi
    // Hapus cookie sesi juga
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    header('Location: index.php'); // Arahkan ke halaman utama setelah logout
    exit;
}