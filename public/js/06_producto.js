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
      '<td class="text-center hide-1200" style="color:var(--gray-400);font-size:12px">' + (i+1) + '</td>' +
      '<td class="hide-1100" style="font-size:12px;color:var(--gray-600)">' + (p.codigo || '') + '</td>' +
      '<td style="text-align:left;padding-left:8px">' + 
        '<span class="text-truncate" title="' + p.detalle + '">' + p.detalle + '</span>' +
      '</td>' +
      '<td class="text-center hide-980">S/ ' + Number(p.pcompra).toFixed(2) + '</td>' +
      '<td class="text-center" style="font-weight:600;color:var(--success)">S/ ' + Number(p.pventa).toFixed(2) + '</td>' +
      '<td class="text-center hide-860">' + (p.ppromo ? 'S/ '+Number(p.ppromo).toFixed(2) : '-') + '</td>' +
      '<td class="text-center">' + p.stock + '</td>' +
      (EXP ? '<td class="text-center hide-760">' + (p.expmes || '-') + '/' + (p.expanio || '-') + '</td>' : '') +
      '<td>' +
        '<div class="row-actions">' +
          '<button class="btn-icon btn-icon-edit" onclick="modalEditar(' + p.id + ')" title="Editar">' +
            '<span class="material-symbols-outlined">edit</span>' +
          '</button>' +
          '<button class="btn-icon btn-icon-delete" onclick="eliminarProducto(' + p.id + ',\'' + p.detalle.replace(/'/g,"\\'") + '\')" title="Eliminar">' +
            '<span class="material-symbols-outlined">delete</span>' +
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
            '<div style="margin-top:4px"><strong>P. Compra:</strong> S/ ' + Number(p.pcompra).toFixed(2) + '</div>' +
            '<div style="margin-top:4px"><strong>P. Promo:</strong> ' + (p.ppromo ? 'S/ '+Number(p.ppromo).toFixed(2) : '-') + '</div>' +
            (EXP ? '<div style="margin-top:4px"><strong>Vence:</strong> ' + (p.expmes || '-') + '/' + (p.expanio || '-') + '</div>' : '') +
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
  html += '<th>#</th><th>Código</th><th>Descripción</th><th class="text-right">P. Compra (S/)</th><th class="text-right">P. Venta (S/)</th><th class="text-center">Stock</th>';
  const EXP = document.getElementById('tieneExp').value === '1';
  if (EXP) html += '<th>Vencimiento</th>';
  html += '</tr></thead><tbody>';
  
  todosProductos.forEach((p, i) => {
    html += '<tr>';
    html += '<td class="text-center">' + (i + 1) + '</td>';
    html += '<td>' + (p.codigo || '-') + '</td>';
    html += '<td>' + p.detalle + '</td>';
    html += '<td class="text-right">' + Number(p.pcompra).toFixed(2) + '</td>';
    html += '<td class="text-right">' + Number(p.pventa).toFixed(2) + '</td>';
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

async function descargarExcelProductos() {
  if (todosProductos.length === 0) {
    showSnack('No hay productos para descargar', 'error');
    return;
  }
  
  const EXP = document.getElementById('tieneExp').value === '1';
  
  // 1. Crear el libro y la hoja
  const workbook = new ExcelJS.Workbook();
  const worksheet = workbook.addWorksheet('Inventario');
  
  // 2. Título principal
  const titleRow = worksheet.addRow(["Reporte de Inventario de Productos"]);
  titleRow.getCell(1).font = { name: 'Arial', size: 16, bold: true, color: { argb: '0E7490' } };
  
  const dateRow = worksheet.addRow(["Generado el: " + new Date().toLocaleString()]);
  dateRow.getCell(1).font = { name: 'Arial', size: 10, italic: true, color: { argb: '64748B' } };
  
  worksheet.addRow([]); // Fila vacía
  
  // 3. Configurar columnas y encabezados
  const columns = [
    { header: '#', key: 'idx', width: 6 },
    { header: 'Código', key: 'codigo', width: 18 },
    { header: 'Descripción', key: 'detalle', width: 38 },
    { header: 'P. Compra', key: 'pcompra', width: 12 },
    { header: 'P. Venta', key: 'pventa', width: 12 },
    { header: 'P. Promoción', key: 'ppromo', width: 12 },
    { header: 'Stock', key: 'stock', width: 10 }
  ];
  if (EXP) {
    columns.push({ header: 'Vencimiento', key: 'vencimiento', width: 14 });
  }
  
  // Agregar fila de cabecera
  const headerRowData = columns.map(col => col.header);
  const headerRow = worksheet.addRow(headerRowData);
  headerRow.height = 26;
  
  // Estilo de la cabecera (Azul/Cian corporativo, texto blanco negrita)
  headerRow.eachCell((cell) => {
    cell.fill = {
      type: 'pattern',
      pattern: 'solid',
      fgColor: { argb: '0E7490' }
    };
    cell.font = {
      name: 'Arial',
      size: 11,
      bold: true,
      color: { argb: 'FFFFFF' }
    };
    cell.alignment = { vertical: 'middle', horizontal: 'left' };
    cell.border = {
      top: { style: 'thin', color: { argb: '0891B2' } },
      bottom: { style: 'thin', color: { argb: '0891B2' } },
      left: { style: 'thin', color: { argb: '0891B2' } },
      right: { style: 'thin', color: { argb: '0891B2' } }
    };
  });
  
  // 4. Agregar datos de productos
  todosProductos.forEach((p, i) => {
    const rowData = [
      i + 1,
      p.codigo ? String(p.codigo) : '-', // Mantener como string para preservar ceros
      p.detalle,
      p.pcompra ? Number(p.pcompra) : 0,
      p.pventa ? Number(p.pventa) : 0,
      p.ppromo ? Number(p.ppromo) : '-',
      Number(p.stock)
    ];
    if (EXP) rowData.push(p.expmes ? `${p.expmes}/${p.expanio}` : '-');
    
    const row = worksheet.addRow(rowData);
    row.height = 20;
    
    // Zebra striping alternado
    const isEven = i % 2 === 1;
    const bgColor = isEven ? 'F8FAFC' : 'FFFFFF';
    
    row.eachCell((cell, colNumber) => {
      cell.fill = {
        type: 'pattern',
        pattern: 'solid',
        fgColor: { argb: bgColor }
      };
      cell.font = { name: 'Arial', size: 10 };
      cell.border = {
        top: { style: 'thin', color: { argb: 'E2E8F0' } },
        bottom: { style: 'thin', color: { argb: 'E2E8F0' } },
        left: { style: 'thin', color: { argb: 'E2E8F0' } },
        right: { style: 'thin', color: { argb: 'E2E8F0' } }
      };
      
      // Alineaciones y formatos numéricos personalizados
      if (colNumber === 1 || colNumber === 2 || colNumber === 7 || (EXP && colNumber === 8)) {
        cell.alignment = { horizontal: 'center', vertical: 'middle' };
        if (colNumber === 2) cell.numFmt = '@'; // Forzar formato texto para códigos
      } else if (colNumber === 4 || colNumber === 5 || colNumber === 6) {
        cell.alignment = { horizontal: 'right', vertical: 'middle' };
        if (typeof cell.value === 'number') {
          cell.numFmt = '"S/" #,##0.00';
        }
      } else {
        cell.alignment = { horizontal: 'left', vertical: 'middle' };
      }
    });
  });
  
  // Establecer anchos de columna definidos
  columns.forEach((col, index) => {
    worksheet.getColumn(index + 1).width = col.width;
  });
  
  // 5. Escribir y descargar el archivo XLSX binario
  const buffer = await workbook.xlsx.writeBuffer();
  const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = 'Inventario_Productos_' + new Date().toISOString().split('T')[0] + '.xlsx';
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
}
