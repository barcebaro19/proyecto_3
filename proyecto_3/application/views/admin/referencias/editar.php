<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<div class="container-fluid px-4 py-3">
  <div class="row mb-3">
    <div class="col-md-8">
      <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-tags me-2 text-primary"></i>Editar Referencia
      </h1>
    </div>
    <div class="col-md-4 text-end">
      <a href="<?= site_url('admin/referencias') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Volver
      </a>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <form action="<?= site_url('admin/actualizar_referencia') ?>" method="post" class="row g-3">
        <input type="hidden" name="id_referencia" value="<?= $referencia->id_referencia ?>">

        <div class="col-md-4">
          <label class="form-label text-muted">Código</label>
          <input type="text" name="codigo_referencia" class="form-control" value="<?= $referencia->codigo_referencia ?>" required>
        </div>

        <div class="col-md-8">
          <label class="form-label text-muted">Nombre</label>
          <input type="text" name="nombre_referencia" class="form-control" value="<?= $referencia->nombre_referencia ?>" required>
        </div>

        <div class="col-md-6">
          <label class="form-label text-muted">Categoría</label>
          <select name="id_categoria" class="form-select">
            <option value="">Sin categoría</option>
            <?php if (!empty($categorias)): foreach ($categorias as $cat): ?>
              <option value="<?= $cat->id_categoria ?>" <?= $cat->id_categoria == $referencia->id_categoria ? 'selected' : '' ?>>
                <?= $cat->nombre ?>
              </option>
            <?php endforeach; endif; ?>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label text-muted">Estado</label>
          <select name="id_estado" class="form-select">
            <option value="1" <?= $referencia->id_estado == 1 ? 'selected' : '' ?>>Activo</option>
            <option value="2" <?= $referencia->id_estado == 2 ? 'selected' : '' ?>>Inactivo</option>
          </select>
        </div>

        <div class="col-12">
          <label class="form-label text-muted">Descripción</label>
          <textarea name="descripcion" rows="2" class="form-control"><?= $referencia->descripcion ?></textarea>
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
