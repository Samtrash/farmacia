<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, viewport-fit=cover">
<meta name="robots" content="noindex">
<title><?= $titulo ?? 'Farmacia' ?></title>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('css/00_base.css') ?>">
<?php if(isset($css)): foreach($css as $c): ?>
<link rel="stylesheet" href="<?= base_url('css/'.$c) ?>">
<?php endforeach; endif; ?>
<link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>">
</head>
<body>


<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <div class="brand"><?= session('h2') ?? 'Farmacia' ?></div>
    <div class="sub"><?= session('h1') ?? '' ?></div>
  </div>

  <nav class="sidebar-nav">
    <?php $uri = service('uri')->getSegment(1); ?>

    <a href="<?= base_url('ventas') ?>" class="<?= $uri === 'ventas' ? 'active' : '' ?>">
      <span class="material-symbols-outlined">point_of_sale</span> Ventas
    </a>

    <?php if(session('master') >= 2): ?>
    <a href="<?= base_url('productos') ?>" class="<?= $uri === 'productos' ? 'active' : '' ?>">
      <span class="material-symbols-outlined">inventory_2</span> Productos
    </a>
    <a href="<?= base_url('usuarios') ?>" class="<?= $uri === 'usuarios' ? 'active' : '' ?>">
      <span class="material-symbols-outlined">group</span> Usuarios
    </a>
    <a href="<?= base_url('reportes') ?>" class="<?= $uri === 'reportes' ? 'active' : '' ?>">
      <span class="material-symbols-outlined">monitoring</span> Reportes
    </a>
    <?php endif; ?>

    <?php if(session('master') == 3): ?>
    <a href="<?= base_url('ajustes') ?>" class="<?= $uri === 'ajustes' ? 'active' : '' ?>">
      <span class="material-symbols-outlined">settings</span> Ajustes
    </a>
    <?php endif; ?>

    <div class="divider"></div>

    <a href="<?= base_url('logout') ?>">
      <span class="material-symbols-outlined">logout</span> Cerrar Sesión
    </a>
  </nav>

  <div class="sidebar-footer">
    <?= session('usuario') ?? '' ?><br>
    <?= date('d/m/Y') ?>
  </div>
</aside>

<!-- MAIN -->
<div class="main" id="main">
  <div class="topbar">
    <button class="hamburger-inline" onclick="toggleSidebar()">
      <span class="material-symbols-outlined">menu</span>
    </button>
    <span class="page-title"><?= $titulo ?? '' ?></span>
    <span class="user-info">
      <strong><?= session('usuario') ?? '' ?></strong> · <?= session('h4') ?? '' ?>
    </span>
  </div>

  <?= $this->renderSection('contenido') ?>
</div>

<!-- SNACKBAR -->
<div class="snackbar" id="snackbar"></div>

<!-- GENERIC PROMPT MODAL -->
  <div class="modal-overlay" id="modalPrompt">
    <div class="modal-box" style="max-width:350px">
      <div class="modal-header">
        <h3 id="promptTitle">Título</h3>
      </div>
      <div class="modal-body">
        <label id="promptLabel" style="font-size:14px;color:var(--gray-600);margin-bottom:8px;display:block">Label</label>
        <input type="text" id="promptInput" class="form-control" autocomplete="off">
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" onclick="closeModal('modalPrompt')">Cancelar</button>
        <button class="btn btn-primary" id="btnPromptOk">Aceptar</button>
      </div>
    </div>
  </div>

  <div class="modal-overlay" id="modalConfirm">
    <div class="modal-box" style="max-width:400px; text-align:center">
      <div class="modal-body" style="padding: 24px">
        <span class="material-symbols-outlined" style="font-size:48px; color:var(--warning); margin-bottom:16px">warning</span>
        <h3 id="confirmTitle" style="margin-bottom:8px">¿Estás seguro?</h3>
        <p id="confirmText" style="color:var(--gray-600); font-size:14px; margin-bottom:24px">Esta acción no se puede deshacer.</p>
        <div style="display:flex; gap:12px; justify-content:center">
          <button class="btn btn-secondary" onclick="closeModal('modalConfirm')" style="flex:1">Cancelar</button>
          <button class="btn btn-primary" id="btnConfirmOk" style="flex:1">Sí, confirmar</button>
        </div>
      </div>
    </div>
  </div>

<!-- Hidden input para JS API calls -->
<input type="hidden" id="baseUrl" value="<?= base_url() ?>">
<script src="<?= base_url('js/00_main.js?v=' . time()) ?>"></script>
<?php if(isset($js)): foreach($js as $j): ?>
<script src="<?= base_url('js/'.$j . '?v=' . time()) ?>"></script>
<?php endforeach; endif; ?>

</body>
</html>
