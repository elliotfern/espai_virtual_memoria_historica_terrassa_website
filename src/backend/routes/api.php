<?php

// Configuración por defecto para rutas que requieren sesión, sin header_footer, con header_menu_footer
$defaultApiConfig = [
    'needs_session' => false,
    'header_footer' => false,
    'header_menu_footer' => false,
    'apiSenseHTML' => true
];

// Rutas principales sin idioma explícito (solo para el idioma por defecto)
$routes = [

    // 01. Auth
    APP_API . $url['auth'] . '/get/{slug}' => array_merge($defaultApiConfig, [
        'view' => BACKEND_API . $url['auth'] . '/get-auth.php',
    ]),

    APP_API . $url['auth'] . '/post/{slug}' => array_merge($defaultApiConfig, [
        'view' => BACKEND_API . $url['auth'] . '/post-auth.php',
    ]),

    APP_API . $url['auth'] . '/put/{slug}' => array_merge($defaultApiConfig, [
        'view' => BACKEND_API . $url['auth'] . '/put-auth.php',
    ]),

    // 02. Taules auxiliars
    APP_API . $url['auxiliars'] . '/get/{slug}' => array_merge($defaultApiConfig, [
        'view' => BACKEND_API . $url['auxiliars'] . '/get-auxiliars.php',
    ]),

    APP_API . $url['auxiliars'] . '/put/{slug}' => array_merge($defaultApiConfig, [
        'view' => BACKEND_API . $url['auxiliars'] . '/put-auxiliars.php',
    ]),

    APP_API . $url['auxiliars'] . '/post/{slug}' => array_merge($defaultApiConfig, [
        'view' => BACKEND_API . $url['auxiliars'] . '/post-auxiliars.php',
    ]),

    APP_API . $url['auxiliars'] . '/delete/{slug}/{id}' => array_merge($defaultApiConfig, [
        'view' => BACKEND_API . $url['auxiliars'] . '/delete-auxiliars.php',
    ]),

    // API Taules resum represaliats
    APP_API . $urlApi['represaliats'] . '/get/{slug}' => array_merge($defaultApiConfig, [
        'view' => BACKEND_API . $urlApi['represaliats'] . '/get-represaliats.php',
    ]),

    // API db_cost_huma_morts_front
    '/api/cost_huma_front/get/{slug}' => ['view' => 'src/backend/api/db_cost_huma_front/get-cost-huma-front.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/cost_huma_front/put' => ['view' => 'src/backend/api/db_cost_huma_front/put-cost-huma-front.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/cost_huma_front/post' => ['view' => 'src/backend/api/db_cost_huma_front/post-cost-huma-front.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API db_cost_huma_morts_civils
    '/api/cost_huma_civils/get/{slug}' => ['view' => 'src/backend/api/db_cost_huma_civils/get-cost-huma-civils.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/cost_huma_civils/put' => ['view' => 'src/backend/api/db_cost_huma_civils/put-cost-huma-civils.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/cost_huma_civils/post' => ['view' => 'src/backend/api/db_cost_huma_civils/post-cost-huma-civils.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API db_dades_personals
    '/api/dades_personals/get' => ['view' => 'src/backend/api/db_dades_personals/get-dades-personals.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/dades_personals/get/{slug}' => ['view' => 'src/backend/api/db_dades_personals/get-dades-personals.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/dades_personals/put' => ['view' => 'src/backend/api/db_dades_personals/put-dades-personals.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/dades_personals/post' => ['view' => 'src/backend/api/db_dades_personals/post-dades-personals.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/dades_personals/delete/{slug}' => ['view' => 'src/backend/api/db_dades_personals/delete-dades-personals.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],


    '/api/dades_personals/geo/get' => ['view' => 'src/backend/api/db_dades_personals/geo.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/dades_personals/geo/put' => ['view' => 'src/backend/api/db_dades_personals/put-geo.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/dades_personals/geo/tots' => ['view' => 'src/backend/api/db_dades_personals/tots-geo.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/dades_personals/adreces' => ['view' => 'src/backend/api/db_dades_personals/adreces.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],


    // API db_exiliats
    '/api/exiliats/get/{slug}' => ['view' => 'src/backend/api/db_exiliats/get-exiliats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/exiliats/put' => ['view' => 'src/backend/api/db_exiliats/put-exiliats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/exiliats/post' => ['view' => 'src/backend/api/db_exiliats/post-exiliats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API db_deportats
    '/api/deportats/get/{slug}' => ['view' => 'src/backend/api/db_deportats/get-deportats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/deportats/put' => ['view' => 'src/backend/api/db_deportats/put-deportats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/deportats/post' => ['view' => 'src/backend/api/db_deportats/post-deportats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API db_familiars
    '/api/familiars/get/{slug}' => ['view' => 'src/backend/api/db_familiars/get-familiars.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/familiars/put' => ['view' => 'src/backend/api/db_familiars/put-familiars.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/familiars/post' => ['view' => 'src/backend/api/db_familiars/post-familiars.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/familiars/delete/{id}' => ['view' => 'src/backend/api/db_familiars/delete-familiars.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API db_afusellats
    '/api/afusellats/get/{slug}' => ['view' => 'src/backend/api/db_afusellats/get-afusellats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/afusellats/put' => ['view' => 'src/backend/api/db_afusellats/put-afusellats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/afusellats/post' => ['view' => 'src/backend/api/db_afusellats/post-afusellats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API db_processats
    '/api/processats/get/{slug}' => ['view' => 'src/backend/api/db_processats/get-processats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/processats/put' => ['view' => 'src/backend/api/db_processats/put-processats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/processats/post' => ['view' => 'src/backend/api/db_processats/post-processats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API db_depurats
    '/api/depurats/get' => ['view' => 'src/backend/api/db_depurats/get-depurats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/depurats/put' => ['view' => 'src/backend/api/db_depurats/put-depurats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/depurats/post' => ['view' => 'src/backend/api/db_depurats/post-depurats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API db_detinguts_model
    '/api/preso_model/get/{slug}' => ['view' => 'src/backend/api/db_detinguts_model/get-model.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/preso_model/put' => ['view' => 'src/backend/api/db_detinguts_model/put-model.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/preso_model/post' => ['view' => 'src/backend/api/db_detinguts_model/post-model.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API db_depurats
    '/api/depurats/get/{slug}' => ['view' => 'src/backend/api/db_depurats/get-depurats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/depurats/put' => ['view' => 'src/backend/api/db_depurats/put-depurats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/depurats/post' => ['view' => 'src/backend/api/db_depurats/post-depurats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API db detinguts guardia urbana
    '/api/detinguts_guardia_urbana/get/{slug}' => ['view' => 'src/backend/api/db_detinguts_guardia_urbana/get-guardia-urbana.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/detinguts_guardia_urbana/put' => ['view' => 'src/backend/api/db_detinguts_guardia_urbana/put-guardia-urbana.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/detinguts_guardia_urbana/post' => ['view' => 'src/backend/api/db_detinguts_guardia_urbana/post-guardia-urbana.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API db Afectats Llei responsabilitats politiques
    '/api/responsabilitats_politiques/get/{slug}' => ['view' => 'src/backend/api/db_responsabilitats_politiques/get-responsabilitats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/responsabilitats_politiques/put' => ['view' => 'src/backend/api/db_responsabilitats_politiques/put-responsabilitats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/responsabilitats_politiques/post' => ['view' => 'src/backend/api/db_responsabilitats_politiques/post-responsabilitats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API db Tribunal orden publico
    '/api/top/get/{slug}' => ['view' => 'src/backend/api/db_tribunal_orden_publico/get-top.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/top/put' => ['view' => 'src/backend/api/db_tribunal_orden_publico/put-top.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/top/post' => ['view' => 'src/backend/api/db_tribunal_orden_publico/post-top.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API db comitè solidaritat
    '/api/comite_solidaritat/get/{slug}' => ['view' => 'src/backend/api/db_comite_solidaritat/get-comite-solidaritat.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/comite_solidaritat/put' => ['view' => 'src/backend/api/db_comite_solidaritat/put-comite-solidaritat.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/comite_solidaritat/post' => ['view' => 'src/backend/api/db_comite_solidaritat/post-comite-solidaritat.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],


    // API db comitè relacions solidaritat
    '/api/comite_relacions_solidaritat/get/{slug}' => ['view' => 'src/backend/api/db_comite_relacions_solidaritat/get-comite-relacions-solidaritat.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/comite_relacions_solidaritat/put' => ['view' => 'src/backend/api/db_comite_relacions_solidaritat/put-comite-relacions-solidaritat.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/comite_relacions_solidaritat/post' => ['view' => 'src/backend/api/db_comite_relacions_solidaritat/post-comite-relacions-solidaritat.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API fonts documentals
    APP_API . $urlApi['fonts'] . '/get/{slug}' => array_merge($defaultApiConfig, [
        'view' => BACKEND_API . '/db_fonts_documentals/get-fonts-bibliografia.php',
    ]),

    APP_API . $urlApi['fonts'] . '/post/{slug}' => array_merge($defaultApiConfig, [
        'view' => BACKEND_API . '/db_fonts_documentals/post-fonts-documentals.php',
    ]),

    APP_API . $urlApi['fonts'] . '/put/{slug}' => array_merge($defaultApiConfig, [
        'view' => BACKEND_API . '/db_fonts_documentals/put-fonts-documentals.php',
    ]),

    APP_API . '/fonts/put/{slug}' => array_merge($defaultApiConfig, [
        'view' => BACKEND_API . '/db_fonts_documentals/put-fonts-documentals.php',
    ]),

    APP_API . '/fonts/post/{slug}' => array_merge($defaultApiConfig, [
        'view' => BACKEND_API . '/db_fonts_documentals/post-fonts-documentals.php',
    ]),

    APP_API . '/fonts/get/{slug}' => array_merge($defaultApiConfig, [
        'view' => BACKEND_API . '/db_fonts_documentals/get-fonts-bibliografia.php',
    ]),

    APP_API . $urlApi['fonts'] . '/delete/{slug}/{id}' => array_merge($defaultApiConfig, [
        'view' => BACKEND_API . '/db_fonts_documentals/delete-fonts-documentals.php',
    ]),


    // API db_cronologia
    '/api/cronologia/post' => ['view' => 'src/backend/api/cronologia/post-cronologia.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/cronologia/put' => ['view' => 'src/backend/api/cronologia/put-cronologia.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/cronologia/get' => ['view' => 'src/backend/api/cronologia/get-cronologia.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/cronologia/get/esdeveniment' => ['view' => 'src/backend/api/cronologia/get-cronologia-esd.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API Biografies
    APP_API . $urlApi['biografies'] . '/get/{slug}' => array_merge($defaultApiConfig, [
        'view' => BACKEND_API . '/db_biografies/get-biografia.php',
    ]),

    '/api/biografies/post' => ['view' => 'src/backend/api/db_biografies/post-biografia.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/biografies/put' => ['view' => 'src/backend/api/db_biografies/put-biografia.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // DB Formulari de contacte
    '/api/form_contacte/get' => ['view' => 'src/backend/api/db_form_contacte/get-form-contacte.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/form_contacte/post' => ['view' => 'src/backend/api/db_form_contacte/post-form-contacte.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // AUX imatges
    '/api/aux_imatges/upload' => ['view' => 'src/backend/api/aux_imatges/post-imatges.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/aux_imatges/clear-image' => ['view' => 'src/backend/api/aux_imatges/delete-imatge-id.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // API EXPORT
    '/api/export/persones_csv/{lang}' => ['view' => 'src/backend/api/export/persones_csv.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/export/persones_xlsx/{lang}' => ['view' => 'src/backend/api/export/persones_xlsx.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/export/persones_pdf/{lang}' => ['view' => 'src/backend/api/export/persones_pdf.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // SITEMAP
    '/api/sitemaps/ca' => ['view' => 'src/backend/api/sitemaps/sitemap-ca.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    // SCRIPT PHP
    '/api/script-processats' => ['view' => 'src/backend/api/script/import-processats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/script-model' => ['view' => 'src/backend/api/script/import-model.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/script/guardia-urbana' => ['view' => 'src/backend/api/script/import-guardia-urbana.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/script/responsabilitats' => ['view' => 'src/backend/api/script/import-responsabilitats.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],

    '/api/script/test' => ['view' => 'src/backend/api/script/test.php', 'needs_session' => false, 'header_footer' => false, 'header_menu_footer' => false,  'apiSenseHTML' => true],
];

return $routes;
