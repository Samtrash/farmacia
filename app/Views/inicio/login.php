<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, viewport-fit=cover">

<link rel="stylesheet" href="<?= base_url('css/00_base.css') ?>">
<link rel="stylesheet" href="<?= base_url('css/02_snackbar.css') ?>">
<link rel="stylesheet" href="<?= base_url('css/04_login.css') ?>">

<script src="<?= base_url('js/02_snackbar.js') ?>"></script>
</head>

<body>

<div class="container">
    <div class="card">
        <div class="top">ACCESO AL SISTEMA</div>

        <div class="field">
            <em>Usuario</em>
            <input type="text" id="usuario" maxlength="12">
        </div>

        <div class="field">
            <em>Contraseña</em>
            <input type="password" id="clave" maxlength="4">
        </div>

        <button type="button" onclick="login()">Continuar</button>
    </div>
</div>

<script>
async function login(){

    const usuario = document.getElementById("usuario").value.trim();
    const clave   = document.getElementById("clave").value.trim();

    // 🔥 VALIDACIÓN FRONTEND
    if(usuario === '' || clave === ''){
        showFeedback('warning','Debe completar los campos');
        return;
    }

    try{
        const res = await fetch("<?= base_url('login-async') ?>", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `usuario=${encodeURIComponent(usuario)}&clave=${encodeURIComponent(clave)}`
        });

        const data = await res.json();

        let status = data[0];
        let msg    = data[1];
        let url    = data[2];

        if(status === 0){
            showFeedback('success', msg);

            setTimeout(()=>{
                window.location.href = url;
            }, 600);

        // 🔥 MULTI EMPRESA
        else if(status === 2){

            let opciones = '';

            data[2].forEach(e => {
                opciones += `${e.empresa_id} - ${e.empresa}\n`;
            });

            let seleccion = prompt("Seleccione empresa:\n" + opciones);

            if(!seleccion) return;

            window.location.href = "<?= base_url('empresa/') ?>" + seleccion;
        }
        else{
            showFeedback('error', msg);

            setTimeout(()=>{
                document.getElementById("usuario").value = "";
                document.getElementById("clave").value = "";
            }, 300);
        }

    }catch(e){
        showFeedback('error','Error de conexión');
    }
}

// 🔥 ENTER PARA LOGIN
document.addEventListener("keydown", function(e){
    if(e.key === "Enter"){
        login();
    }
});
</script>


<div id="feedback" class="feedback">
  <div class="feedback-box">

    <!-- SUCCESS -->
    <div class="icon success">
      <svg viewBox="0 0 52 52">
        <circle class="circle" cx="26" cy="26" r="25"/>
        <path class="check" d="M14 27l7 7 16-16"/>
      </svg>
    </div>

    <!-- ERROR -->
    <div class="icon error">
      <svg viewBox="0 0 52 52">
        <circle class="circle" cx="26" cy="26" r="25"/>
        <path d="M16 16 L36 36 M36 16 L16 36"/>
      </svg>
    </div>

    <!-- WARNING -->
    <div class="icon warning">
      <svg viewBox="0 0 52 52">
        <circle class="circle" cx="26" cy="26" r="25"/>
        <line class="warn-line" x1="26" y1="14" x2="26" y2="30"/>
        <circle class="warn-dot" cx="26" cy="38" r="2"/>
      </svg>
    </div>

    <!-- MENSAJE -->
    <div id="msg"></div>

  </div>
</div>

</body>
</html>