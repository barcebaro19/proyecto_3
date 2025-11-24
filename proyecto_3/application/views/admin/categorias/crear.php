<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
        <a href="<?= site_url('categoria') ?>" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Volver al listado
        </a>
    </div>

    <!-- Mensajes de error de validación -->
    <?php if (validation_errors()): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="alert-heading">Por favor corrija los siguientes errores:</h6>
            <?= validation_errors('<div>', '</div>') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Formulario de creación -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Datos de la categoría</h6>
        </div>
        <div class="card-body">
            <?= form_open('categoria/guardar', ['id' => 'formCategoria']) ?>
                <div class="form-group row">
                    <label for="nombre" class="col-sm-2 col-form-label">Nombre *</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control <?= form_error('nombre') ? 'is-invalid' : '' ?>" 
                               id="nombre" name="nombre" value="<?= set_value('nombre') ?>" required>
                        <small class="form-text text-muted">Ingrese el nombre de la categoría</small>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="descripcion" class="col-sm-2 col-form-label">Descripción</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?= set_value('descripcion') ?></textarea>
                        <small class="form-text text-muted">Descripción opcional de la categoría</small>
                    </div>
                </div>
                
                <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                        <a href="<?= site_url('categoria') ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<?php $this->load->view('templates/footer'); ?>

<script>
    $(document).ready(function() {
        // Validación del formulario
        $('#formCategoria').validate({
            rules: {
                nombre: {
                    required: true,
                    minlength: 3,
                    maxlength: 100
                },
                descripcion: {
                    maxlength: 255
                }
            },
            messages: {
                nombre: {
                    required: "El nombre es obligatorio",
                    minlength: "El nombre debe tener al menos 3 caracteres",
                    maxlength: "El nombre no puede tener más de 100 caracteres"
                },
                descripcion: {
                    maxlength: "La descripción no puede tener más de 255 caracteres"
                }
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });
    });
</script>
