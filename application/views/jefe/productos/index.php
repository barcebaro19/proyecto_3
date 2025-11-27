<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-box me-2"></i>Inventario de Productos
                <span class="badge bg-primary ms-2"><?= isset($productos) ? count($productos) : 0 ?></span>
            </h5>
            <div>
                <button class="btn btn-success btn-sm" id="btnExportExcel">
                    <i class="fas fa-file-excel me-1"></i> Exportar a Excel
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle" id="productosTable">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Código</th>
                            <th>Referencia</th>
                            <th class="text-center">Talla</th>
                            <th class="text-center">Color</th>
                            <th class="text-center">Stock Actual</th>
                            <th class="text-center">Stock Mínimo</th>
                            <th class="text-center">Precio</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($productos)): ?>
                            <?php $i = 1; foreach ($productos as $producto): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($producto->codigo_interno ?? 'N/A') ?></strong>
                                    </td>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars($producto->nombre_referencia ?? 'N/A') ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($producto->codigo_referencia ?? '') ?></small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($producto->talla ?? 'N/A') ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($producto->color ?? 'N/A') ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                        $stock = $producto->cantidad_stock ?? 0;
                                        $stock_min = $producto->stock_minimo ?? 0;
                                        $badge_class = $stock <= $stock_min ? 'bg-danger' : ($stock <= $stock_min * 1.5 ? 'bg-warning' : 'bg-success');
                                        ?>
                                        <span class="badge <?= $badge_class ?>"><?= $stock ?></span>
                                    </td>
                                    <td class="text-center"><?= $producto->stock_minimo ?? 0 ?></td>
                                    <td class="text-center">
                                        $<?= number_format($producto->precio_unitario ?? 0, 2) ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-<?= ($producto->id_estado ?? 0) == 1 ? 'success' : 'secondary' ?>">
                                            <?= htmlspecialchars($producto->nombre_estado ?? 'N/A') ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= site_url('jefe/ver_producto/' . ($producto->id_producto ?? 0)) ?>" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Ver Detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>No hay productos registrados</p>
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
    // Inicializar DataTable
    const table = $('#productosTable').DataTable({
        language: {
            url: '<?= IP_SERVER ?>assets/datatables/es-ES.json'
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel me-1"></i> Exportar a Excel',
                className: 'btn btn-success btn-sm d-none',
                title: 'Inventario de Productos - ' + new Date().toLocaleDateString(),
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                }
            }
        ],
        order: [[1, 'asc']],
        pageLength: 25,
        responsive: true
    });

    // Botón personalizado de exportar
    $('#btnExportExcel').on('click', function() {
        table.button('.buttons-excel').trigger();
    });
});
</script>
