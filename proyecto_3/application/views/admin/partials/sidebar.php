<!-- Sidebar Navigation -->
<div class="sidebar-navigation">
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?= $this->uri->segment(2) == 'dashboard' ? 'active' : '' ?>" href="<?= site_url('admin/dashboard') ?>">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $this->uri->segment(2) == 'productos' ? 'active' : '' ?>" href="<?= site_url('admin/productos') ?>">
                <i class="bi bi-box-seam"></i>
                <span>Productos</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $this->uri->segment(2) == 'referencias' ? 'active' : '' ?>" href="<?= site_url('admin/referencias') ?>">
                <i class="bi bi-tag"></i>
                <span>Referencias</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $this->uri->segment(2) == 'categorias' ? 'active' : '' ?>" href="<?= site_url('admin/categorias') ?>">
                <i class="bi bi-tags"></i>
                <span>Categorías</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $this->uri->segment(2) == 'movimientos' ? 'active' : '' ?>" href="<?= site_url('admin/movimientos') ?>">
                <i class="bi bi-arrow-left-right"></i>
                <span>Movimientos</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $this->uri->segment(2) == 'usuarios' ? 'active' : '' ?>" href="<?= site_url('admin/usuarios') ?>">
                <i class="bi bi-people"></i>
                <span>Usuarios</span>
            </a>
        </li>
        <li class="nav-item mt-auto">
            <a class="nav-link text-white" href="<?= site_url('login/logout') ?>">
                <i class="bi bi-box-arrow-right"></i>
                <span>Cerrar Sesión</span>
            </a>
        </li>
    </ul>
</div>

<style>
.sidebar-navigation {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 60px);
    padding: 1rem 0;
}

.sidebar-navigation .nav-item {
    margin: 0.2rem 0;
}

.sidebar-navigation .nav-link {
    color: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    padding: 0.7rem 1.5rem;
    border-radius: 0 30px 30px 0;
    margin-right: 1rem;
    transition: all 0.3s;
}

.sidebar-navigation .nav-link:hover,
.sidebar-navigation .nav-link.active {
    background-color: rgba(255, 255, 255, 0.2);
    color: white;
    text-decoration: none;
}

.sidebar-navigation .nav-link i {
    font-size: 1.1rem;
    margin-right: 12px;
    width: 24px;
    text-align: center;
}

.sidebar-navigation .nav-link span {
    font-weight: 500;
}

/* Efecto de resaltado al pasar el mouse */
.sidebar-navigation .nav-link::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 4px;
    background-color: white;
    transform: scaleY(0);
    transition: transform 0.2s, width 0.3s cubic-bezier(1, 0, 0, 1) 0.1s;
}

.sidebar-navigation .nav-link:hover::before,
.sidebar-navigation .nav-link.active::before {
    transform: scaleY(1);
}

/* Responsive */
@media (max-width: 768px) {
    .sidebar-navigation {
        padding: 0.5rem 0;
    }
    
    .sidebar-navigation .nav-link {
        padding: 0.6rem 1rem;
        margin-right: 0.5rem;
    }
    
    .sidebar-navigation .nav-link i {
        font-size: 1.2rem;
        margin-right: 0.8rem;
    }
    
    .sidebar-navigation .nav-link span {
        font-size: 0.9rem;
    }
}
</style>
