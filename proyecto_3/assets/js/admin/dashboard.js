// Inicialización cuando el documento está listo
$(document).ready(function() {
    // Inicializar DataTable para la tabla de movimientos
    if ($('#movimientosTable').length) {
        $('#movimientosTable').DataTable({
            "order": [[3, "desc"]], // Ordenar por fecha descendente por defecto
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            },
            "responsive": true,
            "pageLength": 10
        });
    }

    // Inicializar gráfico de inventario
    initInventoryChart();

    // Configurar botón de exportar gráfico
    $('#exportChartBtn').on('click', function(e) {
        e.preventDefault();
        exportChartToImage();
    });

    // Inicializar tooltips
    $('[data-toggle="tooltip"]').tooltip();
});

// Inicializar gráfico de inventario
function initInventoryChart() {
    var ctx = document.getElementById('inventarioChart');
    if (!ctx) return;

    // Datos de ejemplo (deberían venir del controlador)
    var chartData = {
        labels: <?php echo json_encode(array_column($productos_por_categoria ?? [], 'categoria')); ?>,
        datasets: [{
            label: 'Productos por Categoría',
            data: <?php echo json_encode(array_column($productos_por_categoria ?? [], 'total')); ?>,
            backgroundColor: [
                'rgba(54, 162, 235, 0.6)',
                'rgba(255, 99, 132, 0.6)',
                'rgba(255, 206, 86, 0.6)',
                'rgba(75, 192, 192, 0.6)',
                'rgba(153, 102, 255, 0.6)'
            ],
            borderColor: [
                'rgba(54, 162, 235, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)'
            ],
            borderWidth: 1
        }]
    };

    var options = {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            }
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.parsed.y + ' productos';
                    }
                }
            }
        }
    };

    // Crear el gráfico
    window.inventarioChart = new Chart(ctx, {
        type: 'bar',
        data: chartData,
        options: options
    });
}

// Exportar gráfico a imagen
function exportChartToImage() {
    if (!window.inventarioChart) return;
    
    // Crear un enlace temporal
    const link = document.createElement('a');
    link.download = 'inventario-' + new Date().toISOString().split('T')[0] + '.png';
    link.href = window.inventarioChart.toBase64Image('image/png');
    link.click();
}

// Función para actualizar el dashboard
function updateDashboard() {
    $.ajax({
        url: '<?php echo site_url("admin/dashboard/actualizar"); ?>',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Actualizar contadores
                if (response.data.total_usuarios !== undefined) {
                    $('.card-total-usuarios .count').text(response.data.total_usuarios);
                }
                if (response.data.total_productos !== undefined) {
                    $('.card-total-productos .count').text(response.data.total_productos);
                }
                if (response.data.productos_bajo_stock !== undefined) {
                    $('.card-bajo-stock .count').text(response.data.productos_bajo_stock);
                }
                
                // Mostrar notificación de actualización
                showNotification('success', 'Dashboard actualizado correctamente');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al actualizar el dashboard:', error);
            showNotification('error', 'Error al actualizar el dashboard');
        }
    });
}

// Mostrar notificación
function showNotification(type, message) {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    
    const toastContainer = document.getElementById('toastContainer');
    if (toastContainer) {
        toastContainer.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        // Eliminar el toast después de que se oculte
        toast.addEventListener('hidden.bs.toast', function () {
            toast.remove();
        });
    }
}

// Actualizar el dashboard cada 5 minutos
setInterval(updateDashboard, 5 * 60 * 1000);
