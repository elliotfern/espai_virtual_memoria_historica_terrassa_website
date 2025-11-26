<?php

// Obtener la ruta de la URL (sin el dominio)
$requestUri = parse_url($requestUri, PHP_URL_PATH);

// Normalizar la ruta eliminando las barras finales
$requestUri = rtrim($requestUri, '/');
// Detectar el idioma desde la URL (primer segmento después del dominio)
preg_match('#^/(fr|en|es|pt|it)#', $requestUri, $matches);
$language = $matches[1] ?? '';

// Obtener traducciones generales
$translate = $translations['benvinguda'] ?? [];
?>

<!-- IN MEMORIAN JUAN ANTONIO OLIVARES -->
<div class="container d-flex flex-column justify-content-center align-items-center text-center" style="padding-top:10px;padding-bottom:20px;">
    <span class="titol gran lora negreta mb-3">
        Juan Antonio Olivares Abad in memoriam
    </span>

    <span class="text1 mitja raleway mb-3">
        (25 de novembre de 2025)
    </span>

    <img src="https://media.memoriaterrassa.cat/assets_usuaris/juan_antonio_olivares.jpg"
        alt="Foto"
        class="img-fluid"
        loading="lazy"
        decoding="async">
</div>

<div class="container text1 mitja raleway mb-3" style="padding-top:10px;padding-bottom:60px;">
    <p>
        La Història de Terrassa s'ha construït gràcies a la tasca de magnífics historiadors i historiadores, uns dedicats a aquest ofici durant tota la seva vida professional i d'altres després d'una llarga vida professional i de compromís polític, sindical, social i cultural al servei de les persones, aquest és el cas de l'amic i historiador Juan Antonio Olivares Abad, nascut a Terrassa l’any 1951, del barri de Sant Pere de Terrassa i veí de Viladecavalls.
    </p>

    <p>
        Juan Antonio Olivares ha dedicat milers d'hores als altres, a la col·lectivitat humana, de la qual formava part i ho ha fet per voluntat de servei a la majoria de les persones els treballadors/es. Persones de carn i os, que fa avui vuitanta anys s'han jugat la seva vida en defensa de la democràcia republicana i de la justícia social i que van perdre patrimoni, llibertat i vida. Persones que no vàrem dubtar a continuar el combat contra la dictadura franquista, com havien fet els seus, en defensa de la democràcia i els dels drets dels treballadors des de les files de les esquerres compromeses.
    </p>

    <p>
        Juan Antonio va ser una persona compromesa va ser treballador bancari, delegat sindical de CCOO (1983-2007), membre de la Comissió de Control Caixa Terrassa (2000-2006) i regidor de l'Ajuntament de Viladecavalls (1999-2007) per diferents organitzacions i coalicions d’esquerres. Un home al servei dels altres.
    </p>

    <p>
        A Juan Antonio el movia un impuls ètic i uns valors profundament democràtics i civilitzadors, per això es va dedicar a la recuperació de la memòria històrica. La seva història personal i familiar el va impulsar a recuperar la dignitat dels derrotats i derrotades, ja que aquests són els nostres i són la majoria social: els i les treballadores. Ho va fer al servei de tots i totes en multitud de treballs molts dels quals han estat base sòlida per a poder crear la web de l'Espai Virtual de la Memòria Històrica de Terrassa, en el que tant d’esforç va posar.
    </p>

    <p>
        Però mai va oblidar la terra d’origen de la família i va escriure el llibre: "Abans. Durant. Després. Repressió Franquista a Alhama de Granada" (2016) una obra magna, d'un historiador rigorós i compromès amb els valors democràtics i de defensa dels drets humans. Un treball veraç, justament per aquests valors que professem, i realitzat a base de treball voluntari, cosa que no demostra que fos de l'acadèmia i sense ajuda institucional –o molt limitada– el treball de qualitat dels historiadors locals és possible.
    </p>

    <p>
        Juan tampoc s’oblidà dels deportats o exiliats, un bon exemple és el llibre "Terrassa exiliada" (2021) una extensa aproximació a l'exili terrassenc. Gràcies a aquest treball han sortit a la llum moltes persones anònimes cosa que ens permetran avançar en el coneixement de la repressió franquista i nazi-feixista que van patir els exiliats de la vella Ègara.
    </p>

    <p>
        Avui, l'amic i historiador terrassenc, Juan Antonio Olivares Abad ens ha deixat, però amb la seva vida d'home, historiador i lluitador antifranquista exemplar, ens ha recordat amb força i dignitat que els noms dels homes i dones comunes, el que tots i totes som, no es podran esborrar de la història.
    </p>

    <p>
        <strong>Espai Virtual de la Memòria Històrica de Terrassa.</strong><br>
        <strong>Junta del Centre d'Estudis Històrics de Terrassa.</strong>
    </p>

    <p>
        <em>Terrassa, 25 de novembre de 2025.</em>
    </p>
