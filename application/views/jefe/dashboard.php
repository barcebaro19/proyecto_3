<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card primary">
                <div class="icon">
                    <i class="fas fa-box"></i>
                </div>
                <h3><?= isset($total_productos) ? $total_productos : 0 ?></h3>
                <p>Total de Productos</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card success">
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3><?= isset($productos_bajo_stock) ? count($productos_bajo_stock) : 0 ?></h3>
                <p>Productos Bajo Stock</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card warning">
                <div class="icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <h3><?= isset($ultimos_movimientos) ? count($ultimos_movimientos) : 0 ?></h3>
                <p>Movimientos Recientes</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card danger">
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>100%</h3>
                <p>Eficiencia Operativa</p>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Movimientos de Inventario</h5>
                </div>
                <div class="card-body">
                    <canvas id="movimientosChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-pie-chart me-2"></i>Estado de Stock</h5>
                </div>
                <div class="card-body">
                    <canvas id="stockChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2 text-warning"></i>Productos Bajo Stock</h5>
                    <a href="<?= site_url('jefe/productos') ?>" class="btn btn-sm btn-outline-primary">Ver Todos</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($productos_bajo_stock)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th class="text-center">Stock Actual</th>
                                        <th class="text-center">Stock Mínimo</th>
                                        <th class="text-center">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($productos_bajo_stock, 0, 5) as $producto): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($producto->nombre_referencia ?? 'N/A') ?></strong><br>
                                                <small class="text-muted"><?= htmlspecialchars($producto->codigo_interno ?? '') ?></small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-danger"><?= $producto->cantidad_stock ?? 0 ?></span>
                                            </td>
                                            <td class="text-center"><?= $producto->stock_minimo ?? 0 ?></td>
                                            <td class="text-center">
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-exclamation-circle"></i> Bajo
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                            <p>No hay productos con stock bajo</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-history me-2 text-info"></i>Últimos Movimientos</h5>
                    <a href="<?= site_url('jefe/reportes') ?>" class="btn btn-sm btn-outline-primary">Ver Reportes</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($ultimos_movimientos)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($ultimos_movimientos as $mov): ?>
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1"><?= htmlspecialchars($mov->nombre_referencia ?? 'N/A') ?></h6>
                                            <small class="text-muted">
                                                <?= htmlspecialchars($mov->tipo_movimiento ?? 'N/A') ?> - 
                                                <?= htmlspecialchars($mov->descripcion ?? '') ?>
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-<?= ($mov->tipo_movimiento ?? '') == 'entrada' ? 'success' : 'danger' ?>">
                                                <?= ($mov->tipo_movimiento ?? '') == 'entrada' ? '+' : '-' ?><?= $mov->cantidad ?? 0 ?>
                                            </span>
                                            <br>
                                            <small class="text-muted"><?= date('d/m/Y', strtotime($mov->fecha_movimiento ?? 'now')) ?></small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>No hay movimientos recientes</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Gráfico de Movimientos
const ctxMovimientos = document.getElementById('movimientosChart').getContext('2d');
new Chart(ctxMovimientos, {
    type: 'line',
    data: {
        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        datasets: [{
            label: 'Entradas',
            data: [12, 19, 15, 25, 22, 30, 28, 35, 32, 38, 42, 45],
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4
        }, {
            label: 'Salidas',
            data: [8, 12, 10, 15, 18, 20, 22, 25, 28, 30, 35, 38],
            borderColor: '#ef4444',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Gráfico de Stock
const ctxStock = document.getElementById('stockChart').getContext('2d');
new Chart(ctxStock, {
    type: 'doughnut',
    data: {
        labels: ['Stock Normal', 'Stock Bajo', 'Sin Stock'],
        datasets: [{
            data: [<?= isset($total_productos) ? $total_productos - count($productos_bajo_stock ?? []) : 0 ?>, <?= count($productos_bajo_stock ?? []) ?>, 0],
            backgroundColor: ['#10b981', '#f59e0b', '#ef4444']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});
</script>
