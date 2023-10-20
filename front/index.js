const texto = /^[a-jl-vx-zA-JL-VX-ZñÑ]$/;
let e_sig_letra;

$('#pal6let6').val('DP');
$('#pal7let7').val('TP');

$('input.btn-secondary').each(function () {
    $(this).on('click', cambiarValorBotones);
    cambiarClaseBotones($(this));
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
    formularioEnviar('encontradas');
    formularioEnviar('ganadoras');
});

function cambiarClaseBotones(e) {
    const clase = {DL: 'rosa', TL: 'bg-success', DP: 'bg-primary', TP: 'bg-danger', I: 'info'};
    const valor = e.val().length > 0 ? e.val() : 'I';
    e
        .removeClass('rosa bg-success bg-primary bg-danger info')
        .addClass(clase[valor]);
}

function cambiarValorBotones(e) {
    const clase = {I: 'rosa', DL: 'bg-success', TL: 'info'};
    const valor = {I: 'DL', DL: 'TL', TL: ''};
    const value = $(e.currentTarget).val().length > 0 ? $(e.currentTarget).val() : 'I';
    $(e.currentTarget)
        .removeClass('rosa bg-success info')
        .val(valor[value])
        .addClass(clase[value]);
}

function formularioEnviar(obtener) {
    let formulario = new FormData(document.getElementById('principal'));
    formulario.append('obtener', obtener);
    const initObject = {
        method: 'POST',
        body: formulario,
        cache: 'no-cache'
    };
    const userRequest = new Request('index.php', initObject);
    fetch(userRequest)
        .then(response => response.text())
        .then(data => {
            if (obtener == 'encontradas') {
                $('#idPalabrasEncontradas').html(data);
            } else {
                $('#idPalabrasGanadoras').html(data);
                $('#idPalabrasEncontradas')[0].scrollIntoView();
            }
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
        })
        .catch(err => {
            console.error(err);
        });
}

function restablecer() {
    window.location.href = window.location.href;
}