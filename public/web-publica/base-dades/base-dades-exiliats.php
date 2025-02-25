<!-- Sección con container-fluid -->
<div class="container-fluid background-image-cap">

    <div class="container px-4">
        <span class="negreta gran italic-text cap">Base de dades<br>exiliats i deportats</span>

    </div>
</div>


<div class="container d-flex flex-column card-body" style="padding-top: 50px;padding-bottom:10px;">
    <span class="titol italic-text gran lora">Explora les històries, documents i testimonis</span>

    <span class="text1 mitja raleway" style="margin-top:20px">
        El llistat de les persones exiliades i deportades està format per totes les persones terrassenques (residents o nascudes a Terrassa) que van exiliar-se arran de la guerra civil i els que van ser deportats als camps de concentració de l’Alemanya nazi. El llistat inclou els familiars dels exiliats, molts dels quals tornaren poc temps després a Catalunya i Espanya.</span>

    <span class="more-text d-none text1 mitja raleway">


    </span>


    <button class="btn-toggle btn btn-primary btn-custom-2 w-auto align-self-start" style="margin-top:25px">
        llegir més
    </button>
</div>

<div class="container mt-4 text-center">
    <span class="titol italic-text gran lora">Selecciona una categoria per començar la teva recerca:</span>
    <div class="row g-3 justify-content-center" style="padding-top: 50px;padding-bottom:10px;">
        <div class="col-12 col-md-6 d-flex justify-content-center">
            <a class="btn-div" href="<?php echo $langCode2 === 'ca' ? '/' : '/' . $langCode2 . '/'; ?>base-dades/general/#filtre">
                <span class="lora mitja">General</span>
            </a>
        </div>

        <div class="col-12 col-md-6 d-flex justify-content-center">
            <a class="btn-div" href="<?php echo $langCode2 === 'ca' ? '/' : '/' . $langCode2 . '/'; ?>base-dades/cost-huma/#filtre">
                <span class="lora mitja">Cost humà <br>de la Guerra Civil</span>
            </a>
        </div>

        <div class="col-12 col-md-6 d-flex justify-content-center">
            <a class="btn-div active" href="#filtre">
                <span class="lora mitja">Exiliats<br>i deportats</span>
            </a>
        </div>

        <div class="col-12 col-md-6 d-flex justify-content-center">
            <a class="btn-div" href="<?php echo $langCode2 === 'ca' ? '/' : '/' . $langCode2 . '/'; ?>base-dades/represaliats/#filtre">
                <span class="lora mitja">Represaliats <br>de la dictadura</span>
            </a>
        </div>
    </div>
</div>

<div class="container" id="filtre" style="margin-top: 50px;margin-bottom:50px;">

    <input type="text" id="searchInput" placeholder="Cercar...">

    <div class="table-responsive" style="margin-top:30px">
        <table class="table table-striped table-hover" id="represaliatsTable">
            <thead class="table-dark">
                <tr>
                    <th>Nom complet</th>
                    <th>Dades naixement</th>
                    <th>Dades defunció</th>
                    <th>Col·lectiu represaliat</th>
                </tr>
            </thead>
            <tbody id="represaliatsBody">
                <!-- Aquí se insertarán las filas de la tabla dinámicamente -->
            </tbody>
        </table>
        <div id="pagination" style="margin-bottom:50px">
            <button id="prevPage" disabled>Anterior</button>
            <span id="currentPage">1</span> de <span id="totalPages">1</span>
            <button id="nextPage">Següent</button>
        </div>
    </div>
</div>

<style>
    .active {
        background-color: #C2AF96 !important;
        color: #133B7C !important;
    }

    .btn-div {
        width: 70%;
        padding: 20px;
        background-color: #133B7C;
        color: #C2AF96;
        border-radius: 8px;
        text-align: center;
        cursor: pointer;
        transition: background 0.3s ease-in-out;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        text-decoration: none !important;
    }

    .btn-div:hover {
        background-color: #C2AF96;
        color: #133B7C;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".btn-toggle").forEach(button => {
            button.addEventListener("click", function() {
                const cardBody = this.closest(".card-body");
                const moreText = cardBody.querySelector(".more-text");

                if (moreText.classList.contains("d-none")) {
                    moreText.classList.remove("d-none");
                    this.textContent = "veure menys";
                } else {
                    moreText.classList.add("d-none");
                    this.textContent = "veure més";
                }
            });
        });
    });
</script>