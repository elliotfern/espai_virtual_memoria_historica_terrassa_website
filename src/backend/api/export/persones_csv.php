<?php

declare(strict_types=1);

use App\Config\DatabaseConnection;
use function MT\Export\getScalar;
use function MT\Export\buildQuery;
use function MT\Export\translateRow;
use function MT\Export\initSession;

require_once __DIR__ . '/_export_common.php';

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="memoriaterrassa_export_' . date('Ymd_His') . '.csv"');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
set_time_limit(0);

$pdo = DatabaseConnection::getConnection();
if (!$pdo) {
    http_response_code(500);
    exit('DB error');
}
initSession($pdo);

$type = getScalar('type') ?: 'filtreGeneral';
$q    = getScalar('q');

[$headers, $sql, $params] = buildQuery($type, $q);

$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) $stmt->bindValue($k, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
$stmt->execute();

// BOM + hint separador
echo "\xEF\xBB\xBF";
echo "sep=,\n";

$out = fopen('php://output', 'w');
fputcsv($out, $headers);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    translateRow($row);
    $line = [];
    foreach ($headers as $h) $line[] = (string)($row[$h] ?? '');
    fputcsv($out, $line);
}
fclose($out);
exit;
