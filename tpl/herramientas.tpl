<!-- Herramientas -->
<div id="idHerramientas" class="herramientas d-none col px-3 py-2">
    <h4>Herramientas</h4>
    <form class="row">
        <div class="col">Palabra nueva:</div>
        <div class="col">
            <input name="palabraGuardar" id="palabraGuardar" type="text" minlength="3" maxlength="7" autocomplete="off" required="required" class="form-control text-uppercase" value="">
        </div>
        <div class="col">
            <button id="idGuardar" type="button" class="btn btn-primary">Guardar</button>
        </div>
    </form>
    <form class="row mt-3">
        <div class="col">Borrar palabra:</div>
        <div class="col">
            <input name="palabraBorrar" id="palabraBorrar" type="text" minlength="3" maxlength="7" autocomplete="off" required="required" class="form-control text-uppercase" value="">
        </div>
        <div class="col">
            <button id="idBorrar" type="button" class="btn btn-danger">Borrar</button>
        </div>
    </form>
    <form class="row mt-3">
        <div class="col">Exportar JSON:</div>
        <div class="col"></div>
        <div class="col">
            <button type="button" onclick="palabras.exportar()" class="btn btn-primary">Exportar</button>
        </div>
    </form>
    <form class="row mt-3">
        <div class="col">Importar JSON:</div>
        <div class="col custom-file">
            <input type="file" class="form-control" accept=".json" id="formFile" required="required">
            <label class="form-label" for="formFile"></label>
        </div>
        <div class="col input-group-append">
            <button type="button" class="btn btn-warning" onclick="palabras.importar()">Importar</button>
        </div>
    </form>
</div>