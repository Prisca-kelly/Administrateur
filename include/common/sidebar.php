<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="accueil.php" class="app-brand-link">
            <span class="app-brand-text text-center">
                <img src="assets/img/view.png" alt="logo" class="mt-3 mb-2" style="width: 170px;">
            </span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <li class="menu-item <?= $page == 'Accueil' ? 'active' : ''; ?> ">
            <a href="accueil.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Acceuil</div>
            </a>
        </li>
        <li class="menu-item <?= $page == 'Utilisateur' ? 'active' : ''; ?> ">
            <a href="utilisateur.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                <div data-i18n="Basic">Utilisateur</div>
            </a>
        </li>
        <li class="menu-item <?= $page == 'Client' ? 'active' : ''; ?>">
            <a href="client.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-lock-open-alt"></i>
                <div data-i18n="Basic">Clients</div>
            </a>
        </li>
        <li class="menu-item <?= $page == 'Blog' ? 'active' : ''; ?> ">
            <a href="blog.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Blog</div>
            </a>
        </li>
        <li class="menu-item <?= $page == 'Hotel' ? 'active' : ''; ?> ">
            <a href="hotel.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Hotel</div>
            </a>
        </li>
        <li class="menu-item <?= $page == 'Destination' ? 'active' : ''; ?> ">
            <a href="destination.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Destination</div>
            </a>
        </li>
        <li class="menu-item <?= $page == 'Billetterie' ? 'active' : ''; ?> ">
            <a href="billetterie.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-table"></i>
                <div data-i18n="Tables">Billetterie</div>
            </a>
        </li>
    </ul>
</aside>