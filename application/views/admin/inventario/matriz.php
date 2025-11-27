<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold">
                <i class="fas fa-th me-2 text-primary"></i>Matriz de Inventario
            </h1>
            <p class="text-muted mb-0">Vista consolidada por Referencia y Color (Estilo Excel)</p>
        </div>
        <div>
            <a href="<?= site_url('admin/productos') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-list me-2"></i>Vista Lista
            </a>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importarModal">
                <i class="fas fa-file-csv me-2"></i>Importar
            </button>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle" id="matrizTable">
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
                            <?php foreach ($matriz as $row): ?>
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
                        <?php else: ?>
                            <tr>
                                <td colspan="<?= 6 + count($tallas_cols) ?>" class="text-center py-5 text-muted">
                                    No hay datos para mostrar. Importa tu inventario.
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
    // Inicializar Popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
      return new bootstrap.Popover(popoverTriggerEl)
    });
});
</script>
