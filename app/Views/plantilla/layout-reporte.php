<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, viewport-fit=cover">
<meta name="format-detection" content="telephone=no, email=no, address=no">
<meta name="google" content="notranslate">
<title><?= session('empresa') ?></title>
<style>
:root {
    --c1: <?= session('color1') ?>;
    --c2: <?= session('color2') ?>;
    --c3: <?= session('color3') ?>;
    --c4: <?= session('color4') ?>;
    --bg: <?= session('color5') ?>;
}
</style>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('css/00_base.css') ?>">
<link rel="stylesheet" href="<?= base_url('css/00_gestor.css') ?>">
<link rel="stylesheet" href="<?= base_url('css/01_tabla.css') ?>">
<link rel="stylesheet" href="<?= base_url('css/01_tabla-reporte.css') ?>"> <!-- cambia -->
<link rel="stylesheet" href="<?= base_url('css/02_snackbar.css') ?>">
<link rel="stylesheet" href="<?= base_url('css/03_wizard.css') ?>">
<link rel="icon" type="image/png" href="<?= base_url('img/logo.png') ?>">
<script src="<?= base_url('js/00_main.js') ?>" defer></script>
<script src="<?= base_url('js/01_swipe.js') ?>" defer></script>
<script src="<?= base_url('js/02_snackbar.js') ?>" defer></script>
<script src="<?= base_url('js/03_wizard-core.js') ?>" defer></script>
<script src="<?= base_url('js/04_numero.js') ?>" defer></script>
<script src="<?= base_url('js/05_letra.js') ?>" defer></script>
</head>

<body>

<div class="admin">
  <!-- SIDEBAR -->
  <div class="sidebar hide" id="sidebar">
    <button class="hamburguesa" onclick="togglecatego()">☰</button>
        <div class="side-header">
            <div class="logo"><img src="<?= base_url('img/logo.png') ?>" alt="logo"></div>
            <div class="title"><?= session('empresa') ?></div>
            <div class="subtitle"><?= session('subnombre') ?></div>
        </div>
        <?php  echo $this->include('plantilla/menu'); ?>
    </div>

    <?php  echo $this->renderSection("contenido");?>

    <?php  echo $this->include('plantilla/icon-animated'); ?>
    <?php  echo $this->include('plantilla/wizard'); ?>
</body>
</html>