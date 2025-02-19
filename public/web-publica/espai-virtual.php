<?php
// Obtener traducciones generales
$translate = $translations['general'] ?? [];
$translate2 = $translations['cerca-avan'] ?? [];
?>

<!-- Sección con container-fluid -->
<div class="container-fluid background-image-cap">

    <div class="container px-4">
        <span class="negreta gran italic-text cap">Què és<br> l'Espai Virtual de la Memòria Històrica de Terrassa?</span>

    </div>
</div>


<div class="container d-flex flex-column" style="padding-top: 50px;padding-bottom:50px;">
    <span class="titol gran lora negreta">Espai virtual</span>
    <span class="titol italic-text gran lora">Preservant la memòria,<br> construint el futur.</span>
    <span class="text1 mitja raleway" style="margin-top:20px">
        <p>La pàgina web de l'Espai Virtual de la Memòria Històrica de Terrassa és el lloc on trobareu un arxiu virtual històric, visual, sonor, documental, divulgatiu i educatiu sobre el cost humà de la lluita per la llibertat dels terrassencs i terrassenques durant el període la guerra civil i la dictadura franquista (1936-1983).</p>

        <p>Els continguts que mostrem en aquest espai virtual, són el resultat d'anys d'investigació i recerca de diversos historiadors i historiadores locals i de la resta del món.</p>

        <p>Les dades es mostrem de forma ordenada i utilitzable pels investigadors/es, mitjançant bases de dades relacionals i interactives, permeten fer recerques combinades i generar arxius en diferents formats. Això permet a les futures investigacions estudiar les víctimes de la repressió segons el gènere, la procedència, la militància, l'edat, l'ofici, etc.</p>

        <p>La informació individual es presenta en forma de fitxa, que recull els documents, fotografies, gravacions sonores i audiovisuals de cada persona estudiada; però el que és més important, cada fitxa recull una petita biografia de la persona i de la seva família, així com els de documents històrics relacionats amb cadascuna d'elles, siguin del passat o del present (declaració de familiars). Considerem fonamental les aportacions de les famílies o dels arxius privats. Amb tota aquesta informació, es pretén facilitat l'anàlisi col·lectiva de les dades, la reconstrucció de les experiències vitals dels terrassencs i terrassenques i les seves famílies i construir una veritable memòria col·lectiva democràtica i respectuosa amb els drets humans.</p>

        <p>La combinació de la base de dades conformada a partir d'anys de recerca amb una eina digital que permet la seva difusió i estudi és una aposta innovadora, amb molts pocs casos semblants a tot l'Estat.</p>

    </span>

    <div class="container py-5">
        <div class="row row-cols-4 g-4 text-center">
            <div class="col">
                <div class=" p-4">
                    <img src="<?php echo APP_WEB; ?>/public/img/icon1.png" class="mb-3 w-6" alt="Imagen 1">
                    <h5 class="lora blau2">Històries personals de les represaliades <br>i represaliats</h5>
                </div>
            </div>
            <div class="col">
                <div class="p-4">
                    <img src="<?php echo APP_WEB; ?>/public/img/icon2.png" class="mb-3 w-6" alt="Imagen 2">
                    <h5 class="lora blau2">Eines de cerca avançada<br> per explorar arxius</h5>
                </div>
            </div>
            <div class="col">
                <div class="p-4">
                    <img src="<?php echo APP_WEB; ?>/public/img/icon3.png" class="mb-3 w-6" alt="Imagen 3">
                    <h5 class="lora blau2">Bases de dades detallades<br> segons el tipus de repressió</h5>
                </div>
            </div>
            <div class="col">
                <div class="p-4">
                    <img src="<?php echo APP_WEB; ?>/public/img/icon4.png" class="mb-3 w-6" alt="Imagen 4">
                    <h5 class="lora blau2">Fonts documentals<br> verificades i accessibles</h5>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="container-fluid" style="padding-top:60px;padding-bottom:60px;background-color:#EEEAD9">

    <div class="container py-5 d-flex flex-column">
        <span class="titol gran lora negreta">Espai virtual</span>
        <span class="titol italic-text gran lora">Explora els moments clau en la creació i evolució d'aquest<br> espai dedicat a la memòria històrica de Terrassa.</span>
    </div>

    <div class="container py-5">
        <div class="timeline">

            <!-- Evento 1 -->
            <div class="timeline-item">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title lora marro2">2003-2007</h5>
                        <p class="card-text blanc raleway negreta">
                            Projecte per a la recuperació de la memòria històrica de Terrassa (1939-1979).</p>
                        <span class="more-text d-none blanc raleway">Coordinat pel Dr. Manuel Márquez Berrocal, amb el suport del Memorial Democràtic de la Generalitat de Catalunya i l'Ajuntament de Terrassa.</p>
                            <p>Resultats:</p>
                            <ul>
                                <li>Exposició.</li>
                                <li>Gravació de 40 biografies de lluitadors/es antifranquistes.</li>
                                <li>Publicació del llibre Combat per la llibertat. Memòria de la lluita antifranquista a Terrassa (1939-1979).</li>
                            </ul>
                        </span>

                        <button class="btn btn-primary btn-custom-2 w-auto align-self-start btn-toggle">veure més</button>
                    </div>
                </div>
            </div>

            <!-- Evento 2 -->
            <div class="timeline-item">
                <div class="card text-start">
                    <div class="card-body">
                        <h5 class="card-title lora marro2">2016</h5>
                        <p class="card-text blanc raleway negreta">
                            Cicle de conferències: El Vallès: Segona República, Guerra Civil i Postguerra (1931-1945).</p>
                        <span class="more-text d-none blanc raleway">
                            <p>Text.</p>
                        </span>

                        <button class="btn btn-primary btn-custom-2 w-auto align-self-start btn-toggle">veure més</button>
                    </div>
                </div>
            </div>

            <!-- Evento 3 -->
            <div class="timeline-item">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title lora marro2">2017</h5>
                        <p class="card-text blanc raleway negreta">
                            Constitució de l'Espai de la Memòria i dels Valors Democràtics de Terrassa.</p>
                        <span class="more-text d-none blanc raleway">
                            <p>Text.</p>
                        </span>

                        <button class="btn btn-primary btn-custom-2 w-auto align-self-start btn-toggle">veure més</button>
                    </div>
                </div>
            </div>

            <!-- Evento 4 -->
            <div class="timeline-item">
                <div class="card text-start">
                    <div class="card-body">
                        <h5 class="card-title lora marro2">2019-2020</h5>
                        <p class="card-text blanc raleway negreta">
                            Recuperació de la Memòria Històrica de les persones afusellades per la dictadura franquista a Terrassa (1939-1975).</p>
                        <span class="more-text d-none blanc raleway">
                            <p>Text.</p>
                        </span>

                        <button class="btn btn-primary btn-custom-2 w-auto align-self-start btn-toggle">veure més</button>
                    </div>
                </div>
            </div>

            <!-- Evento 5 -->
            <div class="timeline-item">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title lora marro2">2021</h5>
                        <p class="card-text blanc raleway negreta">
                            Investigació sobre l'exili terrassenc</p>
                        <span class="more-text d-none blanc raleway">
                            <p>Text.</p>
                        </span>

                        <button class="btn btn-primary btn-custom-2 w-auto align-self-start btn-toggle">veure més</button>
                    </div>
                </div>
            </div>

            <!-- Evento 6 -->
            <div class="timeline-item">
                <div class="card text-start">
                    <div class="card-body">
                        <h5 class="card-title lora marro2">2021</h5>
                        <p class="card-text blanc raleway negreta">
                            Estudi sobre les fosses comunes de la Guerra Civil.</p>
                        <span class="more-text d-none blanc raleway">
                            <p>Text.</p>
                        </span>

                        <button class="btn btn-primary btn-custom-2 w-auto align-self-start btn-toggle">veure més</button>
                    </div>
                </div>
            </div>

            <!-- Evento 7 -->
            <div class="timeline-item">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title lora marro2">2021</h5>
                        <p class="card-text blanc raleway negreta">
                            Publicació de llistats de represaliats i víctimes del franquisme.</p>
                        <span class="more-text d-none blanc raleway">
                            <p>Text.</p>
                        </span>

                        <button class="btn btn-primary btn-custom-2 w-auto align-self-start btn-toggle">veure més</button>
                    </div>
                </div>
            </div>

            <!-- Evento 8 -->
            <div class="timeline-item">
                <div class="card text-start">
                    <div class="card-body">
                        <h5 class="card-title lora marro2">2025</h5>
                        <p class="card-text blanc raleway negreta">
                            Estrena del web de l'Espai Virtual de la Memòria Històrica de Terrassa</p>
                        <span class="more-text d-none blanc raleway">
                            <p>Text.</p>
                        </span>

                        <button class="btn btn-primary btn-custom-2 w-auto align-self-start btn-toggle">veure més</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


