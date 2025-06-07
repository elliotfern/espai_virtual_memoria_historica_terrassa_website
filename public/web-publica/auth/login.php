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

            <label for="email" class="negreta">Correu electrònic</label>
            <input type="text" name="email" id="email" class="form-control">
            <div class="invalid-feedback" id="usernameFeedback"></div>

            <label for="password" class="negreta">Contrasenya</label>
            <input type="password" name="password" id="password" class="form-control">
            <div class="invalid-feedback" id="passwordFeedback"></div>

            <button name="login" id="btnLogin" class="btn btn-primary">Entra</button>
          </div>


        </form>
        <a href="<?php echo APP_WEB; ?>/recuperacio-contrasenya">No recordes la teva contrasenya? Clica aquí per iniciar el procés de recuperació.</a>
      </div>
    </div>
  </div>
</div>