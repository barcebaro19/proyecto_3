<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">
        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    <?= $this->session->userdata('nombre') ?: 'Administrador' ?>
                </span>
                <i class="fas fa-user-circle fa-lg"></i>
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in"
                aria-labelledby="userDropdown">
                <?php 
                $rol = $this->session->userdata('rol');
                $perfil_url = '#';
                if ($rol == 'administrador') $perfil_url = site_url('admin/perfil');
                elseif ($rol == 'jefe') $perfil_url = site_url('jefe/perfil');
                elseif ($rol == 'operario') $perfil_url = site_url('operario/perfil'); // Si existe, sino ocultar o redirigir a dashboard
                ?>
                <a class="dropdown-item" href="<?= $perfil_url ?>">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Perfil
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Cerrar Sesión
                </a>
            </div>
        </li>
    </ul>
</nav>
