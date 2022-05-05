<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="favicon.ico" type="image/ico" />
    <link rel="stylesheet" href="cdn/bootstrap.min.css">
    <link rel="stylesheet" href="index.css">
    {herramientas_css}
    <title>Busca palabras</title>
</head>

<body>
    <div class="container-fluid p-0">
        <!-- Cabecera -->
        <nav class="navbar px-2" style="background-color: #e3f2fd;">
            <span class="navbar-brand">Buscador de palabras para Wordzee! <a class="fs-6" data-bs-toggle="modal" href="#id_ventana_version">{version}</a></span>
            {herramientas_conmutador}
            <span class="navbar-text">
                <a href="https://www.facebook.com/groups/338600778038469" target="_blank"><img src="cdn/Facebook_f_logo_2019.svg" height="20"></a>
                <a href="https://t.me/wordzee" target="_blank"><img src="cdn/Telegram_2019_Logo.svg.png" height="20"></a>
            </span>
        </nav>
        <div class="row m-0">
            <!-- Puntos extra -->
            <div id="idPuntosExtra" class="col px-3 py-2">
                <form name="principal" id="principal" method="POST">
                    <h4>Puntos extra</h4>
                    <div class="p-0 mb-1">
                        <input type="text" name="pal3let1" id="pal3let1" class="btn btn-secondary info" value="{pal3let1}" readonly>
                        <input type="text" name="pal3let2" id="pal3let2" class="btn btn-secondary info" value="{pal3let2}" readonly>
                        <input type="text" name="pal3let3" id="pal3let3" class="btn btn-secondary info" value="{pal3let3}" readonly>
                    </div>
                    <div class="p-0 mb-1">
                        <input type="text" name="pal4let1" id="pal4let1" class="btn btn-secondary info" value="{pal4let1}" readonly>
                        <input type="text" name="pal4let2" id="pal4let2" class="btn btn-secondary info" value="{pal4let2}" readonly>
                        <input type="text" name="pal4let3" id="pal4let3" class="btn btn-secondary info" value="{pal4let3}" readonly>
                        <input type="text" name="pal4let4" id="pal4let4" class="btn btn-secondary info" value="{pal4let4}" readonly>
                    </div>
                    <div class="p-0 mb-1">
                        <input type="text" name="pal5let1" id="pal5let1" class="btn btn-secondary info" value="{pal5let1}" readonly>
                        <input type="text" name="pal5let2" id="pal5let2" class="btn btn-secondary info" value="{pal5let2}" readonly>
                        <input type="text" name="pal5let3" id="pal5let3" class="btn btn-secondary info" value="{pal5let3}" readonly>
                        <input type="text" name="pal5let4" id="pal5let4" class="btn btn-secondary info" value="{pal5let4}" readonly>
                        <input type="text" name="pal5let5" id="pal5let5" class="btn btn-secondary info" value="{pal5let5}" readonly>
                    </div>
                    <div class="p-0 mb-1">
                        <input type="text" name="pal6let1" id="pal6let1" class="btn btn-secondary info" value="{pal6let1}" readonly>
                        <input type="text" name="pal6let2" id="pal6let2" class="btn btn-secondary info" value="{pal6let2}" readonly>
                        <input type="text" name="pal6let3" id="pal6let3" class="btn btn-secondary info" value="{pal6let3}" readonly>
                        <input type="text" name="pal6let4" id="pal6let4" class="btn btn-secondary info" value="{pal6let4}" readonly>
                        <input type="text" name="pal6let5" id="pal6let5" class="btn btn-secondary info" value="{pal6let5}" readonly>
                        <input type="text" name="pal6let6" id="pal6let6" class="btn btn-secondary info" value="{pal6let6}" readonly>
                    </div>
                    <div class="p-0 mb-3">
                        <input type="text" name="pal7let1" id="pal7let1" class="btn btn-secondary info" value="{pal7let1}" readonly>
                        <input type="text" name="pal7let2" id="pal7let2" class="btn btn-secondary info" value="{pal7let2}" readonly>
                        <input type="text" name="pal7let3" id="pal7let3" class="btn btn-secondary info" value="{pal7let3}" readonly>
                        <input type="text" name="pal7let4" id="pal7let4" class="btn btn-secondary info" value="{pal7let4}" readonly>
                        <input type="text" name="pal7let5" id="pal7let5" class="btn btn-secondary info" value="{pal7let5}" readonly>
                        <input type="text" name="pal7let6" id="pal7let6" class="btn btn-secondary info" value="{pal7let6}" readonly>
                        <input type="text" name="pal7let7" id="pal7let7" class="btn btn-secondary info" value="{pal7let7}" readonly>
                    </div>
                    <h4>Letras disponibles</h4>
                    <div class="input-group input-group-sm mb-3" id="letras">
                        <input name="letrasDisponibles[]" id="idLetra1" type="text" size="1" maxlength="1" autocomplete="off" required="required" class="form-control text-uppercase" value="{letdis1}">
                        <input name="letrasDisponibles[]" id="idLetra2" type="text" size="1" maxlength="1" autocomplete="off" required="required" class="form-control text-uppercase" value="{letdis2}">
                        <input name="letrasDisponibles[]" id="idLetra3" type="text" size="1" maxlength="1" autocomplete="off" required="required" class="form-control text-uppercase" value="{letdis3}">
                        <input name="letrasDisponibles[]" id="idLetra4" type="text" size="1" maxlength="1" autocomplete="off" required="required" class="form-control text-uppercase" value="{letdis4}">
                        <input name="letrasDisponibles[]" id="idLetra5" type="text" size="1" maxlength="1" autocomplete="off" required="required" class="form-control text-uppercase" value="{letdis5}">
                        <input name="letrasDisponibles[]" id="idLetra6" type="text" size="1" maxlength="1" autocomplete="off" required="required" class="form-control text-uppercase" value="{letdis6}">
                        <input name="letrasDisponibles[]" id="idLetra7" type="text" size="1" maxlength="1" autocomplete="off" required="required" class="form-control text-uppercase" value="{letdis7}">
                    </div>
                    <h4>Ronda</h4>
                    <div class="form-check form-check-inline mb-3">
                        <input type="radio" id="idRonda1" name="ronda" value="1" class="form-check-input" {ronda1}>
                        <label class="form-check-label" for="idRonda1">1</label>
                    </div>
                    <div class="form-check form-check-inline mb-3">
                        <input type="radio" id="idRonda2" name="ronda" value="2" class="form-check-input" {ronda2}>
                        <label class="form-check-label" for="idRonda2">2</label>
                    </div>
                    <div class="form-check form-check-inline mb-3">
                        <input type="radio" id="idRonda3" name="ronda" value="3" class="form-check-input" {ronda3}>
                        <label class="form-check-label" for="idRonda3">3</label>
                    </div>
                    <div class="form-check form-check-inline mb-3">
                        <input type="radio" id="idRonda4" name="ronda" value="4" class="form-check-input" {ronda4}>
                        <label class="form-check-label" for="idRonda4">4</label>
                    </div>
                    <div class="form-check form-check-inline mb-3">
                        <input type="radio" id="idRonda5" name="ronda" value="5" class="form-check-input" {ronda5}>
                        <label class="form-check-label" for="idRonda5">5</label>
                    </div>
                    <div class="btn-toolbar justify-content-between mt-1">
                        <button type="submit" class="btn btn-primary">Buscar</button>
                        <button type="button" onclick="restablecer()" class="btn btn-warning">Restablecer</button>
                    </div>
                </form>
            </div>
            {herramientas_panel}
            <div class="col px-3 py-2">
                <!-- Palabras sugeridas -->
                <h4 class="palenc" data-bs-toggle="collapse" data-bs-target="#id_palabrasSugeridas" aria-expanded="true" aria-controls="id_palabrasSugeridas">Palabras de mayor valor</h4>
                <div class="collapse show" id="id_palabrasSugeridas">
                    <table class="table table-sm table-striped table-hover">
                        <thead>
                            <tr>
                                <th>3 letras</th>
                                <th>4 letras</th>
                                <th>5 letras</th>
                                <th>6 letras</th>
                                <th>7 letras</th>
                            </tr>
                        </thead>
                        <tbody id="idPalabrasGanadoras"></tbody>
                    </table>
                </div>
                <!-- Palabras encontradas -->
                <h4>Palabras encontradas</h4>
                <table class="table table-sm table-striped table-hover">
                    <thead>
                        <tr>
                            <th>3 letras</th>
                            <th>4 letras</th>
                            <th>5 letras</th>
                            <th>6 letras</th>
                            <th>7 letras</th>
                        </tr>
                    </thead>
                    <tbody id="idPalabrasEncontradas"></tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="id_ventana_version" tabindex="-1" aria-labelledby="id_ventana_version_label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="id_ventana_version_label">Instrucciones y novedades</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>Instrucciones</h6>
                    <p class="m-0">1. Pulsa en los cuadros de "Puntos extra" para indicar las casillas con DL y TP.</p>
                    <p class="m-0">2. Introduce las "Letras disponibles" que te da Wordzee.</p>
                    <p class="m-0">3. Indica la "Ronda" en la que estás para obtener los puntos correctos.</p>
                    <p class="m-0">4. Pulsa en Buscar y elige la palabra que más te convenga.</p>
                    <p class="m-0">Se pueden ocultar las Palabras de mayor valor pulsando en el título.</p>
                    <p class="m-0">Al pulsar en una de las Palabras encontradas, ésta se venvía a Letras disponibles.</p>
                    <hr>
                    <h6>Novedades</h6>
                    <p class="m-0">Ahora se sugieren las "Palabras de mayor valor".</p>
                    <p class="m-0">Optimización de código.</p>
                    <p class="m-0">Retoques de diseño</p>
                    <p class="m-0">Más palabras incluidas y eliminadas del diccionario.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    {herramientas_notificaciones}
    <script src="cdn/bootstrap.bundle.min.js"></script>
    {herramientas_js}
    <script src="index.js"></script>
</body>

</html>