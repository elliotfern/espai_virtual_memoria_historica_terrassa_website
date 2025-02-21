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
        <p>
            L’Espai Virtual de la Memòria Històrica de Terrassa (EVMHT) és un espai virtual històric, documental, visual, sonor, divulgatiu, educatiu i d’investigació sobre el cost humà de la lluita per la llibertat dels terrassencs i terrassenques durant el període 1936-1983.
        </p>
        <p>
            L’EVMHT recull el cost humà provocat pel <strong>cop d’estat franquista</strong> i la <strong>guerra civil</strong>; l’exili (<em>deportats</em>) i per la repressió franquista a Terrassa (<em>afusellats</em>), entre els anys, 1936 i el 1983. És a dir, recollirà els períodes històrics: <strong>República</strong>, <strong>Guerra Civil</strong>, <strong>Dictadura Franquista</strong> i <strong>Transició</strong>.
        </p>
        <p>
            Els continguts que trobareu a l’espai virtual, són el resultat d’anys d’investigació i recerca de diversos historiadors i historiadores locals i de la Catalunya i el món. Recerques que continuen encara avui i que s’incorporen de forma contínua i sistemàtica a l’EVMHT.
        </p>
        <p>
            El projecte de l’EVMHT és una iniciativa del <strong>Centre d’Estudis Històrics de Terrassa (CEHT)</strong>, associació cultural, amb 40 anys d’història, que reuneix tots els historiadors i historiadores de Terrassa i que col·labora amb les institucions públiques –i privades– de la ciutat i el país.
        </p>

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
        <span class="titol gran lora negreta">Antecedents</span>
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

                        <a href="#esde1" class="btn btn-primary btn-custom-2 w-auto align-self-start">
                            veure més
                        </a>
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

                        <a href="#esde2" class="btn btn-primary btn-custom-2 w-auto align-self-start">
                            veure més
                        </a>
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

                        <a href="#esde3" class="btn btn-primary btn-custom-2 w-auto align-self-start">
                            veure més
                        </a>
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

                        <a href="#esde4" class="btn btn-primary btn-custom-2 w-auto align-self-start">
                            veure més
                        </a>
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

                        <a href="#esde5" class="btn btn-primary btn-custom-2 w-auto align-self-start">
                            veure més
                        </a>
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



    <div class="container mt-4" style="padding-top: 60px;padding-bottom:60px;">
        <div class="row">
            <!-- Texto a la izquierda -->
            <div id="esde1" class="col-md-8 raleway d-flex flex-column d-grid gap-2">
                <span class="titol gran lora negreta">2003-2007</span>
                <span class="titol italic-text gran lora">Projecte per a la recuperació de la memòria històrica de Terrassa (1939-1979).</span>
                <p>
                    El primer treball del <strong>CEHT</strong>, en l’àmbit, del que anomenem <strong>Memòria Històrica</strong> va ser, el “<strong>Projecte per a la recuperació de la memòria històrica de Terrassa (1939-1979)</strong>”, coordinat pel Dr. Manuel Márquez Berrocal i recolzat pel <strong>Memorial Democràtic de la Generalitat de Catalunya</strong>, l’Ajuntament de Terrassa, el <strong>CEHT</strong> i la societat civil (2003-2007).
                </p>
                <p>
                    El resultat va ser una exposició, la gravació de quaranta biografies de lluitadors/es antifranquistes i l’elaboració d’un llibre, amb el pròleg de Josep Fontana i Lázaro.
                </p>
                <p>
                    <em>Combat per la llibertat. Memòria de la lluita antifranquista a Terrassa (1939-1979)</em>. LACUEVA MORENO, José Luis, MÁRQUEZ BERROCAL, Manuel i PLANS I CAMPDERRÓS, Lourdes. Terrassa: Fundació Torre del Palau, 2007.
                </p>
            </div>

            <!-- Imagen a la derecha -->
            <div class="col-md-4 text-center">
                <img src="<?php echo APP_WEB; ?>/public/img/llibre_combat_per_la_llibertat.jpg" class="img-fluid " alt="Elliot Fernandez">

            </div>
        </div>
    </div>

    <div class="container mt-4" style="padding-top: 60px;padding-bottom:60px;">
        <div class="row">

            <!-- Imagen a la derecha -->
            <div class="col-md-4 text-center">
                <img src="<?php echo APP_WEB; ?>/public/img/conferencies_valles_2016.jpg" class="img-fluid " alt="Elliot Fernandez">

            </div>
            <!-- Texto a la izquierda -->
            <div id="esde2" class="col-md-8 raleway d-flex flex-column d-grid gap-2">
                <span class="titol gran lora negreta">Octubre - novembre 2016</span>
                <span class="titol italic-text gran lora">Cicle de conferencies: El Vallès: Segona República, Guerra Civil i Postguerra (1931-1945)</span>
                <p>
                    Entre els mesos d’octubre i novembre de l’any 2016 es va realitzar el cicle de conferències: <strong>El Vallès: Segona República, Guerra Civil i Postguerra (1931-1945)</strong>, on vàrem presentar l’estudi:
                </p>
                <p>
                    <strong>MÁRQUEZ BERROCAL, Manuel. “La repressió franquista al Vallès: Terrassa venjança, mort i resistència, 1939-1945”</strong>. A, <em>El Vallès: Segona República, Guerra Civil i Postguerra (1931-1945)</em>. Sabadell: Fundació Bosch i Cardellach, 2017.
                </p>
            </div>
        </div>
    </div>

    <div class="container mt-4" style="padding-top: 60px;padding-bottom:60px;">
        <div class="row">
            <!-- Texto a la izquierda -->
            <div id="esde3" class="col-md-8 raleway d-flex flex-column d-grid gap-2">
                <span class="titol gran lora negreta">7 de Juliol 2017</span>
                <span class="titol italic-text gran lora">Constitució de l’Espai de la Memòria i dels Valors Democràtics de Terrassa</span>
                <p>
                    Més tard, es va constituir l’<strong>Espai de la Memòria i dels Valors Democràtics de Terrassa</strong> –7 de juny de 2017– a l’Arxiu Històric de Terrassa amb la presència de:
                </p>
                <ul>
                    <li><strong>Manuel Màrquez</strong>, president del Centre d’Estudis Històrics;</li>
                    <li><strong>Plàcid Garcia-Planas</strong>, Director del Memorial Democràtic de la Generalitat de Catalunya;</li>
                    <li><strong>Enric Cama</strong>, historiador;</li>
                    <li><strong>Joan Calvo</strong>, representant de la Junta de l’Amical de Mauthausen;</li>
                    <li>i la regidora de Ciutadania, Qualitat Democràtica i Drets Humans, <strong>Meritxell Lluís</strong>.</li>
                </ul>
            </div>

            <!-- Imagen a la derecha -->
            <div class="col-md-4 text-center">
                <img src="<?php echo APP_WEB; ?>/public/img/espai_memoria_democratica_2017.jpg" class="img-fluid " alt="Elliot Fernandez">

            </div>
        </div>
    </div>

    <div class="container mt-4" style="padding-top: 60px;padding-bottom:60px;">
        <div class="row">
            <!-- Texto a la izquierda -->
            <div id="esde4" class="col-md-12 raleway d-flex flex-column d-grid gap-2">
                <span class="titol gran lora negreta">2019-2020</span>
                <span class="titol italic-text gran lora">Recuperació de la Memòria Històrica de les persones afusellades per la dictadura franquista a Terrassa (1939-1975)</span>

                L’any 2019 i 2020 es va elaborar el projecte d’investigació: <strong>Recuperació de la Memòria Històrica de les persones afusellades per la dictadura franquista a Terrassa (1939-1975)</strong>, que tenia com a principal objectiu recuperar la dignitat i el prestigi social de les persones afusellades per la dictadura franquista i alhora reconèixer la seva lluita per la república democràtica i per un món millor.
                </p>
                <p>
                    El projecte es desenvoluparà en col·laboració amb <strong>Terrassa Memòria - Espai de la Memòria i dels Valors Democràtics</strong> (formada per l’ajuntament de Terrassa, entitats cíviques i el <strong>CEHT</strong>) consistirà en l’elaboració d’un conjunt de biografies dels afusellats terrassencs a càrrec del <strong>Dr. Just Casas Soriano</strong> i <strong>Dr. Manuel Márquez Berrocal</strong>.
                </p>

            </div>

        </div>
    </div>

    <div class="container mt-4" style="padding-top: 60px;padding-bottom:60px;">
        <div class="row">

            <!-- Imagen a la derecha -->
            <div class="col-md-4 text-center">
                <img src="<?php echo APP_WEB; ?>/public/img/llibre_terrassa_exiliada.jpg" class="img-fluid " alt="Elliot Fernandez">

            </div>
            <!-- Texto a la izquierda -->
            <div id="esde5" class="col-md-8 raleway d-flex flex-column d-grid gap-2">
                <span class="titol gran lora negreta">2021</span>
                <span class="titol italic-text gran lora">Treball d’investigació sobre l’exili terrassenc</span>
                <p>
                    L’any <strong>2021</strong> es va realitzar un treball d’investigació sobre l’exili terrassenc, a càrrec de l’historiador <strong>Juan Antonio Olivares</strong>, projecte que encara continua actualitzant-se i que va donar com a resultat un primer treball:
                </p>
                <p>
                    <strong>OLIVARES ABAD, Juan Antonio, <em>Terrassa exiliada</em></strong>. Granada, Taller del Sur, <strong>2021</strong>.
                </p>
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