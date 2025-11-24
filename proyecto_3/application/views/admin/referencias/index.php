<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>


<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold">
                <i class="fas fa-tags me-2 text-primary"></i><?= $title ?>
            </h1>
            <p class="text-muted mb-0">Administra y gestiona las referencias de productos</p>
        </div>
        <!-- Button with manual onclick to ensure it works -->
        <button type="button" class="btn btn-primary" onclick="abrirModalReferencia()">
            <i class="fas fa-plus me-2"></i>Nueva Referencia
        </button>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Listado de Referencias
                <span class="badge badge-primary ms-2"><?= isset($referencias) ? count($referencias) : 0 ?></span>
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
                                        <span class="badge badge-<?= $ref->id_estado == 1 ? 'success' : ($ref->id_estado == 2 ? 'secondary' : 'danger') ?>">
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

<!-- Custom Modal Implementation -->
<style>
/* Custom styles to ensure modal works regardless of Bootstrap version */
#customModalOverlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1050;
    overflow-y: auto;
    overflow-x: hidden;
    transition: opacity 0.15s linear;
}

#customModalDialog {
    position: relative;
    width: auto;
    margin: 1.75rem auto;
    max-width: 800px;
    pointer-events: none;
    transform: translate(0, -50px);
    transition: transform 0.3s ease-out;
}

#customModalOverlay.show {
    display: block;
}

#customModalOverlay.show #customModalDialog {
    transform: none;
    pointer-events: auto;
}

.custom-modal-content {
    position: relative;
    display: flex;
    flex-direction: column;
    width: 100%;
    pointer-events: auto;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid rgba(0,0,0,.2);
    border-radius: .3rem;
    outline: 0;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
</style>

<!-- Custom Modal HTML -->
<div id="customModalOverlay">
  <div id="customModalDialog">
    <div class="custom-modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fas fa-tag me-2"></i>Nueva Referencia</h5>
        <button type="button" class="close text-white" onclick="cerrarModalReferencia()" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="referenciaForm">
          <div class="form-row">
              <div class="form-group col-md-4">
                <label class="text-muted">Código</label>
                <input type="text" name="codigo_referencia" class="form-control" required>
              </div>
              <div class="form-group col-md-8">
                <label class="text-muted">Nombre</label>
                <input type="text" name="nombre_referencia" class="form-control" required>
              </div>
          </div>
          <div class="form-row">
              <div class="form-group col-md-6">
                <label class="text-muted">Categoría</label>
                <select name="id_categoria" class="form-control">
                  <option value="" selected>Sin categoría</option>
                  <?php if (!empty($categorias)): foreach ($categorias as $cat): ?>
                    <option value="<?= $cat->id_categoria ?>"><?= $cat->nombre ?></option>
                  <?php endforeach; endif; ?>
                </select>
              </div>
              <div class="form-group col-md-6">
                <label class="text-muted">Estado</label>
                <select name="id_estado" class="form-control">
                  <option value="1" selected>Activo</option>
                  <option value="2">Inactivo</option>
                </select>
              </div>
          </div>
          <div class="form-group">
            <label class="text-muted">Descripción</label>
            <textarea name="descripcion" rows="2" class="form-control"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer bg-light border-0">
        <button type="button" class="btn btn-light" onclick="cerrarModalReferencia()">Cancelar</button>
        <button type="button" class="btn btn-primary" id="guardarReferencia">Guardar</button>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.32/sweetalert2.all.min.js"></script>

<script>
// Pure JavaScript Modal Control
function abrirModalReferencia() {
    console.log('Abriendo modal personalizado...');
    var overlay = document.getElementById('customModalOverlay');
    overlay.style.display = 'block';
    // Small timeout to allow display:block to apply before adding class for transition
    setTimeout(function() {
        overlay.classList.add('show');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }, 10);
}

function cerrarModalReferencia() {
    console.log('Cerrando modal personalizado...');
    var overlay = document.getElementById('customModalOverlay');
    overlay.classList.remove('show');
    document.body.style.overflow = ''; // Restore scrolling
    
    // Wait for transition to finish before hiding
    setTimeout(function() {
        overlay.style.display = 'none';
        // Reset form
        document.getElementById('referenciaForm').reset();
    }, 300);
}

// Close modal if clicking outside
document.getElementById('customModalOverlay').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModalReferencia();
    }
});

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

// Main initialization
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado, inicializando eventos...');

    // Initialize DataTable if jQuery is available (it might be loaded later, so we check)
    // We use a interval to check for jQuery for the table, as it's not critical for the modal
    var checkJQuery = setInterval(function() {
        if (typeof $ !== 'undefined' && $.fn.DataTable) {
            $('#referenciasTable').DataTable();
            clearInterval(checkJQuery);
        }
    }, 100);

    // Save Reference Logic (Vanilla JS)
    const btnGuardar = document.getElementById('guardarReferencia');
    if(btnGuardar) {
        btnGuardar.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Botón Guardar clickeado');

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
                    'X-Requested-With': 'XMLHttpRequest' // Mark as AJAX for CodeIgniter
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Respuesta servidor:', data);
                if (data.success) {
                    showAlert('success', '¡Éxito!', 'Referencia agregada satisfactoriamente');
                    cerrarModalReferencia();
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

    // Delete Reference Logic (Vanilla JS delegation not strictly needed if we attach to existing buttons, 
    // but since buttons might be dynamic if we used AJAX table, delegation is better. 
    // For now, we'll attach to existing buttons since the table is server-side rendered initially)
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
