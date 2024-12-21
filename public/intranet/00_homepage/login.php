<div class="container" style="margin-top:50px">
  <div class="card mx-auto" style="max-width: 400px;">
    <div class="card-body">
      <div class="container">
        <h3>Espai Virtual de la Memòria Històrica de Terrassa - EVMHT</h3>
        <?php
        echo '<div class="alert alert-success" id="loginMessageOk" style="display:none" role="alert">
                  <h4 class="alert-heading"><strong>Dades correctes!</strong></h4>
                  <h6>Les dades introduïdes són correcte, en uns segons et redirigim a la pàgina.</h6>
                  </div>';

        echo '<div class="alert alert-danger" id="loginMessageErr" style="display:none" role="alert">
                  <h4 class="alert-heading"><strong>Error!</strong></h4>
                  <h6>Nom d\'usuari o contrasenya incorrectes</h6>
                  </div>';
        ?>

        <form action="" method="post" class="login">
          <label for="username">Correu electrònic</label>
          <input type="text" name="username" id="username" class="form-control">
          <br>

          <label for="password">Contrasenya</label>
          <input type="password" name="password" id="password" class="form-control">
          <br>
          <button name="login" id="btnLogin" class="btn btn-primary">Entra</button>

        </form>
      </div>
    </div>
  </div>
</div>