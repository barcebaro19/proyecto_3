<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid px-4 py-3">
  <div class="row mb-3">
    <div class="col-md-8">
      <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-tag me-2 text-primary"></i>Detalle de Referencia
      </h1>
      <small class="text-muted">Código: <?= htmlspecialchars($referencia->codigo_referencia) ?></small>
    </div>
    <div class="col-md-4 text-end">
      <a href="<?= site_url('admin/referencias') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Volver
      </a>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-8">
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
          <h6 class="m-0 fw-bold text-primary">Información de la referencia</h6>
        </div>
        <div class="card-body">
          <dl class="row mb-0">
            <dt class="col-sm-4">Nombre</dt>
            <dd class="col-sm-8"><?= htmlspecialchars($referencia->nombre_referencia) ?></dd>

            <dt class="col-sm-4">Categoría</dt>
            <dd class="col-sm-8"><?= htmlspecialchars($referencia->nombre_categoria ?? 'Sin categoría') ?></dd>

            <dt class="col-sm-4">Descripción</dt>
            <dd class="col-sm-8"><?= htmlspecialchars($referencia->descripcion ?? '') ?></dd>
          </dl>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
          <h6 class="m-0 fw-bold text-primary">Estado</h6>
        </div>
        <div class="card-body">
          <p class="mb-1 text-muted">Estado</p>
          <p>
            <span class="badge bg-<?= $referencia->id_estado == 1 ? 'success' : ($referencia->id_estado == 2 ? 'secondary' : 'danger') ?>">
              <?= htmlspecialchars($referencia->nombre_estado ?? '') ?>
            </span>
          </p>

          <p class="mb-1 text-muted">Fecha de creación</p>
          <p class="mb-2"><?= !empty($referencia->fecha_creacion) ? date('d/m/Y H:i', strtotime($referencia->fecha_creacion)) : '-' ?></p>

          <?php if (!empty($referencia->fecha_modificacion)): ?>
            <p class="mb-1 text-muted">Última modificación</p>
            <p class="mb-0"><?= date('d/m/Y H:i', strtotime($referencia->fecha_modificacion)) ?></p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
