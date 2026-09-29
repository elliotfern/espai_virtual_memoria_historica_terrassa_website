<?php

use App\Config\Database;
use App\Utils\Response;
use App\Utils\MissatgesAPI;

use App\Config\DatabaseConnection;

$conn = DatabaseConnection::getConnection();

if (!$conn) {
    die("No se pudo establecer conexión a la base de datos.");
}


// Configuración de cabeceras para aceptar JSON y responder JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");

$slug = $routeParams[0];

// GET : Moviments comptes
// URL: /api/transparencia/get/movimentsComptes
if ($slug === 'movimentsComptes') {

    $db = new Database();

    $query = "SELECT
                id,
                exercici,
                tipus,
                concepte,
                import,
                data,
                ordre
              FROM db_moviments_comptes
              ORDER BY exercici ASC, tipus ASC, ordre ASC, id ASC";

    try {
        $result = $db->getData($query);

        if (empty($result)) {
            Response::error(
                MissatgesAPI::error('not_found'),
                [],
                404
            );
            return;
        }

        $exercicis = [];
        $totalIngressosGlobal = 0.0;
        $totalDespesesGlobal = 0.0;

        foreach ($result as $moviment) {

            $exercici = (int) $moviment['exercici'];
            $tipus = $moviment['tipus'];
            $import = (float) $moviment['import'];

            if (!isset($exercicis[$exercici])) {
                $exercicis[$exercici] = [
                    'exercici' => $exercici,
                    'ingressos' => [
                        'moviments' => [],
                        'total' => 0.0
                    ],
                    'despeses' => [
                        'moviments' => [],
                        'total' => 0.0
                    ],
                    'resultat' => 0.0
                ];
            }

            $movimentData = [
                'id' => (int) $moviment['id'],
                'concepte' => $moviment['concepte'],
                'import' => $import,
                'data' => $moviment['data'],
                'ordre' => (int) $moviment['ordre']
            ];

            if ($tipus === 'ingres') {

                $exercicis[$exercici]['ingressos']['moviments'][] = $movimentData;
                $exercicis[$exercici]['ingressos']['total'] += $import;

                $totalIngressosGlobal += $import;
            } elseif ($tipus === 'despesa') {

                $exercicis[$exercici]['despeses']['moviments'][] = $movimentData;
                $exercicis[$exercici]['despeses']['total'] += $import;

                $totalDespesesGlobal += $import;
            }
        }

        foreach ($exercicis as &$exercici) {
            $exercici['resultat'] =
                $exercici['ingressos']['total']
                - $exercici['despeses']['total'];
        }

        unset($exercici);

        $data = [
            'exercicis' => array_values($exercicis),
            'global' => [
                'ingressos' => $totalIngressosGlobal,
                'despeses' => $totalDespesesGlobal,
                'resultat' => $totalIngressosGlobal - $totalDespesesGlobal
            ]
        ];


        Response::success(
            MissatgesAPI::success('get'),
            $data,
            200
        );
    } catch (PDOException $e) {
        Response::error(
            MissatgesAPI::error('errorBD'),
            [$e->getMessage()],
            500
        );
    }
}
