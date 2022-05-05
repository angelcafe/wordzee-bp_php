(function () {
    'use strict'
    document.getElementById('pal6let6').value = 'DP';
    document.getElementById('pal7let7').value = 'TP';
    for (let x = 3; x < 8; x++) {
        for (let y = 1; y < (x + 1); y++) {
            document.getElementById("pal" + x + "let" + y).addEventListener("click", cambiarValorBotones);
            cambiarClaseBotones(document.getElementsByName("pal" + x + "let" + y)[0]);
        }
    }
    var e_letras = document.getElementById('letras');
    document.querySelectorAll("td.palenc").forEach(element => {
        element.addEventListener("click", function (e) {
            let palabra = e.target.innerText.split(' ')[0];
            if (palabra.length > 0) {
                for (let x = 0; x < 7; x++) {
                    document.getElementById('idLetra' + (x + 1)).value = palabra[x] || '';
                }
                e_letras.scrollIntoView();
            }
        });
    });
    var texto = /^[a-jl-vx-zA-JL-VX-ZñÑ]$/;
    var e_sig_letra;
    document.querySelectorAll("#letras > input").forEach(element => {
        element.addEventListener("keyup", e => {
            if (texto.test(element.value) === true) {
                if (e_sig_letra = e.currentTarget.nextElementSibling) {
                    e_sig_letra.focus();
                    e_sig_letra.select();
                }
            } else {
                e.currentTarget.value = '';
            }
        });
        element.addEventListener("click", e => {
            e.currentTarget.select();
        });
    });
    document.getElementById('principal').addEventListener('submit', e => {
        e.preventDefault();
        formularioEnviar('encontradas');
        formularioEnviar('ganadoras');
    });
})()

function cambiarClaseBotones(e) {
    switch (e.value) {
        case 'DL':
            e.className = 'btn btn-secondary rosa';
            break;
        case 'TL':
            e.className = 'btn btn-secondary bg-success';
            break;
        case 'DP':
            e.className = 'btn btn-secondary bg-primary';
            break;
        case 'TP':
            e.className = 'btn btn-secondary bg-danger';
            break;
        case '':
            e.className = 'btn btn-secondary info';
            break;
        default:
            break;
    }
}

function cambiarValorBotones(e) {
    switch (e.srcElement.value) {
        case '':
            e.srcElement.value = 'DL';
            e.srcElement.className = 'btn btn-secondary rosa';
            break;
        case 'DL':
            e.srcElement.value = 'TL';
            e.srcElement.className = 'btn btn-secondary bg-success';
            break;
        case 'TL':
            e.srcElement.value = '';
            e.srcElement.className = 'btn btn-secondary info';
            break;
        default:
            break;
    }
}

function formularioEnviar(obtener) {
    let formulario = new FormData(document.getElementById('principal'));
    formulario.append('obtener', obtener);
    let initObject = {
        method: 'POST',
        body: formulario,
        cache: 'no-cache'
    };
    let userRequest = new Request('index.php', initObject);
    fetch(userRequest)
        .then(response => response.text())
        .then(data => {
            if (obtener == 'encontradas') {
                document.getElementById('idPalabrasEncontradas').innerHTML = data;
            } else {
                document.getElementById('idPalabrasGanadoras').innerHTML = data;
                document.getElementById('idPalabrasEncontradas').scrollIntoView();
            }
        })
        .catch(err => {
            console.log(err);
        });
}

function restablecer() {
    window.location.href = window.location.href;
}