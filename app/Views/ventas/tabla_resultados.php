<?php if(empty($productos)): ?>
<tr><td colspan="6" style="padding:30px;color:#94a3b8;text-align:center">Ningún resultado</td></tr>
<?php else: foreach($productos as $i => $p): ?>
<tr <?php if($p['ppromo']): ?>title="Descuento: <?= $p['pventa'] ?> → <?= $p['ppromo'] ?>"<?php endif; ?>>
  <td style="text-align:center" class="hide-mobile"><?= $i + 1 ?></td>
  <td style="text-align:left;padding-left:8px"><?= esc($p['detalle']) ?></td>
  <td style="text-align:center" class="hide-mobile">
    <input class="qty-input" type="text" id="qty_<?= $p['id'] ?>" value="1" maxlength="3" onkeypress="soloNumeros(event)">
  </td>
  <td style="text-align:center"><?= $p['ppromo'] ?? $p['pventa'] ?></td>
  <td style="text-align:center"><?= $p['stock'] ?></td>
  <td style="text-align:center">
    <button class="btn-add" onclick="agregarAlCarrito(<?= $p['stock'] ?>, <?= $p['id'] ?>, '<?= esc($p['detalle'], 'js') ?>', <?= $p['ppromo'] ?? $p['pventa'] ?>)">
      <span class="material-symbols-outlined icon">add</span>
      <span class="text">Agregar</span>
    </button>
  </td>
</tr>
<?php endforeach; endif; ?>
