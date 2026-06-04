/* ─── Usuarios: CRUD ─── */
let BASE = '';
let todosUsuarios = [];

document.addEventListener('DOMContentLoaded', cargarUsuarios);

function cargarUsuarios() {
  BASE = document.getElementById('baseUrl').value;
  fetch(BASE + 'usuarios/listar')
    .then(r => r.json())
    .then(data => { todosUsuarios = data; renderUsuarios(data); });
}

function renderUsuarios(lista) {
  const tb = document.getElementById('tbUsuarios');
  if (lista.length === 0) {
    tb.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:30px;color:#94a3b8">Sin usuarios</td></tr>';
    return;
  }
  let html = '';
  lista.forEach((u, i) => {
    html += '<tr>' +
      '<td class="text-center hide-mobile">' + (i+1) + '</td>' +
      '<td class="text-center hide-mobile">' + u.dni_ruc + '</td>' +
      '<td style="text-align:left;padding-left:8px">' + 
        '<span class="text-truncate" title="' + u.nombre_razon + '">' + u.nombre_razon + '</span>' +
      '</td>' +
      '<td style="text-align:left;padding-left:8px" class="hide-mobile">' + u.apellidos + '</td>' +
      '<td class="text-center hide-mobile">••••</td>' +
      '<td class="text-center"><span class="badge ' + (u.master == 2 ? 'badge-info' : 'badge-success') + '">' +
        (u.master == 2 ? 'Admin' : 'Vendedor') + '</span></td>' +
      '<td class="text-center">' +
        '<div style="display:flex; gap:4px; justify-content:center">' +
          '<button class="btn btn-sm btn-primary btn-action-mobile" onclick="modalEditarUsuario(' + u.persona_id + ')" title="Editar">' +
            '<span class="material-symbols-outlined">edit</span><span class="btn-text-mobile"> Editar</span>' +
          '</button>' +
          '<button class="btn btn-sm btn-danger btn-action-mobile" onclick="eliminarUsuario(' + u.persona_id + ',\'' + u.nombre_razon.replace(/'/g,"\\'") + '\')" title="Eliminar">' +
            '<span class="material-symbols-outlined">delete</span><span class="btn-text-mobile"> Eliminar</span>' +
          '</button>' +
          '<button class="btn-expand" onclick="toggleRow(' + u.persona_id + ')"><span class="material-symbols-outlined">expand_more</span></button>' +
        '</div>' +
      '</td></tr>';
      
    // Fila expandible
    html += '<tr class="row-details" id="row-' + u.persona_id + '">' +
      '<td colspan="7">' +
        '<div class="row-content" id="details-' + u.persona_id + '">' +
          '<div style="padding: 12px 16px; font-size:12px;">' +
            '<div><strong>Documento:</strong> ' + u.dni_ruc + '</div>' +
            '<div style="margin-top:4px"><strong>Apellidos:</strong> ' + (u.apellidos || '-') + '</div>' +
            '<div style="margin-top:4px"><strong>Contraseña:</strong> ••••</div>' +
            '<div style="margin-top:12px; display:flex; gap:8px;">' +
              '<button class="btn btn-sm btn-primary" onclick="modalEditarUsuario(' + u.persona_id + ')"><span class="material-symbols-outlined" style="font-size:16px">edit</span> Editar</button>' +
              '<button class="btn btn-sm btn-danger" onclick="eliminarUsuario(' + u.persona_id + ',\'' + u.nombre_razon.replace(/'/g,"\\'") + '\')"><span class="material-symbols-outlined" style="font-size:16px">delete</span> Eliminar</button>' +
            '</div>' +
          '</div>' +
        '</div>' +
      '</td></tr>';
  });
  tb.innerHTML = html;
}

function modalNuevoUsuario() {
  document.getElementById('modalTituloU').textContent = 'Agregar Usuario';
  document.getElementById('userId').value = '';
  ['userDni','userNombre','userApellidos','userPass'].forEach(id => document.getElementById(id).value = '');
  document.getElementById('userMaster').value = '1';
  openModal('modalUsuario');
}

function modalEditarUsuario(id) {
  const u = todosUsuarios.find(x => x.persona_id == id);
  if (!u) return;
  document.getElementById('modalTituloU').textContent = 'Editar Usuario';
  document.getElementById('userId').value = u.persona_id;
  document.getElementById('userDni').value = u.dni_ruc;
  document.getElementById('userNombre').value = u.nombre_razon;
  document.getElementById('userApellidos').value = u.apellidos;
  document.getElementById('userPass').value = u.pass;
  document.getElementById('userMaster').value = u.master;
  openModal('modalUsuario');
}

function guardarUsuario() {
  const id = document.getElementById('userId').value;
  const url = id ? BASE + 'usuarios/editar/' + id : BASE + 'usuarios/crear';
  const data = new FormData();
  data.append('dni_ruc', document.getElementById('userDni').value);
  data.append('nombre_razon', document.getElementById('userNombre').value);
  data.append('apellidos', document.getElementById('userApellidos').value);
  data.append('pass', document.getElementById('userPass').value);
  data.append('master', document.getElementById('userMaster').value);

  fetch(url, { method: 'POST', body: data })
    .then(r => r.json())
    .then(d => {
      showSnack(d.msg, d.error === 0 ? 'success' : 'error');
      if (d.error === 0) { closeModal('modalUsuario'); cargarUsuarios(); }
    })
    .catch(() => showSnack('Error de conexión', 'error'));
}

function eliminarUsuario(id, nombre) {
  customConfirm('Eliminar Usuario', '¿Estás seguro de eliminar al usuario "' + nombre + '"?', 'Eliminar', 'btn-danger', function() {
    fetch(BASE + 'usuarios/eliminar/' + id, { method: 'POST', body: new FormData() })
      .then(r => r.json())
      .then(d => {
        showSnack(d.msg, d.error === 0 ? 'success' : 'error');
        if (d.error === 0) cargarUsuarios();
      });
  });
}
