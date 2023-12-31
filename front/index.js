$(function () {
    const texto = /^[a-jl-vx-zA-JL-VX-ZñÑ]$/;
    $('input.btn-secondary').each(function () {
        $(this).on('click', cambiarValorBotones);
    });
    $('#letras>input')
        .on('keyup', function () {
            if (texto.test($(this).val()) === true) {
                $(this).next()
                    .trigger('focus')
                    .trigger('select');
            } else {
                $(this).val('');
            }
        })
        .on('click', function () {
            $(this).trigger('select');
        });
    $('#principal').on('submit', function (e) {
        e.preventDefault();
        formularioEnviar();
    });
    $('#ronda1').attr('checked', true);

    const acther_ls = localStorage.getItem('ActivarHerramientas');
    const acther = acther_ls !== null && acther_ls.indexOf('none') < 0;
    $('#idHerramientas').addClass(acther_ls);
    $('#idActivarHerramientas')
        .prop('checked', acther)
        .on('change', activarHerramientas);
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        $('html').attr('data-bs-theme', 'dark');
    }
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
        const newColorScheme = event.matches ? "dark" : "light";
        $('html').attr('data-bs-theme', newColorScheme);
    });
});

function cambiarClaseBotones(e) {
    const clase = { DL: 'rosa', TL: 'bg-success', DP: 'bg-primary', TP: 'bg-danger', I: 'info' };
    const valor = e.val().length > 0 ? e.val() : 'I';
    e
        .removeClass('rosa bg-success bg-primary bg-danger info')
        .addClass(clase[valor]);
}

function cambiarValorBotones(e) {
    const clase = { I: 'rosa', DL: 'bg-success', TL: 'info' };
    const valor = { I: 'DL', DL: 'TL', TL: '' };
    const value = $(e.currentTarget).val().length > 0 ? $(e.currentTarget).val() : 'I';
    $(e.currentTarget)
        .removeClass('rosa bg-success info')
        .val(valor[value])
        .addClass(clase[value]);
}

function formularioEnviar() {
    let formulario = new FormData(document.getElementById('principal'));
    $('#cargando').removeClass('d-none');
    $.ajax({
        url: './back/bp.php',
        data: formulario,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (data) {
            mostrarPalabrasEncontradas('idPalabrasEncontradas', 'menu palenc noselect', JSON.parse(data)['encontradas']);
            mostrarPalabrasEncontradas('idPalabrasGanadoras', 'menu noselect', JSON.parse(data)['sugeridas']);
            $('#cargando').addClass('d-none');
            $('td.palenc').on('click', function () {
                let palabra = $(this).text().split(' ')[0];
                if (palabra.length > 0) {
                    let contador = 0;
                    $('#letras>input').each(function () {
                        $(this).val(palabra[contador++]);
                    });
                    $('#letras')[0].scrollIntoView();
                }
            });
        },
        error: function (data) {
            console.error(data);
        }
    });
}

function mostrarPalabrasEncontradas(id, clase, datos) {
    const tabla = [[], [], [], [], []];
    for (const [palabra, puntos] of Object.entries(datos)) {
        const index = palabra.length - 3;
        tabla[index].push(`<td class="${clase}">${palabra} - ${puntos}</td>`);
    }
    const maximo = Math.max(...tabla.map(arr => arr.length));
    const filas = Array.from({ length: maximo }, (_, x) => {
        return '<tr>' + tabla.map(arr => arr[x] || '<td></td>').join('') + '</tr>';
    });
    $('#' + id)
        .empty()
        .append(filas.join(''));
}

function restablecer() {
    window.location.href = window.location.href;
}

/*
Admin
 */

const palabras = {
    alerta: function () {
        let myModal = new bootstrap.Modal(document.getElementById('modalAlert'));
        document.querySelector('#modalAlert .modal-title').innerText = palabras.alertaTitulo;
        document.querySelector('#modalAlert .modal-body p').innerText = palabras.alertaTexto;
        myModal.show();
        if (palabras.alertaDescargar) {
            palabras.descargar();
        }
    },
    alertaDescargar: false,
    alertaTexto: '',
    alertaTitulo: '',
    borrar: function (palabra) {
        ajax('borrar=' + palabra);
    },
    descargar: function () {
        let blob = JSON.stringify(palabras.responde);
        let fileName = 'sp.json';
        let link = document.createElement('a');
        let binaryData = [];
        binaryData.push(blob);
        link.href = window.URL.createObjectURL(new Blob(binaryData, { type: "application/json" }));
        link.download = fileName;
        link.click();
    },
    exportar: function () {
        palabras.alertaTitulo = 'Exportar JSON';
        palabras.alertaTexto = 'Exportación finalizada.';
        palabras.alertaDescargar = true;
        ajax('exportar=true', true);
    },
    guardar: function () {
        let palabra = document.getElementById('palabraGuardar').value;
        ajax('guardar=' + palabra);
    },
    importar: function () {
        const file = document.getElementById('formFile').files[0];
        const formd = new FormData();
        formd.append('archivo', file);
        palabras.alertaTitulo = 'Importar JSON';
        palabras.alertaTexto = 'Importación finalizada.';
        palabras.alertaDescargar = false;
        ajax(formd, true, false);
        return false;
    },
    responde: '',
};

$('#idBorrar').on('click', e => {
    palabras.borrar($('#palabraBorrar').val());
});
$('#idGuardar').on('click', palabras.guardar);

document.onclick = function (e) {
    document.getElementById('menu').style.display = 'none';
}
document.oncontextmenu = function (e) {
    document.getElementById('menu').style.display = 'none';
    if (e.target.className.includes("menu")) {
        e.preventDefault();
        document.getElementById('menu').style.left = e.pageX - 100 + 'px';
        document.getElementById('menu').style.top = e.pageY + 'px';
        document.getElementById('menu').style.display = 'block';
        document.getElementById('idBorrarPalabra').innerText = e.target.innerText.split(' ')[0];
        document.getElementById('idBorrarPalabra').addEventListener('click', e => {
            palabras.borrar(e.target.innerText);
        });
    }
}
Array.from(document.querySelectorAll("td.palenc")).forEach(element => {
    element.addEventListener("click", function (e) {
        let palabra = e.target.innerText.split(' ')[0];
        if (palabra.length > 0) {
            $('#palabraBorrar').val(palabra);
        }
    });
});

function activarHerramientas(e) {
    if (e.currentTarget.checked) {
        localStorage.setItem('ActivarHerramientas', '');
        $('#idHerramientas').removeClass('d-none');
    } else {
        localStorage.setItem('ActivarHerramientas', 'd-none');
        $('#idHerramientas').addClass('d-none');
    }
}

function ajax(enviar = null, alerta = false, header = true) {
    let reqHeader = new Headers();
    if (header) {
        reqHeader.append('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    }
    let initObject = {
        method: 'POST', headers: reqHeader, body: enviar, cache: 'no-cache',
    };
    const userRequest = new Request('back/buscapalabras.php', initObject);
    fetch(userRequest)
        .then(response => {
            if (response.status == 200) {
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.indexOf("application/json") !== -1) {
                    return response.json();
                } else {
                    return response;
                }
            } else {
                throw "Respuesta incorrecta del servidor";
            }
        })
        .then(function (data) {
            if (alerta) {
                palabras.responde = data;
                palabras.alerta();
            } else if (enviar.indexOf('borrar') !== -1 || enviar.indexOf('guardar') !== -1) {
                $('#principal').trigger('submit');
            } else if (enviar.indexOf('cargar') !== -1) {
                palabrasCargadas(data);
            }
        })
        .catch(function (err) {
            console.log(err);
        });
}