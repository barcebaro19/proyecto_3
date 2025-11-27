<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold">
                <i class="fas fa-tags me-2 text-primary"></i><?= $title ?>
            </h1>
            <p class="text-muted mb-0">Administra y gestiona las referencias de productos</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevaReferenciaModal">
            <i class="fas fa-plus me-2"></i>Nueva Referencia
        </button>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Listado de Referencias
                <span class="badge bg-primary ms-2"><?= isset($referencias) ? count($referencias) : 0 ?></span>
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle" id="referenciasTable">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Descripción</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($referencias)): ?>
                            <?php $i = 1; foreach ($referencias as $ref): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= htmlspecialchars($ref->codigo_referencia) ?></td>
                                    <td><?= htmlspecialchars($ref->nombre_referencia) ?></td>
                                    <td><?= htmlspecialchars($ref->nombre_categoria ?? 'Sin categoría') ?></td>
                                    <td><?= htmlspecialchars($ref->descripcion) ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-<?= $ref->id_estado == 1 ? 'success' : ($ref->id_estado == 2 ? 'secondary' : 'danger') ?>">
                                            <?= htmlspecialchars($ref->nombre_estado ?? '') ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group" role="group">
                                            <a href="<?= site_url('admin/ver_referencia/' . $ref->id_referencia) ?>" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= site_url('admin/editar_referencia/' . $ref->id_referencia) ?>" class="btn btn-outline-secondary btn-sm">
                                                <i class="far fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger btn-sm btn-eliminar-ref" data-id="<?= $ref->id_referencia ?>">
                                                <i class="far fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">No hay referencias registradas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Referencia (Bootstrap 5) -->
<div class="modal fade" id="nuevaReferenciaModal" tabindex="-1" aria-labelledby="nuevaReferenciaModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="nuevaReferenciaModalLabel"><i class="fas fa-tag me-2"></i>Nueva Referencia</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="referenciaForm">
          <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label text-muted">Código</label>
                <input type="text" name="codigo_referencia" class="form-control" required>
              </div>
              <div class="col-md-8">
                <label class="form-label text-muted">Nombre</label>
                <input type="text" name="nombre_referencia" class="form-control" required>
              </div>
              <div class="col-md-6">
                <label class="form-label text-muted">Categoría</label>
                <select name="id_categoria" class="form-select">
                  <option value="" selected>Sin categoría</option>
                  <?php if (!empty($categorias)): foreach ($categorias as $cat): ?>
                    <option value="<?= $cat->id_categoria ?>"><?= $cat->nombre ?></option>
                  <?php endforeach; endif; ?>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label text-muted">Estado</label>
                <select name="id_estado" class="form-select">
                  <option value="1" selected>Activo</option>
                  <option value="2">Inactivo</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label text-muted">Descripción</label>
                <textarea name="descripcion" rows="2" class="form-control"></textarea>
              </div>
          </div>
        </form>
      </div>
      <div class="modal-footer bg-light border-0">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="guardarReferencia">Guardar</button>
      </div>
    </div>
  </div>
</div>

<script>
// Helper function for alerts
function showAlert(icon, title, text) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: icon,
            title: title,
            text: text,
            showConfirmButton: icon !== 'success',
            timer: icon === 'success' ? 1500 : undefined
        }).then(() => {
            if (icon === 'success') location.reload();
        });
    } else {
        alert(title + ': ' + text);
        if (icon === 'success') location.reload();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable
    var checkJQuery = setInterval(function() {
        if (typeof $ !== 'undefined' && $.fn.DataTable) {
            $('#referenciasTable').DataTable({
                language: {
                    url: '<?= IP_SERVER ?>assets/datatables/es-ES.json'
                },
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                     '<"row"<"col-sm-12 col-md-6"B>>' +
                     '<"row"<"col-sm-12"tr>>' +
                     '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel me-1"></i> Exportar a Excel',
                        className: 'btn btn-success btn-sm',
                        title: 'Referencias - ' + new Date().toLocaleDateString(),
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5] // Exclude actions column
                        }
                    }
                ]
            });
            clearInterval(checkJQuery);
        }
    }, 100);

    // Save Reference Logic
    const btnGuardar = document.getElementById('guardarReferencia');
    if(btnGuardar) {
        btnGuardar.addEventListener('click', function(e) {
            e.preventDefault();
            
            const form = document.getElementById('referenciaForm');
            const formData = new FormData(form);
            
            // Validation
            const codigo = formData.get('codigo_referencia');
            const nombre = formData.get('nombre_referencia');

            if (!codigo || !nombre) {
                showAlert('warning', 'Atención', 'Por favor complete los campos obligatorios (Código y Nombre)');
                return;
            }

            // Disable button
            btnGuardar.disabled = true;
            btnGuardar.textContent = 'Guardando...';

            // Send Request
            fetch('<?= site_url('admin/crear_referencia') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', '¡Éxito!', 'Referencia agregada satisfactoriamente');
                    // Close modal using jQuery/Bootstrap
                    if (typeof $ !== 'undefined') {
                        $('#nuevaReferenciaModal').modal('hide');
                    }
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('error', 'Error', data.message || 'Error al crear la referencia');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Error', 'Error de conexión al guardar.');
            })
            .finally(() => {
                btnGuardar.disabled = false;
                btnGuardar.textContent = 'Guardar';
            });
        });
    }

    // Delete Reference Logic
    const deleteButtons = document.querySelectorAll('.btn-eliminar-ref');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('¿Está seguro de eliminar esta referencia?')) return;
            
            const id = this.getAttribute('data-id');
            
            fetch('<?= site_url('admin/eliminar_referencia/') ?>' + id, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Error al eliminar');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar la solicitud');
            });
        });
    });
});
</script>
