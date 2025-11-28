<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid px-4 py-3">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-1 text-gray-800 fw-bold">
        <i class="fas fa-exchange-alt me-2 text-primary"></i><?= $title ?>
      </h1>
      <p class="text-muted mb-0">Historial de entradas y salidas de inventario</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#movimientoModal">
        <i class="fas fa-plus me-2"></i>Registrar Movimiento
    </button>
  </div>

  <div class="card shadow-sm">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">
        <i class="fas fa-list-ul me-2"></i>Historial de Movimientos
      </h6>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover table-striped align-middle mb-0" id="movimientosTable">
          <thead class="bg-light">
            <tr>
              <th>#</th>
              <th>Fecha</th>
              <th>Tipo</th>
              <th>Producto</th>
              <th>Referencia</th>
              <th>Color</th>
              <th>Talla</th>
              <th class="text-end">Cantidad</th>
              <th>Descripción</th>
              <th>Usuario</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($movimientos)): ?>
              <?php foreach ($movimientos as $m): ?>
                <tr>
                  <td><?= $m->id_movimiento ?></td>
                  <td>
                    <small><?= date('d/m/Y H:i', strtotime($m->fecha_movimiento)) ?></small>
                  </td>
                  <td>
                    <?php if ($m->tipo_movimiento == 'entrada'): ?>
                      <span class="badge bg-success">
                        <i class="fas fa-arrow-down me-1"></i>Entrada
                      </span>
                    <?php else: ?>
                      <span class="badge bg-danger">
                        <i class="fas fa-arrow-up me-1"></i>Salida
                      </span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <small class="text-muted"><?= htmlspecialchars($m->codigo_interno ?? 'N/A') ?></small>
                  </td>
                  <td>
                    <strong><?= htmlspecialchars($m->nombre_referencia ?? 'N/A') ?></strong><br>
                    <small class="text-muted">Cód: <?= htmlspecialchars($m->codigo_referencia ?? 'N/A') ?></small>
                  </td>
                  <td>
                    <span class="badge bg-light text-dark border">
                      <?= htmlspecialchars($m->color ?? 'N/A') ?>
                    </span>
                  </td>
                  <td><?= htmlspecialchars($m->talla ?? 'N/A') ?></td>
                  <td class="text-end">
                    <strong class="<?= $m->tipo_movimiento == 'entrada' ? 'text-success' : 'text-danger' ?>">
                      <?= $m->tipo_movimiento == 'entrada' ? '+' : '-' ?><?= $m->cantidad ?>
                    </strong>
                  </td>
                  <td>
                    <small><?= htmlspecialchars($m->descripcion ?? '-') ?></small>
                  </td>
                  <td>
                    <small><?= htmlspecialchars(($m->nombre_usuario ?? '') . ' ' . ($m->apellido_usuario ?? '')) ?></small>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="10" class="text-center py-5 text-muted">
                  <i class="fas fa-inbox fs-1 d-block mb-2"></i>
                  No hay movimientos registrados
                </td>
              </tr>
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

<script>
$(document).ready(function() {
  $('#movimientosTable').DataTable({
    language: { url: '<?= IP_SERVER ?>assets/datatables/es-ES.json' },
    order: [[0, 'desc']],
    pageLength: 25,
    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
         '<"row"<"col-sm-12 col-md-6"B>>' +
         '<"row"<"col-sm-12"tr>>' +
         '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
    buttons: [{
        extend: 'excelHtml5',
        text: '<i class="fas fa-file-excel me-1"></i> Exportar a Excel',
        className: 'btn btn-success btn-sm',
        title: 'Movimientos - ' + new Date().toLocaleDateString()
    }]
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
