/* ─── Reportes ─── */
let BASE = '';
let ventaActualId = 0;
let ventaActualNumero = '';

function buscarReporte() {
  BASE = document.getElementById('baseUrl').value;
  const mes = document.getElementById('selMes').value;
  const anio = document.getElementById('inputAnio').value;
  if (!anio || anio.length < 4) { showSnack('Ingrese un año válido', 'error'); return; }

  const data = new FormData();
  data.append('mes', mes);
  data.append('anio', anio);

  fetch(BASE + 'reportes/buscar', { method: 'POST', body: data })
    .then(r => r.text())
    .then(text => {
      try {
        const d = JSON.parse(text);
        if (d.error) { showSnack(d.msg, 'error'); return; }
        renderReporte(d);
      } catch(e) {
        console.error("JSON Parse Error. Server returned:", text);
        console.error(e);
        showSnack('Error interno (revisa consola)', 'error');
      }
    })
    .catch((e) => {
      console.error("Fetch/Network Error:", e);
      showSnack('Error de conexión', 'error');
    });
}

function renderReporte(data) {
  const container = document.getElementById('resultados');
  const fechas = Object.keys(data.ventas);

  if (fechas.length === 0) {
    container.innerHTML = '<div class="card"><div class="card-body" style="text-align:center;padding:40px;color:#94a3b8">No hay ventas en este periodo</div></div>';
    return;
  }

  let totalMesVentas = 0;
  let totalMesCajas = 0;
  let totalRegistros = 0;
  let html = '<div class="card"><div class="card-header"><h2>Reporte de ventas — ' + data.titulo + '</h2></div>';
  html += '<div class="table-wrap"><table class="tabla"><thead><tr>' +
    '<th style="width:5%" class="hide-mobile">#</th><th style="width:12%">Fecha</th><th style="width:18%" class="hide-mobile">Comprobante</th>' +
    '<th style="width:13%">Total</th><th class="hide-mobile">Cajero</th><th style="width:10%; text-align:center">Detalle</th></tr></thead><tbody>';

  let num = 1;
  fechas.forEach(fecha => {
    const ventas = data.ventas[fecha];
    const resumen = data.resumenDias[fecha];
    totalMesVentas += Number(resumen.totalDia);
    totalMesCajas += Number(resumen.cajaInicial);
    totalRegistros += ventas.length;

    ventas.forEach(v => {
      const tipoLabel = v.formato == 3 ? 'Boleta' : (v.formato == 1 ? 'Factura' : 'Nota');
      const badgeClass = v.formato == 3 ? 'badge-success' : (v.formato == 1 ? 'badge-warning' : 'badge-info');
      html += '<tr>' +
        '<td class="text-center hide-mobile">' + (num++) + '</td>' +
        '<td class="text-center">' + v.fecha_fmt + '</td>' +
        '<td class="text-center hide-mobile"><span class="badge ' + badgeClass + '">' + tipoLabel + '</span> ' + v.numero_recibo + '</td>' +
        '<td class="text-right" style="padding-right:10px;font-weight:600">S/ ' + Number(v.total).toFixed(2) + '</td>' +
        '<td style="padding-left:8px" class="hide-mobile">' + (v.atiende || '-') + '</td>' +
        '<td class="text-center">' +
          '<button class="btn btn-sm btn-primary" onclick="verDetalle(' + v.id + ',\'' + v.numero_recibo + '\', \'' + (v.atiende || '-') + '\')" title="Ver detalle"><span class="material-symbols-outlined">visibility</span></button>' +
        '</td>' +
        '</tr>';
    });

    // Subtotal del día
    html += '<tr style="background:#f1f5f9">' +
      '<td colspan="6" style="padding:10px 14px;">' +
        '<div style="display:flex; flex-wrap:wrap; align-items:center; gap:6px 16px; line-height:1.7; font-size:12px;">' +
          '<span>📅 <strong>' + fecha + '</strong></span>' +
          '<span>Ventas: <strong style="color:#0e7490">S/ ' + Number(resumen.totalDia).toFixed(2) + '</strong></span>' +
          '<span>+ Caja inicial: <strong>S/ ' + Number(resumen.cajaInicial).toFixed(2) + '</strong></span>' +
          '<span>= Efectivo: <strong style="color:#059669; font-size:13px">S/ ' + Number(resumen.efectivo).toFixed(2) + '</strong></span>' +
          '<button class="btn btn-sm btn-warning" onclick="editarCajaInicial(\'' + fecha + '\',' + Number(resumen.cajaInicial) + ')" style="margin-left:auto">' +
            '<span class="material-symbols-outlined" style="font-size:14px;vertical-align:middle">edit</span> Editar Caja' +
          '</button>' +
        '</div>' +
      '</td></tr>';
  });

  const efectivoTotal = totalMesVentas + totalMesCajas;
  html += '</tbody></table></div></div>';
  
  // Footbar
  html += '<div class="footbar" style="justify-content: space-between; background: var(--gray-900); color: white; border-top: none;">' +
    '<div style="font-size: 13px; opacity: 0.8;">' +
      'Registros: <strong>' + totalRegistros + '</strong>' +
    '</div>' +
    '<div style="font-size: 13px; text-align: right;">' +
      '<span class="hide-mobile">Ingresos: <strong>S/ ' + totalMesVentas.toFixed(2) + '</strong> + ' +
      'Cajas: <strong>S/ ' + totalMesCajas.toFixed(2) + '</strong> = </span>' +
      '<strong style="font-size: 18px; color: var(--accent); margin-left: 8px;">Total: S/ ' + efectivoTotal.toFixed(2) + '</strong>' +
    '</div>' +
  '</div>';

  container.innerHTML = html;
}

