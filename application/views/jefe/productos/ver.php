<div class="container-fluid">
    <?php if (isset($producto) && $producto): ?>
        <div class="row g-4">
            <!-- Información del Producto -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información del Producto</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Código Interno:</th>
                                <td><strong><?= htmlspecialchars($producto->codigo_interno ?? 'N/A') ?></strong></td>
                            </tr>
                            <tr>
                                <th>Referencia:</th>
                                <td><?= htmlspecialchars($producto->nombre_referencia ?? 'N/A') ?></td>
                            </tr>
                            <tr>
                                <th>Código Referencia:</th>
                                <td><?= htmlspecialchars($producto->codigo_referencia ?? 'N/A') ?></td>
                            </tr>
                            <tr>
                                <th>Código Proveedor:</th>
                                <td><?= htmlspecialchars($producto->codigo_proveedor ?? 'N/A') ?></td>
                            </tr>
                            <tr>
                                <th>Descripción:</th>
                                <td><?= htmlspecialchars($producto->descripcion ?? 'Sin descripción') ?></td>
                            </tr>
                            <tr>
                                <th>Talla:</th>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($producto->talla ?? 'N/A') ?></span></td>
                            </tr>
                            <tr>
                                <th>Color:</th>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($producto->color ?? 'N/A') ?></span></td>
                            </tr>
                            <tr>
                                <th>Estado:</th>
                                <td>
                                    <span class="badge bg-<?= ($producto->id_estado ?? 0) == 1 ? 'success' : 'secondary' ?>">
                                        <?= htmlspecialchars($producto->nombre_estado ?? 'N/A') ?>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Información de Stock -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Información de Stock</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Stock Actual:</th>
                                <td>
                                    <?php 
                                    $stock = $producto->cantidad_stock ?? 0;
                                    $stock_min = $producto->stock_minimo ?? 0;
                                    $badge_class = $stock <= $stock_min ? 'bg-danger' : ($stock <= $stock_min * 1.5 ? 'bg-warning' : 'bg-success');
                                    ?>
                                    <h3 class="mb-0">
                                        <span class="badge <?= $badge_class ?>"><?= $stock ?></span>
                                    </h3>
                                </td>
                            </tr>
                            <tr>
                                <th>Stock Mínimo:</th>
                                <td><strong><?= $producto->stock_minimo ?? 0 ?></strong></td>
                            </tr>
                            <tr>
                                <th>Precio Unitario:</th>
                                <td><strong>$<?= number_format($producto->precio_unitario ?? 0, 2) ?></strong></td>
                            </tr>
                            <tr>
                                <th>Valor Total:</th>
                                <td>
                                    <h4 class="text-primary mb-0">
                                        $<?= number_format(($producto->cantidad_stock ?? 0) * ($producto->precio_unitario ?? 0), 2) ?>
                                    </h4>
                                </td>
                            </tr>
                            <tr>
                                <th>Total Entradas:</th>
                                <td><span class="badge bg-success">+<?= $producto->total_entradas ?? 0 ?></span></td>
                            </tr>
                            <tr>
                                <th>Total Salidas:</th>
                                <td><span class="badge bg-danger">-<?= $producto->total_salidas ?? 0 ?></span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Historial de Movimientos -->
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Historial de Movimientos</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($movimientos)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped" id="movimientosTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Tipo</th>
                                            <th class="text-center">Cantidad</th>
                                            <th>Descripción</th>
                                            <th>Usuario</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($movimientos as $mov): ?>
                                            <tr>
                                                <td><?= date('d/m/Y H:i', strtotime($mov->fecha_movimiento ?? 'now')) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= ($mov->tipo_movimiento ?? '') == 'entrada' ? 'success' : 'danger' ?>">
                                                        <i class="fas fa-<?= ($mov->tipo_movimiento ?? '') == 'entrada' ? 'arrow-down' : 'arrow-up' ?>"></i>
                                                        <?= ucfirst($mov->tipo_movimiento ?? 'N/A') ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <strong><?= ($mov->tipo_movimiento ?? '') == 'entrada' ? '+' : '-' ?><?= $mov->cantidad ?? 0 ?></strong>
                                                </td>
                                                <td><?= htmlspecialchars($mov->descripcion ?? 'Sin descripción') ?></td>
                                                <td><?= htmlspecialchars($mov->usuario_nombre ?? 'N/A') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p>No hay movimientos registrados para este producto</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="col-12">
                <div class="d-flex justify-content-between">
                    <a href="<?= site_url('jefe/productos') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver a Productos
                    </a>
                    <button class="btn btn-success" onclick="exportarDetalle()">
                        <i class="fas fa-file-excel me-2"></i>Exportar Detalle
                    </button>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Producto no encontrado
        </div>
        <a href="<?= site_url('jefe/productos') ?>" class="btn btn-primary">
            <i class="fas fa-arrow-left me-2"></i>Volver a Productos
        </a>
    <?php endif; ?>
</div>

<script>
$(document).ready(function() {
    $('#movimientosTable').DataTable({
        language: {
            url: '<?= IP_SERVER ?>assets/datatables/es-ES.json'
        },
        order: [[0, 'desc']],
        pageLength: 10
    });
});

function exportarDetalle() {
    Swal.fire({
        title: 'Exportando Detalle',
        text: 'Generando archivo Excel...',
        icon: 'success',
        timer: 1500,
        showConfirmButton: false
    });
}
</script>
