/* ─── Productos: CRUD ─── */
let BASE = '';
let EXP = false;
let todosProductos = [];

document.addEventListener('DOMContentLoaded', cargarProductos);

function cargarProductos() {
  BASE = document.getElementById('baseUrl').value;
  EXP = document.getElementById('tieneExp') ? document.getElementById('tieneExp').value === '1' : false;
  fetch(BASE + 'productos/listar')
    .then(r => r.json())
    .then(data => { todosProductos = data; renderTabla(data); });
}

function renderTabla(lista) {
  const tb = document.getElementById('tbProductos');
  if (lista.length === 0) {
    tb.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:30px;color:#94a3b8">Sin productos</td></tr>';
    return;
  }
  let html = '';
  lista.forEach((p, i) => {
    // Fila principal
    html += '<tr>' +
      '<td class="text-center hide-mobile">' + (i+1) + '</td>' +
      '<td class="hide-mobile">' + (p.codigo || '') + '</td>' +
      '<td style="text-align:left;padding-left:8px">' + 
        '<span class="text-truncate" title="' + p.detalle + '">' + p.detalle + '</span>' +
      '</td>' +
      '<td class="text-center hide-mobile">S/ ' + Number(p.pcompra).toFixed(2) + '</td>' +
      '<td class="text-center" style="font-weight:600;color:var(--success)">S/ ' + Number(p.pventa).toFixed(2) + '</td>' +
      '<td class="text-center hide-mobile">' + (p.ppromo ? 'S/ '+Number(p.ppromo).toFixed(2) : '-') + '</td>' +
      '<td class="text-center">' + p.stock + '</td>' +
      (EXP ? '<td class="text-center hide-mobile">' + (p.expmes || '-') + '/' + (p.expanio || '-') + '</td>' : '') +
      '<td class="text-center">' +
        '<div style="display:flex; gap:4px; justify-content:center">' +
          '<button class="btn btn-sm btn-primary btn-action-mobile" onclick="modalEditar(' + p.id + ')" title="Editar">' +
            '<span class="material-symbols-outlined">edit</span><span class="btn-text-mobile"> Editar</span>' +
          '</button>' +
          '<button class="btn btn-sm btn-danger btn-action-mobile" onclick="eliminarProducto(' + p.id + ',\'' + p.detalle.replace(/'/g,"\\'") + '\')" title="Eliminar">' +
            '<span class="material-symbols-outlined">delete</span><span class="btn-text-mobile"> Eliminar</span>' +
          '</button>' +
          '<button class="btn-expand" onclick="toggleRow(' + p.id + ')"><span class="material-symbols-outlined">expand_more</span></button>' +
        '</div>' +
      '</td></tr>';
      
    // Fila expandible (solo visible en móviles cuando se abre)
    html += '<tr class="row-details" id="row-' + p.id + '">' +
      '<td colspan="10">' +
        '<div class="row-content" id="details-' + p.id + '">' +
          '<div style="padding: 12px 16px; font-size:12px;">' +
            '<div><strong>Código:</strong> ' + (p.codigo || '-') + '</div>' +
            '<div style="margin-top:4px"><strong>P. Compra:</strong> S/ ' + Number(p.pcompra).toFixed(2) + 
            ' &nbsp;|&nbsp; <strong>P. Promo:</strong> ' + (p.ppromo ? 'S/ '+Number(p.ppromo).toFixed(2) : '-') + '</div>' +
            (EXP ? '<div style="margin-top:4px"><strong>Vence:</strong> ' + (p.expmes || '-') + '/' + (p.expanio || '-') + '</div>' : '') +
            '<div style="margin-top:12px; display:flex; gap:8px;">' +
              '<button class="btn btn-sm btn-primary" onclick="modalEditar(' + p.id + ')"><span class="material-symbols-outlined" style="font-size:16px">edit</span> Editar</button>' +
              '<button class="btn btn-sm btn-danger" onclick="eliminarProducto(' + p.id + ',\'' + p.detalle.replace(/'/g,"\\'") + '\')"><span class="material-symbols-outlined" style="font-size:16px">delete</span> Eliminar</button>' +
            '</div>' +
          '</div>' +
        '</div>' +
      '</td></tr>';
  });
  tb.innerHTML = html;
}

function filtrarProductos() {
  const q = document.getElementById('buscarProducto').value.toLowerCase();
  const filtrados = todosProductos.filter(p =>
    p.detalle.toLowerCase().includes(q) || (p.codigo && p.codigo.includes(q))
  );
  renderTabla(filtrados);
}

/* ─── Modal ─── */
function modalNuevo() {
  document.getElementById('modalTitulo').textContent = 'Agregar Producto';
  document.getElementById('prodId').value = '';
  ['prodCodigo','prodDetalle','prodPcompra','prodPventa','prodPpromo','prodStock','prodContiene','prodExpmes','prodExpanio']
    .forEach(id => document.getElementById(id).value = '');
  openModal('modalProducto');
}

function modalEditar(id) {
  const p = todosProductos.find(x => x.id == id);
  if (!p) return;
  document.getElementById('modalTitulo').textContent = 'Editar Producto';
  document.getElementById('prodId').value = p.id;
  document.getElementById('prodCodigo').value = p.codigo || '';
  document.getElementById('prodDetalle').value = p.detalle;
  document.getElementById('prodPcompra').value = p.pcompra;
  document.getElementById('prodPventa').value = p.pventa;
  document.getElementById('prodPpromo').value = p.ppromo || '';
  document.getElementById('prodStock').value = p.stock;
  document.getElementById('prodContiene').value = p.contiene || '';
  // Mes como select: normalizar a 2 dígitos
  const mes = p.expmes ? String(p.expmes).padStart(2, '0') : '';
  document.getElementById('prodExpmes').value = mes;
  document.getElementById('prodExpanio').value = p.expanio || '';
  openModal('modalProducto');
}

function guardarProducto() {
  const id = document.getElementById('prodId').value;
  const url = id ? BASE + 'productos/editar/' + id : BASE + 'productos/crear';
  const data = new FormData();
  data.append('codigo', document.getElementById('prodCodigo').value);
  data.append('detalle', document.getElementById('prodDetalle').value);
  data.append('pcompra', document.getElementById('prodPcompra').value);
  data.append('pventa', document.getElementById('prodPventa').value);
  data.append('ppromo', document.getElementById('prodPpromo').value);
  data.append('stock', document.getElementById('prodStock').value);
  data.append('contiene', document.getElementById('prodContiene').value);
  data.append('expmes', document.getElementById('prodExpmes').value);
  data.append('expanio', document.getElementById('prodExpanio').value);

  fetch(url, { method: 'POST', body: data })
    .then(r => r.json())
    .then(d => {
      if (d.error === 0) {
        showSnack(d.msg, 'success');
        closeModal('modalProducto');
        cargarProductos();
      } else {
        showSnack(d.msg, 'error');
      }
    })
    .catch(() => showSnack('Error de conexión', 'error'));
}

function eliminarProducto(id, nombre) {
  customConfirm('Eliminar Producto', '¿Estás seguro de eliminar el producto "' + nombre + '"?', 'Eliminar', 'btn-danger', function() {
    const data = new FormData();
    fetch(BASE + 'productos/eliminar/' + id, { method: 'POST', body: data })
      .then(r => r.json())
      .then(d => {
        showSnack(d.msg, d.error === 0 ? 'success' : 'error');
        if (d.error === 0) cargarProductos();
      });
  });
}

function imprimirReporteProductos() {
  if (todosProductos.length === 0) {
    showSnack('No hay productos para imprimir', 'error');
    return;
  }
  
  let html = '<html><head><title>Reporte de Productos</title>';
  html += '<style>';
  html += 'body { font-family: Arial, sans-serif; padding: 20px; color: #333; }';
  html += 'h2 { text-align: center; margin-bottom: 20px; }';
  html += 'table { width: 100%; border-collapse: collapse; font-size: 12px; }';
  html += 'th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }';
  html += 'th { background-color: #f4f4f4; }';
  html += '.text-right { text-align: right; }';
  html += '.text-center { text-align: center; }';
  html += '@media print { button { display: none; } }';
  html += '</style></head><body>';
  
  html += '<h2>Reporte de Inventario de Productos</h2>';
  html += '<div style="text-align: right; margin-bottom: 10px;">Fecha: ' + new Date().toLocaleDateString() + '</div>';
  
  html += '<table><thead><tr>';
  html += '<th>#</th><th>Código</th><th>Descripción</th><th>P. Compra</th><th>P. Venta</th><th>Stock</th>';
  const EXP = document.getElementById('tieneExp').value === '1';
  if (EXP) html += '<th>Vencimiento</th>';
  html += '</tr></thead><tbody>';
  
  todosProductos.forEach((p, i) => {
    html += '<tr>';
    html += '<td class="text-center">' + (i + 1) + '</td>';
    html += '<td>' + (p.codigo || '-') + '</td>';
    html += '<td>' + p.detalle + '</td>';
    html += '<td class="text-right">S/ ' + Number(p.pcompra).toFixed(2) + '</td>';
    html += '<td class="text-right">S/ ' + Number(p.pventa).toFixed(2) + '</td>';
    html += '<td class="text-center">' + p.stock + '</td>';
    if (EXP) html += '<td class="text-center">' + (p.expmes ? p.expmes + '/' + p.expanio : '-') + '</td>';
    html += '</tr>';
  });
  
  html += '</tbody></table>';
  html += '<div style="margin-top: 20px; text-align: center;">';
  html += '<button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">Imprimir / Guardar PDF</button>';
  html += '</div>';
  html += '</body></html>';
  
  const win = window.open('', '_blank');
  win.document.write(html);
  win.document.close();
  
  // Auto-trigger print after short delay
  setTimeout(() => {
    win.print();
  }, 500);
}

function descargarExcelProductos() {
  if (todosProductos.length === 0) {
    showSnack('No hay productos para descargar', 'error');
    return;
  }
  
  const EXP = document.getElementById('tieneExp').value === '1';
  let csv = 'Codigo,Descripcion,P.Compra,P.Venta,Stock' + (EXP ? ',Vencimiento' : '') + '\n';
  
  todosProductos.forEach(p => {
    const cod = p.codigo || '';
    const det = '"' + p.detalle.replace(/"/g, '""') + '"'; // Escapar comillas dobles
    const venc = EXP ? (p.expmes ? `${p.expmes}/${p.expanio}` : '') : '';
    
    csv += `${cod},${det},${p.pcompra},${p.pventa},${p.stock}`;
    if (EXP) csv += `,${venc}`;
    csv += '\n';
  });
  
  // Agregar BOM para que Excel lea UTF-8 correctamente
  const blob = new Blob(["\ufeff" + csv], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = 'Inventario_Productos_' + new Date().toISOString().split('T')[0] + '.csv';
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
}
