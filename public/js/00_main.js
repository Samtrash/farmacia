/* ─── SIDEBAR TOGGLE ─── */
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  const main = document.getElementById('main');
  if (window.innerWidth <= 768) {
    sidebar.classList.toggle('show');
  } else {
    sidebar.classList.toggle('hide');
    main.classList.toggle('full');
  }
}

// Close sidebar on outside click (mobile)
document.addEventListener('click', function(e) {
  const sidebar = document.getElementById('sidebar');
  const btn = document.querySelector('.hamburger-inline');
  if (!sidebar || !btn) return;
  
  if (window.innerWidth <= 768) {
    const isOpen = sidebar.classList.contains('show');
    if (isOpen && !sidebar.contains(e.target) && !btn.contains(e.target)) {
      sidebar.classList.remove('show');
    }
  }
});

/* ─── SNACKBAR ─── */
function showSnack(msg, type = '', duration = 3000) {
  const el = document.getElementById('snackbar');
  el.textContent = msg;
  el.className = 'snackbar show ' + type;
  setTimeout(() => { el.className = 'snackbar'; }, duration);
}

/* ─── MODAL HELPERS ─── */
function openModal(id) {
  document.getElementById(id).classList.add('show');
}
function closeModal(id) {
  document.getElementById(id).classList.remove('show');
}

/* ─── SOLO NUMEROS ─── */
function soloNumeros(e) {
  if (e.which < 48 || e.which > 57) e.preventDefault();
}
function soloDecimal(e) {
  const v = e.target.value;
  if ((e.which < 48 || e.which > 57) && e.which !== 46) e.preventDefault();
  if (e.which === 46 && v.indexOf('.') !== -1) e.preventDefault();
}
function filtrarDecimal(input) {
  // Permite solo dígitos y un punto decimal
  input.value = input.value.replace(/[^0-9.]/g, '');
  const parts = input.value.split('.');
  if (parts.length > 2) input.value = parts[0] + '.' + parts.slice(1).join('');
}

/* ─── CUSTOM PROMPT ─── */
function customPrompt(title, label, initialValue, callback, type = 'text') {
  document.getElementById('promptTitle').innerText = title;
  document.getElementById('promptLabel').innerText = label;
  const input = document.getElementById('promptInput');
  
  // Limpiar listeners previos y eventos
  input.onkeypress = null;
  input.oninput = null;
  input.onkeydown = null;
  
  if (type === 'number') {
    input.type = 'text'; // Usamos text + inputmode numeric para evitar spinners nativos de type="number"
    input.inputMode = 'numeric';
    input.pattern = '[0-9]*';
    input.onkeypress = soloNumeros;
    input.oninput = function() {
      this.value = this.value.replace(/[^0-9]/g, '');
    };
  } else if (type === 'decimal') {
    input.type = 'text';
    input.inputMode = 'decimal';
    input.onkeypress = soloDecimal;
    input.oninput = function() {
      filtrarDecimal(this);
    };
  } else {
    input.type = 'text';
    input.inputMode = 'text';
    input.removeAttribute('pattern');
  }
  
  input.value = initialValue;
  
  const btnOk = document.getElementById('btnPromptOk');
  btnOk.onclick = function() {
    closeModal('modalPrompt');
    callback(input.value);
  };
  
  // Aceptar con la tecla Enter
  input.onkeydown = function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      btnOk.click();
    }
  };
  
  openModal('modalPrompt');
  setTimeout(() => { input.focus(); input.select(); }, 100);
}

/* ─── CUSTOM CONFIRM ─── */
function customConfirm(title, text, confirmBtnText, confirmBtnClass, callback) {
  document.getElementById('confirmTitle').innerText = title;
  document.getElementById('confirmText').innerText = text;
  
  const btnOk = document.getElementById('btnConfirmOk');
  btnOk.innerText = confirmBtnText || 'Sí, confirmar';
  btnOk.className = 'btn ' + (confirmBtnClass || 'btn-primary');
  
  btnOk.onclick = function() {
    closeModal('modalConfirm');
    callback();
  };
  openModal('modalConfirm');
}

/* ─── EXPANDABLE ROW (MOBILE) ─── */
function toggleRow(id) {
  const currentContent = document.getElementById('details-' + id);
  const currentRow = document.getElementById('row-' + id);
  if (!currentContent || !currentRow) return;
  const isOpening = !currentContent.classList.contains('show');

  // Cerrar todos los demás que estén abiertos
  document.querySelectorAll('.row-content.show').forEach(el => {
    el.classList.remove('show');
    // Esperar a que termine la animación antes de ocultar el TR
    setTimeout(() => {
      if (el.parentElement && el.parentElement.parentElement) {
        el.parentElement.parentElement.classList.remove('show');
      }
    }, 300);
  });

  if (isOpening) {
    currentRow.classList.add('show');
    // Pequeño delay para que el display:table-row haga efecto antes de animar
    setTimeout(() => currentContent.classList.add('show'), 10);
  }
}