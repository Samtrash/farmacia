<?= $this->extend('plantilla/layout-simple') ?>
<?= $this->section('contenido'); ?>

<style>
.container.full { background:#e5e5e5; }
.a4 {
    width: 210mm;
    min-height: 297mm;
    margin: auto;
    background: white;
    padding: 40px;
}

/* 👉 SOLO MOVIL */
@media screen and (max-width: 768px) {
    .a4 {
        transform: scale(0.55);   /* ajusta aquí */
        transform-origin: top center;
        translate: -182px 8px;
    }
}

/* 👉 ASEGURA QUE PDF NO SE AFECTE */
@media print {
    .a4 {
        transform: scale(1);
    }
}
</style>

<div class="container full" id="container">
<div class="a4">
    <?= view('pdf/contrato', ['contrato' => $contrato]) ?>
</div>
</div>
<?= $this->endSection(); ?>