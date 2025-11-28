<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold">
                <i class="fas fa-boxes-stacked me-2 text-primary"></i><?= $title ?>
            </h1>
            <p class="text-muted mb-0">Gestión de inventario y productos</p>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-box me-2"></i>Listado de Productos
                <span class="badge bg-primary ms-2"><?= isset($productos) ? count($productos) : 0 ?></span>
            </h6>
            <div class="d-flex gap-2">
                <a href="<?= site_url('operario/crear_producto') ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nuevo Producto
                </a>
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
                            <th class="text-center bg-white border-start border-end">Stock</th>
                            <th class="text-center text-success bg-white">Entradas</th>
                            <th class="text-center text-danger bg-white border-end">Salidas</th>
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
                                    <td class="text-center border-start border-end fw-bold <?= $producto->cantidad_stock <= $producto->stock_minimo ? 'text-danger' : '' ?>">
                                        <?= $producto->cantidad_stock ?>
                                    </td>
                                    <td class="text-center text-success bg-soft-success fw-medium">+<?= $producto->total_entradas ?></td>
                                    <td class="text-center text-danger bg-soft-danger fw-medium border-end">-<?= $producto->total_salidas ?></td>
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
                                            <a href="<?= site_url('operario/ver_producto/' . $producto->id_producto) ?>" 
                                               class="btn btn-outline-secondary btn-sm" title="Ver Detalle">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= site_url('operario/editar_producto/' . $producto->id_producto) ?>" 
                                               class="btn btn-outline-primary btn-sm" title="Editar">
                                                <i class="far fa-edit"></i>
                                            </a>
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
                        <label class="form-label text-muted">Producto ID</label>
                        <input type="text" name="id_producto" id="mov_id_producto" class="form-control" readonly>
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
    // Inicializar DataTable
    $('#productosTable').DataTable({
        language: { url: '<?= IP_SERVER ?>assets/datatables/es-ES.json' },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
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
            url: '<?= site_url("operario/registrar_movimiento") ?>',
            type: 'POST',
            data: $('#movimientoForm').serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.exito) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: res.mensaje,
                        confirmButtonColor: '#3085d6'
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: res.mensaje || 'Error desconocido',
                        confirmButtonColor: '#d33'
                    });
                }
            },
            error: function(xhr) {
                console.error('Error AJAX:', xhr.responseText);
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
});
</script>
