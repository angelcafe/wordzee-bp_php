var palabras = {
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
        var formd = new FormData();
        formd.append('archivo', file);
        palabras.alertaTitulo = 'Importar JSON';
        palabras.alertaTexto = 'Importación finalizada.';
        palabras.alertaDescargar = false;
        ajax(formd, true, false);
        return false;
    },
    responde: '',
};
window.onload = function () {
    document.getElementById('idActivarHerramientas').addEventListener("change", activarHerramientas);
    document.getElementById('idBorrar').addEventListener("click", e => { palabras.borrar(document.getElementById('palabraBorrar').value); });
    document.getElementById('idGuardar').addEventListener("click", palabras.guardar);
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
            document.getElementById('idBorrarPalabra').addEventListener('click', e => { palabras.borrar(e.target.innerText); });
        }
    }
    Array.from(document.querySelectorAll("td.palenc")).forEach(element => {
        element.addEventListener("click", function (e) {
            let palabra = e.target.innerText.split(' ')[0];
            if (palabra.length > 0) {
                document.getElementById('palabraBorrar').value = palabra;
            }
        });
    });
    document.getElementById('idHerramientas').className = document.getElementById('idHerramientas').className.replace('d-none', localStorage.getItem('ActivarHerramientas'));
    document.getElementById('idActivarHerramientas').checked = (localStorage.getItem('ActivarHerramientas').indexOf('none') < 0) ? (true) : (false);
};
function activarHerramientas(e) {
    let h = document.getElementById('idHerramientas');
    if (e.currentTarget.checked) {
        h.className = h.className.replace('d-none ', '');
        localStorage.setItem('ActivarHerramientas', '');
    } else {
        h.className = 'd-none ' + h.className;
        localStorage.setItem('ActivarHerramientas', 'd-none');
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
    var userRequest = new Request('back/buscapalabras.php', initObject);
    fetch(userRequest)
        .then(response => {
            if (response.status == 200) {
                var contentType = response.headers.get("content-type");
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
                document.getElementById('principal').submit();
            } else if (enviar.indexOf('cargar') !== -1) {
                palabrasCargadas(data);
            }
        })
        .catch(function (err) {
            console.log(err);
        });

}