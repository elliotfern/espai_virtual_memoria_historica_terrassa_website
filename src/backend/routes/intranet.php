<?php

// Configuración por defecto para rutas que requieren sesión, sin header_footer, con header_menu_footer
$defaultProtectedConfig = [
    'needs_session' => true,
    'header_footer' => false,
    'header_menu_footer' => true,
    'apiSenseHTML' => false
];

// Rutas principales sin idioma explícito (solo para el idioma por defecto)
$routes = [

    // 00. Intranet Homepage
    APP_INTRANET => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['homepage'] . '/admin.php'
    ]),

    APP_INTRANET . '/admin' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['homepage'] . '/admin.php'
    ]),

    // 01. Pàgines base de dades
    APP_INTRANET . '/base-dades/general' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['base_dades'] . '/llistat_general.php'
    ]),

    APP_INTRANET . '/base-dades/general/llistat-duplicats' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['base_dades'] . '/llistat-duplicats.php'
    ]),

    APP_INTRANET . '/base-dades/general/quadre-general' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['base_dades'] . '/llistat-quadre-general.php'
    ]),

    // Grup 2: Exiliats
    APP_INTRANET . '/base-dades/exiliats-deportats' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['base_dades'] . '/llistat_exiliats.php'
    ]),

    APP_INTRANET . '/base-dades/exiliats-deportats/llistat-exiliats' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['base_dades'] . '/2_llistat-exiliats.php'
    ]),

    APP_INTRANET . '/base-dades/exiliats-deportats/llistat-deportats' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['base_dades'] . '/2_llistat-deportats.php'
    ]),

    // Grup 1: Cost humà
    APP_INTRANET . '/base-dades/cost-huma' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['base_dades'] . '/llistat_cost_huma.php'
    ]),

    APP_INTRANET . '/base-dades/cost-huma/llistat-morts-al-front' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['base_dades'] . '/1_llistat-morts_al_front.php'
    ]),

    APP_INTRANET . '/base-dades/cost-huma/llistat-morts-civils' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['base_dades'] . '/1_llistat-morts_civils.php'
    ]),

    APP_INTRANET . '/base-dades/cost-huma/llistat-represalia-republicana' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['base_dades'] . '/1_llistat-represalia_republicana.php'
    ]),

    // Grup 3: Represaliats 1939-79
    APP_INTRANET . '/base-dades/represaliats' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['base_dades'] . '/llistat_represaliats.php'
    ]),

    APP_INTRANET . '/base-dades/represaliats/llistat-processats' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['base_dades'] . '/3_llistat-processats.php'
    ]),

    APP_INTRANET . '/base-dades/represaliats/llistat-afusellats' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['base_dades'] . '/3_llistat-afusellats.php'
    ]),

    APP_INTRANET . '/base-dades/represaliats/llistat-preso-model' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['base_dades'] . '/3_llistat-preso-model.php'
    ]),

    // 01. Modificació dades de les fitxes
    APP_INTRANET . '/base-dades/modifica-fitxa/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['base_dades'] . '/modifica_fitxa_persona.php'
    ]),

    APP_INTRANET . '/base-dades/nova-fitxa' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['base_dades'] . '/modifica_fitxa_persona.php'
    ]),

    APP_INTRANET . '/base-dades/modifica-repressio/{categoriaId}/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['base_dades'] . '/modifica_fitxa_tipus_repressio.php'
    ]),

    // empresonaments
    APP_INTRANET . '/base-dades/empresonaments/modifica-empresonament/{idPersona}/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['base_dades'] . '/tipus_repressio/1_represaliats/detinguts_guardia_urbana.php'
    ]),

    APP_INTRANET . '/base-dades/empresonaments/nou-empresonament/{idPersona}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['base_dades'] . '/tipus_repressio/1_represaliats/detinguts_guardia_urbana.php'
    ]),

    // 2. Pàgines taules auxiliars
    APP_INTRANET . $urlIntranet['auxiliars'] => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/index.php'
    ]),

    // 2.1 Municipis
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-municipis' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-municipis.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nou-municipi' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-municipi.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-municipi/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-municipi.php'
    ]),

    // 2.2 Partits politics
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-partits-politics' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-partits-politics.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nou-partit-politic' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-partit-politic.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-partit-politic/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-partit-politic.php'
    ]),

    // 2.3 Sindicats
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-sindicats' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-sindicats.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nou-sindicat' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-sindicat.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-sindicat/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-sindicat.php'
    ]),


    // 2.4 Llistat taula usuaris i avatars
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-usuaris' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-usuaris.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-usuari/{slug}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-usuari.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nou-usuari' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-usuari.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-avatar-usuari/{slug}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-usuari-avatar.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nou-avatar-usuari' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-usuari-avatar.php'
    ]),

    // 2.5 Llistat comarques
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-comarques' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-comarques.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nova-comarca' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-comarca.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-comarca/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-comarca.php'
    ]),

    // 2.6 Llistat províncies
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-provincies' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-provincies.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nova-provincia' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-provincia.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-provincia/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-provincia.php'
    ]),

    // 2.7 Llistat comunitats autònomes
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-comunitats' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-comunitats.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nova-comunitat' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-comunitat.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-comunitat/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-comunitat.php'
    ]),

    // 2.8 Llistat països - estats
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-estats' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-estats.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nou-estat' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-estat.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-estat/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-estat.php'
    ]),

    // 2.9 Llistat oficis
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-oficis' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-oficis.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nou-ofici' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-ofici.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-ofici/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-ofici.php'
    ]),

    // tipologia d'espai
    APP_INTRANET . $urlIntranet['auxiliars'] . '/nova-tipologia-espai' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-tipologia-espai.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-tipologia-espai/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-tipologia-espai.php'
    ]),

    // 2.10 Llistat empreses
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-empreses' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-empreses.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nova-empresa' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-empresa.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-empresa/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-empresa.php'
    ]),


    // Causa de mort
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-causa-mort' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-causa-mort.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nova-causa-mort' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-causa-mort.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-causa-mort/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-causa-mort.php'
    ]),

    // sector econòmic
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-sectors-economics' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-sectors-economics.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nou-sector-economic' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-sector-economic.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-sector-economic/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-sector-economic.php'
    ]),

    // Sub-sector econòmic
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-subsectors-economics' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-subsectors-economics.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nou-sub-sector-economic' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-sub-sector-economic.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-sub-sector-economic/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-sub-sector-economic.php'
    ]),

    // Categories de repressió
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-categories-repressio' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-categories-repressio.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-categoria-repressio/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-categoria-repressio.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nova-categoria-repressio' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-categoria-repressio.php'
    ]),

    // carrecs empresa
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-carrecs-empresa' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-carrecs-empresa.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nou-carrec-empresa' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-carrec-empresa.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-carrec-empresa/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-carrec-empresa.php'
    ]),

    // acusacions judicials
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-acusacions-judicials' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-acusacions.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nova-acusacio' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-acusacions.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-acusacio/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-acusacions.php'
    ]),

    // bàndols guerra
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-bandols-guerra' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-bandols.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nou-bandol' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-bandol.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-bandol/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-bandol.php'
    ]),

    // condicions militars guerra
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-condicions-militars' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-condicions-militars.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nova-condicio-militar' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-condicio-militar.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-condicio-militar/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-condicio-militar.php'
    ]),

    // condicions cossos guerra
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-cossos-militars' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-cossos-militars.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nou-cos-militar' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-cos-militar.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-cos-militar/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-cos-militar.php'
    ]),

    // espais (d'execució, enterrament, afusellament...)
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-espais' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-espais.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nou-espai' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-espai.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-espai/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-espai.php'
    ]),

    // estat civil
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-estats-civils' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-estats-civils.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nou-estat-civil' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-estat-civil.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-estat-civil/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-estat-civil.php'
    ]),

    // nivell estudis
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-nivells-estudis' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-nivells-estudis.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nou-nivell-estudis' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-nivell-estudis.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-nivell-estudis/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-nivell-estudis.php'
    ]),

    // tipus procediments judicials
    APP_INTRANET . $urlIntranet['auxiliars'] . '/llistat-tipus-procediments-judicials' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/llistat-procediments-judicials.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/nou-tipus-procediment-judicial' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-tipus-procediment-judicial.php'
    ]),

    APP_INTRANET . $urlIntranet['auxiliars'] . '/modifica-tipus-procediment-judicial/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['auxiliars'] . '/form-tipus-procediment-judicial.php'
    ]),



    // Gestió familiars
    APP_INTRANET . $urlIntranet['familiars'] . '/modifica-familiar/{id}/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['familiars'] . '/form-familiar.php'
    ]),

    APP_INTRANET . $urlIntranet['familiars'] . '/nou-familiar/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['familiars'] . '/form-familiar.php'
    ]),

    // GESTIÓ FONTS DOCUMENTALS (bibliografia i arxius)
    // Bibliografia / llibres > fitxa represaliat (llistat de tots els fonts vinculats a una fitxa)
    APP_INTRANET . $urlIntranet['fonts'] . '/fitxa/afegir-bibliografia/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['fonts'] . '/form-fitxa-llibre.php'
    ]),

    APP_INTRANET . $urlIntranet['fonts'] . '/fitxa/modifica-bibliografia/{id}/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['fonts'] . '/form-fitxa-llibre.php'
    ]),

    APP_INTRANET . $urlIntranet['fonts'] . '/fitxa/afegir-arxiu/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['fonts'] . '/form-fitxa-arxiu.php'
    ]),

    APP_INTRANET . $urlIntranet['fonts'] . '/fitxa/modifica-arxiu/{id}/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['fonts'] . '/form-fitxa-arxiu.php'
    ]),

    // Bibliografia
    APP_INTRANET . $urlIntranet['fonts'] . '/llistat-llibres' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['fonts'] . '/llistat-llibres.php'
    ]),

    APP_INTRANET . $urlIntranet['fonts'] . '/nou-llibre' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['fonts'] . '/form-llibre.php'
    ]),

    APP_INTRANET . $urlIntranet['fonts'] . '/modifica-llibre/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['fonts'] . '/form-llibre.php'
    ]),

    // Arxius
    APP_INTRANET . $urlIntranet['fonts'] . '/llistat-arxius' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['fonts'] . '/llistat-arxius.php'
    ]),

    APP_INTRANET . $urlIntranet['fonts'] . '/nou-arxiu' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['fonts'] . '/form-arxiu.php'
    ]),

    APP_INTRANET . $urlIntranet['fonts'] . '/modifica-arxiu/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['fonts'] . '/form-arxiu.php'
    ]),


    // REGISTRE CANVIS
    '/gestio/registre-canvis' => ['view' => 'public/intranet/control_registre_canvis/index.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/control-acces' => ['view' => 'public/intranet/control_registre_canvis/control-acces.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    // CRONOLOGIA
    '/gestio/cronologia' => ['view' => 'public/intranet/cronologia/index.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/cronologia/afegir-esdeveniment' => ['view' => 'public/intranet/cronologia/afegir-esdeveniment.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    '/gestio/cronologia/modifica-esdeveniment/{id}' => ['view' => 'public/intranet/cronologia/afegir-esdeveniment.php', 'needs_session' => true, 'header_footer' => false, 'header_menu_footer' => true, 'apiSenseHTML' => false],

    // Biografia
    APP_INTRANET . $urlIntranet['biografies'] . '/modifica-biografia/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['biografies'] . '/form-biografia.php'
    ]),

    APP_INTRANET . $urlIntranet['biografies'] . '/nova-biografia/{id}' => array_merge($defaultProtectedConfig, [
        'view' => BACKEND_URL . $urlIntranetDir['biografies'] . '/form-biografia.php'
    ]),

];

return $routes;
