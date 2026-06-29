<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ticket de Venta</title>
<style>
  @page { size: 80mm auto; margin: 0; }
  * { margin:0; padding:0; box-sizing:border-box; }
  body {
    font-family: 'Courier New', monospace;
    font-size: 12px;
    width: 80mm;
    padding: 8px;
    color: #000;
  }
  .center { text-align: center; }
  .bold { font-weight: bold; }
  .divider { border-top: 1px dashed #000; margin: 6px 0; }
  .header { margin-bottom: 8px; }
  .header h2 { font-size: 14px; margin-bottom: 2px; }
  .header p { font-size: 10px; }
  table { width: 100%; border-collapse: collapse; margin: 4px 0; }
  th { text-align: left; font-size: 10px; border-bottom: 1px solid #000; padding: 2px 0; }
  td { font-size: 11px; padding: 2px 0; vertical-align: top; }
  .total-row { font-size: 14px; font-weight: bold; text-align: right; margin-top: 4px; }
  .footer { margin-top: 8px; font-size: 10px; text-align: center; }
  .qr-section { margin-top: 8px; text-align: center; }
  .qr-section p { font-size: 9px; color: #555; margin-top: 3px; }
  #qrcode { display: inline-block; }
  #qrcode canvas, #qrcode img { width: 80px !important; height: 80px !important; }
</style>
</head>
<body>
<?php if (!empty($todoV)): $v = $todoV[0]; ?>

<div class="header center">
  <h2><?= session('h2') ?></h2>
  <p><?= session('h1') ?></p>
  <p><?= session('h3') ?></p>
  <p><?= session('h4') ?></p>
  <?php if(session('h5')): ?><p>RUC: <?= session('h5') ?></p><?php endif; ?>
</div>

<div class="divider"></div>

<div class="center bold">
  <?php
    $tipo = '';
    if ($v->formato == 3) $tipo = 'BOLETA DE VENTA';
    elseif ($v->formato == 1) $tipo = 'FACTURA';
    else $tipo = 'NOTA INTERNA';
  ?>
  <?= $tipo ?>
</div>
<div class="center"><?= $v->serie ?? '' ?></div>
<p>Fecha: <?= $v->fecha ?></p>
<p>Atendido por: <?= $v->atiende ?></p>

<?php if (isset($v->dni_ruc) && $v->dni_ruc): ?>
<p><?= ($v->tipo == 2 ? 'RUC' : 'DNI') ?>: <?= $v->dni_ruc ?></p>
<p><?= ($v->tipo == 2 ? 'Razón Social' : 'Cliente') ?>: <?= $v->nombre_razon ?></p>
<?php endif; ?>

<div class="divider"></div>

<table>
  <thead>
    <tr>
      <th style="width:8%">#</th>
      <th style="width:10%">Cant</th>
      <th style="width:52%">Descripción</th>
      <th style="width:15%">P.U.</th>
      <th style="width:15%;text-align:right">Sub.</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($todoV as $i => $item): ?>
    <tr>
      <td><?= $i + 1 ?></td>
      <td><?= $item->cantidad ?></td>
      <td><?= $item->detalle ?></td>
      <td><?= number_format($item->precio, 2) ?></td>
      <td style="text-align:right"><?= number_format($item->cantidad * $item->precio, 2) ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<div class="divider"></div>

<?php
  $total     = floatval($v->total);
  $esGravado = ($v->formato == 3 || $v->formato == 1); // Boleta o Factura
  if ($esGravado) {
    $opGravadas = round($total / 1.18, 2);
    $igv        = round($total - $opGravadas, 2);
  }
?>

<?php if ($esGravado): ?>
<table style="margin:4px 0">
  <tr>
    <td style="font-size:11px">OP. GRAVADAS:</td>
    <td style="text-align:right;font-size:11px">S/ <?= number_format($opGravadas, 2) ?></td>
  </tr>
  <tr>
    <td style="font-size:11px">IGV (18%):</td>
    <td style="text-align:right;font-size:11px">S/ <?= number_format($igv, 2) ?></td>
  </tr>
</table>
<div class="divider"></div>
<?php endif; ?>

<div class="total-row">TOTAL: S/ <?= number_format($total, 2) ?></div>
<div class="divider"></div>

<div class="footer">
  <p><?= session('pie') ?></p>
  <p>¡Gracias por su compra!</p>
</div>

<div class="divider"></div>
<div class="qr-section">
  <div id="qrcode"></div>
  <?php if ($v->formato == 3): ?>
    <p>Representación digital de la<br>Boleta de Venta Electrónica</p>
  <?php elseif ($v->formato == 1): ?>
    <p>Representación digital de la<br>Factura de Venta Electrónica</p>
  <?php else: ?>
    <p>Representación digital de la<br>Nota Interna</p>
  <?php endif; ?>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
  var qrData = <?= json_encode(
    ($v->serie ?? 'TICKET') . '|S/' . number_format($v->total, 2) . '|' . $v->fecha . '|' . $v->atiende
  ) ?>;
  new QRCode(document.getElementById('qrcode'), {
    text: qrData,
    width: 80,
    height: 80,
    colorDark: '#000000',
    colorLight: '#ffffff',
    correctLevel: QRCode.CorrectLevel.M
  });
</script>

<?php else: ?>
<p class="center">No hay datos de venta</p>
<?php endif; ?>

</body>
</html>
