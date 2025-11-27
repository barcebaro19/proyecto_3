<div class="container-fluid">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <div class="user-avatar mx-auto mb-3" style="width: 100px; height: 100px; font-size: 2.5rem;">
                        <?= strtoupper(substr($this->session->userdata('nombre') ?? 'U', 0, 1)) ?>
                    </div>
                    <h4><?= $this->session->userdata('nombre') ?? 'Usuario' ?> <?= $this->session->userdata('apellido') ?? '' ?></h4>
                    <p class="text-muted">Jefe de Operaciones</p>
                    <hr>
                    <div class="text-start">
                        <p class="mb-2">
                            <i class="fas fa-envelope me-2 text-primary"></i>
                            <strong>Email:</strong> <?= $this->session->userdata('correo') ?? 'No disponible' ?>
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-phone me-2 text-success"></i>
                            <strong>Teléfono:</strong> <?= $this->session->userdata('telefono') ?? 'No disponible' ?>
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-calendar me-2 text-info"></i>
                            <strong>Miembro desde:</strong> <?= date('d/m/Y') ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Editar Perfil</h5>
                </div>
                <div class="card-body">
                    <form action="<?= site_url('jefe/perfil') ?>" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre *</label>
                                <input type="text" name="nombre" class="form-control" 
                                       value="<?= set_value('nombre', $usuario->nombre ?? $this->session->userdata('nombre')) ?>" required>
                                <?= form_error('nombre', '<small class="text-danger">', '</small>') ?>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Apellido</label>
                                <input type="text" name="apellido" class="form-control" 
                                       value="<?= set_value('apellido', $usuario->apellido ?? $this->session->userdata('apellido')) ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Correo Electrónico *</label>
                                <input type="email" name="correo" class="form-control" 
                                       value="<?= set_value('correo', $usuario->correo ?? $this->session->userdata('correo')) ?>" required>
                                <?= form_error('correo', '<small class="text-danger">', '</small>') ?>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" class="form-control" 
                                       value="<?= set_value('telefono', $usuario->telefono ?? $this->session->userdata('telefono')) ?>">
                            </div>

                            <div class="col-12">
                                <hr>
                                <h6 class="text-muted">Cambiar Contraseña (Opcional)</h6>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nueva Contraseña</label>
                                <input type="password" name="nueva_contrasena" class="form-control" 
                                       placeholder="Dejar en blanco para mantener la actual">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Confirmar Contraseña</label>
                                <input type="password" name="confirmar_contrasena" class="form-control" 
                                       placeholder="Confirmar nueva contraseña">
                            </div>

                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                                        <i class="fas fa-arrow-left me-2"></i>Cancelar
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Guardar Cambios
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
