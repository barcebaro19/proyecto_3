<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid px-4 py-3">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-1 text-gray-800 fw-bold">
        <i class="bi bi-arrow-left-right me-2"></i><?= $title ?>
      </h1>
      <p class="text-muted mb-0">Historial de entradas y salidas de inventario</p>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">
        <i class="bi bi-list-ul me-2"></i>Historial de Movimientos
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
                        <i class="bi bi-arrow-down-circle me-1"></i>Entrada
                      </span>
                    <?php else: ?>
                      <span class="badge bg-danger">
                        <i class="bi bi-arrow-up-circle me-1"></i>Salida
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
                  <i class="bi bi-inbox fs-1 d-block mb-2"></i>
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

<script>
$(document).ready(function() {
  $('#movimientosTable').DataTable({
    language: {
      url: '<?= IP_SERVER ?>assets/datatables/es-ES.json'
    },
    order: [[0, 'desc']], // Ordenar por ID descendente (más recientes primero)
    pageLength: 25,
    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
         '<"row"<"col-sm-12 col-md-6"B>>' +
         '<"row"<"col-sm-12"tr>>' +
         '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
    buttons: [
      {
        extend: 'excelHtml5',
        text: '<i class="fas fa-file-excel me-1"></i> Exportar a Excel',
        className: 'btn btn-success btn-sm',
        title: 'Movimientos de Inventario - ' + new Date().toLocaleDateString(),
        exportOptions: {
          columns: ':visible'
        }
      }
    ]
  });
});
</script>
