/* ─── Variables Globales ─── */
let BASE = '';
let carrito = []; // [{id, nombre, cantidad, precio}]
let ventaEnProceso = false;

/* ─── BÚSQUEDA ─── */
let timerCodigo, timerNombre;

function buscarCodigo() {
  clearTimeout(timerCodigo);
  timerCodigo = setTimeout(() => {
    const code = document.getElementById('inputCodigo').value.trim();
    if (!code) return;
    BASE = document.getElementById('baseUrl').value;
    fetch(BASE + 'ventas/buscarC?code=' + encodeURIComponent(code))
      .then(r => r.text())
      .then(html => {
        document.getElementById('tbCatalogo').innerHTML = html;
        actualizarFooter();
      });
  }, 200);
}

function buscarNombre() {
  clearTimeout(timerNombre);
  timerNombre = setTimeout(() => {
    const name = document.getElementById('inputNombre').value.trim();
    if (name.length < 1) return;
    BASE = document.getElementById('baseUrl').value;
    fetch(BASE + 'ventas/buscarN?name=' + encodeURIComponent(name))
      .then(r => r.text())
      .then(html => {
        document.getElementById('tbCatalogo').innerHTML = html;
        actualizarFooter();
      });
  }, 250);
}

function actualizarFooter() {
  const rows = document.querySelectorAll('#tbCatalogo tr:not(.empty)');
  const count = rows.length;
  const footer = document.getElementById('catalogFooter');
  if (count === 0) footer.textContent = 'Ningún resultado';
  else if (count === 1) footer.textContent = '1 producto encontrado';
  else footer.textContent = count + ' productos encontrados';
}

/* ─── CARRITO ─── */
function agregarAlCarrito(stock, id, nombre, precio) {
  const qtyInput = document.getElementById('qty_' + id);
  
  // Si estamos en móvil, la columna está oculta, así que usamos el prompt
  if (window.innerWidth <= 768) {
    customPrompt('Cantidad', '¿Cuántas unidades de ' + nombre + ' desea añadir?', 1, function(val) {
      const cant = parseInt(val) || 1;
      procesarAgregar(cant, stock, id, nombre, precio);
    });
  } else {
    // Si la columna está visible (escritorio), usamos el valor del input directamente
    const cant = parseInt(qtyInput.value) || 1;
    procesarAgregar(cant, stock, id, nombre, precio);
  }
}

function procesarAgregar(cant, stock, id, nombre, precio) {
  if (cant <= 0) { showSnack('Ingrese una cantidad válida', 'error'); return; }
  if (cant > stock) { showSnack('La cantidad excede el stock disponible (' + stock + ')', 'error'); return; }

  // Verificar si ya existe en carrito
  const existe = carrito.find(item => item.id === id);
  if (existe) {
    existe.cantidad += cant;
    if (existe.cantidad > stock) {
      existe.cantidad = stock;
      showSnack('Cantidad ajustada al stock máximo', 'error');
    }
  } else {
    carrito.push({ id, nombre, cantidad: cant, precio });
  }

  renderCarrito();
  showSnack('✓ ' + nombre, 'success');
}

function eliminarDelCarrito(index) {
  const item = carrito[index];
  carrito.splice(index, 1);
  renderCarrito();
  showSnack('Eliminado: ' + item.nombre);
}

function renderCarrito() {
  const tbody = document.getElementById('tbCarrito');
  if (carrito.length === 0) {
    tbody.innerHTML = '<tr class="empty"><td colspan="5" style="padding:40px;color:#94a3b8;text-align:center">' +
      '<span class="material-symbols-outlined" style="font-size:36px;display:block;margin-bottom:8px">add_shopping_cart</span>' +
      'Agregue productos al carrito</td></tr>';
    document.getElementById('totalMonto').textContent = '0.00';
    document.getElementById('totalItems').textContent = '0 productos';
    return;
  }

  let html = '';
  let total = 0;
  carrito.forEach((item, i) => {
    const sub = (item.cantidad * item.precio).toFixed(2);
    total += parseFloat(sub);
    html += '<tr onclick="eliminarDelCarrito(' + i + ')" title="Clic para quitar">' +
      '<td style="text-align:center">' + (i + 1) + '</td>' +
      '<td style="text-align:left;padding-left:8px">' + item.nombre + '</td>' +
      '<td style="text-align:center">' + item.cantidad + '</td>' +
      '<td style="text-align:right;padding-right:8px">S/ ' + item.precio.toFixed(2) + '</td>' +
      '<td style="text-align:right;padding-right:8px;font-weight:600">S/ ' + sub + '</td></tr>';
  });

  tbody.innerHTML = html;
  document.getElementById('totalMonto').textContent = total.toFixed(2);
  document.getElementById('totalItems').textContent = carrito.length + (carrito.length === 1 ? ' producto' : ' productos');
}

