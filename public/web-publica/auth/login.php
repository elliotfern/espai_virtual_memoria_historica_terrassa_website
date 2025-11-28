<div class="container" style="margin-top:30px;margin-bottom:60px">
  <div class="card mx-auto" style="max-width: 400px;">
    <div class="card-body">
      <div class="container">
        <h3>Accés Intranet</h3>
        <div class="alert alert-success" id="loginMessageOk" style="display:none" role="alert">
        </div>

        <div class="alert alert-danger" id="loginMessageErr" style="display:none" role="alert">
        </div>

        <form action="" method="post" class="login" style="margin-bottom:20px;margin-top:25px">
          <div class="row g-3">

            <label for="username" class="negreta">Correu electrònic</label>
            <input type="text" name="username" id="username" class="form-control">
            <div class="invalid-feedback" id="usernameFeedback"></div>

            <label for="password" class="negreta">Contrasenya</label>
            <div class="input-group">
              <input type="password" name="password" id="password" class="form-control">
              <button class="btn btn-outline-secondary" type="button" id="togglePassword"
                aria-label="Mostrar o amagar la contrasenya">
                👁
              </button>
            </div>
            <div class="invalid-feedback" id="passwordFeedback"></div>


            <button name="login" id="btnLogin" class="btn btn-primary">Entra</button>
          </div>


        </form>
        <a href="<?php echo APP_WEB; ?>/recuperacio-contrasenya">No recordes la teva contrasenya? Clica aquí per iniciar el procés de recuperació.</a>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", () => {
    const passwordInput = document.getElementById("password");
    const toggleBtn = document.getElementById("togglePassword");

    let hideTimeout = null;

    if (toggleBtn && passwordInput) {
      toggleBtn.addEventListener("click", (e) => {
        e.preventDefault();

        // Si actualmente está oculta, la mostramos temporalmente
        if (passwordInput.type === "password") {
          passwordInput.type = "text";
          toggleBtn.textContent = "🙈"; // cambia el icono

          // Limpiar cualquier timeout anterior
          if (hideTimeout) {
            clearTimeout(hideTimeout);
          }

          // Volver a ocultar la contraseña después de 4 segundos
          hideTimeout = setTimeout(() => {
            passwordInput.type = "password";
            toggleBtn.textContent = "👁";
          }, 4000);
        } else {
          // Si ya está visible y vuelve a clicar, la ocultamos inmediatamente
          if (hideTimeout) {
            clearTimeout(hideTimeout);
          }
          passwordInput.type = "password";
          toggleBtn.textContent = "👁";
        }
      });
    }
  });
</script>