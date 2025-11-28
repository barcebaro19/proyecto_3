<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid px-4 py-3">
  <div class="row mb-3">
    <div class="col-md-8">
      <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-box-open me-2 text-primary"></i>Detalle de Producto
      </h1>
      <small class="text-muted">Código interno: <?= htmlspecialchars($producto->codigo_interno) ?></small>
    </div>
    <div class="col-md-4 text-end">
      <a href="<?= site_url('admin/productos') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Volver
      </a>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-8">
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
          <h6 class="m-0 fw-bold text-primary">Información del producto</h6>
        </div>
        <div class="card-body">
          <dl class="row mb-0">
            <dt class="col-sm-4">Referencia</dt>
            <dd class="col-sm-8">
              <?= htmlspecialchars($producto->nombre_referencia ?? '') ?>
              <small class="d-block text-muted">Código: <?= htmlspecialchars($producto->codigo_referencia ?? '') ?></small>
            </dd>

            <dt class="col-sm-4">Descripción</dt>
            <dd class="col-sm-8"><?= htmlspecialchars($producto->descripcion) ?></dd>

            <dt class="col-sm-4">Talla</dt>
            <dd class="col-sm-8"><?= htmlspecialchars($producto->talla ?? 'N/A') ?></dd>

            <dt class="col-sm-4">Color</dt>
            <dd class="col-sm-8"><?= htmlspecialchars($producto->color ?? 'N/A') ?></dd>
          </dl>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
          <h6 class="m-0 fw-bold text-primary">Inventario</h6>
        </div>
        <div class="card-body">
          <p class="mb-1 text-muted">Stock actual</p>
          <p class="h4">
            <span class="badge bg-<?= $producto->cantidad_stock <= $producto->stock_minimo ? 'danger' : 'success' ?>">
              <?= (int)$producto->cantidad_stock ?> unidades
            </span>
          </p>

          <p class="mb-1 text-muted">Stock mínimo</p>
          <p class="mb-3"><?= (int)$producto->stock_minimo ?> unidades</p>

          <p class="mb-1 text-muted">Precio unitario</p>
          <p class="h5 text-success">$<?= number_format($producto->precio_unitario, 2, ',', '.') ?></p>

          <p class="mb-1 text-muted">Fecha de creación</p>
          <p class="mb-2"><?= !empty($producto->fecha_creacion) ? date('d/m/Y H:i', strtotime($producto->fecha_creacion)) : '-' ?></p>

          <p class="mb-1 text-muted">Estado</p>
          <p>
            <span class="badge bg-<?= $producto->id_estado == 1 ? 'success' : 'secondary' ?>">
              <?= htmlspecialchars($producto->nombre_estado ?? ($producto->id_estado == 1 ? 'Activo' : 'Inactivo')) ?>
            </span>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>
