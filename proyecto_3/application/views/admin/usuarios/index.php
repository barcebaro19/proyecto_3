<div class="container-fluid py-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1 text-gray-800 fw-bold">
                    <i class="fas fa-users me-2 text-primary"></i><?= $title ?>
                </h1>
                <p class="text-muted mb-0">Administra y gestiona los usuarios del sistema</p>
            </div>
            <!-- Barra de búsqueda y botón de nuevo usuario movidos al encabezado de la tabla -->
        </div>

        <!-- Alert Messages -->
        <?php if($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= $this->session->flashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <?php if($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?= $this->session->flashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <!-- Users Table -->
        <div class="card shadow-sm mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-users me-2"></i>Lista de Usuarios
                    <span class="badge bg-primary ms-2"><?= count($usuarios) ?? 0 ?></span>
                </h6>
                <div class="d-flex">
                    <div class="input-group me-2" style="width: 250px;">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Buscar usuario...">
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoUsuarioModal">
                        <i class="fas fa-plus me-2"></i>Nuevo Usuario
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle table-row-dashed fs-6 gy-5" id="usersTable">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4" width="5%">#</th>
                                <th>Usuario</th>
                                <th>Información de Contacto</th>
                                <th>Detalles</th>
                                <th class="text-center">Rol</th>
                                <th class="text-center">Estado</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($usuarios)): ?>
                                <?php 
                                $counter = 1;
                                foreach ($usuarios as $usuario): 
                                    $avatarBg = 'bg-' . ['primary', 'success', 'info', 'warning', 'danger', 'dark'][array_rand(['primary', 'success', 'info', 'warning', 'danger', 'dark'])];
                                    // Formatear fecha de nacimiento
                                    $fecha_nacimiento = !empty($usuario->fecha_nacimiento) ? date('d/m/Y', strtotime($usuario->fecha_nacimiento)) : 'No especificada';
                                    // Determinar el estado del usuario
                                    $estadoClass = $usuario->id_estado == 1 ? 'success' : 'danger';
                                    $estadoText = $usuario->id_estado == 1 ? 'Activo' : 'Inactivo';
                                    // Obtener nombre del rol
                                    $rolNombre = !empty($usuario->nombre_rol) ? $usuario->nombre_rol : (isset($roles[$usuario->id_rol]) ? $roles[$usuario->id_rol] : 'Sin rol');
                                ?>
                                <tr class="position-relative border-top">
                                    <td class="ps-4 align-middle">
                                        <div class="text-muted"><?= str_pad($counter++, 2, '0', STR_PAD_LEFT) ?></div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-40px symbol-circle me-4">
                                                <span class="symbol-label fs-6 fw-bold <?= $avatarBg ?> text-white">
                                                    <?= strtoupper(substr($usuario->nombre, 0, 1) . substr($usuario->apellido, 0, 1)) ?>
                                                </span>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <a href="#" class="text-gray-800 text-hover-primary fw-bold mb-1"><?= htmlspecialchars($usuario->nombre . ' ' . $usuario->apellido) ?></a>
                                                <span class="text-muted"><?= htmlspecialchars($usuario->tipo_documento . ': ' . $usuario->numero_documento) ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex flex-column">
                                            <span class="text-gray-700 fw-semibold d-flex align-items-center">
                                                <i class="fas fa-envelope text-muted me-2 fs-6"></i>
                                                <?= htmlspecialchars($usuario->correo) ?>
                                            </span>
                                            <span class="text-muted d-flex align-items-center mt-1">
                                                <i class="fas fa-phone text-muted me-2 fs-6"></i>
                                                <?= !empty($usuario->telefono) ? htmlspecialchars($usuario->telefono) : '<span class="text-muted">Sin teléfono</span>' ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex flex-column">
                                            <span class="text-muted">
                                                <i class="fas fa-birthday-cake me-2"></i>
                                                <?= $fecha_nacimiento ?>
                                            </span>
                                            <span class="text-muted mt-1">
                                                <i class="fas fa-map-marker-alt me-2"></i>
                                                <?= !empty($usuario->direccion) ? htmlspecialchars(substr($usuario->direccion, 0, 20) . (strlen($usuario->direccion) > 20 ? '...' : '')) : 'Sin dirección' ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="badge bg-primary text-white fw-bold py-2 px-3">
                                            <i class="fas fa-user me-1"></i>
                                            <?= htmlspecialchars($rolNombre) ?>
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="badge bg-<?= $estadoClass ?> text-white fw-bold py-2 px-3">
                                            <i class="fas fa-<?= $usuario->id_estado == 1 ? 'check-circle' : 'times-circle' ?> me-1"></i>
                                            <?= $estadoText ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group" role="group" aria-label="Acciones usuario">
                                            <a href="<?= site_url('admin/ver_usuario/' . $usuario->id_usuario) ?>" 
                                               class="btn btn-outline-primary btn-sm btn-ver" 
                                               data-id="<?= $usuario->id_usuario ?>">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= site_url('admin/editar_usuario/' . $usuario->id_usuario) ?>" 
                                               class="btn btn-outline-secondary btn-sm btn-editar" 
                                               data-id="<?= $usuario->id_usuario ?>">
                                                <i class="far fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-outline-danger btn-sm btn-eliminar" 
                                                    data-id="<?= $usuario->id_usuario ?>">
                                                <i class="far fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="bg-light rounded-circle p-4 mb-3">
                                            <i class="fas fa-users text-muted" style="font-size: 2.5rem;"></i>
                                        </div>
                                        <h5 class="fw-bold mb-2">No se encontraron usuarios</h5>
                                        <p class="text-muted mb-0">No hay usuarios registrados en el sistema</p>
                                        <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#nuevoUsuarioModal">
                                            <i class="fas fa-plus me-2"></i>Agregar Usuario
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Nuevo Usuario -->
<div class="modal fade" id="nuevoUsuarioModal" tabindex="-1" aria-labelledby="nuevoUsuarioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="nuevoUsuarioModalLabel">
                    <i class="fas fa-user-plus me-2"></i>Nuevo Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="usuarioForm" class="row g-3 needs-validation" novalidate>
                    <div class="col-md-4">
                        <label for="tipo_documento" class="form-label text-muted">Tipo de documento</label>
                        <select name="tipo_documento" id="tipo_documento" class="form-select" required>
                            <option value="" selected disabled>Seleccione...</option>
                            <option value="CC">Cédula de ciudadanía</option>
                            <option value="TI">Tarjeta de identidad</option>
                            <option value="CE">Cédula de extranjería</option>
                            <option value="PA">Pasaporte</option>
                        </select>
                        <div class="invalid-feedback">Seleccione un tipo de documento.</div>
                    </div>

                    <div class="col-md-4">
                        <label for="numero_documento" class="form-label text-muted">Número de documento</label>
                        <input type="text" name="numero_documento" id="numero_documento" class="form-control" required>
                        <div class="invalid-feedback">Ingrese el número de documento.</div>
                    </div>

                    <div class="col-md-4">
                        <label for="telefono" class="form-label text-muted">Teléfono</label>
                        <input type="text" name="telefono" id="telefono_nuevo" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label for="nombre_nuevo" class="form-label text-muted">Nombre</label>
                        <input type="text" name="nombre" id="nombre_nuevo" class="form-control" required>
                        <div class="invalid-feedback">Ingrese el nombre.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="apellido_nuevo" class="form-label text-muted">Apellido</label>
                        <input type="text" name="apellido" id="apellido_nuevo" class="form-control" required>
                        <div class="invalid-feedback">Ingrese el apellido.</div>
                    </div>

                    <div class="col-md-4">
                        <label for="fecha_nacimiento_nuevo" class="form-label text-muted">Fecha de nacimiento</label>
                        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento_nuevo" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label for="id_genero_nuevo" class="form-label text-muted">Género</label>
                        <select name="id_genero" id="id_genero_nuevo" class="form-select">
                            <option value="">Seleccione...</option>
                            <option value="1">Masculino</option>
                            <option value="2">Femenino</option>
                            <option value="3">Otro</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="id_estado_civil_nuevo" class="form-label text-muted">Estado civil</label>
                        <select name="id_estado_civil" id="id_estado_civil_nuevo" class="form-select">
                            <option value="">Seleccione...</option>
                            <option value="1">Soltero(a)</option>
                            <option value="2">Casado(a)</option>
                            <option value="3">Unión libre</option>
                            <option value="4">Divorciado(a)</option>
                            <option value="5">Viudo(a)</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="correo_nuevo" class="form-label text-muted">Correo electrónico</label>
                        <input type="email" name="correo" id="correo_nuevo" class="form-control" required>
                        <div class="invalid-feedback">Ingrese un correo válido.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="direccion_nueva" class="form-label text-muted">Dirección</label>
                        <input type="text" name="direccion" id="direccion_nueva" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label for="password" class="form-label text-muted">Contraseña</label>
                        <input type="password" name="contrasena" id="password" class="form-control" required>
                        <div class="invalid-feedback">Ingrese una contraseña (mínimo 6 caracteres).</div>
                    </div>

                    <div class="col-md-6">
                        <label for="confirm_password" class="form-label text-muted">Confirmar contraseña</label>
                        <input type="password" id="confirm_password" class="form-control" required>
                        <div class="invalid-feedback">Las contraseñas deben coincidir.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="id_rol_nuevo" class="form-label text-muted">Rol</label>
                        <select name="id_rol" id="id_rol_nuevo" class="form-select" required>
                            <option value="" selected disabled>Seleccione...</option>
                            <?php if (!empty($roles)): ?>
                                <?php foreach ($roles as $id_rol => $nombre_rol): ?>
                                    <option value="<?= $id_rol ?>"><?= $nombre_rol ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <div class="invalid-feedback">Seleccione un rol.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="id_estado_nuevo" class="form-label text-muted">Estado</label>
                        <select name="id_estado" id="id_estado_nuevo" class="form-select" required>
                            <option value="1" selected>Activo</option>
                            <option value="2">Inactivo</option>
                        </select>
                        <div class="invalid-feedback">Seleccione un estado.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="guardarUsuario">
                    <i class="fas fa-save me-2"></i>Guardar Usuario
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Floating Action Button -->
<button class="btn btn-primary btn-icon btn-lg rounded-circle shadow-lg position-fixed bottom-3 end-3" data-bs-toggle="modal" data-bs-target="#nuevoUsuarioModal" style="z-index: 99;">
    <i class="fas fa-plus fs-4"></i>
</button>   

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="confirmarEliminarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center py-3">
                    <div class="mb-3">
                        <i class="fas fa-exclamation-circle text-danger" style="font-size: 4rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-2">¿Está seguro de eliminar este usuario?</h5>
                    <p class="text-muted">Esta acción no se puede deshacer y se eliminarán todos los datos asociados al usuario.</p>
                </div>
                <input type="hidden" id="usuario_id_eliminar">
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="confirmarEliminar">
                    <i class="fas fa-trash-alt me-2"></i>Eliminar Usuario
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
$(document).ready(function() {
    // Initialize DataTable with better configuration
    const table = $('#usersTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
            search: "",
            searchPlaceholder: "Buscar usuario...",
            lengthMenu: "Mostrar _MENU_ registros por página",
            zeroRecords: "No se encontraron usuarios",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "No hay registros disponibles",
            infoFiltered: "(filtrado de _MAX_ registros en total)",
            paginate: {
                first: "Primero",
                last: "Último",
                next: "Siguiente",
                previous: "Anterior"
            }
        },
        columnDefs: [
            { orderable: false, targets: [0, 5] },
            { searchable: false, targets: [0, 5] },
            { className: "text-nowrap", targets: [0, 3, 4] },
            { width: "5%", targets: 0 },
            { width: "10%", targets: 5 }
        ],
        order: [[1, 'asc']],
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        initComplete: function() {
            $('.dataTables_filter input').addClass('form-control form-control-sm');
            $('.dataTables_length select').addClass('form-select form-select-sm');
            $('.dataTables_info, .dataTables_paginate').addClass('mt-3');
        }
    });
    
    // Custom search box
    $('#searchInput').on('keyup', function() {
        table.search(this.value).draw();
    });
    
    // Mostrar/ocultar campos de contraseña en edición
    $('#editar_cambiar_contrasena').change(function() {
        if($(this).is(':checked')) {
            $('#editar_contrasena_fields').slideDown();
            $('#editar_password, #editar_confirm_password').prop('required', true);
        } else {
            $('#editar_contrasena_fields').slideUp();
            $('#editar_password, #editar_confirm_password').prop('required', false);
        }
    });
    
    // Manejador para el botón de editar - redirige al formulario de edición
    $(document).on('click', '.btn-editar', function(e) {
        e.preventDefault();
        const userId = $(this).data('id');
        window.location.href = '<?= site_url('admin/editar_usuario/') ?>' + userId;
    });

    // Manejar envío del formulario de edición
    $(document).on('submit', '#editarUsuarioForm', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const formData = new FormData(form[0]);
        const submitButton = form.find('button[type="submit"]');
        const originalButtonText = submitButton.html();
        
        // Validar contraseñas si se están cambiando
        if ($('#editar_cambiar_contrasena').is(':checked')) {
            const password = $('#editar_password').val();
            const confirmPassword = $('#editar_confirm_password').val();
            
            if (password !== confirmPassword) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Las contraseñas no coinciden',
                    confirmButtonColor: '#4e73df'
                });
                return false;
            }
            
            if (password.length < 6) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'La contraseña debe tener al menos 6 caracteres',
                    confirmButtonColor: '#4e73df'
                });
                return false;
            }
        }
        
        // Mostrar loading en el botón
        submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...');
        
        // Enviar datos por AJAX
        $.ajax({
            url: '<?= site_url('admin/actualizar_usuario') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: response.message || 'Usuario actualizado correctamente',
                        confirmButtonColor: '#4e73df',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                } else {
                    let errorMessage = response.message || 'Error al actualizar el usuario';
                    
                    // Mostrar errores de validación si existen
                    if (response.errors) {
                        errorMessage = '';
                        for (const field in response.errors) {
                            errorMessage += `${response.errors[field]}\n`;
                        }
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage,
                        confirmButtonColor: '#4e73df'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al actualizar usuario:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al actualizar el usuario',
                    confirmButtonColor: '#4e73df'
                });
            },
            complete: function() {
                submitButton.prop('disabled', false).html(originalButtonText);
            }
        });
    });

    // Toggle password visibility
    $('#togglePassword').click(function() {
        const password = $('#password');
        const confirmPassword = $('#confirm_password');
        const type = password.attr('type') === 'password' ? 'text' : 'password';
        
        password.attr('type', type);
        confirmPassword.attr('type', type);
        
        const icon = $(this).find('i');
        icon.toggleClass('fa-eye fa-eye-slash');
        
        // Update button title for accessibility
        const title = type === 'password' ? 'Mostrar contraseña' : 'Ocultar contraseña';
        $(this).attr('title', title);
    });

    // Handle edit button click - redirect to edit page
    $(document).on('click', '.btn-editar', function(e) {
        e.preventDefault();
        const userId = $(this).data('id');
        window.location.href = '<?= site_url('admin/usuarios/editar/') ?>' + userId;
        // Show loading state
        const $button = $(this);
        const originalContent = $button.html();
        $button.html('<i class="fas fa-spinner fa-spin me-1"></i>Cargando...').prop('disabled', true);
        
        // Manejador para el botón de ver
        $(document).on('click', '.btn-ver', function(e) {
            e.preventDefault();
            const userId = $(this).data('id');
            const $button = $(this);
            const originalContent = $button.html();
            $button.html('<i class="fas fa-spinner fa-spin me-1"></i>Cargando...').prop('disabled', true);
            
            // Redirigir a la vista de detalles
            window.location.href = '<?= site_url('admin/ver_usuario/') ?>' + userId;
        });
        
        // Simular API call
            // Reset button state
            $button.html(originalContent).prop('disabled', false);
            
            // Show success message
            const toast = `
                <div class="toast align-items-center text-white bg-success border-0 position-fixed bottom-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas fa-check-circle me-2"></i> Cargando datos del usuario...
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>`;
            
            $('body').append(toast);
            $('.toast').toast('show');
            
            // Remove toast after 3 seconds
            setTimeout(() => {
                $('.toast').toast('hide').on('hidden.bs.toast', function() {
                    $(this).remove();
                });
            }, 3000);
            
            // Here you would typically load the user data into the form
            console.log('Edit user:', userId);
            
        }, 1000);
    });

    // Cambiar estado (activar / desactivar) con AJAX real
    $(document).on('click', '.btn-cambiar-estado', function(e) {
        e.preventDefault();
        const $button = $(this);
        const userId = $button.data('id');
        const nuevoEstado = $button.data('estado') == 1 ? 0 : 1;
        const accion = nuevoEstado == 1 ? 'activar' : 'desactivar';

        if (!confirm(`¿Está seguro de ${accion} este usuario?`)) {
            return;
        }

        const originalContent = $button.html();
        $button.html('<i class="fas fa-spinner fa-spin me-1"></i>Procesando...').prop('disabled', true);

        $.ajax({
            url: '<?= site_url('admin/cambiar_estado_usuario') ?>',
            type: 'POST',
            dataType: 'json',
            data: { id: userId, estado: nuevoEstado },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.message || 'Error al cambiar el estado del usuario');
                }
            },
            error: function() {
                alert('Error al procesar la solicitud');
            },
            complete: function() {
                $button.html(originalContent).prop('disabled', false);
            }
        });
    });

    // Handle delete button click
    $(document).on('click', '.btn-eliminar', function(e) {
        e.preventDefault();
        const userId = $(this).data('id');
        $('#usuario_id_eliminar').val(userId);
        $('#confirmarEliminarModal').modal('show');
    });

    // Confirm delete (real AJAX call to set user as inactive)
    $('#confirmarEliminar').click(function() {
        const userId = $('#usuario_id_eliminar').val();
        const $button = $(this);

        if (!userId) {
            return;
        }

        const originalContent = $button.html();
        $button.html('<i class="fas fa-spinner fa-spin me-1"></i>Eliminando...').prop('disabled', true);

        $.ajax({
            url: '<?= site_url("admin/eliminar_usuario/") ?>' + userId,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                // Hide modal
                $('#confirmarEliminarModal').modal('hide');

                const isSuccess = response && response.status === 'success';
                const message = response && response.message ? response.message : (isSuccess ? 'Usuario desactivado correctamente' : 'Error al desactivar el usuario');

                const bgClass = isSuccess ? 'bg-success' : 'bg-danger';

                const toast = `
                    <div class="toast align-items-center text-white ${bgClass} border-0 position-fixed bottom-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="fas ${isSuccess ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i> ${message}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>`;

                $('body').append(toast);
                $('.toast').toast('show');

                setTimeout(() => {
                    $('.toast').toast('hide').on('hidden.bs.toast', function() {
                        $(this).remove();
                        if (isSuccess) {
                            location.reload();
                        }
                    });
                }, 2000);
            },
            error: function() {
                alert('Error al procesar la solicitud de eliminación');
            },
            complete: function() {
                $button.html(originalContent).prop('disabled', false);
            }
        });
    });

    // Guardar nuevo usuario (crear) con AJAX real
    $('#guardarUsuario').click(function() {
        const $form = $('#usuarioForm');

        if ($form[0].checkValidity() === false) {
            $form.addClass('was-validated');
            return;
        }

        const password = $('#password').val();
        const confirmPassword = $('#confirm_password').val();

        if (password !== confirmPassword) {
            alert('Las contraseñas no coinciden');
            return;
        }

        const $button = $(this);
        const originalContent = $button.html();
        $button.html('<i class="fas fa-spinner fa-spin me-1"></i>Guardando...').prop('disabled', true);

        $.ajax({
            url: '<?= site_url('admin/crear_usuario') ?>',
            type: 'POST',
            dataType: 'json',
            data: $form.serialize(),
            success: function(response) {
                if (response.exito || response.success) {
                    location.reload();
                } else {
                    let msg = response.mensaje || response.message || 'Error al crear el usuario';
                    alert(msg);
                }
            },
            error: function() {
                alert('Error al procesar la solicitud');
            },
            complete: function() {
                $button.html(originalContent).prop('disabled', false);
                $('#nuevoUsuarioModal').modal('hide');
            }
        });
    });
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Refresh button
    $('#btnRefresh').click(function() {
        const $button = $(this);
        $button.addClass('fa-spin');
        
        // Simulate refresh
        setTimeout(() => {
            $button.removeClass('fa-spin');
            
            // Show success message
            const toast = `
                <div class="toast align-items-center text-white bg-success border-0 position-fixed bottom-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas fa-sync-alt me-2"></i> Datos actualizados
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>`;
            
            $('body').append(toast);
            $('.toast').toast('show');
            
            // Remove toast after 2 seconds
            setTimeout(() => {
                $('.toast').toast('hide').on('hidden.bs.toast', function() {
                    $(this).remove();
                });
            }, 2000);
        });
    });
    
    // Guardar usuario (crear o actualizar)
    $('#btnGuardar').click(function() {
        var formData = $('#usuarioForm').serialize();
        var id = $('#id_usuario').val();
        var url = id ? '<?= site_url("admin/actualizar_usuario") ?>' : '<?= site_url("admin/crear_usuario") ?>';
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function(response) {
                var res = JSON.parse(response);
                if (res.status === 'success') {
                    $('#nuevoUsuarioModal').modal('hide');
                    location.reload();
                } else {
                    alert('Error al guardar el usuario');
                }
            },
            error: function() {
                alert('Error al procesar la solicitud');
            }
        });
    });

    // Eliminar usuario
    $('.btn-eliminar').click(function() {
        if (!confirm('¿Está seguro de eliminar este usuario?')) return;
        var id = $(this).data('id');
        
        $.ajax({
            url: '<?= site_url("admin/eliminar_usuario/") ?>' + id,
            type: 'POST',
            success: function(response) {
                var res = JSON.parse(response);
                if (res.status === 'success') {
                    location.reload();
                } else {
                    alert('Error al eliminar el usuario');
                }
            },
            error: function() {
                alert('Error al procesar la solicitud');
            }
        });
    });
    
    // Limpiar formulario al cerrar el modal para nuevo usuario
    $('#nuevoUsuarioModal').on('hidden.bs.modal', function () {
        const form = document.getElementById('usuarioForm');
        if (form) {
            form.reset();
        }
        $('#nuevoUsuarioModalLabel').text('Nuevo Usuario');
    });
</script>

</body>
</html>
