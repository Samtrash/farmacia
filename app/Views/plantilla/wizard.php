<div id="wzOverlay" class="wz-overlay hidden">
    <div class="wz-container">

        <!-- HEADER -->
        <div class="wz-header">
            <span id="wzStepText">Paso 1 de 4</span>
            <div class="btn-cancelar" onclick="cerrarwz()">Cancelar</div>
        </div>

        <!-- PROGRESO -->
        <div class="wz-progress">
            <div id="wzProgressBar"></div>
        </div>

        <!-- CONTENIDO -->
        <div id="wzContent" class="wz-cont"></div>

        <!-- FOOTER -->
        <div class="wz-footer">
          <div></div>
          <button onclick="prevStep()" id="btnPrev">Atrás</button>
          <div></div>
          <button onclick="nextStep()" id="btnNext" class="btn-next">Siguiente</button>
          <div></div>
        </div>

    </div>
</div>