<div class="container d-flex flex-column" style="padding-top: 50px;padding-bottom:50px;">
    <span class="titol gran lora negreta">Què trobareu a l'Espai Virtual?</span>
    <span class="titol italic-text gran lora">Les dades es presenten en forma de taules interactives i <br>fitxes individuals que inclouen biografies, documents i <br>testimonis audiovisuals</span>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12 col-md-3">
                <div class="btn-div d-flex flex-column">
                    <img class="img-m" src="<?php echo APP_WEB; ?>/public/img/icon5.png" alt="Icono 1">
                    <span class="lora mitja">Fitxes <br>detallades</span>
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="btn-div d-flex flex-column">
                    <img class="img-m" src="<?php echo APP_WEB; ?>/public/img/icon6.png" alt="Icono 1">
                    <span class="lora mitja">Base de dades <br>relacionals</span>
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="btn-div d-flex flex-column">
                    <img class="img-m" src="<?php echo APP_WEB; ?>/public/img/icon7.png" alt="Icono 1">
                    <span class="lora mitja">Documentació <br>històrica</span>
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="btn-div d-flex flex-column">
                    <img class="img-m" src="<?php echo APP_WEB; ?>/public/img/icon8.png" alt="Icono 1">
                    <span class="lora mitja">Testimonis <br>audiovisuals</span>
                </div>
            </div>
        </div>
    </div>

    <style>
        .btn-div {
            height: 200px;
            background-color: #EEEAD9;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-weight: bold;
            cursor: pointer;
            border-radius: 5px;
            transition: background 0.3s;
            color: #426296 !important;
        }

        .btn-div:hover {
            background-color: #B39B7C;
            color: white !important;
        }
    </style>

