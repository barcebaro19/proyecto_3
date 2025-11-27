<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css " rel="stylesheet">
<div class="container-fluid px-4 py-3">
  <div class="row mb-3">
    <div class="col-md-8">
      <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-boxes me-2 text-primary"></i>Editar Producto
      </h1>
    </div>
    <div class="col-md-4 text-end">
      <a href="<?= site_url('admin/productos') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Volver
      </a>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <form action="<?= site_url('admin/actualizar_producto') ?>" method="post" class="row g-3">
        <input type="hidden" name="id_producto" value="<?= $producto->id_producto ?>">

        <div class="col-md-4">
          <label class="form-label text-muted">Referencia</label>
          <select name="id_referencia" class="form-select" required>
            <?php if (!empty($referencias)): foreach ($referencias as $ref): ?>
              <option value="<?= $ref->id_referencia ?>" <?= $ref->id_referencia == $producto->id_referencia ? 'selected' : '' ?>>
                <?= $ref->nombre_referencia ?>
              </option>
            <?php endforeach; endif; ?>
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label text-muted">Código interno</label>
          <input type="text" name="codigo_interno" class="form-control" value="<?= $producto->codigo_interno ?>" required>
        </div>

        <div class="col-md-4">
          <label class="form-label text-muted">Código proveedor</label>
          <input type="text" name="codigo_proveedor" class="form-control" value="<?= $producto->codigo_proveedor ?>">
        </div>

        <div class="col-md-6">
          <label class="form-label text-muted">Descripción</label>
          <input type="text" name="descripcion" class="form-control" value="<?= $producto->descripcion ?>">
        </div>

        <div class="col-md-3">
          <label class="form-label text-muted">Talla</label>
          <input type="text" name="talla" class="form-control" value="<?= $producto->talla ?>">
        </div>

        <div class="col-md-3">
          <label class="form-label text-muted">Color</label>
          <input type="text" name="color" class="form-control" value="<?= $producto->color ?>">
        </div>

        <div class="col-md-3">
          <label class="form-label text-muted">Stock</label>
          <input type="number" name="cantidad_stock" class="form-control" value="<?= $producto->cantidad_stock ?>" min="0" required>
        </div>

        <div class="col-md-3">
          <label class="form-label text-muted">Stock mínimo</label>
          <input type="number" name="stock_minimo" class="form-control" value="<?= $producto->stock_minimo ?>" min="0" required>
        </div>

        <div class="col-md-3">
          <label class="form-label text-muted">Precio unitario</label>
          <input type="number" step="0.01" name="precio_unitario" class="form-control" value="<?= $producto->precio_unitario ?>" min="0" required>
        </div>

        <div class="col-md-3">
          <label class="form-label text-muted">Estado</label>
          <select name="id_estado" class="form-select" required>
            <option value="1" <?= $producto->id_estado == 1 ? 'selected' : '' ?>>Activo</option>
            <option value="2" <?= $producto->id_estado == 2 ? 'selected' : '' ?>>Inactivo</option>
          </select>
        </div>

        <div class="col-12 text-end mt-3">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i> Guardar cambios
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
