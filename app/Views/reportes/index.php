<?= $this->extend('plantilla/layout') ?>
<?= $this->section('contenido') ?>

<div class="content has-footbar">
  <div class="reporte-filtro-bar" style="margin-bottom:16px">
    <div class="reporte-filtro-inner">
      <span class="reporte-filtro-label">Periodo:</span>
      <select class="form-control reporte-select" id="selMes">
        <?php
        $meses = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        $mesActual = date('n');
        for($i=1;$i<=12;$i++):
        ?>
        <option value="<?= $i ?>" <?= $i==$mesActual?'selected':'' ?>><?= $meses[$i] ?></option>
        <?php endfor; ?>
      </select>
      <input class="form-control reporte-anio" type="text" id="inputAnio" value="<?= date('Y') ?>" maxlength="4" onkeypress="soloNumeros(event)">
      <button class="btn btn-primary reporte-btn-buscar" onclick="buscarReporte()">
        <span class="material-symbols-outlined" style="font-size:18px">search</span>
        <span class="reporte-buscar-text">Buscar</span>
      </button>
    </div>
  </div>

  <!-- Resultados -->
  <div id="resultados"></div>
</div>

<!-- La tabla se genera desde JS, aquí no hay thead estático -->
<div class="modal-overlay" id="modalDetalle">
  <div class="modal-box" style="max-width:560px">
    <div class="modal-header">
      <h3 id="detalleTitulo">Detalle de Venta</h3>
      <button class="close-btn" onclick="closeModal('modalDetalle')">✕</button>
    </div>
    <div class="modal-body">
      <div class="table-wrap">
        <table class="tabla" id="tablaDetalle"></table>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modalDetalle')">Cerrar</button>
      <button class="btn btn-primary" onclick="imprimirVenta()">
        <span class="material-symbols-outlined" style="font-size:16px">print</span> Imprimir
      </button>
      <button class="btn btn-danger" onclick="anularVenta()">
        <span class="material-symbols-outlined" style="font-size:16px">delete</span> Anular
      </button>
    </div>
  </div>
</div>

<input type="hidden" id="baseUrl" value="<?= base_url() ?>">

<?= $this->endSection() ?>