</div>

<!-- Secció amb container-fluid -->
<div class="container-fluid bg-image2">
    <div class="container px-4">
        <div class="row gx-5 gy-3">
            <div class="col">
                <div class="p-4 border bloc1">
                    <span class="titol" data-i18n="basicSearch_titleA">Cerca bàsica de</span>
                    <br />
                    <span class="titol italic-text" data-i18n="basicSearch_titleB">represaliats i represaliades</span>

                    <div class="d-flex flex-column d-grid gap-3" style="margin-top:20px;margin-bottom:20px">
                        <div class="container mt-4">
                            <div class="position-relative">
                                <input
                                    id="searchInput"
                                    type="text"
                                    class="form-control mb-2"
                                    data-i18n-ph="basicSearch_placeholder"
                                    placeholder="Nom i cognoms..." />
                                <div id="results" class="search-results"></div>
                            </div>
                        </div>

                        <span class="text2" style="margin-top:30px;" data-i18n="basicSearch_help">
                            Si vols fer una cerca avançada, aplicant filtres per lloc de naixement, sexe, afiliació sindical i/o
                            política, entre d'altres, clica al següent botó.
                        </span>

                        <a
                            href="../<?php echo empty($language) ? '' : $language . '/'; ?>cerca-represaliat"
                            class="btn btn-primary btn-custom-2 w-auto align-self-start"
                            data-i18n="basicSearch_cta">Cerca avançada</a>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="p-4 border bloc2">
                    <span class="titol" data-i18n="explore_titleA">Explora les nostres</span>
                    <br />
                    <span class="titol italic-text" data-i18n="explore_titleB">bases de dades</span>

                    <div class="d-flex flex-column d-grid gap-3" style="margin-top:40px;margin-bottom:20px">
                        <span class="text3" data-i18n="explore_text">
                            Consulta les bases de dades de l'Espai Virtual de la Memòria Històrica de Terrassa, on podràs trobar el
                            llistat complet de represaliats i fer cerques avançades.
                        </span>

                        <a
                            href="../<?php echo empty($language) ? '' : $language . '/'; ?>base-dades/general"
                            class="btn btn-primary btn-custom-1"
                            data-i18n="db_btn_general">General</a>
                        <a
                            href="../<?php echo empty($language) ? '' : $language . '/'; ?>base-dades/cost-huma"
                            class="btn btn-primary btn-custom-1"
                            data-i18n="db_btn_cost">Cost Humà de la Guerra civil</a>
                        <a
                            href="../<?php echo empty($language) ? '' : $language . '/'; ?>base-dades/exiliats-deportats"
                            class="btn btn-primary btn-custom-1"
                            data-i18n="db_btn_exili">Exiliats i deportats</a>
                        <a
                            href="../<?php echo empty($language) ? '' : $language . '/'; ?>base-dades/represaliats"
                            class="btn btn-primary btn-custom-1"
                            data-i18n="db_btn_repres">Represaliats de la dictadura</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container px-4 d-flex flex-column d-grid gap-2" style="margin-top:60px;margin-bottom:60px">
    <span class="titol gran lora negreta" data-i18n="hero_title">Espai virtual de la Memòria Històrica de Terrassa</span>
    <span class="titol italic-text gran lora" data-i18n-html="hero_subtitle">Descobreix les històries, dades<br /> i testimonis que conformen<br /> la memòria històrica.</span>
    <span class="text1 mitja raleway" data-i18n="hero_text">L'Espai Virtual de la Memòria Històrica de Terrassa (EVMHT) és un espai històric, documental, educatiu i
        d'investigació que recull el cost humà de la lluita per la llibertat dels terrassencs i terrassenques entre 1936 i
        1983.</span>
    <a
        href="../<?php echo empty($language) ? '' : $language . '/'; ?>que-es-espai-virtual"
        class="btn btn-primary btn-custom-2 w-auto align-self-start"
        data-i18n="hero_cta">Coneix més sobre l'Espai Virtual</a>
