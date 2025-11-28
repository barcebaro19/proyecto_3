<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Nuevo Producto</h1>
        <a href="<?= site_url('operario/productos') ?>" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Volver
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Información del Producto</h6>
        </div>
        <div class="card-body">
            <?= form_open('operario/crear_producto', ['class' => 'needs-validation', 'novalidate' => '']) ?>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Referencia <span class="text-danger">*</span></label>
                        <select name="id_referencia" id="select_referencia" class="form-control" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($referencias as $ref): ?>
                                <option value="<?= $ref->id_referencia ?>" data-codigo="<?= $ref->codigo_referencia ?>">
                                    <?= $ref->nombre_referencia ?> (<?= $ref->codigo_referencia ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Código Interno <span class="text-danger">*</span></label>
                        <input type="text" name="codigo_interno" id="input_codigo_interno" class="form-control" required readonly>
                        <small class="text-muted">Se genera automáticamente al seleccionar referencia</small>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="2"></textarea>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Talla</label>
                        <input type="text" name="talla" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Color</label>
                        <input type="text" name="color" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Precio Unitario</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="precio_unitario" class="form-control" step="0.01" min="0">
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card border-left-primary h-100">
                            <div class="card-body">
                                <h6 class="text-primary font-weight-bold">Inventario Inicial</h6>
                                <div class="form-group">
                                    <label>Cantidad Stock <span class="text-danger">*</span></label>
                                    <input type="number" name="cantidad_stock" class="form-control form-control-lg" value="0" min="0" required>
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
                                    <input type="number" name="stock_minimo" class="form-control" value="5" min="0" required>
                                    <small class="text-muted">El sistema avisará cuando el stock baje de esta cantidad</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i> Guardar Producto
                    </button>
                </div>

            <?= form_close() ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Auto-generar código
    $('#select_referencia').on('change', function() {
        const id_referencia = $(this).val();
        const $codigoInput = $('#input_codigo_interno');
        
        if (!id_referencia) {
            $codigoInput.val('');
            return;
        }

        $codigoInput.val('Generando...');

        $.ajax({
            url: '<?= site_url("operario/obtener_siguiente_codigo") ?>',
            type: 'POST',
            data: { id_referencia: id_referencia },
            dataType: 'json',
            global: false, // Evitar alertas globales
            success: function(res) {
                if (res.exito) {
                    $codigoInput.val(res.datos.codigo);
                } else {
                    $codigoInput.val('');
                    // Si hay error, mostrarlo discretamente o permitir manual
                    console.error(res.mensaje);
                    $codigoInput.prop('readonly', false);
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr.responseText);
                $codigoInput.val('');
                $codigoInput.prop('readonly', false);
            }
        });
    });
});
</script>
