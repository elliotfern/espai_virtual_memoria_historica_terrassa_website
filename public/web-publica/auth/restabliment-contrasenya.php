<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

<div class="container" style="margin-top:30px;margin-bottom:60px">
    <div class="card mx-auto" style="max-width: 400px;">
        <div class="card-body">
            <div class="container">

                <h3>Canvi de contrasenya</h3>

                <div class="alert alert-secondary" role="alert">
                    <small>La contrasenya ha de contenir, almenys:
                        <ul>
                            <li>8 caràcters</li>
                            <li>Una lletra majúscula</li>
                            <li>Un número</li>
                            <li>Un símbol especial (!, $, &, *)</li>
                        </ul>
                    </small>
                </div>

                <div class="alert alert-success" id="messageOk" style="display:none"></div>
                <div class="alert alert-danger" id="messageErr" style="display:none"></div>

                <form id="reset-form">
                    <div class="mb-3">
                        <label for="password" class="form-label">Nova contrasenya</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required>
                            <button type="button" class="btn btn-outline-secondary" id="togglePassword" tabindex="-1">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="passwordConfirm" class="form-label">Confirma la contrasenya</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="passwordConfirm" name="passwordConfirm" required>
                            <button type="button" class="btn btn-outline-secondary" id="togglePasswordConfirm" tabindex="-1">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <input type="hidden" name="token" id="token">
                    <input type="hidden" name="email" id="email">

                    <button type="submit" class="btn btn-primary w-100">Canviar contrasenya</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    // Mostrar/Ocultar contraseña función
    function togglePasswordVisibility(inputId, buttonId) {
        const input = document.getElementById(inputId);
        const button = document.getElementById(buttonId);
        button.addEventListener('click', () => {
            if (input.type === 'password') {
                input.type = 'text';
                button.textContent = 'Amagar';
            } else {
                input.type = 'password';
                button.textContent = 'Veure';
            }
        });
    }

    togglePasswordVisibility('password', 'togglePassword');
    togglePasswordVisibility('passwordConfirm', 'togglePasswordConfirm');

    // Validación de la contraseña
    function validarPassword(password) {
        const minLength = 8;
        const regexMayuscula = /[A-Z]/;
        const regexNumero = /\d/;
        const regexSimbolo = /[!$&*]/;

        if (password.length < minLength) {
            return 'La contrasenya ha de tenir almenys 8 caràcters.';
        }
        if (!regexMayuscula.test(password)) {
            return 'La contrasenya ha de contenir almenys una lletra majúscula.';
        }
        if (!regexNumero.test(password)) {
            return 'La contrasenya ha de contenir almenys un número.';
        }
        if (!regexSimbolo.test(password)) {
            return 'La contrasenya ha de contenir almenys un símbol (!, $, &, *).';
        }
        return ''; // OK
    }


    // Leer parámetros de la URL
    const urlParams = new URLSearchParams(window.location.search);
    document.getElementById('token').value = urlParams.get('token');
    document.getElementById('email').value = urlParams.get('email');

    document.getElementById('reset-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        const password = this.password.value;
        const passwordConfirm = this.passwordConfirm.value;

        // Validar que las dos contraseñas coincidan
        if (password !== passwordConfirm) {
            mostrarError('Les contrasenyes no coincideixen.');
            return;
        }

        // Validar reglas de contraseña
        const error = validarPassword(password);
        if (error) {
            mostrarError(error);
            return;
        }

        // Si pasa validaciones, enviar a la API
        const token = this.token.value;
        const email = this.email.value;

        const res = await fetch('/api/auth/post/restablimentPassword', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                password,
                token,
                email
            })
        });

        const data = await res.json();

        if (data.status === 'ok') {
            mostrarOk(data.message);

            setTimeout(() => {
                window.location.href = '/acces'; // redirige a la página de login o acceso
            }, 3000); // espera 3000 ms = 3 segundos

        } else {
            mostrarError(data.message);
        }
    });

    function mostrarOk(msg) {
        const divOk = document.getElementById('messageOk');
        const divErr = document.getElementById('messageErr');
        divErr.style.display = 'none';
        divOk.textContent = msg;
        divOk.style.display = 'block';
    }

    function mostrarError(msg) {
        const divErr = document.getElementById('messageErr');
        const divOk = document.getElementById('messageOk');
        divOk.style.display = 'none';
        divErr.textContent = msg;
        divErr.style.display = 'block';
    }
</script>