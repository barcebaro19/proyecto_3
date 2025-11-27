<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold">
                <i class="fas fa-boxes-stacked me-2 text-primary"></i><?= $title ?>
            </h1>
            <p class="text-muted mb-0">Administra y gestiona los productos del inventario</p>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-box me-2"></i>Listado de Productos
                <span class="badge bg-primary ms-2"><?= isset($productos) ? count($productos) : 0 ?></span>
            </h6>
            <div class="d-flex gap-2">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoProductoModal">
                    <i class="fas fa-plus me-2"></i>Nuevo Producto
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle" id="productosTable">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Código</th>
                            <th>Referencia</th>
                            <th class="text-center">Talla</th>
                            <th class="text-center">Color</th>
                            <th class="text-center bg-white border-start border-end">Inv. Inicial</th>
                            <th class="text-center text-success bg-white">Entradas</th>
                            <th class="text-center text-danger bg-white border-end">Salidas</th>
                            <th class="text-center fw-bold bg-light">Saldo</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($productos)): ?>
                            <?php $i = 1; foreach ($productos as $producto): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= htmlspecialchars($producto->codigo_interno) ?></td>
                                    <td>
                                        <div class="fw-semibold text-dark">
                                            <?= htmlspecialchars($producto->nombre_referencia) ?>
                                        </div>
                                        <small class="text-muted"><?= htmlspecialchars($producto->codigo_referencia) ?></small>
                                    </td>
                                    <td class="text-center"><span class="badge bg-light text-dark border"><?= htmlspecialchars($producto->talla) ?></span></td>
                                    <td class="text-center"><span class="badge bg-light text-dark border"><?= htmlspecialchars($producto->color) ?></span></td>
                                    <td class="text-center border-start border-end"><?= $producto->cantidad_stock ?></td>
                                    <td class="text-center text-success bg-soft-success fw-medium">+<?= $producto->total_entradas ?></td>
                                    <td class="text-center text-danger bg-soft-danger fw-medium border-end">-<?= $producto->total_salidas ?></td>
                                    <td class="text-center fw-bold bg-light fs-6"><?= $producto->saldo_actual ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-<?= $producto->id_estado == 1 ? 'success' : 'secondary' ?>">
                                            <?= $producto->nombre_estado ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-info btn-sm btn-movimiento" 
                                                    data-id="<?= $producto->id_producto ?>" 
                                                    title="Registrar Movimiento">
                                                <i class="fas fa-exchange-alt"></i>
                                            </button>
                                            <a href="<?= site_url('admin/editar_producto/' . $producto->id_producto) ?>" 
                                               class="btn btn-outline-secondary btn-sm" title="Editar">
                                                <i class="far fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger btn-sm btn-eliminar-producto" 
                                                    data-id="<?= $producto->id_producto ?>" title="Eliminar">
                                                <i class="far fa-trash-alt"></i>
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

<!-- Modal Nuevo Producto -->
<div class="modal fade" id="nuevoProductoModal" tabindex="-1" aria-labelledby="nuevoProductoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="nuevoProductoModalLabel"><i class="fas fa-box me-2"></i>Nuevo Producto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="productoForm" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label text-muted">Referencia</label>
                        <select name="id_referencia" id="select_referencia" class="form-select" required>
                            <option value="" disabled selected>Seleccione...</option>
                            <?php if (!empty($referencias)): foreach ($referencias as $ref): ?>
                                <option value="<?= $ref->id_referencia ?>" data-codigo="<?= htmlspecialchars($ref->codigo_referencia ?? '') ?>">
                                    <?= htmlspecialchars($ref->nombre_referencia) ?>
                                </option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted">Código interno</label>
                        <input type="text" name="codigo_interno" id="input_codigo_interno" class="form-control" required readonly>
                        <small class="text-muted">Se genera automáticamente</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted">Código proveedor</label>
                        <input type="text" name="codigo_proveedor" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">Descripción</label>
                        <input type="text" name="descripcion" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted">Talla</label>
                        <input type="text" name="talla" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted">Color</label>
                        <input type="text" name="color" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted">Stock Inicial</label>
                        <input type="number" name="cantidad_stock" class="form-control" value="0" min="0" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted">Stock mínimo</label>
                        <input type="number" name="stock_minimo" class="form-control" value="0" min="0" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted">Precio unitario</label>
                        <input type="number" step="0.01" name="precio_unitario" class="form-control" value="0" min="0" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted">Estado</label>
                        <select name="id_estado" class="form-select" required>
                            <option value="1" selected>Activo</option>
                            <option value="2">Inactivo</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="guardarProducto">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Registrar Movimiento -->
