<nav class="navbar navbar-expand-lg bg-white fixed-top">
    <a class="navbar-brand" href="index.php">libro digitale</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-between" id="navbarSupportedContent">
        <div></div>

        <!-- Search bar di tengah -->
        <form method="GET" action="search_results.php" class="navbar-search-form mx-auto">
            <input type="search" name="q" placeholder="Cari buku, penulis..." required autocomplete="off" />
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>

        <!-- User menu dan tombol Profil di kanan -->
        <ul class="navbar-nav align-items-center">
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <!-- Tombol Profil -->
                <li class="nav-item mr-2">
                    <a href="profile.php" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-user"></i> Profil
                    </a>
                </li>

                <!-- Dropdown User -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <strong class="text-danger"><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></strong>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <a class="dropdown-item" href="admin_dashboard.php">Dashboard Admin</a>
                        <?php endif; ?>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php">Logout</a>
                    </div>
                </li>
            <?php else: ?>
                <li class="nav-item mr-2">
                    <a href="login.php" class="btn btn-primary btn-sm">Sign In</a>
                </li>
                <li class="nav-item mr-2">
                    <a href="register.php" class="btn btn-outline-primary btn-sm">Sign Up</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<style>
    /* Search form styling */
    .navbar-search-form {
        position: relative;
        width: 320px;
        max-width: 90vw;
        display: flex;
        align-items: center;
        background: #f9f9f9;
        border-radius: 30px;
        box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);
        padding: 5px 15px;
        transition: box-shadow 0.3s ease, background 0.3s ease;
    }
    .navbar-search-form input[type="search"] {
        border: none;
        outline: none;
        background: transparent;
        flex-grow: 1;
        font-size: 16px;
        padding: 8px 12px;
        border-radius: 30px;
        transition: width 0.3s ease;
        color: #333;
    }
    .navbar-search-form input[type="search"]::placeholder {
        color: #aaa;
    }
    .navbar-search-form button {
        background: none;
        border: none;
        color: #888;
        font-size: 18px;
        cursor: pointer;
        outline: none;
        padding: 0 6px;
        transition: color 0.3s ease;
    }
    .navbar-search-form button:hover {
        color: #0056b3;
    }
    .navbar-search-form input[type="search"]:focus {
        background: #fff;
        box-shadow: 0 0 8px rgba(0, 123, 255, 0.5);
        color: #000;
    }
    /* Responsive fix */
    @media (max-width: 576px) {
        .navbar-search-form {
            width: 100%;
            margin: 8px 0;
        }
    }
</style>
