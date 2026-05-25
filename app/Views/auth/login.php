<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0">
<meta name="robots" content="noindex">
<title>Farmacia - Acceso</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
<style>
* { box-sizing: border-box; margin:0; padding:0; }
body {
  font-family:'Inter', sans-serif;
  min-height:100dvh;
  display:flex;
  align-items:center;
  justify-content:center;
  background: linear-gradient(135deg, #0f172a 0%, #155e75 50%, #0e7490 100%);
  overflow: hidden;
}
/* Animated background circles */
body::before, body::after {
  content:'';
  position:fixed;
  border-radius:50%;
  opacity:.07;
  background:white;
}
body::before { width:500px;height:500px;top:-100px;right:-100px; }
body::after  { width:300px;height:300px;bottom:-80px;left:-60px; }

.login-card {
  background: white;
  border-radius: 20px;
  padding: 40px 36px;
  width: 100%;
  max-width: 400px;
  box-shadow: 0 20px 60px rgba(0,0,0,.3);
  animation: slideUp .5s cubic-bezier(.4,0,.2,1);
  position: relative;
  z-index: 1;
}
.login-card .icon {
  width: 56px; height: 56px;
  background: linear-gradient(135deg, #0e7490, #06b6d4);
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
  color: white;
  font-size: 28px;
  box-shadow: 0 4px 14px rgba(14,116,144,.3);
}
.login-card h1 {
  text-align: center;
  font-size: 22px;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 4px;
}
.login-card .subtitle {
  text-align: center;
  font-size: 13px;
  color: #64748b;
  margin-bottom: 28px;
}
.field { margin-bottom: 18px; position: relative; }
.field label {
  display: block;
  font-size: 12px;
  font-weight: 600;
  color: #475569;
  margin-bottom: 6px;
  text-transform: uppercase;
  letter-spacing: .5px;
}
.field input {
  width: 100%;
  height: 44px;
  padding: 0 14px;
  border: 1.5px solid #e2e8f0;
  border-radius: 10px;
  font-size: 15px;
  color: #1e293b;
  background: #f8fafc;
  outline: none;
  transition: border-color .2s, box-shadow .2s;
}
.field input:focus {
  border-color: #06b6d4;
  box-shadow: 0 0 0 3px rgba(6,182,212,.15);
  background: white;
}
.field input[type="password"] {
  font-size: 24px;
  letter-spacing: 3px;
}
.btn-login {
  width: 100%;
  height: 48px;
  border: none;
  border-radius: 12px;
  background: linear-gradient(135deg, #0e7490, #06b6d4);
  color: white;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  transition: all .2s;
  margin-top: 6px;
  letter-spacing: .3px;
}
.btn-login:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(14,116,144,.35); }
.btn-login:active { transform: scale(.98); }
.btn-login:disabled { opacity: .6; cursor: not-allowed; transform: none; }

.error-msg {
  text-align: center;
  color: #ef4444;
  font-size: 13px;
  font-weight: 600;
  margin-top: 14px;
  min-height: 20px;
}

/* Caja Modal */
.caja-overlay {
  position: fixed;
  top:0;left:0;width:100%;height:100%;
  background: rgba(15,23,42,.7);
  backdrop-filter: blur(6px);
  z-index: 100;
  display: none;
  align-items: center;
  justify-content: center;
}
.caja-overlay.show { display: flex; }
.caja-box {
  background: white;
  border-radius: 20px;
  padding: 36px;
  width: 100%;
  max-width: 380px;
  text-align: center;
  box-shadow: 0 20px 60px rgba(0,0,0,.3);
  animation: slideUp .4s;
}
.caja-box .material-symbols-outlined {
  font-size: 48px;
  color: #0e7490;
  margin-bottom: 12px;
}
.caja-box h2 { font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 6px; }
.caja-box p { font-size: 13px; color: #64748b; margin-bottom: 20px; }
.caja-box input {
  width: 100%;
  height: 48px;
  padding: 0 16px;
  border: 1.5px solid #e2e8f0;
  border-radius: 10px;
  font-size: 22px;
  font-weight: 700;
  text-align: center;
  color: #0f172a;
  outline: none;
  margin-bottom: 14px;
}
.caja-box input:focus { border-color: #06b6d4; box-shadow: 0 0 0 3px rgba(6,182,212,.15); }

@keyframes slideUp { from { transform:translateY(30px); opacity:0 } to { transform:translateY(0); opacity:1 } }
</style>
</head>
<body>

<div class="login-card">
  <div class="icon">
    <span class="material-symbols-outlined">local_pharmacy</span>
  </div>
  <h1>Acceso al Sistema</h1>
  <p class="subtitle">Ingrese sus credenciales para continuar</p>

  <form id="loginForm">
    <div class="field">
      <label>Usuario (DNI)</label>
      <input type="text" id="user" name="user" placeholder="Ej: 70000000" maxlength="11" autofocus
             onkeypress="if(event.which<48||event.which>57)event.preventDefault()">
    </div>
    <div class="field">
      <label>Contraseña</label>
      <input type="password" id="pass" name="pass" placeholder="••••">
    </div>
    <button type="submit" class="btn-login" id="btnLogin">Ingresar</button>
    <div class="error-msg" id="errorMsg"></div>
  </form>
</div>

<!-- Modal Caja -->
<div class="caja-overlay" id="cajaModal">
  <div class="caja-box">
    <span class="material-symbols-outlined">paid</span>
    <h2>Apertura de Caja</h2>
    <p>Ingrese el monto inicial de caja para hoy</p>
    <input type="text" id="cajaInicial" placeholder="0.00"
           onkeypress="if((event.which<48||event.which>57)&&event.which!==46)event.preventDefault()">
    <button class="btn-login" onclick="abrirCaja()">Abrir Caja e Ingresar</button>
  </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const btn = document.getElementById('btnLogin');
  const err = document.getElementById('errorMsg');
  btn.disabled = true;
  err.textContent = '';

  const data = new FormData(this);

  fetch('<?= base_url("login") ?>', {
    method: 'POST',
    body: data
  })
  .then(r => r.json())
  .then(d => {
    if (d.error === 0) {
      if (d.needCaja) {
        document.getElementById('cajaModal').classList.add('show');
      } else {
        window.location.href = d.redirect;
      }
    } else {
      err.textContent = d.msg;
      btn.disabled = false;
    }
  })
  .catch(() => {
    err.textContent = 'Error de conexión';
    btn.disabled = false;
  });
});

function abrirCaja() {
  const monto = document.getElementById('cajaInicial').value;
  if (!monto) return;

  const data = new FormData();
  data.append('inicial', monto);

  fetch('<?= base_url("caja/abrir") ?>', {
    method: 'POST',
    body: data
  })
  .then(r => r.json())
  .then(d => {
    if (d.error === 0) {
      window.location.href = d.redirect;
    }
  });
}
</script>

</body>
</html>