function verDetalle(id, numero, cajero) {
  ventaActualId = id;
  ventaActualNumero = numero;
  document.getElementById('detalleTitulo').innerHTML = 'Detalle: ' + numero + ' <span style="font-size:13px;color:#64748b;font-weight:normal;margin-left:8px">Cajero: ' + (cajero || '-') + '</span>';

  fetch(BASE + 'ventas/detalle/' + id)
    .then(r => r.json())
    .then(data => {
      let html = '<thead><tr><th>#</th><th>Descripción</th><th>Cant.</th><th>Precio</th><th>Subtotal</th><th>Acciones</th></tr></thead><tbody>';
      let total = 0;
      data.lineas.forEach((l, i) => {
        const sub = (l.cantidad * l.precio).toFixed(2);
        total += parseFloat(sub);
        html += '<tr><td class="text-center">' + (i+1) + '</td>' +
          '<td style="text-align:left;padding-left:8px">' + l.detalle + '</td>' +
          '<td class="text-center">' + l.cantidad + '</td>' +
          '<td class="text-right" style="padding-right:8px">S/ ' + Number(l.precio).toFixed(2) + '</td>' +
          '<td class="text-right" style="padding-right:8px;font-weight:600">S/ ' + sub + '</td>' +
          '<td class="text-center"><button class="btn btn-sm btn-secondary" onclick="modalDevolver(' + ventaActualId + ',' + l.producto_id + ',\'' + l.detalle.replace(/'/g,"\\'") + '\',' + l.precio + ',' + l.cantidad + ')" title="Devolver unidades"><span class="material-symbols-outlined" style="font-size:16px">assignment_return</span></button></td></tr>';
      });
      html += '</tbody><tfoot><tr><td colspan="3"></td><td class="text-right" style="font-weight:700">TOTAL:</td>' +
        '<td class="text-right" style="font-weight:700;padding-right:8px">S/ ' + total.toFixed(2) + '</td><td></td></tr></tfoot>';
      document.getElementById('tablaDetalle').innerHTML = html;
      openModal('modalDetalle');
    });
}

function imprimirVenta() {
  window.open(BASE + 'ventas/ticket/' + ventaActualId, 'TK', 'width=400,height=620,resizable=no,toolbar=no');
}

function anularVenta() {
  customConfirm('Anular Venta', '¿Anular la venta ' + ventaActualNumero + '? Se restaurará el stock y se descontará de la caja.', 'Anular Venta', 'btn-danger', function() {
    fetch(BASE + 'ventas/anular/' + ventaActualId, { method: 'POST', body: new FormData() })
      .then(r => r.json())
      .then(d => {
        showSnack(d.msg, d.error === 0 ? 'success' : 'error');
        if (d.error === 0) { closeModal('modalDetalle'); buscarReporte(); }
      });
  });
}

function editarCajaInicial(fecha, actual) {
  customPrompt('Modificar Caja', 'Nueva caja inicial para ' + fecha + ':', actual, function(nuevo) {
    if (nuevo === null || nuevo === '' || isNaN(nuevo)) return;
  
    const data = new FormData();
    data.append('inicial', nuevo);
  
    fetch(BASE + 'reportes/caja/' + fecha, { method: 'POST', body: data })
      .then(r => r.json())
      .then(d => {
        showSnack(d.msg, d.error === 0 ? 'success' : 'error');
        if (d.error === 0) buscarReporte();
      });
  });
}

function modalDevolver(ventaId, prodId, detalle, precio, maxCant) {
  customPrompt('Devolver Producto', '¿Cuántas unidades de "' + detalle + '" devuelve el cliente? (Máximo: ' + maxCant + ')', '1', function(cant) {
    if (cant === null || cant === '') return;
    cant = parseInt(cant);
    if (isNaN(cant) || cant <= 0 || cant > maxCant) {
      showSnack('Cantidad inválida', 'error');
      return;
    }
    
    const monto = (cant * precio).toFixed(2);
    
    customConfirm('Confirmar Devolución', 'Se devolverá S/ ' + monto + ' de la caja de hoy y se restaurará el stock de "' + detalle + '".', 'Devolver', 'btn-warning', function() {
      const data = new FormData();
      data.append('venta_id', ventaId);
      data.append('producto_id', prodId);
      data.append('cantidad', cant);
      data.append('monto', monto);
      
      fetch(BASE + 'devoluciones/procesar', { method: 'POST', body: data })
        .then(r => r.json())
        .then(d => {
          showSnack(d.msg, d.error === 0 ? 'success' : 'error');
          if (d.error === 0) {
            closeModal('modalDetalle');
            buscarReporte();
          }
        });
    });
  });
}