</div>

<style>
    /* Contenedor de la línea de tiempo */
    .timeline {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
    }

    /* Línea central */
    .timeline::before {
        content: "";
        position: absolute;
        top: 0;
        bottom: 0;
        left: 50%;
        width: 3px;
        background-color: #B39B7C;
        transform: translateX(-50%);
    }

    /* Estilo de los eventos */
    .timeline-item {
        position: relative;
        width: 50%;
        padding: 20px;
        display: flex;
        justify-content: center;
    }

    /* Alternar los eventos a la izquierda y derecha */
    .timeline-item:nth-child(odd) {
        align-self: flex-start;
        text-align: left;
    }

    .timeline-item:nth-child(even) {
        align-self: flex-end;
        text-align: right;
    }

    /* Puntos en la línea */
    .timeline-item::before {
        content: "";
        position: absolute;
        top: 30%;
        width: 20px;
        height: 20px;
        background-color: #fff;
        border: 3px solid #B39B7C;
        border-radius: 50%;
        left: 100%;
        transform: translateX(-50%);
    }

    .timeline-item:nth-child(even)::before {
        left: auto;
        right: 100%;
        transform: translateX(50%);
    }

    /* Tarjetas: tamaño normal */
    .card {
        width: 100%;
        max-width: 300px;
        background-color: #133B7C;
        border-radius: 25px;
    }

    .blanc {
        color: white;
    }

    /* En pantallas grandes, hacer las tarjetas más anchas */
    @media (min-width: 992px) {
        .card {
            max-width: 100%
        }
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