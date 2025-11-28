<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Sistema de Inventario' ?></title>

    <!-- Bootstrap 5 -->
    <link href="<?= base_url('assets/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- SB Admin 2 CSS -->
    <link href="<?= base_url('assets/css/sb-admin-2.min.css') ?>" rel="stylesheet">
    
    <!-- Custom styles -->
    <link href="<?= base_url('assets/css/forms/forms.css') ?>" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Si el archivo local de SB Admin 2 no está disponible, usa el CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/css/sb-admin-2.min.css" rel="stylesheet" onerror="this.href='https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/css/sb-admin-2.min.css'">
    
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fc;
        }
        
        #wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }
        
        #sidebar-wrapper {
            min-width: 250px;
            max-width: 250px;
            min-height: 100vh;
            background: linear-gradient(180deg, #343a40 0%, #0d6efd  100%);
            color: white;
            transition: all 0.3s;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        
        #content-wrapper {
            width: 100%;
            overflow-x: hidden;
        }
        
        .sidebar-heading {
            padding: 0.875rem 1.25rem;
            font-size: 1.2rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.8rem 1.5rem;
            transition: all 0.3s;
        }
        
        .nav-link:hover, .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.1);
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 10px;
            text-align: center;
        }
        
        .navbar {
            padding: 0.5rem 1.5rem;
            background: #2c3e50 !important;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .navbar .navbar-nav .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.5rem 1rem;
        }
        
        .navbar .navbar-nav .nav-link:hover {
            color: white;
        }
        
        .main-content {
            padding: 20px;
            margin-top: 60px;
        }
        
        @media (max-width: 768px) {
            #sidebar-wrapper {
                margin-left: -250px;
            }
            #sidebar-wrapper.active {
                margin-left: 0;
                position: fixed;
                z-index: 9999;
                height: 100%;
            }
            #content-wrapper {
                min-width: 100%;
            }
        }
    </style>
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <div class="sidebar-heading">
                <i class="bi bi-shop me-2"></i>
                <span>Inventario</span>
            </div>
            <?php $this->load->view('admin/partials/sidebar'); ?>
        </div>
        
        <!-- Content Wrapper -->
        <div id="content-wrapper">
            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-dark">
                <div class="d-flex justify-content-between w-100">
                    <div>
                        <button class="btn btn-link d-md-none" id="sidebarToggle">
                            <i class="bi bi-list text-white"></i>
                        </button>
                    </div>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-2"></i>
                                <span class="d-none d-lg-inline">
                                    <?= $this->session->userdata('nombre') ?: 'Administrador' ?>
                                </span>
                            </a>
                        <!-- Dropdown - User Information -->
                        <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in"
                            aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="<?= site_url('admin/perfil') ?>">
                                <i class="bi bi-person me-2"></i>
                                Perfil
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Cerrar Sesión
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>
            <!-- End of Topbar -->

            <!-- Main Content -->
            <div id="content">
                <?php if ($this->session->flashdata('success')): ?>
                    <div class="container-fluid px-4 mt-3">
                        <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            <?= $this->session->flashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if ($this->session->flashdata('error')): ?>
                    <div class="container-fluid px-4 mt-3">
                        <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <?= $this->session->flashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                <?php endif; ?>