</div>

<div class="container text-center my-3">
    <div class="row mx-auto my-auto justify-content-cen.carousel-control-next-icon">
        <div id="recipeCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner" role="listbox">
                <div class="carousel-item active gap">
                    <div class="col-md-4">
                        <img src="<?php echo IMG_DOMAIN; ?>/assets_web/foto_memoria1.jpg" class="img-fluid" />
                    </div>
                </div>
                <div class="carousel-item gap">
                    <div class="col-md-4">
                        <img src="<?php echo IMG_DOMAIN; ?>/assets_web/foto_memoria2.jpg" class="img-fluid" />
                    </div>
                </div>
                <div class="carousel-item gap">
                    <div class="col-md-4">
                        <img src="<?php echo IMG_DOMAIN; ?>/assets_web/foto_memoria3.jpg" class="img-fluid" />
                    </div>
                </div>
                <div class="carousel-item gap">
                    <div class="col-md-4">
                        <img src="<?php echo IMG_DOMAIN; ?>/assets_web/foto_memoria4.jpg" class=" img-fluid" />
                    </div>
                </div>
                <div class="carousel-item gap">
                    <div class="col-md-4">
                        <img src="<?php echo IMG_DOMAIN; ?>/assets_web/foto_memoria5.jpg" class="img-fluid" />
                    </div>
                </div>
                <div class="carousel-item gap">
                    <div class="col-md-4">
                        <img src="<?php echo IMG_DOMAIN; ?>/assets_web/foto_memoria1.jpg" class="img-fluid" />
                    </div>
                </div>
            </div>
            <a class="carousel-control-prev bg-transparent w-aut" href="#recipeCarousel" role="button" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </a>
            <a class="carousel-control-next bg-transparent w-aut" href="#recipeCarousel" role="button" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </a>
        </div>
    </div>
</div>

<hr />

<div class="container px-4 d-flex flex-column d-grid gap-2" style="margin-top:60px;margin-bottom:60px">
    <span class="titol gran lora negreta" data-i18n="resources_title">Explora els nostres recursos</span>
    <span class="titol italic-text gran lora" data-i18n-html="resources_subtitle">Consulta i interactua amb bases de dades que inclouen fitxes<br /> individuals, documents, i estudis històrics</span>
</div>

<div class="container-fluid" style="margin-bottom:60px">
    <div class="row">
        <div class="col-md-3 column-hover">
            <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>base-dades/general" class="full-link">
                <h2 class="column-title lora" data-i18n-html="grid_title_general">Base de dades<br />General</h2>
                <div
                    class="hover-bg"
                    style="background-image: url('https://media.memoriaterrassa.cat/assets_web/monument_caidos.jpg');"></div>
            </a>
        </div>
        <div class="col-md-3 column-hover">
            <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>base-dades/cost-huma" class="full-link">
                <h2 class="column-title lora" data-i18n-html="grid_title_cost">Cost humà <br />de la Guerra civil</h2>
                <div
                    class="hover-bg"
                    style="background-image: url('https://media.memoriaterrassa.cat/assets_web/monument_caidos.jpg');"></div>
            </a>
        </div>
        <div class="col-md-3 column-hover">
            <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>base-dades/exiliats-deportats" class="full-link">
                <h2 class="column-title lora" data-i18n-html="grid_title_exili">Exiliats i<br /> deportats</h2>
                <div
                    class="hover-bg"
                    style="background-image: url('https://media.memoriaterrassa.cat/assets_web/monument_caidos.jpg');"></div>
            </a>
        </div>
        <div class="col-md-3 column-hover">
            <a href="../<?php echo empty($language) ? '' : $language . '/'; ?>base-dades/represaliats" class="full-link">
                <h2 class="column-title lora" data-i18n-html="grid_title_repres">Represaliats <br />de la dictadura</h2>
                <div
                    class="hover-bg"
                    style="background-image: url('https://media.memoriaterrassa.cat/assets_web/monument_caidos.jpg');"></div>
            </a>
        </div>
    </div>
