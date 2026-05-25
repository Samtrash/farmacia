<?php echo $this->extend ('plantilla/layout-reporte')?>
<?php echo $this->section('contenido');?>
<style>
  .swipe {
    height: 70px;
}
</style>
<div class="container full" id="container">
  <div class="top-controls">
    <div class="titulo"><?php echo $titulo;?></div>
    <button class="plus" onclick="abrirCrear()">+</button>

    <select id="filtro">
        <option value="0" selected>Próximos</option>
        <option value="1">2027</option>
        <option value="2">2026</option>
        <option value="3">2025</option>
        <option value="4">2024</option>
        <option value="5">Ver todo</option>
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
      <div>Total por cobrar<BR>en: S/. y $</div>
      <div id="sumasaldoSoles">S/. 0.00</div>
      <div id="sumasaldoDolares">$ 0.00</div>
      <div></div>
    </div>

    <div class="tabla-footer">
      Sistema de Gestión Comercial © Ing. Harold Coila
    </div>
  </div>
</div>


<script>

</script>

<script>
    let CACHE = [];
    let FIESTAS = [];
    let LUGARES = [];
    let timeout = null;

    function aplicarFiltros() {

        const texto = document.getElementById("buscar").value.toLowerCase();
        const filtro = document.getElementById("filtro").value;

        let data = CACHE;

        // 🔵 filtro por año / tipo
        if (filtro !== "5") {

            const hoy = new Date();

            data = data.filter(d => {

                const fecha = new Date(d.ini);
                const año = fecha.getFullYear();

                switch (filtro) {
                    case "0":
                        return fecha >= hoy;
                    case "1": return año == 2027;
                    case "2": return año == 2026;
                    case "3": return año == 2025;
                    case "4": return año == 2024;
                    default: return true;
                }
            });
        }

        // filtro por texto
        if (texto.trim() !== "") {
            data = data.filter(d =>
                (d.para ?? "").toLowerCase().includes(texto) ||
                (d.evento ?? "").toLowerCase().includes(texto) ||
                (d.lugar ?? "").toLowerCase().includes(texto)
            );
        }

        renderTabla(data);
    }

    //forzar primera carga proximos0
    document.addEventListener("DOMContentLoaded", function () {

        const filtro = document.getElementById("filtro");

        fetch("<?= base_url('contrato/listar') ?>")
            .then(res => res.json())
            .then(res => {

                CACHE = res.data ?? [];
                FIESTAS = res.fiestas ?? [];
                LUGARES = res.lugares ?? [];

                console.log("FIESTAS FRONT:", FIESTAS);
                console.log("LUGARES FRONT:", LUGARES);

                // 🔥 FORZAR valor por defecto
                filtro.value = "0";

                // 🔥 aplicar filtro inicial (proximos)
                aplicarFiltros();
            });

        document.getElementById("buscar").addEventListener("keyup", function () {
            clearTimeout(timeout);
            timeout = setTimeout(aplicarFiltros, 200);
        });

        filtro.addEventListener("change", aplicarFiltros);
    });


    function formatoEntregables(valor){
        const v = parseInt(valor);

        if (v === 1) return "01 Libro";
        return `${String(v).padStart(2, '0')} Libros`;
    }

    function calcularTotales(data) {

        let saldoSoles = 0;
        let saldoDolares = 0;

        data.forEach(d => {

            const total = parseFloat(d.total ?? 0);
            const acuenta = parseFloat(d.acuenta ?? 0);

            const saldo = total - acuenta;

            if (d.moneda == 0) {
                saldoSoles += saldo;
            }

            if (d.moneda == 2) {
                saldoDolares += saldo;
            }
        });

        return { saldoSoles, saldoDolares };
    }

    function renderTabla(data) {

        const tabla = document.getElementById("tabla");
        const { saldoSoles, saldoDolares } = calcularTotales(data);

        document.getElementById("totalRegistros").textContent = data.length;

        document.getElementById("cantiRegistros").textContent =
            data.length === 0 ? "Ningún registro"
            : data.length === 1 ? "Registro en total"
            : "Registros en total";

        // 💰 TOTALES
        document.getElementById("sumasaldoSoles").textContent =
            `S/. ${formatearDecimal(saldoSoles)}`;

        document.getElementById("sumasaldoDolares").textContent =
            `$ ${formatearDecimal(saldoDolares)}`;

        let html = `
        <div class="columna">
            <div>#</div>
            <div>Fecha</div>
            <div>Evento</div>
            <div>Cliente</div>
            <div>Día</div>
            <div>Total</div>
            <div>Saldo</div>
            <div></div>
        </div>`;

        if (data.length === 0) {
            tabla.innerHTML = html;
            return;
        }

        let c = 1;

        data.forEach(d => {

            html += `
            <div class="row">

                <div class="swipe">
                    <div class="gen" onclick="descargar(${d.id})">
                        <span class="material-symbols-outlined">download</span>
                    </div>
                    <div class="edit" data-json='${JSON.stringify(d)}'>
                        <span class="material-symbols-outlined">edit_square</span>
                    </div>
                    <div class="del" data-id="${d.id}" data-func="eliminarContrato">
                        <span class="material-symbols-outlined">disabled_by_default</span>
                    </div>
                </div>

                <div class="fila">
                    <div>${c++}</div>
                    <div>${formatearFecha(d.ini)}</div>
                    <div>${d.evento}</div>
                    <div>${formatearPara(d.para)}</div>
                    <div>${d.duracion}</div>
                    <div>${d.moneda == 0 ? 'S/.' : '$'} ${formatearDecimal(d.total)}</div>
                    <div>${d.moneda == 0 ? 'S/.' : '$'} ${formatearDecimal(d.total - d.acuenta)}</div>
                    <div class="actions">⋮</div>
                </div>

                <div class="vermas">
                    <div>Serie:</div><div>${d.serie}</div>
                    <div>Lugar:</div><div>${d.lugar}</div>
                    <div>Entrega:</div><div>${formatoEntregables(d.entregables)}</div>
                    <div>Cortesía:</div><div>${d.cortesia}</div>
                    <div>Descripción:</div><div>${d.descripcion}</div>
                </div>

            </div>`;
        });

        tabla.innerHTML = html;
    }

    function renderFiestas(selected = null) {
        const select = document.getElementById("fiesta_id");
        if (!select) return;

        let html = '';

        FIESTAS.forEach(f => {
            html += `<option value="${f.id}" ${selected == f.id ? 'selected' : ''}>
                        ${f.evento}
                    </option>`;
        });

        select.innerHTML = html;
        if(selected !== null) {
            select.value = String(selected);
        }
    }
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
        id: "cliente",
        render: () => `
        <h1>Datos del cliente</h1>

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
              <input class="selx" type="text" id="dni_rut" name="dni_rut" oninput="dni_rut(event)">
              <button type="button" id="btnBuscarDoc" onclick="buscarDocumentoAPI()">🔍</button>
          </div>
        </div>  

        <div class="field">
          <em>Nombre completo</em>
          <input type="text" id="nombre_razon" name="nombre_razon" placeholder="Nombres y apellidos">
        </div>

        <div class="field">
          <em>Celular1</em>
          <input type="text" id="celular1" name="celular1" inputmode="numeric" pattern="[0-9]*" id="celu1" placeholder="999 999 999" maxlength="11" oninput="trionumero(event)">
        </div>

        <div class="field">
          <em>Celular2</em>
          <input type="text" id="celular2" name="celular2" inputmode="numeric" pattern="[0-9]*" id="celu2" placeholder="999 999 999" maxlength="11" oninput="trionumero(event)">
        </div>

        `,
        validate: () => {
            let dni_rut = document.getElementById('dni_rut').value.trim();
            let nombre_razon = document.getElementById('nombre_razon').value.trim();

            let faltantes = [];

            if(dni_rut === '') faltantes.push('numero de documento');
            if(nombre_razon === '') faltantes.push('nombre completo');

            if(faltantes.length > 0){
                let mensaje = '';
                if(faltantes.length === 1){ mensaje = `Falta ingresar: ${faltantes[0]}`;
                }else{ mensaje = `Falta ingresar: ${faltantes.join(' y ')}`; }
                showFeedback('warning', mensaje);
                return false;
            }

            return true;
        }
    },
    {
        id: "evento",
        render: (data) => {
        setTimeout(() => {
            renderFiestas(data.fiesta_id ?? null);
            initAutocompleteLugar(); // 👈 AQUÍ ES DONDE VA
        }, 0);

        return `
        <h1>Datos del evento</h1>
        <div class="selectdiv field">
            <em>Tipo de evento</em>
            <select id="fiesta_id" name="fiesta_id"></select>
        </div>

        <div class="field">
          <em>Para</em>
          <input type="text" id="para" name="para" placeholder="Juan & Juanita" >
        </div>

        <div class="field" style="position:relative;">
            <em>Lugar</em>
            <input type="text" id="lugar" name="lugar" autocomplete="off">
            <div id="suggestLugares" class="suggest-box"></div>
        </div>

        <div class="field">
          <em>Fecha</em>
          <div class="fecha-fields">
            <input type="number" id="dia" name="dia" inputmode="numeric" pattern="[0-9]*" placeholder="01" min="1" max="31" maxlength="2" oninput="numero2(event)">
            <div class="selectdiv">
              <select id="mes" name="mes">
                  <option value="1">Ene</option>
                  <option value="2">Feb</option>
                  <option value="3">Mar</option>
                  <option value="4">Abr</option>
                  <option value="5">May</option>
                  <option value="6">Jun</option>
                  <option value="7">Jul</option>
                  <option value="8">Ago</option>
                  <option value="9">Sep</option>
                  <option value="10">Oct</option>
                  <option value="11">Nov</option>
                  <option value="12">Dic</option>
              </select>
              <input type="number" id="año" name="año" inputmode="numeric" pattern="[0-9]*" maxlength="4" oninput="numero4(event)">
            </div>
          </div>
        </div>

        <div class="field">
          <em>Días de duración</em>
          <input type="number" id="duracion" name="duracion" inputmode="numeric" pattern="[0-9]*" placeholder="1" maxlength="1" oninput="numero2(event)">
        </div>
        `;},
        validate: () => {
            let para = document.getElementById('para').value.trim();
            let lugar = document.getElementById('lugar').value.trim();
            let dia = document.getElementById('dia').value.trim();
            let año = document.getElementById('año').value.trim();
            let duracion = document.getElementById('duracion').value.trim();

            let faltantes = [];

            if(para === '') faltantes.push('para');
            if(lugar === '') faltantes.push('lugar');
            if(dia === '') faltantes.push('dia');
            if(año === '') faltantes.push('año');
            if(duracion === '') faltantes.push('duración');

            if(faltantes.length > 0){
                let mensaje = '';
                if(faltantes.length === 1){ mensaje = `Falta ingresar: ${faltantes[0]}`;
                }else{ mensaje = `Falta ingresar: ${faltantes.join(' y ')}`; }
                showFeedback('warning', mensaje);
                return false;
            }

            // CORREGIR FECHA
            let d = parseInt(dia);
            let m = parseInt(document.getElementById('mes').value);
            let y = parseInt(año);
            console.log(d, m, y);

            // límites básicos
            if (m < 1) m = 1;
            if (m > 12) m = 12;

            if (d < 1) d = 1;

            // obtener último día del mes
            const ultimoDia = new Date(y, m, 0).getDate();

            if (d > ultimoDia) d = ultimoDia;

            // actualizar inputs SI hubo corrección
            document.getElementById('dia').value = String(d).padStart(2, '0');
            document.getElementById('mes').value = String(m);
            document.getElementById('año').value = y;

            // validar si hubo ajuste
            if (d != parseInt(dia) || m != parseInt(document.getElementById('mes').value)) {
                showFeedback('warning', 'Fecha ajustada automáticamente');
                return false;
            }

            return true;
        }
    },
    {
        id: "detalle",
        render: () => `
        <h1>Descripción y entregables</h1>
        <div class="field">
          <em>Descripción</em>
          <textarea id="descripcion" name="descripcion"></textarea>
        </div>

        <div class="selectdiv field">
          <em>Entregables</em>
          <select id="entregables" name="entregables">
              <option value="0">00 Libros</option>
              <option value="1" selected>01 Libro</option>
              <option value="2">02 Libros</option>
              <option value="3">03 Libros</option>
              <option value="4">04 Libros</option>
          </select>
        </div>

        <div class="field">
          <em>Cortesía</em>
          <textarea id="cortesia" name="cortesia"></textarea>
        </div>
        `,
        validate: () => {
            let descripcion = document.getElementById('descripcion').value.trim();
            let cortesia = document.getElementById('cortesia').value.trim();

            let faltantes = [];

            if(descripcion === '') faltantes.push('descripción');
            if(cortesia === '') faltantes.push('cortesía');

            if(faltantes.length > 0){
                let mensaje = '';
                if(faltantes.length === 1){ mensaje = `Falta ingresar: ${faltantes[0]}`;
                }else{ mensaje = `Falta ingresar: ${faltantes.join(' y ')}`; }
                showFeedback('warning', mensaje);
                return false;
            }

            return true;
        }
    },
    {
        id: "pago",
        render: () => `
        <h1>Precio</h1>
        <div class="selectdiv moneda field">
          <em>Moneda</em>
          <select id="moneda" name="moneda">
              <option value="0">Soles</option>
              <option value="1">Pesos</option>
              <option value="2" selected>Dólares</option>
          </select>
        </div>
        
        <div class="field">
          <em>Total</em>
          <input type="text" id="total" name="total" inputmode="numeric" pattern="[0-9]*"  maxlength="8" oninput="precio(event)">
        </div>

        <div class="field">
          <em>A cuenta</em>
          <input type="text" id="acuenta" name="acuenta" inputmode="numeric" pattern="[0-9]*"  maxlength="8" oninput="precio(event)">
        </div>
        `,
        
        validate: () => {
            let total = document.getElementById('total').value.trim();
            let acuenta = document.getElementById('acuenta').value.trim();

            let faltantes = [];

            if(total === '') faltantes.push('total');
            if(acuenta === '') faltantes.push('acuenta');

            if(faltantes.length > 0){
                let mensaje = '';
                if(faltantes.length === 1){ mensaje = `Falta ingresar: ${faltantes[0]}`;
                }else{ mensaje = `Falta ingresar: ${faltantes.join(' y ')}`; }
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
        data: {
            // valores por defecto
            año: new Date().getFullYear(),
            lugar:'Iquique',
            descripcion: 'Servicio profesional de fotografía: Inicia en tradiciones hasta la presentación del último grupo.',
            entregables: '1',
            cortesia:'Pendrive con todas las fotografías en formato digital y anuncio en reel.'
        },
        onFinish: guardarContrato
    });
    setTimeout(() => {
        renderFiestas(1);
    }, 80);
}

