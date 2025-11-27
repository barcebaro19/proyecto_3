<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKIPPER - Gestión de Inventario Inteligente</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="<?php echo base_url('assets/css/forms/home-neon.css'); ?>" rel="stylesheet">
</head>
<body>

    <header>
        <a href="#inicio" class="logo-skkiper">SKIPPER</a>

        <nav>
            <ul>
                <li><a href="#inicio">Inicio</a></li>
                <li><a href="#galeria">Galería</a></li>
                <li><a href="#contacto">Contacto</a></li>
            </ul>
        </nav>

        <a href="<?php echo site_url('login'); ?>" class="login-button-header">
            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
        </a>
    </header>

    <section id="inicio" class="page-section">
        <div class="main-container">

            <h1>Gestión de Inventario Inteligente</h1>
            <p class="welcome-text">
                La solución integral para la gestión de inventario de tu negocio. 
                Controla tus productos, existencias y movimientos de manera eficiente y en tiempo real con Skipper.
            </p>

            <div class="modules-grid">

                <div class="module-card">
                    <i class="fas fa-box"></i>
                    <h3>Gestión de Productos</h3>
                    <p>Administra tu inventario de manera eficiente con un sistema intuitivo y fácil de usar.</p>
                </div>

                <div class="module-card">
                    <i class="fas fa-chart-line"></i>
                    <h3>Reportes en Tiempo Real</h3>
                    <p>Visualiza el estado de tu inventario con reportes detallados y analíticos en tiempo real.</p>
                </div>

                <div class="module-card">
                    <i class="fas fa-users-cog"></i>
                    <h3>Control de Accesos</h3>
                    <p>Gestiona usuarios y permisos de forma segura según los roles de tu equipo de trabajo.</p>
                </div>

                <div class="module-card">
                    <i class="fas fa-exchange-alt"></i>
                    <h3>Movimientos</h3>
                    <p>Registra y realiza un seguimiento de todos los entradas y salidas de productos.</p>
                </div>
                
            </div>
        </div>
    </section>

   <section id="galeria" class="page-section">
    <h2 class="section-title">productos</h2>
    
    <div class="galeria-contenedor">
        
        <div class="galeria-item">
            <img src="assets/imagen/imagen1.png" alt="Dashboard Principal de Skipper">
            <div class="overlay">Dashboard Principal</div>
        </div>

        <div class="galeria-item">
            <img src="assets/imagen/imagen2.png" alt="Interfaz de Reportes de Skipper">
            <div class="overlay">Vista de Reportes</div>
        </div>

        <div class="galeria-item">
            <img src="assets/imagen/imagen3.png" alt="Vista de Gestión de Productos">
            <div class="overlay">Gestión de Productos</div>
        </div>

    </div>
</section>

    <section id="contacto" class="page-section">
        <h2 class="section-title">📞 Contáctanos</h2>
        <div class="content-placeholder">
            <p style="margin-bottom: 20px; color: #AAAAAA;">Síguenos en nuestras redes sociales</p>
            <div class="social-icons-container">
                <a href="URL_TU_FACEBOOK" target="_blank" class="icon facebook">
                    <div class="tooltip">Facebook</div>
                    <span><i class="fab fa-facebook-f"></i></span>
                </a>

                <a href="https://wa.me/NUMERO_DE_TELEFONO" target="_blank" class="icon whatsapp">
                    <div class="tooltip">WhatsApp</div>
                    <span><i class="fab fa-whatsapp"></i></span>
                </a>

                <a href="URL_TU_INSTAGRAM" target="_blank" class="icon instagram">
                    <div class="tooltip">Instagram</div>
                    <span><i class="fab fa-instagram"></i></span>
                </a>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> SKIPPER - Sistema de Gestión de Inventario</p>
    </footer>

</body>
</html>
