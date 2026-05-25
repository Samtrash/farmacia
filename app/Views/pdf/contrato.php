<?php
/* 👉 ESTE ES EL ÚNICO QUE EDITAS SIEMPRE */
$empresa = strtolower(session('empresa')); // fotoya, dh, etc
$pathparrafo = "pdf/con_{$empresa}.jpg";
$pathFirma  = "pdf/fir_{$empresa}.jpg";
?>

<?php
$diasx = [
    'Monday'    => 'Lunes',
    'Tuesday'   => 'Martes',
    'Wednesday' => 'Miércoles',
    'Thursday'  => 'Jueves',
    'Friday'    => 'Viernes',
    'Saturday'  => 'Sábado',
    'Sunday'    => 'Domingo'
];

$mesesx = [
    'January'   => 'enero',
    'February'  => 'febrero',
    'March'     => 'marzo',
    'April'     => 'abril',
    'May'       => 'mayo',
    'June'      => 'junio',
    'July'      => 'julio',
    'August'    => 'agosto',
    'September' => 'septiembre',
    'October'   => 'octubre',
    'November'  => 'noviembre',
    'December'  => 'diciembre'
];

$diax = $diasx[date('l')];
$mesx = $mesesx[date('F')];
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
* {box-sizing: border-box;}
html{
	-webkit-text-size-adjust:100%;
	-ms-text-size-adjust:100%;
}

body{
    margin:0;
    font-family: DejaVu Sans, Helvetica; /*pdf, ver web*/
    font-size: 12px;
}
.topi{
    width: 100%;
}
.topi img{
    width: 100%;
    object-fit: cover; 
    display: block;
}

.contrato,.recuadro,.clausula,.pago {
    width: 89%;
    margin: 0 auto;
    border-collapse: collapse;
}

.contrato{
    margin: 5px auto;
}

.contrato td {
    text-align: center;
    font-size: 12px;
    font-weight: bold;
    border: 1px solid red;
}

.contrato td:first-child {
    width: 20%;
}
.contrato td:nth-child(2) {
    width: 60%;
    font-size: 20px;
    color: #d40000;
}
.contrato td:nth-child(3) {
    width: 25%;
    text-align: right;
}

.parrafo{
    clear: both;
    line-height: 19px;
    text-align: justify;
    width: 89%;
    margin: 0 auto;
}

.recuadro {
    margin-top: 12px;
}

.recuadro th, .recuadro td {
    border:1px solid <?= session('color1') ?>;
    align-content: baseline;
    line-height: 15px;
    padding: 5px 5px 5px 8px;
}
.recuadro td:first-child {
    width: 20%;
    text-align: center;
}
.recuadro td:nth-child(2) {
    width: 40%;
}
.recuadro td:nth-child(3) {
    width: 40%;
}

.clausula{
    margin-top: 10px;
}

.clausula th, .clausula td {
    text-align: left;
    /*border: 1px solid blue;*/
}
.clausula ul{
    padding: 0 0 0 19px;
    margin: 4px auto;
    list-style: square;
    line-height: 17px;
    font-size: 11px;
    text-align: justify;
}

.pago{
    margin-top: 5px;
    margin-bottom: 15px;
}
.pago td {
    text-align: center;
    font-size: 16px;
    font-weight: bold;
    /*border: 1px solid red;*/
}

.pago td:first-child {
    width: 30%;
}
.pago td:nth-child(2) {
    width: 30%;
}
.pago td:nth-child(3) {
    width: 30%;
}

.firma {
    margin:0 auto;
}

.firma th, .firma td {
    align-content: baseline;
    line-height: 15px;
    padding: 5px;
    font-size:12px;
    text-align: center;
}
.firma td:first-child {
    width: 40%;
    border-top: 1px dashed black;
}
.firma td:nth-child(2) {
    width: 30%;
}
.firma td:nth-child(3) {
    width: 40%;
    border-top: 1px dashed black;
}

</style>
</head>

<body>
<div class="topi"><img src="<?= $pathparrafo ?>"></div>

    <table class="contrato">
        <tr>
            <td></td>
            <td>CONTRATO <?= strtoupper(session('empresa')) ?></td>
            <td> N°. <?= $contrato['serie_formateada'] ?></td>
        </tr>
    </table>

    <p class="parrafo">
   Siendo el día <?= $diax . ' ' . date('d') . ' de ' . $mesx . ' de ' . date('Y') ?>, se encuentran presentes <b>Don(ña) <?= session('gerente') ?></b>,
    identificado con <b><?= session('identificado') ?></b> en calidad de Gerente General y Representante de <b><?= session('empresa') ?> <?= session('subnombre') ?></b>
    <?= session('ruc') ? '<b> con  '.session('ruc').'</b>': '' ?>. Por la otra
    parte, se encuentra <b>Don(ña). <?= $contrato['nombre_razon'] ?></b>, identificado(a) con <b>DNI/RUT N°. <?= $contrato['dni_rut'] ?></b> en calidad de contratista.
    Se celebra el presente contrato privado mediante el cual el contratista <b>solicita y adquiere</b> los servicios de <?= session('servicio') ?> correspondiente
    a la fiesta del(la) <b><?= $contrato['evento'] ?></b> de <b><?= $contrato['para'] ?></b> a realizarse en <b><?= $contrato['lugar'] ?></b> en <b><?= date('Y', strtotime($contrato['ini'])) ?></b>.
    Para efectos de coordinación, el contratista declara los números de contacto: <b><?= $contrato['celular1'] ?> <?= $contrato['celular2'] ? '</b>y  <b>'.$contrato['celular2'].'</b>': '' ?>.
    

    <table class="recuadro">
    <tr>
        <th>FECHA EN <?= date('Y', strtotime($contrato['ini'])) ?></th>
        <th>DESCRIPCIÓN DEL SERVICIO</th>
        <th>ENTREGABLES</th>
    </tr>
    <tr>
        <td>
            <?php foreach ($dias as $d): ?>
                <?= is_array($d) ? $d['dia'] : $d ?><br>
            <?php endforeach; ?>
        </td>
        <td><?= $contrato['descripcion'] ?></td>
        <td>0<?= $contrato['entregables'] ?> libros en acabado tipo agenda de cuero de 20 páginas.<br>Cortesía: <?= $contrato['cortesia'] ?></td>
    </tr>
    </table>

    <table class="clausula">
    <tr>
        <th>CLAÚSULAS DEL CONTRATO</th>
    </tr>
    <tr>
        <td>
            <ul>
                <?php foreach ($clausulas as $c): ?>
                <li><?= $c['linea'] ?></li>
                <?php endforeach; ?>
            </ul>
        </td>
    </tr>
    </table>

    <table class="pago">
        <tr>
            <td>Total: <?= $contrato['total_fmt'] ?> <?= $contrato['moneda_texto'] ?> </td>
            <td>A cuenta: <?= $contrato['acuenta_fmt'] ?> <?= $contrato['moneda_texto'] ?></td>
            <td>Saldo: <?= $contrato['total'] - $contrato['acuenta'] ?> <?= $contrato['moneda_texto'] ?></td>
        </tr>
    </table>

    <table class="firma">
    <tr>
        <th><img src="<?= $pathFirma ?>" style="width:120px;"></th>
        <th></th>
        <th></th>
    </tr>
    <tr>
        <td><?= session('gerente') ?><br>Gerente General</td>
        <td></td>
        <td><?= $contrato['nombre_razon'] ?><br>N°. <?= $contrato['dni_rut'] ?></td>
    </tr>
    </table>    


</div>
</body>
</html>