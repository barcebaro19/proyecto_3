<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!-- Estilos en forms.css -->

<!-- Tarjetas de Resumen -->
<div class="row g-3 mb-3">
    <?php 
    $colors = ['primary', 'success', 'info', 'warning'];
    $icons = ['users', 'box', 'tags', 'exclamation-triangle'];
    $i = 0;
    foreach($resumen_rapido as $item): 
        $color = $colors[$i % count($colors)];
        $icon = $icons[$i % count($icons)];
        $i++;
    ?>
    <div class="col-xl-3 col-md-6">
        <div class="card border-left-<?= $color ?> shadow-sm h-100">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-<?= $color ?> text-uppercase mb-1">
                            <?= $item['titulo'] ?></div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800"><?= number_format($item['valor']) ?></div>
                    </div>
                    <div class="rounded-circle bg-<?= $color ?>-light p-2">
                        <i class="fas fa-<?= $icon ?> text-<?= $color ?> fa-lg"></i>
                    </div>
                </div>
            </div>
            <a href="<?= $item['url'] ?>" class="small text-decoration-none text-center py-1 bg-light">
                Ver detalles <i class="fas fa-arrow-right small"></i>
            </a>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Alertas y Actividad Reciente -->
<div class="row">
    <!-- Alertas -->
    <div class="col-lg-6 mb-2">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-warning"><i class="fas fa-exclamation-triangle"></i> Alertas</h6>
            </div>
            <div class="card-body">
                <?php if(!empty($alertas)): ?>
                    <?php foreach($alertas as $alerta): ?>
                        <?php if(is_array($alerta)): ?>
                        <div class="alert alert-<?= isset($alerta['tipo']) ? $alerta['tipo'] : 'info' ?> alert-dismissible fade show" role="alert">
                            <i class="fas fa-<?= (isset($alerta['tipo']) && $alerta['tipo'] == 'warning') ? 'exclamation-triangle' : 'info-circle' ?> me-2"></i>
                            <?= isset($alerta['mensaje']) ? $alerta['mensaje'] : 'Alerta sin mensaje' ?>
                            <?php if(isset($alerta['url'])): ?>
                            <a href="<?= $alerta['url'] ?>" class="alert-link">Ver más</a>
                            <?php endif; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                        <p class="text-muted mb-0">¡Todo en orden! No hay alertas pendientes.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Actividad Reciente -->
    <div class="col-lg-6 mb-2">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history me-2"></i>Actividad Reciente</h6>
                <a href="<?= site_url('admin/movimientos') ?>" class="btn btn-sm btn-primary">Ver todo</a>
            </div>
            <div class="card-body">
                <?php if(!empty($actividad_reciente)): ?>
                    <div class="activity-feed">
                        <?php foreach($actividad_reciente as $actividad): ?>
                        <div class="activity-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong><?= isset($actividad->tipo) ? $actividad->tipo : 'Movimiento' ?></strong>
                                    <div class="text-muted small">
                                        <?= isset($actividad->descripcion) ? $actividad->descripcion : 'Sin descripción' ?>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-muted small">
                                        <?= isset($actividad->fecha) ? date('d/m/Y H:i', strtotime($actividad->fecha)) : 'Fecha no disponible' ?>
                                    </div>
                                    <?php if(isset($actividad->cantidad)): ?>
                                    <span class="badge bg-primary"><?= $actividad->cantidad ?> unidades</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-2">
                        <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                        <p class="text-muted mb-0">No hay actividad reciente para mostrar</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos y alertas -->
<div class="row g-2">
    <!-- Gráfico de movimientos -->
    <div class="col-12 col-xl-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-gray-900">Movimientos de Inventario</h6>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Últimos 6 meses
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="#">Últimos 3 meses</a></li>
                            <li><a class="dropdown-item active" href="#">Últimos 6 meses</a></li>
                            <li><a class="dropdown-item" href="#">Último año</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="chart-area" style="height: 300px;">
                    <canvas id="movimientosChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas de stock -->
    <div class="col-12 col-xl-2">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-gray-900">Productos con bajo stock</h6>
                    <span class="badge bg-danger"><?= $total_bajo_stock ?> alertas</span>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($productos_bajo_stock)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($productos_bajo_stock as $producto): ?>
                            <a href="<?= site_url('admin/productos/editar/' . $producto['id']) ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?= $producto['nombre'] ?></h6>
                                    <small class="text-danger">Stock: <?= $producto['stock'] ?></small>
                                </div>
                                <small class="text-muted">Mínimo: <?= $producto['stock_minimo'] ?></small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <div class="text-center p-3">
                        <a href="<?= site_url('admin/productos?filter=low_stock') ?>" class="btn btn-sm btn-outline-primary">
                            Ver todos los productos con bajo stock
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center p-4">
                        <i class="bi bi-check-circle text-success" style="font-size: 2.5rem;"></i>
                        <p class="mt-2 mb-0 text-muted">No hay productos con bajo stock</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Últimos movimientos y distribución por categoría -->
