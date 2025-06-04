<?php session_start(); ?>
<nav class="navbar navbar-expand-lg bg-dark fixed-top navbar-dark">
    <a class="navbar-brand text-white" href="index.php">libro digitale</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-between" id="navbarSupportedContent">
        <div></div>

        <!-- Search bar di tengah -->
        <form method="GET" action="search_results.php" class="navbar-search-form mx-auto position-relative">
            <input type="search" name="q" id="search-input" placeholder="Cari buku, penulis..." required autocomplete="off" />
            <button type="submit"><i class="fas fa-search"></i></button>
            <div id="search-preview" class="search-preview d-none"></div>
        </form>

        <!-- User menu -->
        <ul class="navbar-nav align-items-center">
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <li class="nav-item mr-2">
                    <a href="./user/profil.php" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-user"></i> Profile
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <strong class="text-warning"><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></strong>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <a class="dropdown-item" href="dashboard.php">Dashboard Admin</a>
                        <?php endif; ?>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php">Logout</a>
                    </div>
                </li>
            <?php else: ?>
                <li class="nav-item mr-2">
                    <a href="login.php" class="btn btn-outline-light btn-sm">Sign In</a>
                </li>
                <li class="nav-item mr-2">
                    <a href="register.php" class="btn btn-light btn-sm">Sign Up</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<style>
    .navbar-search-form {
        position: relative;
        width: 320px;
        max-width: 90vw;
        display: flex;
        align-items: center;
        background: #343a40;
        border-radius: 30px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        padding: 5px 15px;
    }

    .navbar-search-form input[type="search"] {
        border: none;
        outline: none;
        background: transparent;
        flex-grow: 1;
        font-size: 16px;
        padding: 8px 12px;
        color: #fff;
    }

    .navbar-search-form input[type="search"]::placeholder {
        color: #ccc;
    }

    .navbar-search-form button {
        background: none;
        border: none;
        color: #ccc;
        font-size: 18px;
        cursor: pointer;
        padding: 0 6px;
    }

    .navbar-search-form button:hover {
        color: #ffc107;
    }

    .navbar-search-form input[type="search"]:focus {
        background: #495057;
        box-shadow: 0 0 8px rgba(255, 193, 7, 0.6);
        color: #fff;
    }

    @media (max-width: 576px) {
        .navbar-search-form {
            width: 100%;
            margin: 8px 0;
        }
    }

    /* Search preview styling */
    .search-preview {
        position: absolute;
        top: 110%;
        left: 0;
        right: 0;
        background: #212529;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.5);
        z-index: 1000;
        padding: 8px;
        max-height: 250px;
        overflow-y: auto;
    }

    .preview-item {
        display: flex;
        align-items: center;
        padding: 6px;
        border-bottom: 1px solid #444;
        color: #fff;
        cursor: pointer;
    }

    .preview-item img {
        width: 40px;
        height: 60px;
        object-fit: cover;
        margin-right: 10px;
        border-radius: 4px;
    }

    .preview-item:hover {
        background-color: #343a40;
    }

    .d-none {
        display: none;
    }
</style>

<script>
    const searchInput = document.getElementById('search-input');
    const previewBox = document.getElementById('search-preview');

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.trim();

        if (query.length < 2) {
            previewBox.classList.add('d-none');
            previewBox.innerHTML = '';
            return;
        }

        fetch(`search_preview.php?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    previewBox.classList.add('d-none');
                    previewBox.innerHTML = '';
                    return;
                }

                previewBox.innerHTML = data.map(item => `
                    <div class="preview-item" onclick="window.location.href='detail_buku.php?id=${item.id}'">
                        <img src="${item.gambar}" alt="${item.judul}" />
                        <div>
                            <strong>${item.judul}</strong><br>
                            <small>${item.penulis}</small>
                        </div>
                    </div>
                `).join('');

                previewBox.classList.remove('d-none');
            });
    });
</script>
