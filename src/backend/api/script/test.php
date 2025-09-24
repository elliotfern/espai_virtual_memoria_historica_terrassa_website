<?php

declare(strict_types=1);

// test_read_ods.php
error_reporting(E_ALL);
ini_set('display_errors', '1');

use PhpOffice\PhpSpreadsheet\IOFactory;

// --- Comprobaciones de entorno útiles ---
$checks = [
    'ext-zip' => extension_loaded('zip'),
    'ext-xml' => extension_loaded('xml'),
];
foreach ($checks as $ext => $ok) {
    echo ($ok ? "✔️" : "❌") . " $ext " . ($ok ? "cargada" : "NO cargada") . PHP_EOL;
}

// --- Comprobar que la clase existe (composer/autoload) ---
if (!class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
    exit("❌ PhpSpreadsheet no disponible. ¿Ejecutaste `composer require phpoffice/phpspreadsheet`?\n");
}

$defaultPath = '/home/epgylzqu/memoriaterrassa.cat/datos.ods';
$path = $argv[1] ?? $defaultPath;

echo "→ Intentando abrir: $path\n";

if (!is_file($path)) {
    exit("❌ No encuentro el fichero: $path\nColoca 'datos.ods' junto a este script.\n");
}

try {
    // Carga el ODS
    $spreadsheet = IOFactory::load($path);
    $sheet = $spreadsheet->getActiveSheet();

    $highestRow = $sheet->getHighestRow();       // p.ej. 250
    $highestCol = $sheet->getHighestColumn();    // p.ej. N
    echo "✔️ Fichero abierto: datos.ods\n";
    echo "   Filas detectadas: $highestRow, Última columna: $highestCol\n";

    // Lee cabeceras (fila 1)
    $headers = $sheet->rangeToArray("A1:$highestCol" . "1", null, true, true, true)[1] ?? [];
    echo "Cabeceras:\n";
    foreach ($headers as $col => $val) {
        echo "  $col: " . (is_scalar($val) ? $val : json_encode($val)) . PHP_EOL;
    }

    // Muestra 2 primeras filas de datos (si existen)
    for ($r = 2; $r <= min($highestRow, 3); $r++) {
        $rowArr = $sheet->rangeToArray("A$r:$highestCol$r", null, true, true, true)[$r] ?? [];
        echo "Fila $r:\n";
        foreach ($rowArr as $col => $val) {
            echo "  $col: " . (is_scalar($val) ? trim((string)$val) : json_encode($val)) . PHP_EOL;
        }
    }

    echo "✅ Lectura de prueba OK.\n";
} catch (Throwable $e) {
    echo "❌ Error leyendo ODS: " . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
    exit(1);
}
