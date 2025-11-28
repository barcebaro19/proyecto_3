<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<div class="container-fluid px-4 py-3">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-1 text-gray-800 fw-bold">
        <i class="bi bi-box-seam me-2"></i><?= $title ?>
      </h1>
      <p class="text-muted mb-0">Resumen global de inventario por referencia</p>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">
        <i class="bi bi-list-ul me-2"></i>Listado de movimientos
      </h6>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover table-striped align-middle mb-0" id="movimientosTable">
          <thead class="bg-light">
            <tr>
              <th>Referencia</th>
              <th class="text-end">Total referencias</th>
              <th class="text-end">Total stock</th>
              <th class="text-end">Valor total</th>
              <th>Estado</th>
              <th>Creado por</th>
              <th>Fecha creación</th>
              <th>Última modificación</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($movimientos)): ?>
              <?php foreach ($movimientos as $m): ?>
                <tr>
                  <td>
                    <?php if (!empty($m->nombre_referencia)): ?>
                      <strong><?= html_escape($m->nombre_referencia) ?></strong><br>
                      <small class="text-muted">Cód: <?= html_escape($m->codigo_referencia) ?></small>
                    <?php else: ?>
                      <span class="text-muted">Referencia no disponible</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-end"><?= (int)$m->total_referencias ?></td>
                  <td class="text-end"><?= (int)$m->total_stock ?></td>
                  <td class="text-end">$
                    <?= number_format((float)$m->valor_total, 2, ',', '.') ?></td>
                  <td>
                    <span class="badge bg-<?= ($m->id_estado ?? 1) == 1 ? 'success' : 'secondary' ?>">
                      <?= html_escape($m->nombre_estado ?? 'Activo') ?>
                    </span>
                  </td>
                  <td>
                    <?php if (!empty($m->nombre_usuario) || !empty($m->apellido_usuario)): ?>
                      <?= html_escape(trim(($m->nombre_usuario ?? '').' '.($m->apellido_usuario ?? ''))) ?>
                    <?php else: ?>
                      <span class="text-muted">-</span>
                    <?php endif; ?>
                  </td>
                  <td><?= !empty($m->fecha_creacion) ? date('d/m/Y H:i', strtotime($m->fecha_creacion)) : '-' ?></td>
                  <td><?= !empty($m->fecha_modificacion) ? date('d/m/Y H:i', strtotime($m->fecha_modificacion)) : '-' ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="8" class="text-center py-4 text-muted">No hay registros en el inventario global.</td>
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
  if ($.fn.DataTable) {
    $('#movimientosTable').DataTable({
      order: [[0, 'desc']]
    });
  }
});
</script>
