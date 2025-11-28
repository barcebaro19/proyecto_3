<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-2 text-gray-800">Panel de Operario</h2>
            <p class="text-muted">Gestión de inventario y movimientos de bodega</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Productos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_productos'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Movimientos Totales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_movimientos'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Stock Bajo</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($productos_bajo_stock) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Acciones Rápidas</div>
                            <a href="<?= site_url('operario/movimientos') ?>" class="btn btn-sm btn-info shadow-sm">
                                <i class="fas fa-plus fa-sm text-white-50"></i> Nuevo Movimiento
                            </a>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Productos Bajo Stock -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-danger">Alertas de Stock Bajo</h6>
                    <a href="<?= site_url('operario/productos') ?>" class="btn btn-sm btn-danger shadow-sm">Ver Todo</a>
                </div>
                <div class="card-body">
                    <?php if (empty($productos_bajo_stock)): ?>
                        <p class="text-center text-muted my-3">No hay productos con stock bajo.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Ref</th>
                                        <th>Stock</th>
                                        <th>Mínimo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($productos_bajo_stock as $prod): ?>
                                    <tr>
                                        <td><?= $prod->codigo_interno ?></td>
                                        <td><?= $prod->nombre_referencia ?></td>
                                        <td class="text-danger font-weight-bold"><?= $prod->cantidad_stock ?></td>
                                        <td><?= $prod->stock_minimo ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Últimos Movimientos -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Últimos Movimientos</h6>
                    <a href="<?= site_url('operario/movimientos') ?>" class="btn btn-sm btn-primary shadow-sm">Ver Todo</a>
                </div>
                <div class="card-body">
                    <?php if (empty($ultimos_movimientos)): ?>
                        <p class="text-center text-muted my-3">No hay movimientos recientes.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Producto</th>
                                        <th>Cant.</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($ultimos_movimientos as $mov): ?>
                                    <tr>
                                        <td>
                                            <?php if (strtolower($mov->tipo) == 'entrada'): ?>
                                                <span class="badge badge-success">Entrada</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Salida</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small><?= $mov->producto_nombre ?></small>
                                        </td>
                                        <td><?= $mov->cantidad ?></td>
                                        <td><small><?= date('d/m H:i', strtotime($mov->fecha)) ?></small></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Matriz de Inventario en Dashboard -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-th me-2"></i>Tabla Principal de Inventario
                    </h6>
                    <a href="<?= site_url('operario/tabla_principal') ?>" class="btn btn-sm btn-primary shadow-sm">
                        <i class="fas fa-expand me-1"></i>Ver Completa
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0" id="matrizTableDashboard">
                            <thead class="bg-light text-center">
                                <tr>
                                    <th rowspan="2" class="align-middle">Referencia</th>
                                    <th rowspan="2" class="align-middle">Color</th>
                                    <th colspan="<?= count($tallas_cols) ?>">Tallas (Stock Actual)</th>
                                    <th colspan="4" class="bg-white border-start">Totales</th>
                                </tr>
                                <tr>
                                    <?php foreach ($tallas_cols as $talla): ?>
                                        <th class="text-secondary small"><?= $talla ?></th>
                                    <?php endforeach; ?>
                                    <th class="text-primary small bg-white border-start">Inv. Ini</th>
                                    <th class="text-success small bg-white">Ent</th>
                                    <th class="text-danger small bg-white">Sal</th>
                                    <th class="text-dark small bg-white fw-bold">Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($matriz)): ?>
                                    <?php 
                                    // Mostrar solo las primeras 5 filas en el dashboard
                                    $count = 0;
                                    foreach ($matriz as $row): 
                                        if ($count >= 5) break;
                                        $count++;
                                    ?>
                                        <tr>
                                            <td class="fw-bold text-primary"><?= htmlspecialchars($row['referencia']) ?></td>
                                            <td>
                                                <span class="badge bg-light text-dark border">
                                                    <?= htmlspecialchars($row['color']) ?>
                                                </span>
                                            </td>
                                            
                                            <!-- Celdas de Tallas -->
                                            <?php foreach ($tallas_cols as $talla): ?>
                                                <td class="text-center p-1">
                                                    <?php if (isset($row['tallas'][$talla])): 
                                                        $data = $row['tallas'][$talla];
                                                    ?>
                                                        <button type="button" class="btn btn-sm <?= $data['stock'] > 0 ? 'btn-outline-primary' : 'btn-outline-light text-muted' ?> w-100 position-relative btn-detalle-talla"
                                                                data-bs-toggle="popover"
                                                                data-bs-html="true"
                                                                data-bs-trigger="hover"
                                                                title="Detalle <?= $talla ?>"
                                                                data-bs-content="
                                                                    <div class='small'>
                                                                        <div>Inv. Ini: <b><?= $data['inv_inicial'] ?></b></div>
                                                                        <div class='text-success'>Entradas: +<?= $data['entradas'] ?></div>
                                                                        <div class='text-danger'>Salidas: -<?= $data['salidas'] ?></div>
                                                                        <div class='border-top mt-1 pt-1 fw-bold'>Saldo: <?= $data['stock'] ?></div>
                                                                    </div>
                                                                ">
                                                            <?= $data['stock'] ?>
                                                        </button>
                                                    <?php else: ?>
                                                        <span class="text-muted small">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; ?>

                                            <!-- Totales -->
                                            <td class="text-center border-start bg-light"><?= $row['total_inv_inicial'] ?></td>
                                            <td class="text-center text-success bg-light">+<?= $row['total_entradas'] ?></td>
                                            <td class="text-center text-danger bg-light">-<?= $row['total_salidas'] ?></td>
                                            <td class="text-center fw-bold bg-light"><?= $row['total_stock'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (count($matriz) > 5): ?>
                                        <tr>
                                            <td colspan="<?= 6 + count($tallas_cols) ?>" class="text-center py-3 bg-light">
                                                <a href="<?= site_url('operario/tabla_principal') ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye me-1"></i>Ver todas las referencias (<?= count($matriz) - 5 ?> más)
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="<?= 6 + count($tallas_cols) ?>" class="text-center py-5 text-muted">
                                            No hay datos para mostrar.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inicializar Popovers para la matriz en el dashboard
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
      return new bootstrap.Popover(popoverTriggerEl)
    });
});
</script>
