<?php
$is_dashboard = ($this->uri->segment(2) == 'dashboard' || $this->uri->segment(2) == '');
$sidebar_class = $is_dashboard ? 'sidebar-static' : '';
$rol = $this->session->userdata('rol');

// Definir menú según el rol
$menu_items = [];

if ($rol == 'administrador') {
    $menu_items = [
        ['url' => 'admin/dashboard', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard', 'segment' => 'dashboard'],
        ['url' => 'admin/productos', 'icon' => 'bi-box-seam', 'label' => 'Productos', 'segment' => 'productos'],
        ['url' => 'admin/inventario_matriz', 'icon' => 'bi-grid-3x3', 'label' => 'Matriz (Excel)', 'segment' => 'inventario_matriz'],
        ['url' => 'admin/referencias', 'icon' => 'bi-tag', 'label' => 'Referencias', 'segment' => 'referencias'],
        ['url' => 'admin/categorias', 'icon' => 'bi-tags', 'label' => 'Categorías', 'segment' => 'categorias'],
        ['url' => 'admin/movimientos', 'icon' => 'bi-arrow-left-right', 'label' => 'Movimientos', 'segment' => 'movimientos'],
        ['url' => 'admin/usuarios', 'icon' => 'bi-people', 'label' => 'Usuarios', 'segment' => 'usuarios'],
    ];
} elseif ($rol == 'jefe') {
    $menu_items = [
        ['url' => 'jefe/dashboard', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard', 'segment' => 'dashboard'],
        ['url' => 'jefe/productos', 'icon' => 'bi-box-seam', 'label' => 'Productos', 'segment' => 'productos'],
        ['url' => 'jefe/reportes', 'icon' => 'bi-file-earmark-bar-graph', 'label' => 'Reportes', 'segment' => 'reportes'],
        ['url' => 'jefe/perfil', 'icon' => 'bi-person-circle', 'label' => 'Mi Perfil', 'segment' => 'perfil'],
    ];
} elseif ($rol == 'operario') {
    $menu_items = [
        ['url' => 'operario/dashboard', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard', 'segment' => 'dashboard'],
        ['url' => 'operario/tabla_principal', 'icon' => 'bi-grid-3x3', 'label' => 'Tabla Principal', 'segment' => 'tabla_principal'],
        ['url' => 'operario/productos', 'icon' => 'bi-box-seam', 'label' => 'Productos', 'segment' => 'productos'],
        ['url' => 'operario/movimientos', 'icon' => 'bi-arrow-left-right', 'label' => 'Movimientos', 'segment' => 'movimientos'],
    ];
}
?>

<!-- Sidebar Navigation -->
<div class="sidebar-navigation <?= $sidebar_class ?>">
    <!-- Sidebar Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center text-white text-decoration-none py-3" href="<?= site_url($rol . '/dashboard') ?>">
        <div class="sidebar-brand-icon">
            <i class="bi bi-box-seam fs-2"></i>
        </div>
        <div class="sidebar-brand-text mx-3 fw-bold">Inventario</div>
    </a>

    <hr class="sidebar-divider my-0 mx-3 bg-white opacity-25">

    <ul class="nav flex-column mt-3">
        <?php foreach ($menu_items as $item): ?>
            <li class="nav-item">
                <a class="nav-link <?= ($this->uri->segment(2) == $item['segment']) ? 'active' : '' ?>" href="<?= site_url($item['url']) ?>">
                    <i class="bi <?= $item['icon'] ?>"></i>
                    <span><?= $item['label'] ?></span>
                </a>
            </li>
        <?php endforeach; ?>

        <li class="nav-item mt-auto mb-3">
            <a class="nav-link text-white" href="<?= site_url('login/logout') ?>">
                <i class="bi bi-box-arrow-right"></i>
                <span>Cerrar Sesión</span>
            </a>
        </li>
    </ul>
</div>

<style>
/* Sidebar Container */
.sidebar-navigation {
    display: flex;
    flex-direction: column;
    height: 100vh;
    width: 80px; /* Minimized width by default */
    transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow-x: hidden;
    /* Dark Blue Gradient */
    background: linear-gradient(180deg, #0a192f 0%, #112240 100%);
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1050;
    box-shadow: 4px 0 15px rgba(0,0,0,0.25);
}

/* Static State (Dashboard) */
.sidebar-navigation.sidebar-static {
    width: 250px;
}

/* Expand on Hover (only if not static) */
.sidebar-navigation:not(.sidebar-static):hover {
    width: 250px;
}

/* Brand Section */
.sidebar-brand {
    height: 4.375rem;
    text-decoration: none;
    font-size: 1.2rem;
    white-space: nowrap;
    transition: all 0.3s;
}

.sidebar-brand-text {
    display: none;
    opacity: 0;
    transition: opacity 0.3s;
}

/* Show text on hover OR if static */
.sidebar-navigation:hover .sidebar-brand-text,
.sidebar-navigation.sidebar-static .sidebar-brand-text {
    display: block;
    opacity: 1;
}

/* Nav Items */
.sidebar-navigation .nav-item {
    width: 100%;
}

/* Nav Links */
.sidebar-navigation .nav-link {
    color: rgba(255, 255, 255, 0.7) !important;
    display: flex;
    align-items: center;
    padding: 1rem 1.5rem;
    white-space: nowrap;
    transition: all 0.2s;
    height: 56px;
    position: relative;
}

.sidebar-navigation .nav-link:hover,
.sidebar-navigation .nav-link.active {
    color: #fff !important;
    background-color: rgba(255, 255, 255, 0.1);
    font-weight: 600;
}

.sidebar-navigation .nav-link.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 4px;
    background-color: #64ffda; /* Cyan accent for dark blue */
    border-radius: 0 4px 4px 0;
    box-shadow: 0 0 10px rgba(100, 255, 218, 0.5);
}

/* Icons */
.sidebar-navigation .nav-link i {
    font-size: 1.25rem;
    min-width: 2rem;
    text-align: center;
    margin-right: 1rem;
    color: #8892b0; /* Muted blue-grey for icons */
    transition: color 0.2s;
}

.sidebar-navigation .nav-link:hover i,
.sidebar-navigation .nav-link.active i {
    color: #64ffda; /* Cyan accent on active/hover */
}

/* Text Labels */
.sidebar-navigation .nav-link span {
    font-size: 0.95rem;
    opacity: 0;
    display: none;
    transition: opacity 0.3s;
}

/* Show text on hover OR if static */
.sidebar-navigation:hover .nav-link span,
.sidebar-navigation.sidebar-static .nav-link span {
    display: inline-block;
    opacity: 1;
}

/* Divider */
.sidebar-divider {
    margin: 0 1rem 1rem;
}

/* Responsive */
@media (max-width: 768px) {
    .sidebar-navigation {
        width: 0;
        padding: 0;
    }
    
    .sidebar-navigation.active {
        width: 250px;
    }
    
    .sidebar-navigation.active .nav-link span,
    .sidebar-navigation.active .sidebar-brand-text {
        display: block;
        opacity: 1;
    }
}
</style>
