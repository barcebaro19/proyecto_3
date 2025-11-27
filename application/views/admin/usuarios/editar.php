<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

<div class="container py-4">

    <div class="card shadow-lg mb-4">
        <div class="card-header bg-white border-0">
            <h3 class="card-title h5 mb-0 text-dark">
                <i class="fa-solid fa-user-pen me-2 text-primary"></i> Editar Información del Usuario
            </h3>
        </div>
        
        <div class="card-body">

            <?php if($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= $this->session->flashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <?php if($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?= $this->session->flashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            <form action="<?= site_url('admin/actualizar_usuario') ?>" method="POST" class="row g-3">
                
                <input type="hidden" name="id_usuario" value="<?= $usuario->id_usuario ?>">

                <div class="col-md-6">
                    <label for="nombre" class="form-label text-muted">Nombre</label>
                    <input 
                        type="text" 
                        name="nombre" 
                        value="<?= $usuario->nombre ?>" 
                        required
                        id="nombre"
                        class="form-control"
                    >
                </div>
                <div class="col-md-6">
                    <label for="apellido" class="form-label text-muted">Apellido</label>
                    <input 
                        type="text" 
                        name="apellido" 
                        value="<?= $usuario->apellido ?>" 
                        required
                        id="apellido"
                        class="form-control"
                    >
                </div>

                <div class="col-md-4">
                    <label for="fecha_nacimiento" class="form-label text-muted">Fecha de nacimiento</label>
                    <input 
                        type="date" 
                        name="fecha_nacimiento" 
                        value="<?= !empty($usuario->fecha_nacimiento) ? date('Y-m-d', strtotime($usuario->fecha_nacimiento)) : '' ?>" 
                        id="fecha_nacimiento"
                        class="form-control"
                    >
                </div>
                <div class="col-md-4">
                    <label for="id_genero" class="form-label text-muted">Género</label>
                    <select 
                        name="id_genero" 
                        id="id_genero"
                        class="form-select"
                    >
                        <option value="">Seleccione...</option>
                        <option value="1" <?= isset($usuario->id_genero) && $usuario->id_genero == 1 ? 'selected' : '' ?>>Masculino</option>
                        <option value="2" <?= isset($usuario->id_genero) && $usuario->id_genero == 2 ? 'selected' : '' ?>>Femenino</option>
                        <option value="3" <?= isset($usuario->id_genero) && $usuario->id_genero == 3 ? 'selected' : '' ?>>Otro</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="id_estado_civil" class="form-label text-muted">Estado civil</label>
                    <select 
                        name="id_estado_civil" 
                        id="id_estado_civil"
                        class="form-select"
                    >
                        <option value="">Seleccione...</option>
                        <option value="1" <?= isset($usuario->id_estado_civil) && $usuario->id_estado_civil == 1 ? 'selected' : '' ?>>Soltero(a)</option>
                        <option value="2" <?= isset($usuario->id_estado_civil) && $usuario->id_estado_civil == 2 ? 'selected' : '' ?>>Casado(a)</option>
                        <option value="3" <?= isset($usuario->id_estado_civil) && $usuario->id_estado_civil == 3 ? 'selected' : '' ?>>Unión libre</option>
                        <option value="4" <?= isset($usuario->id_estado_civil) && $usuario->id_estado_civil == 4 ? 'selected' : '' ?>>Divorciado(a)</option>
                        <option value="5" <?= isset($usuario->id_estado_civil) && $usuario->id_estado_civil == 5 ? 'selected' : '' ?>>Viudo(a)</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="correo" class="form-label text-muted">Correo</label>
                    <input 
                        type="email" 
                        name="correo" 
                        value="<?= $usuario->correo ?>" 
                        required
                        id="correo"
                        class="form-control"
                    >
                </div>
                <div class="col-md-6">
                    <label for="telefono" class="form-label text-muted">Teléfono</label>
                    <input 
                        type="text" 
                        name="telefono" 
                        value="<?= $usuario->telefono ?>" 
                        id="telefono"
                        class="form-control"
                    >
                </div>

                <div class="col-12">
                    <label for="direccion" class="form-label text-muted">Dirección</label>
                    <textarea 
                        name="direccion" 
                        rows="2" 
                        id="direccion"
                        class="form-control"
                    ><?= $usuario->direccion ?></textarea>
                </div>

                <div class="col-md-6">
                    <label for="rol" class="form-label text-muted">Rol</label>
                    <select 
                        name="id_rol" 
                        required
                        id="rol"
                        class="form-select"
                    >
                        <?php foreach ($roles as $rol): ?>
                        <option 
                            value="<?= $rol->id_rol ?>" 
                            <?= $rol->id_rol == $usuario->id_rol ? 'selected' : '' ?>
                        >
                            <?= $rol->nombre ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="estado" class="form-label text-muted">Estado</label>
                    <select 
                        name="id_estado" 
                        required
                        id="estado"
                        class="form-select"
                    >
                        <?php foreach ($estados as $estado): ?>
                        <option 
                            value="<?= $estado->id_estado ?>" 
                            <?= $estado->id_estado == $usuario->id_estado ? 'selected' : '' ?>
                        >
                            <?= $estado->nombre_estado ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="password" class="form-label text-muted">Nueva contraseña (opcional)</label>
                    <input 
                        type="password" 
                        name="password"
                        id="password"
                        class="form-control"
                        placeholder="Dejar vacío para no cambiar"
                    >
                </div>
                <div class="col-md-6">
                    <label for="password2" class="form-label text-muted">Confirmar contraseña</label>
                    <input 
                        type="password" 
                        name="password2"
                        id="password2"
                        class="form-control"
                    >
                </div>

                <div class="col-12 d-flex justify-content-end pt-3">
                    <button 
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="fa-solid fa-save me-2"></i> Guardar Cambios
                    </button>
                </div>

            </form>
            </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>