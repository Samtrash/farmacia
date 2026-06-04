<?= $this->extend('plantilla/layout') ?>
<?= $this->section('contenido') ?>

<div class="pos-layout">
  <!-- CATÁLOGO (izquierda) -->
  <div class="pos-catalog">
    <div class="search-section">
      <div class="search-bar search-bar-code">
        <span class="material-symbols-outlined">qr_code_scanner</span>
        <input type="text" id="inputCodigo" placeholder="Escanear código..." onkeyup="buscarCodigo()" maxlength="30">
      </div>
      <div class="search-bar search-bar-name">
        <span class="material-symbols-outlined">search</span>
        <input type="text" id="inputNombre" placeholder="Buscar por nombre..." onkeyup="buscarNombre()">
      </div>
    </div>

    <div class="catalog-table" id="catalogTable">
      <table>
        <thead>
          <tr>
            <th style="width:5%" class="hide-mobile">#</th>
            <th style="width:42%">Descripción</th>
            <th style="width:12%" class="hide-mobile">Cant.</th>
            <th style="width:13%">Precio</th>
            <th style="width:13%">Stock</th>
            <th style="width:15%">Acción</th>
          </tr>
        </thead>
        <tbody id="tbCatalogo">
          <tr><td colspan="6" style="padding:40px;color:#94a3b8;text-align:center">Busque un producto para comenzar</td></tr>
        </tbody>
      </table>
      <div class="catalog-footer" id="catalogFooter">0 resultados</div>
    </div>
  </div>

  <!-- CARRITO (derecha) -->
  <div class="pos-cart">
    <div class="cart-header">
      <span class="material-symbols-outlined">shopping_cart</span>
      Carrito de Venta
    </div>

    <div class="cart-items">
      <table>
        <thead>
          <tr>
            <th style="width:5%">#</th>
            <th style="width:45%">Producto</th>
            <th style="width:12%">Cant.</th>
            <th style="width:18%">Precio</th>
            <th style="width:20%">Subtotal</th>
          </tr>
        </thead>
        <tbody id="tbCarrito">
          <tr class="empty"><td colspan="5" style="padding:40px;color:#94a3b8;text-align:center">
            <span class="material-symbols-outlined" style="font-size:36px;display:block;margin-bottom:8px">add_shopping_cart</span>
            Agregue productos al carrito
          </td></tr>
        </tbody>
      </table>
    </div>

    <!-- DATOS DEL CLIENTE -->
    <div class="cart-client">
      <div class="client-title">Datos del Cliente (Boleta / Factura)</div>
      <div class="client-row">
        <label>Tipo:</label>
        <select id="tipoCliente" onchange="cambiarTipo()">
          <option value="1">DNI</option>
          <option value="2">RUC</option>
        </select>
        <label id="lblDoc">N° DNI:</label>
        <input type="text" id="dniRuc" maxlength="8" inputmode="numeric" onkeypress="soloNumeros(event)" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
        <label id="lblNombre">Nombres:</label>
        <input type="text" id="nombreRazon" maxlength="50">
      </div>
    </div>

    <!-- TOTAL -->
    <div class="cart-total">
      <div>
        <div class="total-label">TOTAL A PAGAR</div>
        <div class="total-items" id="totalItems">0 productos</div>
      </div>
      <div class="total-amount">S/ <span id="totalMonto">0.00</span></div>
    </div>

    <!-- BOTONES DE ACCIÓN -->
    <div class="cart-actions">
      <button class="btn-nota" onclick="procesarVenta(0)">Nota Interna</button>
      <button class="btn-boleta" onclick="procesarVenta(3)">Boleta</button>
      <button class="btn-factura" onclick="procesarVenta(1)">Factura</button>
    </div>
  </div>
</div>

<!-- Hidden data -->
<input type="hidden" id="atiende" value="<?= session('usuario') ?>">
<input type="hidden" id="baseUrl" value="<?= base_url() ?>">

<?= $this->endSection() ?>
