<?php echo $this->extend ('plantilla/layout-simple')?>
<?php echo $this->section('contenido');?>
<style>
.columna{
    grid-template-columns:
        0.1fr     /* # */
        0.4fr
        1fr
        0.4fr
        0.1fr;
}
.fila{
    grid-template-columns:
        0.1fr     /* # */
        0.4fr
        1fr
        0.4fr
        0.1fr;
}
.tabla-cuerpo .fila div:nth-child(3){
    padding: 5px 0 5px 5px;
    text-align: left;
}
.tabla-totales{
    grid-template-columns:
        0.1fr     /* # */
        1.8fr
        0.1fr;
}
</style>
<div class="container full" id="container">
  <div class="top-controls">
    <div class="titulo"><?php echo $titulo;?></div>
    <button class="plus" onclick="abrirCrear()">+</button>

    <select id="filtro">
        <option value="0">Reciente</option>
        <option value="1">A-Z</option>
    </select>

    <div class="search-box">
      <span class="material-symbols-outlined">search</span>
      <input id="buscar" type="text" placeholder="Buscar...">
    </div>
  </div>

  <div class="layout">
    <div class="tabla-cuerpo" id="tabla"></div>

    <div class="tabla-totales">
      <div id="totalRegistros">0</div>
      <div id="cantiRegistros">Registros en total</div>
      <div></div>
    </div>

    <div class="tabla-footer">
      Sistema de Gestión Comercial © Ing. Harold Coila
    </div>
  </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", function(){

    const filtro = document.getElementById("filtro");
    const buscar = document.getElementById("buscar");
    const tabla  = document.getElementById("tabla");
    const totalRegistros = document.getElementById("totalRegistros");
    const cantiRegistros = document.getElementById("cantiRegistros");

    let timeout = null;

    function cargarDatos(){
        const f = filtro.value;
        const b = buscar.value;

        fetch(`<?= base_url('personal/listar') ?>?filtro=${f}&buscar=${b}`)
        .then(res => res.json())
        .then(data => {

            totalRegistros.textContent = data.length;
            switch(data.length){
                case 0: cantiRegistros.textContent='Ningún registro';break;
                case 1: cantiRegistros.textContent='Registro en total';break;
                default: cantiRegistros.textContent='Registros en total';break;
            }

            let html = `
            <div class="columna">
                <div>#</div>
                <div>Documento</div>
                <div>Nombres</div>
                <div>Celular</div>
                <div></div>
            </div>
            `;

            if(data.length === 0){
                html += `<div class="row"></div>`;
                tabla.innerHTML = html;
                return;
            }

            let c = 1;

            data.forEach(dato => {
                html += `
                <div class="row">
                    <div class="swipe">
                        <div class="edit" data-json='${JSON.stringify(dato)}'>
                            <span class="material-symbols-outlined">edit_square</span>
                        </div>
                        <div class="del" data-id="${dato.id}" data-func="eliminarPersonal">
                            <span class="material-symbols-outlined">disabled_by_default</span>
                        </div>
                    </div>

                    <div class="fila">
                        <div>${c++}</div>
                        <div>${dato.dni_rut}</div>
                        <div>${dato.nombres}</div>
                        <div>${dato.celular}</div>
                        <div class="actions">⋮</div>
                    </div>
                </div>
                `;
            });

            tabla.innerHTML = html;
        });
    }

    // EVENTOS FILTRO
    filtro.addEventListener("change", cargarDatos);

    buscar.addEventListener("keyup", function(){
        clearTimeout(timeout);
        timeout = setTimeout(cargarDatos, 300);
    });

    cargarDatos(); // primera carga

});
</script>

<script>

/* =========================
   ESTADO GLOBAL
========================= */

let modo = 'crear';
let idActual = null;

/* =========================
   WIZARD (IGUAL QUE CONTRATO)
========================= */

