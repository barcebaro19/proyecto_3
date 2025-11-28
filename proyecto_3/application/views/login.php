<!DOCTYPE html>
<html lang="es" class="login-page">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Iniciar Sesión - Sistema de Inventario</title>
    
    <!-- Bootstrap CSS -->
    <link href="/proyecto_3/assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="/proyecto_3/assets/css/all.min.css" rel="stylesheet">
    <!-- Forms CSS -->
    <link href="/proyecto_3/assets/css/forms/forms.css" rel="stylesheet">
    
</head>
<body class="login-page">
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <h2>Sistema de Inventario</h2>
                <p class="text-muted">Ingresa tus credenciales</p>
            </div>
            
            <div class="login-content">
                <?php if(isset($error) && $error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <form action="<?php echo site_url('login/authenticate'); ?>" method="post" class="needs-validation" novalidate>
                    <div class="form-group mb-3">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" required 
                                   value="<?php echo isset($email) ? html_escape($email) : ''; ?>"
                                   placeholder="tucorreo@ejemplo.com">
                        </div>
                        <div class="invalid-feedback">
                            Por favor ingresa un correo electrónico válido.
                        </div>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required
                                   placeholder="Tu contraseña">
                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback">
                            La contraseña es requerida.
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
                            <label class="form-check-label" for="remember">Recordarme</label>
                        </div>
                        <a href="#" class="text-decoration-none small text-muted">¿Olvidaste tu contraseña?</a>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                        </button>
                        <a href="<?php echo site_url('registro'); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-user-plus me-2"></i>REGISTRATE
                        </a>
                    </div>
                    
                    <div class="text-center small text-muted mt-3">
                        ¿Olvidaste tu contraseña? <a href="<?php echo site_url('recuperar-contrasena'); ?>" class="text-decoration-none">Recuperar aquí</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="/proyecto_3/assets/js/jquery-3.6.0.min.js"></script>
    <script src="/proyecto_3/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility
            const togglePassword = document.querySelectorAll('.toggle-password');
            
            togglePassword.forEach(button => {
                button.addEventListener('click', function() {
                    const passwordInput = document.getElementById('password');
                    const icon = this.querySelector('i');
                    
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        passwordInput.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            });
            
            // Form validation
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            }
            
            // Remember me functionality
            const rememberCheckbox = document.getElementById('remember');
            const emailInput = document.getElementById('email');
            
            // Load saved email if exists
            if (localStorage.getItem('remember') === 'true' && localStorage.getItem('email')) {
                emailInput.value = localStorage.getItem('email');
                rememberCheckbox.checked = true;
            }
            
            // Save email when form is submitted and remember me is checked
            if (form && rememberCheckbox) {
                form.addEventListener('submit', function() {
                    if (rememberCheckbox.checked) {
                        localStorage.setItem('email', emailInput.value);
                        localStorage.setItem('remember', 'true');
                    } else {
                        localStorage.removeItem('email');
                        localStorage.removeItem('remember');
                    }
                });
            }
        });
    </script>
</body>
</html>
