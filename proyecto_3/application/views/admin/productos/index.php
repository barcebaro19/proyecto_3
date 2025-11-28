<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
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
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoProductoModal">
                <i class="fas fa-plus me-2"></i>Nuevo Producto
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle" id="productosTable">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Código Interno</th>
                            <th>Referencia</th>
                            <th>Descripción</th>
                            <th>Talla</th>
                            <th>Color</th>
                            <th class="text-center">Stock</th>
                            <th class="text-center">Precio</th>
                            <th class="text-center">Fecha creación</th>
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
                                        <div class="text-muted small">
                                            <?= htmlspecialchars($producto->codigo_referencia ?? '') ?>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($producto->descripcion) ?></td>
                                    <td><?= htmlspecialchars($producto->talla ?? '') ?></td>
                                    <td><?= htmlspecialchars($producto->color ?? '') ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-<?= $producto->cantidad_stock <= $producto->stock_minimo ? 'danger' : 'success' ?>">
                                            <?= (int)$producto->cantidad_stock ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        $<?= number_format($producto->precio_unitario, 2, ',', '.') ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-<?= $producto->id_estado == 1 ? 'success' : 'secondary' ?>">
                                            <?= htmlspecialchars($producto->nombre_estado ?? ($producto->id_estado == 1 ? 'Activo' : 'Inactivo')) ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group" role="group">
                                            <a href="<?= site_url('admin/ver_producto/' . $producto->id_producto) ?>" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= site_url('admin/editar_producto/' . $producto->id_producto) ?>" class="btn btn-outline-secondary btn-sm">
                                                <i class="far fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger btn-sm btn-eliminar-producto" data-id="<?= $producto->id_producto ?>">
                                                <i class="far fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">No hay productos registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nuevo Producto (estructura básica, se podrá mejorar luego) -->
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
            <label class="form-label text-muted">Stock</label>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
  $('#productosTable').DataTable();

  $('#guardarProducto').click(function() {
    const $form = $('#productoForm');
    const data = $form.serialize();
    $.ajax({
      url: '<?= site_url('admin/crear_producto') ?>',
      type: 'POST',
      dataType: 'json',
      data: data,
      success: function(response) {
        if (response.success) {
          location.reload();
        } else {
          alert(response.message || 'Error al crear el producto');
        }
      },
      error: function() {
        alert('Error al procesar la solicitud');
      }
    });
  });

  $(document).on('click', '.btn-eliminar-producto', function() {
    if (!confirm('¿Está seguro de eliminar este producto?')) return;
    const id = $(this).data('id');
    $.ajax({
      url: '<?= site_url('admin/eliminar_producto/') ?>' + id,
      type: 'POST',
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          location.reload();
        } else {
          alert(response.message || 'Error al eliminar el producto');
        }
      },
      error: function() {
        alert('Error al procesar la solicitud');
      }
    });
  });
});
</script>
