document.addEventListener('DOMContentLoaded', function () {
    const campoPlaca = document.getElementById('placa');

    if (campoPlaca) {
        campoPlaca.addEventListener('input', function (e) {
            let valor = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            if (valor.length > 7) valor = valor.substring(0, 7);
            e.target.value = valor;
        });
    }

    const relogio = document.getElementById('relogio');
    if (relogio && relogio.dataset.entrada) {
        const entrada = new Date(relogio.dataset.entrada.replace(' ', 'T'));

        function atualizarRelogio() {
            const agora = new Date();
            let diffMs = agora - entrada;
            if (diffMs < 0) diffMs = 0;

            const horas = Math.floor(diffMs / 3600000);
            const minutos = Math.floor((diffMs % 3600000) / 60000);
            const segundos = Math.floor((diffMs % 60000) / 1000);

            relogio.textContent =
                String(horas).padStart(2, '0') + ':' +
                String(minutos).padStart(2, '0') + ':' +
                String(segundos).padStart(2, '0');
        }

        atualizarRelogio();
        setInterval(atualizarRelogio, 1000);
    }

    document.querySelectorAll('.confirmar-saida').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            if (!confirm('Confirma o registro de saída deste veículo?')) {
                e.preventDefault();
            }
        });
    });

    document.querySelectorAll('.needs-validation').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
});