function abrirEditar(data){
    console.log("DATA EDIT:", data);

    modo = 'editar';
    idActual = data.id;

    const f = separarFecha(data.ini);

    abrirwz({
        steps: wzSteps,
        data: {
            dni_rut: data.dni_rut ?? '',
            nombre_razon: data.nombre_razon ?? '',
            celular1: data.celular1 ?? '',
            celular2: data.celular2 ?? '',

            fiesta_id: data.fiesta_id ?? '',
            para: data.para ?? '',
            lugar: data.lugar ?? '',
            dia: f.dia,
            mes: parseInt(f.mes),
            año: f.año,
            duracion: data.duracion ?? '',

            descripcion: data.descripcion ?? '',
            entregables: data.entregables ?? '',
            cortesia: data.cortesia ?? '',

            moneda: data.moneda ?? '',
            total: formatearDecimal(data.total),
            acuenta: formatearDecimal(data.acuenta)
        },
        onFinish: guardarContrato
    });
    
    setTimeout(() => {
        const totalEl = document.getElementById("total");
        const acuentaEl = document.getElementById("acuenta");
        if(totalEl) precio(totalEl);
        if(acuentaEl) precio(acuentaEl);
    },80);

}

/* =========================
   GUARDAR
========================= */

function guardarContrato(data){

    let url = modo === 'crear'
        ? "<?= base_url('contrato/create') ?>"
        : "<?= base_url('contrato/update') ?>/" + idActual;

    console.log("DATA A ENVIAR:", data);
    
    fetch(url, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams(data).toString()
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

function eliminarContrato(id){
    if(!confirm("¿Eliminar registro?")) return;

    fetch("<?= base_url('contrato/delete') ?>/" + id, {
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

/* =========================
   DESCARGAR
========================= */
function descargar(id){
    window.open("<?= base_url('contrato/pdf') ?>/" + id, "_blank");
}

</script>

<script>
    async function buscarDocumentoAPI() {

    console.log("CLICK BUSCAR DNI");
    const tipodocuEl = document.getElementById('tipodocu');
    const dniEl = document.getElementById('dni_rut');
    const nombresEl = document.getElementById('nombre_razon');

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


<script>
function initAutocompleteLugar() {

    const input = document.getElementById("lugar");
    const box = document.getElementById("suggestLugares");

    if (!input || !box) {
        console.warn("No existe input lugar aún");
        return;
    }

    // 🔥 evitar duplicados
    input.oninput = null;

    input.oninput = function () {

        const texto = this.value.toLowerCase().trim();

        // 👇 SI BORRA Y VUELVE A ESCRIBIR, FUNCIONA NORMAL
        if (texto.length < 1) {
            box.style.display = "none";
            return;
        }

        const filtrados = LUGARES.filter(l => {
            if (!l.lugar) return false;
            return l.lugar.toLowerCase().includes(texto);
        });

        if (filtrados.length === 0) {
            box.style.display = "none";
            return;
        }

        let html = '';

        filtrados.slice(0, 10).forEach(l => {
            html += `<div class="suggest-item" data-value="${l.lugar}">
                        ${l.lugar}
                     </div>`;
        });

        box.innerHTML = html;
        box.style.display = "block";
    };

    // click en sugerencia
    box.onclick = function (e) {
        if (e.target.classList.contains("suggest-item")) {
            input.value = e.target.dataset.value;
            box.style.display = "none";
        }
    };

    // cerrar si hace click fuera
    document.addEventListener("click", function (e) {
        if (!box.contains(e.target) && e.target !== input) {
            box.style.display = "none";
        }
    });
}
</script>

<?php echo $this->endSection('contenido');?>