/* ─── TIPO CLIENTE ─── */
function cambiarTipo() {
  const tipo = document.getElementById('tipoCliente').value;
  const inputDoc = document.getElementById('dniRuc');
  inputDoc.value = '';
  document.getElementById('nombreRazon').value = '';
  document.getElementById('lblDoc').textContent = tipo === '1' ? 'N° DNI:' : 'N° RUC:';
  document.getElementById('lblNombre').textContent = tipo === '1' ? 'Nombres:' : 'Razón Social:';
  inputDoc.maxLength = tipo === '1' ? 8 : 11;
}

/* ─── PROCESAR VENTA ─── */
function procesarVenta(formato) {
  if (ventaEnProceso) return;
  if (carrito.length === 0) { showSnack('Agregue productos al carrito', 'error'); return; }

  const dniRuc = document.getElementById('dniRuc').value.trim();
  const nombreRazon = document.getElementById('nombreRazon').value.trim();
  const tipoDoc = document.getElementById('tipoCliente').value;

  // Validaciones estrictas de DNI/RUC si se ha ingresado algo o es boleta/factura
  if (dniRuc.length > 0 || formato > 0) {
    if (!dniRuc || !nombreRazon) {
      const docName = formato > 0 ? (formato === 3 ? 'BOLETA' : 'FACTURA') : 'la venta';
      showSnack('Ingrese DNI/RUC y el Nombre para emitir ' + docName, 'error');
      return;
    }
    if (tipoDoc === '1' && dniRuc.length !== 8) {
      showSnack('El DNI debe tener exactamente 8 números', 'error');
      return;
    }
    if (tipoDoc === '2' && dniRuc.length !== 11) {
      showSnack('El RUC debe tener exactamente 11 números', 'error');
      return;
    }
    
    // Reglas de negocio (SUNAT)
    if (formato === 1 && tipoDoc !== '2') { // Factura (1) exige RUC (2)
      showSnack('Para emitir una FACTURA debe seleccionar RUC', 'error');
      return;
    }
    if (formato === 3 && tipoDoc !== '1') { // Boleta (3) típicamente exige DNI (1)
      showSnack('Para emitir una BOLETA debe seleccionar DNI', 'error');
      return;
    }
  }

  ventaEnProceso = true;
  document.querySelectorAll('.cart-actions button').forEach(b => b.disabled = true);

  // Construir array de venta: id*cantidad*precio*id*cantidad*precio...
  const arrayVenta = carrito.map(item => item.id + '*' + item.cantidad + '*' + item.precio).join('*');

  const data = new FormData();
  data.append('formato', formato);
  data.append('dni_ruc', dniRuc);
  data.append('nombre_razon', nombreRazon);
  data.append('atiende', document.getElementById('atiende').value);
  data.append('lot', carrito.length);
  data.append('array', arrayVenta);

  fetch(BASE + 'ventas/vender', { method: 'POST', body: data })
    .then(r => r.json())
    .then(d => {
      if (d.error === 0) {
        showSnack('✓ Venta realizada', 'success');
        // Abrir ticket
        const W = window.open(d.url, 'TK', 'width=400,height=620,resizable=no,toolbar=no');
        if (W) {
          W.onload = function() { W.print(); };
        } else {
          showSnack('Venta registrada. Permita ventanas emergentes para imprimir.', 'success', 5000);
        }
        // Limpiar carrito
        carrito = [];
        renderCarrito();
        document.getElementById('dniRuc').value = '';
        document.getElementById('nombreRazon').value = '';
      } else {
        showSnack(d.msg, 'error', 4000);
      }
      ventaEnProceso = false;
      document.querySelectorAll('.cart-actions button').forEach(b => b.disabled = false);
    })
    .catch(err => {
      showSnack('Error de conexión', 'error');
      ventaEnProceso = false;
      document.querySelectorAll('.cart-actions button').forEach(b => b.disabled = false);
    });
}
