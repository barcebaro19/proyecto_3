<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid px-4 py-3">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold">
                <i class="fas fa-tags me-2 text-primary"></i><?= $title ?>
            </h1>
            <p class="text-muted mb-0">Gestiona las categorías de productos del sistema</p>
        </div>
    </div>

    <!-- Mensajes de éxito/error -->
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= $this->session->flashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?= $this->session->flashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Tabla de categorías -->
    <div class="card shadow-sm mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Listado de Categorías
                <span class="badge bg-primary ms-2"><?= count($categorias) ?></span>
            </h6>
            <a href="<?= site_url('categoria/crear') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nueva Categoría
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($categorias)): ?>
                            <?php foreach ($categorias as $categoria): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-muted">#<?= $categoria->id_categoria ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-40px symbol-circle me-3">
                                                <span class="symbol-label bg-light-primary text-primary fs-6 fw-bold rounded-circle p-2">
                                                    <?= strtoupper(substr($categoria->nombre, 0, 1)) ?>
                                                </span>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="text-gray-800 fw-bold"><?= html_escape($categoria->nombre) ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= !empty($categoria->descripcion) ? html_escape($categoria->descripcion) : '<span class="text-muted fst-italic">Sin descripción</span>' ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-<?= $categoria->id_estado == 1 ? 'success' : 'secondary' ?> rounded-pill">
                                            <i class="fas fa-<?= $categoria->id_estado == 1 ? 'check' : 'times' ?> me-1"></i>
                                            <?= $categoria->id_estado == 1 ? 'Activo' : 'Inactivo' ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group" role="group">
                                            <a href="<?= site_url('categoria/editar/' . $categoria->id_categoria) ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               data-bs-toggle="tooltip" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <?php if ($categoria->id_estado == 1): ?>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-warning btn-cambiar-estado" 
                                                        data-id="<?= $categoria->id_categoria ?>"
                                                        data-estado="1"
                                                        data-bs-toggle="tooltip" 
                                                        title="Desactivar">
                                                    <i class="fas fa-toggle-on"></i>
                                                </button>
                                            <?php else: ?>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-secondary btn-cambiar-estado" 
                                                        data-id="<?= $categoria->id_categoria ?>"
                                                        data-estado="0"
                                                        data-bs-toggle="tooltip" 
                                                        title="Activar">
                                                    <i class="fas fa-toggle-off"></i>
                                                </button>
                                            <?php endif; ?>
                                            
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger btn-eliminar" 
                                                    data-id="<?= $categoria->id_categoria ?>"
                                                    data-nombre="<?= html_escape($categoria->nombre) ?>"
                                                    data-bs-toggle="tooltip" 
                                                    title="Eliminar">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="modalEliminar" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalEliminarLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmar eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <i class="fas fa-trash-alt text-danger fa-3x"></i>
                </div>
                <p class="mb-0">¿Está seguro de que desea eliminar la categoría <strong id="nombreCategoria" class="text-dark"></strong>?</p>
                <p class="text-muted small mt-2">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="btnConfirmarEliminar" class="btn btn-danger">
                    <i class="fas fa-trash-alt me-2"></i>Eliminar
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación para cambiar estado -->
<div class="modal fade" id="modalCambiarEstado" tabindex="-1" aria-labelledby="modalCambiarEstadoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalCambiarEstadoLabel">
                    <i class="fas fa-sync-alt me-2"></i>Cambiar estado
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <p class="mb-0">¿Está seguro de que desea <span id="accionEstado" class="fw-bold"></span> la categoría <strong id="nombreCategoriaEstado" class="text-dark"></strong>?</p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="btnConfirmarCambioEstado" class="btn btn-primary">
                    <i class="fas fa-check me-2"></i>Confirmar
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Inicializar DataTable con botón de Excel
        $('#dataTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12 col-md-6"B>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel me-1"></i> Exportar a Excel',
                    className: 'btn btn-success btn-sm',
                    title: 'Categorías - ' + new Date().toLocaleDateString(),
                    exportOptions: {
                        columns: [0, 1, 2, 3] // Exclude actions column
                    }
                }
            ]
        });
        // Inicializar DataTable con configuración en español
        $('#dataTable').DataTable({
            language: {
                url: '<?= IP_SERVER ?>assets/datatables/es-ES.json',
                emptyTable: '<div class="d-flex flex-column align-items-center py-5"><i class="fas fa-folder-open text-muted mb-3" style="font-size: 3rem;"></i><h5 class="text-muted">No hay categorías registradas</h5></div>'
            },
            order: [[0, "desc"]],
            pageLength: 10,
            responsive: true,
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            initComplete: function() {
                $('.dataTables_filter input').addClass('form-control form-control-sm');
                $('.dataTables_length select').addClass('form-select form-select-sm');
            }
        });
        
        // Inicializar tooltips de Bootstrap 5
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
        
        // Manejar clic en botón de eliminar
        $(document).on('click', '.btn-eliminar', function() {
            var id = $(this).data('id');
            var nombre = $(this).data('nombre');
            
            $('#nombreCategoria').text(nombre);
            $('#btnConfirmarEliminar').attr('href', '<?= site_url('categoria/eliminar/') ?>' + id);
            $('#modalEliminar').modal('show');
        });
        
        // Manejar clic en botón de cambiar estado
        $(document).on('click', '.btn-cambiar-estado', function() {
            var id = $(this).data('id');
            var estado = $(this).data('estado');
            var nombre = $(this).closest('tr').find('td:eq(1) span.fw-bold').text(); // Ajustado selector
            var accion = estado == 1 ? 'desactivar' : 'activar';
            
            $('#nombreCategoriaEstado').text(nombre);
            $('#accionEstado').text(accion);
            $('#btnConfirmarCambioEstado').attr('href', '<?= site_url('categoria/cambiar_estado/') ?>' + id);
            $('#modalCambiarEstado').modal('show');
        });
    });
</script>
