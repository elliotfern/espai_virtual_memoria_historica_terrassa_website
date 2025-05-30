<div class="container" style="margin-top:30px;margin-bottom:60px">
    <div class="card mx-auto" style="max-width: 400px;">
        <div class="card-body">
            <div class="container">

                <h3>Recuperació contrasenya</h3>
                <?php
                echo '<div class="alert alert-success" id="messageOk" style="display:none" role="alert">
                  </div>';

                echo '<div class="alert alert-danger" id="messageErr" style="display:none" role="alert">
                  </div>';
                ?>

                <form id="forgot-form" action="" class="login">
                    <label for="email">Correu electrònic</label>
                    <input type="email" name="email" id="email" class="form-control">
                    <br>

                    <button name="login" class="btn btn-primary" type="submit">Recuperar contrasenya</button>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('forgot-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const email = this.email.value;

        // Oculta mensajes anteriores
        const okBox = document.getElementById('messageOk');
        const errBox = document.getElementById('messageErr');
        okBox.style.display = 'none';
        errBox.style.display = 'none';
        okBox.innerText = '';
        errBox.innerText = '';

        try {
            const res = await fetch('/api/auth/post/recuperacioPassword', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    email
                })
            });

            const data = await res.json();

            if (data.status === 'ok') {
                okBox.innerText = data.message;
                okBox.style.display = 'block';
            } else {
                errBox.innerText = data.message || 'Error desconegut';
                errBox.style.display = 'block';
            }

        } catch (error) {
            errBox.innerText = 'Error de connexió amb el servidor.';
            errBox.style.display = 'block';
        }
    });
</script>