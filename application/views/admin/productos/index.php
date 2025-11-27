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
                                            <?= htmlspecialchars($producto->nombre_referencia ?? '') ?>
                                        </div>
                                    </td>
                                    <td class="text-center"><?= htmlspecialchars($producto->talla ?? '') ?></td>
                                    <td class="text-center"><?= htmlspecialchars($producto->color ?? '') ?></td>
                                    <td class="text-center bg-white border-start border-end"><?= (int)($producto->inv_inicial ?? 0) ?></td>
                                    <td class="text-center text-success bg-white">+<?= (int)($producto->total_entradas ?? 0) ?></td>
                                    <td class="text-center text-danger bg-white border-end">-<?= (int)($producto->total_salidas ?? 0) ?></td>
                                    <td class="text-center fw-bold bg-light">
                                        <span class="badge bg-<?= $producto->cantidad_stock <= $producto->stock_minimo ? 'danger' : 'primary' ?> fs-6">
                                            <?= (int)$producto->cantidad_stock ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-<?= $producto->id_estado == 1 ? 'success' : 'secondary' ?>">
                                            <?= htmlspecialchars($producto->nombre_estado ?? ($producto->id_estado == 1 ? 'Activo' : 'Inactivo')) ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-info text-white btn-movimiento" 
                                                    data-id="<?= $producto->id_producto ?>"
                                                    data-nombre="<?= htmlspecialchars(($producto->nombre_referencia ?? '') . ' - ' . ($producto->color ?? '') . ' ' . ($producto->talla ?? '')) ?>"
                                                    title="Registrar Movimiento">
                                                <i class="fas fa-exchange-alt"></i>
                                            </button>
                                            <a href="<?= site_url('admin/editar_producto/' . $producto->id_producto) ?>" class="btn btn-sm btn-warning text-white" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger btn-eliminar-producto" data-id="<?= $producto->id_producto ?>" title="Eliminar">
                                                <i class="fas fa-trash"></i>
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
                        <select name="id_referencia" class="form-select" required>
                            <option value="" disabled selected>Seleccione...</option>
                            <?php if (!empty($referencias)): foreach ($referencias as $ref): ?>
                                <option value="<?= $ref->id_referencia ?>"><?= $ref->nombre_referencia ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted">Código interno</label>
                        <input type="text" name="codigo_interno" class="form-control" required>
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

<script>
$(document).ready(function() {
    // Inicializar DataTable con botón de Excel
    $('#productosTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
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
            success: function(res) {
                if (res.success) {
                    Swal.fire('¡Éxito!', res.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },
            error: function(xhr) {
                let msg = 'Error al procesar la solicitud';
                try { const r = JSON.parse(xhr.responseText); if (r.message) msg = r.message; }
                catch(e) { if (xhr.responseText) msg = 'Error del servidor.'; }
                Swal.fire('Error', msg, 'error');
            },
            complete: function() { $btn.prop('disabled', false).html(original); }
        });
    });

    // Guardar nuevo producto
    $('#guardarProducto').click(function() {
        const $form = $('#productoForm');
        if (!$form[0].checkValidity()) { $form[0].reportValidity(); return; }
        $.ajax({
            url: '<?= site_url("admin/crear_producto") ?>',
            type: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function(res) { if (res.success) location.reload(); else alert(res.message || 'Error al guardar'); },
            error: function() { alert('Error en la solicitud'); }
        });
    });

    // Eliminar producto
    $(document).on('click', '.btn-eliminar-producto', function() {
        if (!confirm('¿Está seguro de eliminar este producto?')) return;
        const id = $(this).data('id');
        $.ajax({
            url: '<?= site_url('admin/eliminar_producto/') ?>' + id,
            type: 'POST',
            dataType: 'json',
            success: function(res) { if (res.success) location.reload(); else alert(res.message || 'Error al eliminar'); },
            error: function() { alert('Error al procesar la solicitud'); }
        });
    });
});
</script>