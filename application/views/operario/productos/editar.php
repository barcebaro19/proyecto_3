<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Editar Producto</h1>
        <a href="<?= site_url('operario/productos') ?>" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Volver
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <?= $producto->codigo_interno ?> - <?= $producto->nombre_referencia ?>
            </h6>
        </div>
        <div class="card-body">
            <?= form_open('operario/editar_producto/' . $producto->id_producto, ['class' => 'needs-validation', 'novalidate' => '']) ?>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Referencia</label>
                        <input type="text" class="form-control" value="<?= $producto->nombre_referencia ?>" readonly disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Código Interno</label>
                        <input type="text" class="form-control" value="<?= $producto->codigo_interno ?>" readonly disabled>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="2"><?= $producto->descripcion ?></textarea>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Talla</label>
                        <input type="text" name="talla" class="form-control" value="<?= $producto->talla ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Color</label>
                        <input type="text" name="color" class="form-control" value="<?= $producto->color ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Precio Unitario</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="precio_unitario" class="form-control" step="0.01" min="0" value="<?= $producto->precio_unitario ?>">
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card border-left-primary h-100">
                            <div class="card-body">
                                <h6 class="text-primary font-weight-bold">Inventario</h6>
                                <div class="form-group">
                                    <label>Cantidad Stock <span class="text-danger">*</span></label>
                                    <input type="number" name="cantidad_stock" class="form-control form-control-lg" value="<?= $producto->cantidad_stock ?>" min="0" required>
                                    <small class="text-muted">Modificar esto ajustará el stock manualmente (ajuste de inventario)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-left-warning h-100">
                            <div class="card-body">
                                <h6 class="text-warning font-weight-bold">Alertas</h6>
                                <div class="form-group">
                                    <label>Stock Mínimo <span class="text-danger">*</span></label>
                                    <input type="number" name="stock_minimo" class="form-control" value="<?= $producto->stock_minimo ?>" min="0" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i> Actualizar Producto
                    </button>
                </div>

            <?= form_close() ?>
        </div>
    </div>
</div>
