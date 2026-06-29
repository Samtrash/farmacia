<?= $this->extend('plantilla/layout') ?>
<?= $this->section('contenido') ?>

<div class="content has-footbar">
    <div class="ajustes-container">
        
        <!-- TARJETA 1: Datos del negocio -->
        <div class="ajustes-card">
            <div class="card-header-flex">
                <div class="header-title">
                    <span class="material-symbols-outlined icon-negocio">storefront</span>
                    <div>
                        <h3>Datos del negocio</h3>
                        <p>Nombre, dirección y datos fiscales</p>
                    </div>
                </div>
                <button class="btn btn-warning" onclick="toggleEditNegocio()" id="btnEditNegocio">
                    <span class="material-symbols-outlined">edit</span> Editar
                </button>
            </div>
            
            <div class="card-body">
                <form id="formNegocio" onsubmit="guardarNegocio(event)">
                    <div class="ajustes-grid">
                        <div class="info-row">
                            <span class="info-label">NOMBRE</span>
                            <div class="info-value">
                                <span class="view-mode"><?= esc($negocio->nombre) ?></span>
                                <input type="text" name="nombre" class="form-control edit-mode" value="<?= esc($negocio->nombre) ?>" required>
                            </div>
                        </div>

                        <div class="info-row">
                            <span class="info-label">RUBRO</span>
                            <div class="info-value">
                                <span class="view-mode"><?= esc($negocio->rubro) ?></span>
                                <input type="text" name="rubro" class="form-control edit-mode" value="<?= esc($negocio->rubro) ?>" required>
                            </div>
                        </div>

                        <div class="info-row">
                            <span class="info-label">DIRECCIÓN</span>
                            <div class="info-value">
                                <span class="view-mode"><?= esc($negocio->direccion) ?></span>
                                <input type="text" name="direccion" class="form-control edit-mode" value="<?= esc($negocio->direccion) ?>" required>
                            </div>
                        </div>

                        <div class="info-row">
                            <span class="info-label">SEDE</span>
                            <div class="info-value">
                                <span class="view-mode"><?= esc($negocio->sede) ?></span>
                                <input type="text" name="sede" class="form-control edit-mode" value="<?= esc($negocio->sede) ?>" required>
                            </div>
                        </div>

                        <div class="info-row">
                            <span class="info-label">RUC</span>
                            <div class="info-value">
                                <span class="view-mode"><?= esc($negocio->ruc) ?></span>
                                <input type="text" name="ruc" class="form-control edit-mode" value="<?= esc($negocio->ruc) ?>">
                            </div>
                        </div>

                        <div class="info-row">
                            <span class="info-label">PIE TICKET</span>
                            <div class="info-value">
                                <span class="view-mode"><em><?= esc($negocio->foot) ?></em></span>
                                <input type="text" name="foot" class="form-control edit-mode" value="<?= esc($negocio->foot) ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions edit-mode">
                        <button type="button" class="btn btn-secondary" onclick="toggleEditNegocio()">Cancelar</button>
                        <button type="submit" class="btn btn-success"><span class="material-symbols-outlined">save</span> Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- TARJETA 2: Licencia -->
        <div class="ajustes-card mt-4">
            <div class="card-header-flex">
                <div class="header-title">
                    <span class="material-symbols-outlined icon-licencia">lock</span>
                    <div>
                        <h3>Licencia</h3>
                        <p>Estado de la licencia del sistema</p>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <div class="ajustes-grid">
                    <div class="info-row">
                        <span class="info-label">ESTADO</span>
                        <div class="info-value">
                            <?php 
                                $esActiva = ($licencia && $licencia->error == 0); 
                            ?>
                            <span class="badge <?= $esActiva ? 'badge-success' : 'badge-danger' ?> status-badge">
                                <?= $esActiva ? 'Activa' : 'Inactiva / Vencida' ?>
                            </span>
                        </div>
                    </div>

                    <div class="info-row">
                        <span class="info-label">VENCE</span>
                        <div class="info-value">
                            <span><?= esc($negocio->soporte) ?></span>
                        </div>
                    </div>
                </div>

                <div class="licencia-action mt-4 pt-3 border-top">
                    <button class="btn btn-primary" onclick="renovarLicencia()">
                        <span class="material-symbols-outlined">key</span> Ingresar nueva licencia
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
