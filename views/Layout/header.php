<header class="sticky-top">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">PHP Object</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse align-items-center" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/postes">Liste des postes</a>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <?php if (isset($_SESSION['user']) && !empty($_SESSION['user']['id'])): ?>
                        <?php if (in_array('ROLE_ADMIN', $_SESSION['user']['roles'])): ?>
                            <li class="nav-item">
                                <p class="navbar-text mb-0">User : <?= $_SESSION['user']['prenom'] ?> | </p>
                            </li>
                            <li class="nav-item ms-2">
                                <div class="btn-group">
                                    <a class="btn btn-light" href="/admin">
                                        Admin
                                    </a>
                                    <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="visually-hidden">Toggle Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdown-admin">
                                        <a class="dropdown-item" href="/admin/users">Users</a>
                                        <hr class="dropdown-divider">
                                        <a class="dropdown-item" href="/admin/postes">Postes</a>
                                    </div>
                                </div>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/user/profil">Profil</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-danger" href="/logout">Déconnexion</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="btn btn-outline-light" href="/login">Connexion</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>