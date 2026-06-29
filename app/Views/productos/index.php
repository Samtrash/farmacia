<?= $this->extend('plantilla/layout') ?>
<?= $this->section('contenido') ?>

<div class="content has-footbar">
  <!-- Top Actions -->
  <div style="display:flex;gap:10px;margin-bottom:16px;align-items:center;flex-wrap:wrap">
    <div class="search-bar" style="flex:1;min-width:200px">
      <span class="material-symbols-outlined">search</span>
      <input type="text" id="buscarProducto" placeholder="Buscar producto..." onkeyup="filtrarProductos()">
    </div>
    <button class="btn btn-primary" onclick="modalNuevo()">
      <span class="material-symbols-outlined" style="font-size:18px">add</span> Agregar Producto
    </button>
  </div>

  <!-- Tabla -->
  <div class="card">
    <div class="table-wrap">
      <table class="tabla">
        <thead>
          <tr>
            <th style="width:4%" class="hide-1200">#</th>
            <th style="width:14%" class="hide-1100">Código</th>
            <th>Descripción</th>
            <th style="width:9%" class="hide-980">P. Compra</th>
            <th style="width:9%">P. Venta</th>
            <th style="width:9%" class="hide-860">P. Promo</th>
            <th style="width:7%">Stock</th>
            <?php if(session('exp')): ?>
            <th style="width:9%" class="hide-760">Vencimiento</th>
            <?php endif; ?>
            <th style="width:9%"></th>
          </tr>
        </thead>
        <tbody id="tbProductos">
          <tr><td colspan="9" style="text-align:center;padding:30px;color:#94a3b8">Cargando...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- FOOTBAR -->
<div class="footbar" style="justify-content: center; gap: 16px;">
  <button class="btn btn-success" onclick="descargarExcelProductos()" style="padding: 12px 24px; font-size: 14px;">
    <span class="material-symbols-outlined">download</span> <span class="btn-text-mobile">Descargar Excel</span>
  </button>
  <button class="btn btn-secondary" onclick="imprimirReporteProductos()" style="padding: 12px 24px; font-size: 14px;">
    <span class="material-symbols-outlined">print</span> <span class="btn-text-mobile">Imprimir Reporte</span>
  </button>
</div>

<!-- MODAL Agregar/Editar -->
<div class="modal-overlay" id="modalProducto">
  <div class="modal-box">
    <div class="modal-header">
      <h3 id="modalTitulo">Agregar Producto</h3>
      <button class="close-btn" onclick="closeModal('modalProducto')">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="prodId">
      <div class="form-group">
        <label>Código de Barras</label>
        <input class="form-control" type="text" id="prodCodigo" maxlength="30" placeholder="Ej: 7750304005708"
          inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
      </div>
      <div class="form-group">
        <label>Descripción</label>
        <input class="form-control" type="text" id="prodDetalle" maxlength="70" placeholder="Nombre del producto">
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
        <div class="form-group">
          <label>P. Compra</label>
          <input class="form-control" type="text" id="prodPcompra" placeholder="0.00"
            inputmode="decimal" oninput="filtrarDecimal(this)">
        </div>
        <div class="form-group">
          <label>P. Venta</label>
          <input class="form-control" type="text" id="prodPventa" placeholder="0.00"
            inputmode="decimal" oninput="filtrarDecimal(this)">
        </div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
        <div class="form-group">
          <label>P. Promoción <span style="font-weight:400;text-transform:none;color:var(--gray-400)">(opcional)</span></label>
          <input class="form-control" type="text" id="prodPpromo" placeholder="0.00"
            inputmode="decimal" oninput="filtrarDecimal(this)">
        </div>
        <div class="form-group">
          <label>Stock</label>
          <input class="form-control" type="text" id="prodStock" placeholder="0"
            inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
        </div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px">
        <div class="form-group">
          <label>Contiene <span style="font-weight:400;text-transform:none;color:var(--gray-400)">(uds)</span></label>
          <input class="form-control" type="text" id="prodContiene" placeholder="Opcional"
            inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
        </div>
        <div class="form-group">
          <label>Mes venc.</label>
          <select class="form-control" id="prodExpmes">
            <option value="">--</option>
            <option value="01">Enero</option>
            <option value="02">Febrero</option>
            <option value="03">Marzo</option>
            <option value="04">Abril</option>
            <option value="05">Mayo</option>
            <option value="06">Junio</option>
            <option value="07">Julio</option>
            <option value="08">Agosto</option>
            <option value="09">Septiembre</option>
            <option value="10">Octubre</option>
            <option value="11">Noviembre</option>
            <option value="12">Diciembre</option>
          </select>
        </div>
        <div class="form-group">
          <label>Año venc.</label>
          <input class="form-control" type="text" id="prodExpanio" maxlength="4" placeholder="AAAA"
            inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modalProducto')">Cancelar</button>
      <button class="btn btn-primary" id="btnGuardarProducto" onclick="guardarProducto()">Guardar</button>
    </div>
  </div>
</div>

<input type="hidden" id="baseUrl" value="<?= base_url() ?>">
<input type="hidden" id="tieneExp" value="<?= session('exp') ? '1' : '0' ?>">

<script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js"></script>

<?= $this->endSection() ?>
