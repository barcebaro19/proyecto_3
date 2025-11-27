<div class="container-fluid">
    <!-- Report Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Reportes de Inventario</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <button class="btn btn-outline-primary w-100" onclick="generarReporteGeneral()">
                                <i class="fas fa-file-alt fa-2x mb-2"></i><br>
                                Reporte General
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-outline-success w-100" onclick="generarReporteStock()">
                                <i class="fas fa-boxes fa-2x mb-2"></i><br>
                                Estado de Stock
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-outline-warning w-100" onclick="generarReporteMovimientos()">
                                <i class="fas fa-exchange-alt fa-2x mb-2"></i><br>
                                Movimientos
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-outline-danger w-100" onclick="generarReporteValoracion()">
                                <i class="fas fa-dollar-sign fa-2x mb-2"></i><br>
                                Valoración
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos Más Vendidos -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-trophy me-2 text-warning"></i>Productos Más Vendidos</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($productos_mas_vendidos)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Posición</th>
                                        <th>Producto</th>
                                        <th class="text-center">Salidas</th>
                                        <th class="text-end">Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $pos = 1; foreach (array_slice($productos_mas_vendidos, 0, 10) as $producto): ?>
                                        <tr>
                                            <td>
                                                <?php if ($pos <= 3): ?>
                                                    <span class="badge bg-<?= $pos == 1 ? 'warning' : ($pos == 2 ? 'secondary' : 'danger') ?>">
                                                        #<?= $pos ?>
                                                    </span>
                                                <?php else: ?>
                                                    #<?= $pos ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($producto->nombre_referencia ?? 'N/A') ?></strong><br>
                                                <small class="text-muted"><?= htmlspecialchars($producto->codigo_interno ?? '') ?></small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-danger"><?= $producto->total_salidas ?? 0 ?></span>
                                            </td>
                                            <td class="text-end">
                                                $<?= number_format(($producto->total_salidas ?? 0) * ($producto->precio_unitario ?? 0), 2) ?>
                                            </td>
                                        </tr>
                                        <?php $pos++; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-chart-line fa-3x mb-3"></i>
                            <p>No hay datos de ventas disponibles</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2 text-danger"></i>Alertas de Stock</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($productos_bajo_stock)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th class="text-center">Stock</th>
                                        <th class="text-center">Mínimo</th>
                                        <th class="text-center">Nivel</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($productos_bajo_stock, 0, 10) as $producto): ?>
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
                                                <?php 
                                                $nivel = (($producto->cantidad_stock ?? 0) / ($producto->stock_minimo ?? 1)) * 100;
                                                ?>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-danger" role="progressbar" 
                                                         style="width: <?= min($nivel, 100) ?>%">
                                                        <?= round($nivel) ?>%
                                                    </div>
                                                </div>
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
    </div>

    <!-- Gráfico de Valoración -->
    <div class="row g-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-area me-2"></i>Valoración del Inventario</h5>
                </div>
                <div class="card-body">
                    <canvas id="valoracionChart" height="60"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Gráfico de Valoración
const ctxValoracion = document.getElementById('valoracionChart').getContext('2d');
new Chart(ctxValoracion, {
    type: 'bar',
    data: {
        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        datasets: [{
            label: 'Valor del Inventario ($)',
            data: [45000, 52000, 48000, 61000, 58000, 65000, 72000, 68000, 75000, 82000, 88000, 95000],
            backgroundColor: 'rgba(37, 99, 235, 0.7)',
            borderColor: '#2563eb',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Funciones para generar reportes
function generarReporteGeneral() {
    Swal.fire({
        title: 'Generando Reporte General',
        text: 'Por favor espere...',
        icon: 'info',
        showConfirmButton: false,
        timer: 1500
    }).then(() => {
        window.location.href = '<?= site_url("jefe/productos") ?>';
    });
}

function generarReporteStock() {
    Swal.fire({
        title: 'Reporte de Stock',
        html: '<p>Este reporte mostrará el estado actual del inventario.</p>',
        icon: 'success',
        confirmButtonText: 'Entendido'
    });
}

function generarReporteMovimientos() {
    Swal.fire({
        title: 'Reporte de Movimientos',
        html: '<p>Este reporte mostrará todas las entradas y salidas del inventario.</p>',
        icon: 'info',
        confirmButtonText: 'Entendido'
    });
}

function generarReporteValoracion() {
    Swal.fire({
        title: 'Reporte de Valoración',
        html: '<p>Este reporte mostrará el valor total del inventario.</p>',
        icon: 'warning',
        confirmButtonText: 'Entendido'
    });
}
</script>