let wzSteps = [
{
    id: "personal",
    render: (data = {}) => `
    <h1>${modo === 'crear' ? 'Nuevo personal' : 'Editar personal'}</h1>

    <div class="selectdiv field">
        <em>Tipo de documento</em>
        <select id="tipodocu">
            <option value="1">DNI</option>
            <option value="2" selected>RUT</option>
        </select>
    </div>

    <div class="field">
        <em>Número de documento</em>
        <div class="doc-flex">
            <input class="selx" type="text" id="dni_rut" name="dni_rut" value="" oninput="dni_rut(event)">
            <button type="button" id="btnBuscarDoc" onclick="buscarDocumentoAPI()">🔍</button>
        </div>
    </div>

    <div class="field">
        <em>Nombre completo</em>
        <input type="text" id="nombres" name="nombres" placeholder="Nombres y apellidos">
    </div>

    <div class="field">
        <em>Celular</em>
        <input type="text" id="celular" name="celular" inputmode="numeric" pattern="[0-9]*" placeholder="999 999 999" maxlength="11" oninput="trionumero(event)" value="">
    </div>

    `,

    validate: () => {

        let dni_rut = document.getElementById('dni_rut').value.trim();
        let nombres = document.getElementById('nombres').value.trim();

        let faltantes = [];

        if(dni_rut === '') faltantes.push('numero de documento');
        if(nombres === '') faltantes.push('nombre completo');

        if(faltantes.length > 0){

            let mensaje = '';

            if(faltantes.length === 1){
                mensaje = `Falta ingresar: ${faltantes[0]}`;
            }else{
                mensaje = `Falta ingresar: ${faltantes.join(' y ')}`;
            }

            showFeedback('warning', mensaje);
            return false;
        }

        return true;
    }
}
];

</script>

<script>
/* =========================
   ABRIR WIZARD
========================= */


function abrirCrear(){
    modo = 'crear';
    idActual = null;

    abrirwz({
        steps: wzSteps,
        data: {},
        onFinish: guardarPersonal
    });
}

function abrirEditar(data){
  console.log("DATA EDIT:", data);

    modo = 'editar';
    idActual = data.id;

    abrirwz({
        steps: wzSteps,
        data: {
            dni_rut: data.dni_rut,
            nombres: data.nombres,
            celular: data.celular
        },
        onFinish: guardarPersonal
    });
}

/* =========================
   GUARDAR
========================= */

function guardarPersonal(data){

    let url = modo === 'crear'
        ? "<?= base_url('personal/create') ?>"
        : "<?= base_url('personal/update') ?>/" + idActual;

    console.log("DATA A ENVIAR:", data);
    
    fetch(url, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: "dni_rut="+encodeURIComponent(data.dni_rut)+
              "&nombres=" + encodeURIComponent(limpiarTexto(data.nombres))+
              "&celular="+encodeURIComponent(data.celular)
    })
    .then(res => res.json())
    .then(res => {

        let status = res[0];
        let msg = res[1];

        if(status === 0){
            showFeedback('success', msg);
            setTimeout(() => location.reload(), 800);
        }else{
            showFeedback('error', msg);
        }

    })
    .catch(() => {
        showFeedback('error','Error de conexión');
    });
}

/* =========================
   ELIMINAR
========================= */

function eliminarPersonal(id){
    if(!confirm("¿Eliminar registro?")) return;

    fetch("<?= base_url('personal/delete') ?>/" + id, {
        method: 'POST'
    })
    .then(res => res.json())
    .then(res => {

        let [status, msg] = res;

        if(status === 0){
            showFeedback('success', msg);
            setTimeout(() => location.reload(), 800);
        }else{
            showFeedback('error', msg);
        }

    })
    .catch(() => {
        showFeedback('error','Error de conexión');
    });
}

</script>

<script>
    async function buscarDocumentoAPI() {

    console.log("CLICK BUSCAR DNI");
    const tipodocuEl = document.getElementById('tipodocu');
    const dniEl = document.getElementById('dni_rut');
    const nombresEl = document.getElementById('nombres');

    if(!tipodocuEl || !dniEl || !nombresEl){
        console.error("Elementos no encontrados en el DOM");
        return;
    }

    const tipodocu = tipodocuEl.value;
    const numedocu = dniEl.value.trim();

    if(numedocu === ''){
        showFeedback('warning', 'Ingrese número de documento');
        return;
    }

    try {
        const res = await fetch("<?= base_url('api/buscar-documento') ?>", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `tipodocu=${tipodocu}&numedocu=${numedocu}`
        });

        const data = await res.json();

        console.log("RESPUESTA API:", data); // 👈 DEBUG CLAVE

        if(data.status === 0){
            nombresEl.value = data.data.nombres;
        } else {
            showFeedback('warning', data.msg);
        }

    } catch (error) {
        console.error(error);
        showFeedback('error', 'Error de conexión');
    }
}
</script>


<?php echo $this->endSection('contenido');?>