<div class="modal fade" id="movimientoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-exchange-alt me-2"></i>Registrar Movimiento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="movimientoForm">
                    <div class="mb-3">
                        <label class="form-label text-muted">Producto</label>
                        <select name="id_producto" id="mov_id_producto" class="form-select" required>
                            <option value="" disabled selected>Seleccione un producto...</option>
                            <?php if (!empty($productos)): foreach ($productos as $p): ?>
                                <option value="<?= $p->id_producto ?>">
                                    <?= htmlspecialchars($p->nombre_referencia . ' - ' . $p->color . ' - ' . $p->talla) ?> (Stock: <?= $p->cantidad_stock ?>)
                                </option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo de Movimiento</label>
                            <select name="tipo_movimiento" class="form-select" required>
                                <option value="entrada">Entrada (+)</option>
                                <option value="salida">Salida (-)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cantidad</label>
                            <input type="number" name="cantidad" class="form-control" min="1" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción / Motivo</label>
                        <textarea name="descripcion" class="form-control" rows="2" placeholder="Ej: Compra de proveedor, Venta #123..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-info text-white" id="btnGuardarMovimiento">Registrar</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inicializar DataTable con botón de Excel
    $('#productosTable').DataTable({
        language: { url: '<?= IP_SERVER ?>assets/datatables/es-ES.json' },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12 col-md-6"B>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        buttons: [{
            extend: 'excelHtml5',
            text: '<i class="fas fa-file-excel me-1"></i> Exportar a Excel',
            className: 'btn btn-success btn-sm',
            title: 'Productos - ' + new Date().toLocaleDateString(),
            exportOptions: { columns: [0,1,2,3,4,5,6,7,8,9] }
        }]
    });

    // Abrir modal de movimiento
    $(document).on('click', '.btn-movimiento', function() {
        const id = $(this).data('id');
        $('#movimientoForm')[0].reset();
        $('#mov_id_producto').val(id);
        $('#movimientoModal').modal('show');
    });

    // Guardar movimiento
    $('#btnGuardarMovimiento').click(function() {
        const $btn = $(this);
        const original = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');
        
        $.ajax({
            url: '<?= site_url("admin/registrar_movimiento") ?>',
            type: 'POST',
            data: $('#movimientoForm').serialize(),
            dataType: 'json',
            global: false,
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: res.message,
                        confirmButtonColor: '#3085d6'
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: res.message || 'Error desconocido',
                        confirmButtonColor: '#d33'
                    });
                }
            },
            error: function(xhr) {
                console.error('Error AJAX:', xhr.responseText);
                let msg = 'Error al procesar la solicitud';
                try {
                    const r = JSON.parse(xhr.responseText);
                    if (r.message) msg = r.message;
                } catch(e) {
                    if (xhr.responseText) msg = 'Error del servidor: ' + xhr.status;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error del Sistema',
                    text: msg,
                    footer: '<span class="text-muted">Ver consola para más detalles</span>'
                });
            },
            complete: function() {
                $btn.prop('disabled', false).html(original);
            }
        });
    });

    // Auto-generar código interno cuando se selecciona una referencia
    $('#select_referencia').on('change', function() {
        const id_referencia = $(this).val();
        const $codigoInput = $('#input_codigo_interno');
        
        // DEBUG: Verificar ID
        // alert('Referencia seleccionada ID: ' + id_referencia);

        if (!id_referencia) {
            $codigoInput.val('');
            return;
        }

        // Mostrar estado de carga
        $codigoInput.val('Generando...').prop('readonly', true).prop('disabled', true);

        $.ajax({
            url: '<?= site_url("admin/obtener_siguiente_codigo") ?>',
            type: 'POST',
            data: { id_referencia: id_referencia },
            dataType: 'json',
            global: false,
            success: function(res) {
                // DEBUG: Verificar respuesta
                // console.log(res);
                
                if (res.success) {
                    $codigoInput.val(res.codigo);
                    // Éxito: mantener readonly pero asegurar que se envíe (disabled=false)
                    $codigoInput.prop('readonly', true).prop('disabled', false);
                    $codigoInput.removeClass('is-invalid').addClass('is-valid');
                } else {
                    $codigoInput.val('');
                    // Fallo lógico: permitir escritura manual
                    $codigoInput.prop('readonly', false).prop('disabled', false);
                    $codigoInput.removeClass('is-valid').addClass('is-invalid');
                    
                    Swal.fire({
                        icon: 'warning',
                        title: 'Atención',
                        text: res.message || 'No se pudo generar el código. Ingréselo manualmente.',
                        confirmButtonText: 'Entendido'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', status, error, xhr.responseText);
                $codigoInput.val('');
                // Error de red: permitir escritura manual
                $codigoInput.prop('readonly', false).prop('disabled', false);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor (' + status + '). Por favor ingrese el código manualmente.',
                    footer: 'Detalle: ' + error
                });
            }
        });
    });

    // Guardar nuevo producto
    $('#guardarProducto').click(function() {
        const $form = $('#productoForm');
        if (!$form[0].checkValidity()) { 
            $form[0].reportValidity(); 
            return; 
        }
        
        const $btn = $(this);
        const original = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

        $.ajax({
            url: '<?= site_url("admin/crear_producto") ?>',
            type: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            global: false,
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Producto creado correctamente',
                        confirmButtonColor: '#3085d6'
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: res.message || 'Error al guardar',
                        confirmButtonColor: '#d33'
                    });
                }
            },
            error: function(xhr) {
                console.error('Error crear producto:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al procesar la solicitud'
                });
            },
            complete: function() {
                $btn.prop('disabled', false).html(original);
            }
        });
    });

    // Eliminar producto
    $(document).on('click', '.btn-eliminar-producto', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción eliminará el producto",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= site_url("admin/eliminar_producto/") ?>' + id,
                    type: 'POST',
                    dataType: 'json',
                    global: false,
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminado',
                                text: res.message || 'Producto eliminado correctamente',
                                confirmButtonColor: '#3085d6'
                            }).then(() => location.reload());
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: res.message || 'Error al eliminar'
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error('Error eliminar producto:', xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al procesar la solicitud'
                        });
                    }
                });
            }
        });
    });
});
</script>