<div class="row mt-4">
    <!-- Últimos movimientos -->
    <div class="col-lg-6 mb-2">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold text-gray-900">Últimos Movimientos</h6>
            </div>
            <div class="card-body">
                <?php if (!empty($ultimos_movimientos)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Producto</th>
                                    <th>Tipo</th>
                                    <th class="text-end">Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ultimos_movimientos as $movimiento): ?>
                                <tr>
                                    <td class="text-nowrap"><?= date('d/m/Y', strtotime($movimiento['fecha'])) ?></td>
                                    <td class="text-truncate" style="max-width: 150px;" title="<?= htmlspecialchars($movimiento['producto_nombre']) ?>">
                                        <?= $movimiento['producto_nombre'] ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= $movimiento['tipo'] === 'entrada' ? 'bg-success' : 'bg-danger' ?>">
                                            <?= ucfirst($movimiento['tipo']) ?>
                                        </span>
                                    </td>
                                    <td class="text-end"><?= number_format($movimiento['cantidad']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 text-center">
                        <a href="<?= site_url('admin/movimientos') ?>" class="btn btn-sm btn-outline-primary">
                            Ver todos los movimientos
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-inbox text-muted" style="font-size: 2.5rem;"></i>
                        <p class="mt-2 mb-0 text-muted">No hay movimientos recientes</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Distribución por categoría -->
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold text-gray-900">Distribución por Categoría</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="categoriasChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <?php if (!empty($productos_por_categoria)): ?>
                        <?php foreach ($productos_por_categoria as $categoria): ?>
                            <span class="me-3">
                                <i class="fas fa-circle" style="color: <?= $categoria['color'] ? '#'.$categoria['color'] : '#4e73df' ?>"></i> 
                                <?= $categoria['nombre'] ?>
                            </span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No hay datos disponibles</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts para los gráficos principales (movimientos e inventario global por categoría) -->
<script>
// Esperar a que el documento esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de movimientos (entradas vs salidas por mes)
    var ctx = document.getElementById('movimientosChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: [
                    <?php 
                    if (!empty($movimientos_ultimos_meses)) {
                        $labels = [];
                        foreach ($movimientos_ultimos_meses as $mes) {
                            $labels[] = '"' . $mes['mes'] . '"';
                        }
                        echo implode(',', $labels);
                    } else {
                        echo '"Sin datos"';
                    }
                    ?>
                ],
                datasets: [{
                    label: 'Entradas',
                    data: [
                        <?php 
                        if (!empty($movimientos_ultimos_meses)) {
                            $entradas = [];
                            foreach ($movimientos_ultimos_meses as $mes) {
                                $entradas[] = $mes['entradas'] ?? 0;
                            }
                            echo implode(',', $entradas);
                        } else {
                            echo '0';
                        }
                        ?>
                    ],
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28, 200, 138, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }, {
                    label: 'Salidas',
                    data: [
                        <?php 
                        if (!empty($movimientos_ultimos_meses)) {
                            $salidas = [];
                            foreach ($movimientos_ultimos_meses as $mes) {
                                $salidas[] = abs($mes['salidas'] ?? 0);
                            }
                            echo implode(',', $salidas);
                        } else {
                            echo '0';
                        }
                        ?>
                    ],
                    borderColor: '#e74a3b',
                    backgroundColor: 'rgba(231, 74, 59, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    // Gráfico de distribución por categoría usando productos_por_categoria
    var ctx2 = document.getElementById('categoriasChart');
    if (ctx2) {
        new Chart(ctx2.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: [
                    <?php 
                    if (!empty($productos_por_categoria)) {
                        $nombres = [];
                        foreach ($productos_por_categoria as $categoria) {
                            $nombres[] = '"' . addslashes($categoria['nombre']) . '"';
                        }
                        echo implode(',', $nombres);
                    } else {
                        echo '"Sin datos"';
                    }
                    ?>
                ],
                datasets: [{
                    data: [
                        <?php 
                        if (!empty($productos_por_categoria)) {
                            $cantidades = [];
                            foreach ($productos_por_categoria as $categoria) {
                                $cantidades[] = $categoria['total'];
                            }
                            echo implode(',', $cantidades);
                        } else {
                            echo '1';
                        }
                        ?>
                    ],
                    backgroundColor: [
                        <?php 
                        if (!empty($productos_por_categoria)) {
                            $colores = [];
                            foreach ($productos_por_categoria as $categoria) {
                                $colores[] = '"#' . ($categoria['color'] ?: '4e73df') . '"';
                            }
                            echo implode(',', $colores);
                        } else {
                            echo '"#6c757d"';
                        }
                        ?>
                    ],
                    hoverBackgroundColor: [
                        <?php 
                        if (!empty($productos_por_categoria)) {
                            $colores_hover = [];
                            foreach ($productos_por_categoria as $categoria) {
                                $colores_hover[] = '"#' . ($categoria['color'] ?: '2e59d9') . '"';
                            }
                            echo implode(',', $colores_hover);
                        } else {
                            echo '"#5a6268"';
                        }
                        ?>
                    ],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    }
                },
                cutout: '70%'
            },
        });
    }

    // Inicializar tooltips de Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
