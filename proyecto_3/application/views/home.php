<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BodegaPro - Gestión de Inventario</title>
  <link href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets/css/bootstrap-icons.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets/css/forms/forms.css'); ?>" rel="stylesheet">
  <link href="assets/css/fonts/css2.css" rel="stylesheet">
  <script src="<?php echo base_url('assets/js/jquery-3.6.0.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets/js/bootstrap.bundle.min.js'); ?>"></script>
</head>
<body class="home-page">
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark justify-content-center">
    <div class="container d-flex justify-content-center">
      <a class="navbar-brand fs-3" href="#">
        <i class="bi bi-box-seam"></i>Sistema de Bodega
      </a>
    </div>
  </nav>

  <!-- Contenido Principal -->
  <div class="main-container">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-10">
          <div class="welcome-card">
            <div class="card-body text-center">
              <h1>Gestión de Inventario Inteligente</h1>
              <p class="lead-text">
                Bienvenido
                <br>
                la solución integral para la gestión de inventario de tu negocio. 
                Controla tus productos, existencias y movimientos de manera eficiente y en tiempo real.
              </p>

              <!-- Sección de Características -->
              <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                  <div class="feature-card h-100">
                    <div class="feature-icon mx-auto">
                      <i class="bi bi-box-seam"></i>
                    </div>
                    <h5 class="text-center">Gestión de Productos</h5>
                    <p class="text-center">Administra tu inventario de manera eficiente con nuestro sistema intuitivo y fácil de usar.</p>
                  </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                  <div class="feature-card h-100">
                    <div class="feature-icon mx-auto">
                      <i class="bi bi-graph-up"></i>
                    </div>
                    <h5 class="text-center">Reportes en Tiempo Real</h5>
                    <p class="text-center">Visualiza el estado de tu inventario con reportes detallados y análisis en tiempo real.</p>
                  </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                  <div class="feature-card h-100">
                    <div class="feature-icon mx-auto">
                      <i class="bi bi-people"></i>
                    </div>
                    <h5 class="text-center">Control de Accesos</h5>
                    <p class="text-center">Gestiona usuarios y permisos de forma segura según los roles de tu equipo de trabajo.</p>
                  </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                  <div class="feature-card h-100">
                    <div class="feature-icon mx-auto">
                      <i class="bi bi-arrow-left-right"></i>
                    </div>
                    <h5 class="text-center">Movimientos</h5>
                    <p class="text-center">Registra y realiza un seguimiento de todas las entradas y salidas de productos.</p>
                  </div>
                </div>
              </div>

              <!-- Botón de Acción Principal -->
              <div class="text-center mt-5 pt-3">
                <a href="<?php echo site_url('login'); ?>" class="btn btn-login text-white">
                  <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <script>
    // Asegurar que los iconos de las tarjetas tengan el mismo tamaño
    document.addEventListener('DOMContentLoaded', function() {
      const featureIcons = document.querySelectorAll('.feature-icon i');
      let maxWidth = 0;
      
      // Encontrar el ancho máximo
      featureIcons.forEach(icon => {
        icon.style.display = 'inline-block';
        maxWidth = Math.max(maxWidth, icon.offsetWidth);
      });
      
      // Aplicar el ancho máximo a todos los iconos
      featureIcons.forEach(icon => {
        icon.style.width = maxWidth + 'px';
        icon.style.textAlign = 'center';
      });
    });
  </script>
