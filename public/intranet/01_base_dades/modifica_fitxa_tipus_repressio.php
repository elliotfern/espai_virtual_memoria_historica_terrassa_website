<?php
$categoriaId = $routeParams[0]; // Primero, por ejemplo, 1
$idPersona = $routeParams[1]; // Segundo, por ejemplo, 2
$categoriaId = (int) $categoriaId;

// Verificar si es un número entero válido
if (!is_int($categoriaId) || $categoriaId >= 23) {
    // Si no es un número entero o es menor o igual a cero, detener la ejecución
    header("Location: /404");
    exit();
}

require_once APP_ROOT . '/public/intranet/includes/header.php';

switch ($categoriaId) {
    case 1:
        require_once APP_ROOT . '/public/intranet/01_base_dades/tipus_repressio/1_represaliats/afusellats.php';
        break;
    case 2:
        require_once APP_ROOT . '/public/intranet/01_base_dades/tipus_repressio/2_exiliats/deportats.php';
        break;
    case 3:
        require_once APP_ROOT . '/public/intranet/01_base_dades/tipus_repressio/3_cost_huma/morts_combat.php';
        break;
    case 4:
        require_once APP_ROOT . '/public/intranet/01_base_dades/tipus_repressio/3_cost_huma/morts_civils.php';
        break;
    case 5:
        break;
    case 6:
        require_once APP_ROOT . '/public/intranet/01_base_dades/tipus_repressio/1_represaliats/processats.php';
        break;
    case 7:
        require_once APP_ROOT . '/public/intranet/01_base_dades/tipus_repressio/1_represaliats/depurats.php';
        break;
    case 8:
        break;
    case 9:
        // dona
        break;
    case 10:
        require_once APP_ROOT . '/public/intranet/01_base_dades/tipus_repressio/2_exiliats/exiliats.php';
        break;
    case 11:
        require_once APP_ROOT . '/public/intranet/01_base_dades/tipus_repressio/1_represaliats/pendents.php';
        break;
    case 12:
        require_once APP_ROOT . '/public/intranet/01_base_dades/tipus_repressio/1_represaliats/llistat-detinguts_preso_model.php';
        break;
    case 13:
        require_once APP_ROOT . '/public/intranet/01_base_dades/tipus_repressio/1_represaliats/llistat-detinguts_guardia_urbana.php';
        break;
    case 14:
        require_once APP_ROOT . '/public/intranet/01_base_dades/tipus_repressio/1_represaliats/llistat-detinguts_comite_solidaritat.php';
        break;
    case 15:
        require_once APP_ROOT . '/public/intranet/01_base_dades/tipus_repressio/1_represaliats/responsabilitats_politiques.php';
        break;
    case 16:
        require_once APP_ROOT . '/public/intranet/01_base_dades/tipus_repressio/1_represaliats/llistat-detinguts_guardia_urbana.php';
        break;
    case 17:
        require_once APP_ROOT . '/public/intranet/01_base_dades/tipus_repressio/1_represaliats/tribunal_orden_publico.php';
        break;
    case 18:
        require_once APP_ROOT . '/public/intranet/01_base_dades/tipus_repressio/1_represaliats/llistat-detinguts_comite_relacions_solidaritat.php';
        break;
    case 22:
        require_once APP_ROOT . '/public/intranet/01_base_dades/tipus_repressio/3_cost_huma/morts_combat.php';
        break;
}
