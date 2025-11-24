<!DOCTYPE html>
<html lang="es" class="register-page">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sistema de Inventario</title>
    <!-- Bootstrap CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <link href="<?php echo base_url('assets/css/forms/forms.css'); ?>" rel="stylesheet">
</head>
<body class="register-page">
    <div class="container-fluid">   
        <div class="register-container">
            <div class="register-header">
                <h1>Registro de usuario</h1>
                <p class="mb-0">Completa el formulario para registrarte</p>
            </div>
            
            <div class="register-body">
                    
                    <?php if(isset($error) && $error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if(isset($message) && $message): ?>
                        <div class="alert alert-success"><?php echo $message; ?></div>
                    <?php endif; ?>
                    
                    <form action="<?php echo site_url('registro/procesar'); ?>" method="post" class="needs-validation" novalidate>
                        <div class="row g-4">
                            <!-- Primera Columna - Información de Acceso -->
                            <div class="col-lg-3.5">
                                <div class="form-section">
                                    <h5><i class="bi-shield-lock me-2"></i>Información de Acceso</h5>
                                    
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi-envelope"></i></span>
                                            <input type="email" class="form-control" id="email" name="email" placeholder="tucorreo@ejemplo.com" required>
                                        </div>
                                        <div class="invalid-feedback">
                                            Por favor ingrese un correo electrónico válido.
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi-lock"></i></span>
                                            <input type="password" class="form-control" id="password" name="password" placeholder="•••••••" required minlength="6">
                                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#password">
                                                <i class="bi-eye"></i>
                                            </button>
                                        </div>
                                        <div class="invalid-feedback">
                                            La contraseña debe tener al menos 6 caracteres.
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi-lock"></i></span>
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="•••••••" required>
                                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#confirm_password">
                                                <i class="bi-eye"></i>
                                            </button>
                                        </div>
                                        <div class="invalid-feedback">
                                            Las contraseñas deben coincidir.
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="rol" class="form-label">Rol <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                            <select class="form-select" id="rol" name="rol" required>
                                                <option value="">Seleccione un rol...</option>
                                                <option value="bodeguero">Bodeguero</option>
                                                <option value="jefe" disabled>Jefe (asignado por administrador)</option>
                                                <option value="administrador" disabled>Administrador (asignado por administrador)</option>
                                            </select>
                                        </div>
                                        <div class="form-text text-muted small">Solo se puede registrar como Bodeguero inicialmente.</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Segunda Columna - Información Personal -->
                            <div class="col-lg-3.5">
                                <div class="form-section">
                                    <h5><i class="bi-person me-2"></i>Información Personal</h5>
                                    
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="nombres" class="form-label">Nombres <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi-person"></i></span>
                                                    <input type="text" class="form-control" id="nombres" name="nombres" placeholder="Ej: Juan" required>
                                                </div>
                                                <div class="invalid-feedback">
                                                    Por favor ingrese sus nombres.
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="apellidos" class="form-label">Apellidos <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi-person"></i></span>
                                                    <input type="text" class="form-control" id="apellidos" name="apellidos" placeholder="Ej: Pérez" required>
                                                </div>
                                                <div class="invalid-feedback">
                                                    Por favor ingrese sus apellidos.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="tipo_documento" class="form-label">Tipo de Documento <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi-card-text"></i></span>
                                                    <select class="form-select" id="tipo_documento" name="tipo_documento" required>
                                                        <option value="">Seleccione...</option>
                                                        <option value="CC">Cédula de Ciudadanía</option>
                                                        <option value="CE">Cédula de Extranjería</option>
                                                        <option value="TI">Tarjeta de Identidad</option>
                                                        <option value="PAS">Pasaporte</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="numero_documento" class="form-label">N° Documento <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">#</span>
                                                    <input type="text" class="form-control" id="numero_documento" name="numero_documento" placeholder="Ej: 123456789" required>
                                                </div>
                                                <div class="invalid-feedback">
                                                    Por favor ingrese su número de documento.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi-calendar"></i></span>
                                            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label d-block">Género</label>
                                        <div class="btn-group w-100" role="group">
                                            <input type="radio" class="btn-check" name="genero" id="masculino" value="M" autocomplete="off">
                                            <label class="btn btn-outline-primary" for="masculino"><i class="bi-gender-male me-2"></i>Masculino</label>
                                            
                                            <input type="radio" class="btn-check" name="genero" id="femenino" value="F" autocomplete="off">
                                            <label class="btn btn-outline-primary" for="femenino"><i class="bi-gender-female me-2"></i>Femenino</label>
                                            
                                            <input type="radio" class="btn-check" name="genero" id="otro" value="O" autocomplete="off">
                                            <label class="btn btn-outline-primary" for="otro"><i class="bi-gender-ambiguous me-2"></i>Otro</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tercera Columna - Información de Contacto -->
                            <div class="col-lg-3.5">
                                <div class="form-section">
                                    <h5><i class="bi-book me-2"></i>Información de Contacto</h5>
                                    
                                    <div class="mb-3">
                                        <label for="telefono" class="form-label">Teléfono <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi-telephone"></i></span>
                                            <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="Ej: 3001234567" required>
                                        </div>
                                        <div class="invalid-feedback">
                                            Por favor ingrese su número de teléfono.
                                        </div>
                                    </div>
                                    
                                    <div class="gender-field">
                                        <label for="genero" class="form-label">Género</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                                            <select class="form-select" id="genero" name="genero">
                                                <option value="">Seleccione...</option>
                                                <option value="M">Masculino</option>
                                                <option value="F">Femenino</option>
                                                <option value="O">Otro</option>
                                                <option value="P">Prefiero no decirlo</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="marital-field">
                                        <label for="estado_civil" class="form-label">Estado Civil</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-heart"></i></span>
                                            <select class="form-select" id="estado_civil" name="estado_civil">
                                                <option value="">Seleccione...</option>
                                                <option value="S">Soltero(a)</option>
                                                <option value="C">Casado(a)</option>
                                                <option value="D">Divorciado(a)</option>
                                                <option value="V">Viudo(a)</option>
                                                <option value="U">Unión Libre</option>
                                            </select>
                                        </div>
                                    </div>
                                <div class="mb-3">
    <label for="direccion" class="form-label">Dirección <span class="text-danger">*</span></label>
    <div class="input-group">
        <span class="input-group-text"><i class="bi-geo-alt"></i></span>
        <input type="text" class="form-control" id="direccion" name="direccion" 
               placeholder="Ej: Calle 123 # 45 - 67" required>
    </div>
    <div class="invalid-feedback">
        Por favor ingrese su dirección.
    </div>
</div>
                            </div>
                        </div>

                         <!-- Botón de Registro -->
                        <div class="row mt-4">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary btn-lg btn-register">
                                    <i class="bi-person-plus me-2"></i>Crear cuenta
                                </button>
                        
                        <!-- Campos ocultos -->
                        <input type="hidden" name="id_estado" value="1">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility
            const togglePasswordButtons = document.querySelectorAll('.toggle-password');
            
            togglePasswordButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const passwordInput = document.querySelector(targetId);
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
            
            // Validación de contraseñas coincidentes
            const password = document.getElementById("password");
            const confirmPassword = document.getElementById("confirm_password");
            
            function validatePassword() {
                if (password.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity("Las contraseñas no coinciden");
                } else {
                    confirmPassword.setCustomValidity('');
                }
            }
            
            if (password && confirmPassword) {
                password.addEventListener('change', validatePassword);
                confirmPassword.addEventListener('keyup', validatePassword);
            }
            
            // Validación de formulario con Bootstrap
            (function () {
                'use strict'
                
                // Obtener todos los formularios que necesitan validación
                var forms = document.querySelectorAll('.needs-validation')
                
                // Bucle sobre ellos y evitar el envío
                Array.prototype.slice.call(forms)
                    .forEach(function (form) {
                        form.addEventListener('submit', function (event) {
                            if (!form.checkValidity()) {
                                event.preventDefault()
                                event.stopPropagation()
                            }
                            
                            form.classList.add('was-validated')
                        }, false)
                    })
            })()
            
            // Validación en tiempo real para campos requeridos
            const formInputs = document.querySelectorAll('.form-control, .form-select');
            
            formInputs.forEach(input => {
                input.addEventListener('input', function() {
                    if (this.checkValidity()) {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    } else {
                        this.classList.remove('is-valid');
                        if (this.value) {
                            this.classList.add('is-invalid');
                        }
                    }
                });
                
                // Efecto de foco
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('input-focused');
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('input-focused');
                });
            });
        });
    </script>
</body>
</html>
