<?php echo $this->extend ('plantilla/layout-simple')?>
<?php echo $this->section('contenido');?>
<style>
.row.active .fila {
    transform: translateX(-80px); /* no se puede eliminar: es ForeingKey */
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

        fetch(`<?= base_url('fiesta/listar') ?>?filtro=${f}&buscar=${b}`)
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
                <div><?= $titulo ?></div>
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
                    </div>

                    <div class="fila">
                        <div>${c++}</div>
                        <div>${dato.evento}</div>
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
    id: "fiesta",
    render: (data = {}) => `
    <h1>${modo === 'crear' ? 'Nueva fiesta' : 'Editar fiesta'}</h1>

    <div class="field">
        <em>Fiesta</em>
        <input type="text" 
            id="evento"
            name="evento"
            value="${data?.evento ?? ''}">
    </div>

    `,

    validate: () => {

        let evento = document.getElementById('evento').value.trim();

        if(evento === ''){
            showFeedback('warning','Falta ingresar la fiesta');
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
        onFinish: guardarFiesta
    });
}

function abrirEditar(data){
  console.log("DATA EDIT:", data);

    modo = 'editar';
    idActual = data.id;

    abrirwz({
        steps: wzSteps,
        data: {
            evento: data.evento
        },
        onFinish: guardarFiesta
    });
}

/* =========================
   GUARDAR
========================= */

function guardarFiesta(data){

    let url = modo === 'crear'
        ? "<?= base_url('fiesta/create') ?>"
        : "<?= base_url('fiesta/update') ?>/" + idActual;

    console.log("DATA A ENVIAR:", data);
    
    fetch(url, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: "evento=" + encodeURIComponent(limpiarTexto(data.evento))
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

function eliminarFiesta(id){

    if(!confirm("¿Eliminar registro?")) return;

    fetch("<?= base_url('fiesta/delete') ?>/" + id, {
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


<?php echo $this->endSection('contenido');?>