</div>

<hr />

<div class="container px-4 d-flex flex-column d-grid gap-2" style="margin-top:0px;margin-bottom:60px">
    <span class="titol gran lora negreta" data-i18n="interactive_title">Recursos interactius</span>

    <div class="container py-5">
        <div class="row row-cols-2 g-4 text-center">
            <div class="col">
                <div class="p-4">
                    <img src="<?php echo IMG_DOMAIN; ?>/assets_web/icon1.png" class="mb-3 w-6" alt="Imagen 1" />
                    <h5 class="lora blau2" data-i18n-html="interactive_item1">Històries personals de les represaliades <br />i represaliats</h5>
                </div>
            </div>
            <div class="col">
                <div class="p-4">
                    <img src="<?php echo IMG_DOMAIN; ?>/assets_web/icon2.png" class="mb-3 w-6" alt="Imagen 2" />
                    <h5 class="lora blau2" data-i18n-html="interactive_item2">Eines de cerca avançada<br /> per explorar arxius</h5>
                </div>
            </div>
            <div class="col">
                <div class="p-4">
                    <img src="<?php echo IMG_DOMAIN; ?>/assets_web/icon3.png" class="mb-3 w-6" alt="Imagen 3" />
                    <h5 class="lora blau2" data-i18n-html="interactive_item3">Bases de dades detallades<br /> segons el tipus de repressió</h5>
                </div>
            </div>
            <div class="col">
                <div class="p-4">
                    <img src="<?php echo IMG_DOMAIN; ?>/assets_web/icon4.png" class="mb-3 w-6" alt="Imagen 4" />
                    <h5 class="lora blau2" data-i18n-html="interactive_item4">Fonts documentals<br /> verificades i accessibles</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid" style="margin-top:60px;background-color:#EEEAD9">
    <div class="container px-4 d-flex flex-column d-grid gap-2" style="padding-top:60px;padding-bottom:60px">
        <span class="titol gran lora negreta" data-i18n="team_title">Equip d'investigadors i tècnics del web</span>
        <span class="titol italic-text gran lora" data-i18n="team_subtitle">Espai Virtual de la Memòria Històrica de Terrassa</span>
        <span class="text1 mitja raleway" data-i18n-html="team_text"><span class="negreta">Qui hi ha darrere del projecte?</span> Aquest projecte és possible gràcies a la
            col·laboració d'entitats i professionals dedicats a la recuperació de la memòria històrica</span>

        <div class="container my-5" id="equipLlistaRoot">
        </div>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        let items = document.querySelectorAll(".carousel .carousel-item");

        items.forEach((el) => {
            const minPerSlide = 3; // Número de elementos por slide
            let next = el.nextElementSibling;

            for (let i = 1; i < minPerSlide; i++) {
                if (!next) {
                    next = items[0]; // Si llega al final, vuelve al inicio
                }

                let cloneChild = next.firstElementChild.cloneNode(true);
                el.appendChild(cloneChild); // Agregar el contenido al slide actual
                next = next.nextElementSibling;
            }
        });
    });
</script>

<style>
    #results {
        font-family: Lora;
        font-size: 15px;
    }

    #results a:link,
    #results a:visited,
    #results a:hover {
        text-decoration: underline !important;
    }

    #results .avis {
        color: #d9cdbd !important;
        font-family: Lora;
    }

    .search-results {
        position: absolute;
        top: 100%;
        /* justo debajo del input */
        left: 0;
        width: 100%;
        max-height: 300px;
        overflow-y: auto;
        background: #fff;
        border: 1px solid #ccc;
        z-index: 1050;
        /* Asegúrate de que esté por encima del resto del contenido */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 4px;
    }
</style>