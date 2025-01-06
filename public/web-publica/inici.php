<div class="container-fluid full-screen2 bg-image2">
    <div class="container my-5">
        <!-- Nuevo div con un grid 3x3 usando Bootstrap -->
        <div class="row g-4 justify-content-center" style="margin-bottom: 40px;">
            <div class="col-12 col-md-4 col-lg-3 square">
                <div class="card d-flex flex-row" style="padding:30px;background-color: #05050545!important">
                    <div class="card-body text-end">
                        <h5 class="card-title" style="color:white;font-size: 1.5rem;font-weight: bold;">
                            Víctimes de <br>la Repressió
                        </h5>
                    </div>
                    <!-- Imagen alineada a la derecha -->
                    <img src="../public/img/vector.png" width="20px" alt="..." class="ms-auto">
                </div>
            </div>

            <div class="col-12 col-md-4 col-lg-3 square">
                <div class="card d-flex flex-row" style="padding:30px;background-color: #05050545!important">
                    <div class="card-body text-end">
                        <h5 class="card-title" style="color:white;font-size: 1.5rem;font-weight: bold;">
                            <a href="<?php echo APP_WEB; ?>/base-dades-global">Base de <br>Dades Global</a>
                        </h5>
                    </div>
                    <!-- Imagen alineada a la derecha -->
                    <img src="../public/img/vector.png" width="20px" alt="..." class="ms-auto">
                </div>
            </div>

            <div class="col-12 col-md-4 col-lg-3 square">
                <div class="card d-flex flex-row" style="padding:30px;background-color: #05050545!important">
                    <div class="card-body text-end">
                        <h5 class="card-title" style="color:white;font-size: 1.5rem;font-weight: bold;">
                            Estudis
                        </h5>
                    </div>
                    <!-- Imagen alineada a la derecha -->
                    <img src="../public/img/vector.png" width="20px" alt="..." class="ms-auto">
                </div>
            </div>

        </div>
        <div class="row g-4 justify-content-center" style="margin-bottom: 40px;">
            <div class="col-12 col-md-4 col-lg-3 square">
                <div class="card d-flex flex-row" style="padding:30px;background-color: #05050545!important">
                    <div class="card-body text-end">
                        <h5 class="card-title" style="color:white;font-size: 1.5rem;font-weight: bold;">
                            Afussellaments i <br>
                            Deportacions
                        </h5>
                    </div>
                    <!-- Imagen alineada a la derecha -->
                    <img src="../public/img/vector.png" width="20px" alt="..." class="ms-auto">
                </div>
            </div>

            <div class="col-12 col-md-4 col-lg-3 square">
                <div class="card d-flex flex-row" style="padding:30px;background-color: #05050545!important">
                    <div class="card-body text-end">
                        <h5 class="card-title" style="color:white;font-size: 1.5rem;font-weight: bold;">
                            Exili
                        </h5>
                    </div>
                    <!-- Imagen alineada a la derecha -->
                    <img src="../public/img/vector.png" width="20px" alt="..." class="ms-auto">
                </div>
            </div>

            <div class="col-12 col-md-4 col-lg-3 square">
                <div class="card d-flex flex-row" style="padding:30px;background-color: #05050545!important">
                    <div class="card-body text-end">
                        <h5 class="card-title" style="color:white;font-size: 1.5rem;font-weight: bold;">
                            Cost humà<br>
                            de la Guerra
                        </h5>
                    </div>
                    <!-- Imagen alineada a la derecha -->
                    <img src="../public/img/vector.png" width="20px" alt="..." class="ms-auto">
                </div>
            </div>
        </div>
        <div class="row g-4 justify-content-center" style="margin-bottom: 40px;">
            <div class="col-12 col-md-4 col-lg-3 square">
                <div class="card d-flex flex-row" style="padding:30px;background-color: #05050545!important">
                    <div class="card-body text-end">
                        <h5 class="card-title" style="color:white;font-size: 1.5rem;font-weight: bold;">
                            Espai<br>Virtual
                        </h5>
                    </div>
                    <!-- Imagen alineada a la derecha -->
                    <img src="../public/img/vector.png" width="20px" alt="..." class="ms-auto">
                </div>
            </div>

            <div class="col-12 col-md-4 col-lg-3 square">
                <div class="card d-flex flex-row" style="padding:30px;background-color: #05050545!important">
                    <div class="card-body text-end">
                        <h5 class="card-title" style="color:white;font-size: 1.5rem;font-weight: bold;">
                            Fonts <br>Bibliografia
                        </h5>
                    </div>
                    <!-- Imagen alineada a la derecha -->
                    <img src="../public/img/vector.png" width="20px" alt="..." class="ms-auto">
                </div>
            </div>

            <div class="col-12 col-md-4 col-lg-3 square">
                <div class="card d-flex flex-row" style="padding:30px;background-color: #05050545!important">
                    <div class="card-body text-end">
                        <h5 class="card-title" style="color:white;font-size: 1.5rem;font-weight: bold;">
                            Cercador
                        </h5>
                    </div>
                    <!-- Imagen alineada a la derecha -->
                    <img src="../public/img/vector.png" width="20px" alt="..." class="ms-auto">
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .full-screen2 {
        min-height: 100vh;
        /* Ocupa al menos toda la altura de la pantalla */
        display: flex;
        /* Opcional, para centrar el contenido */
        align-items: center;
        /* Opcional, para centrar verticalmente */
        justify-content: center;
        /* Opcional, para centrar horizontalmente */
    }


    .bg-image2 {
        background-image: url('../public/img/santpere.jpg');
        /* Cambia 'tu-imagen.jpg' por la URL de tu imagen */
        background-size: cover;
        /* Imagen cubre todo el área */
        background-position: center;
        /* Imagen centrada */
        position: relative;
        /* Necesario para apilar correctamente los elementos */
        background-color: rgba(5, 6, 13, 0.79);
        /* Agrega el color azul con transparencia directamente */
        background-blend-mode: overlay;
        /* Combina la imagen de fondo con el color */
    }
</style>