<?= $this->extend('plantilla/layout') ?>
<?= $this->section('contenido') ?>

<div class="content">
  <div style="display:flex;gap:10px;margin-bottom:16px;align-items:center">
    <div style="flex:1"></div>
    <button class="btn btn-primary" onclick="modalNuevoUsuario()">
      <span class="material-symbols-outlined" style="font-size:18px">person_add</span> Agregar Usuario
    </button>
  </div>

  <div class="card">
    <div class="table-wrap">
      <table class="tabla">
        <thead>
          <tr>
            <th style="width:4%" class="hide-1100">#</th>
            <th style="width:15%" class="hide-980">Documento</th>
            <th>Nombres</th>
            <th class="hide-860">Apellidos</th>
            <th style="width:12%" class="hide-760">Contraseña</th>
            <th style="width:12%">Rol</th>
            <th style="width:9%"></th>
          </tr>
        </thead>
        <tbody id="tbUsuarios">
          <tr><td colspan="7" style="text-align:center;padding:30px;color:#94a3b8">Cargando...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- MODAL -->
<div class="modal-overlay" id="modalUsuario">
  <div class="modal-box">
    <div class="modal-header">
      <h3 id="modalTituloU">Agregar Usuario</h3>
      <button class="close-btn" onclick="closeModal('modalUsuario')">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="userId">
      <div class="form-group">
        <label>Documento / ID</label>
        <input class="form-control" type="text" id="userDni" maxlength="12" onkeypress="soloNumeros(event)">
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
        <div class="form-group">
          <label>Nombres</label>
          <input class="form-control" type="text" id="userNombre" maxlength="50">
        </div>
        <div class="form-group">
          <label>Apellidos</label>
          <input class="form-control" type="text" id="userApellidos" maxlength="50">
        </div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
        <div class="form-group">
          <label>Contraseña</label>
          <input class="form-control" type="text" id="userPass" maxlength="20">
        </div>
        <div class="form-group">
          <label>Rol</label>
          <select class="form-control" id="userMaster">
            <option value="1">Vendedor</option>
            <option value="2">Administrador</option>
          </select>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modalUsuario')">Cancelar</button>
      <button class="btn btn-primary" onclick="guardarUsuario()">Guardar</button>
    </div>
  </div>
</div>

<input type="hidden" id="baseUrl" value="<?= base_url() ?>">

<?= $this->endSection() ?>
