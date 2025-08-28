<?php

declare(strict_types=1);

use App\Config\DatabaseConnection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use function MT\Export\getScalar;
use function MT\Export\buildQuery;
use function MT\Export\translateRow;
use function MT\Export\initSession;

require_once __DIR__ . '/_export_common.php';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="memoriaterrassa_export_' . date('Ymd_His') . '.xlsx"');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
set_time_limit(0);

$pdo = DatabaseConnection::getConnection();
if (!$pdo) {
  http_response_code(500);
  echo "DB error";
  exit;
}
initSession($pdo);

$type = getScalar('type') ?: 'filtreGeneral';
$q    = getScalar('q');

[$headers, $sql, $params] = buildQuery($type, $q);

$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) $stmt->bindValue($k, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
$stmt->execute();

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// headers
for ($i = 0; $i < count($headers); $i++) {
  $col = Coordinate::stringFromColumnIndex($i + 1);
  $sheet->setCellValue($col . '1', $headers[$i]);
}

// rows
$r = 2;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  translateRow($row);
  for ($i = 0; $i < count($headers); $i++) {
    $h = $headers[$i];
    $col = Coordinate::stringFromColumnIndex($i + 1);
    $sheet->setCellValue($col . $r, (string)($row[$h] ?? ''));
  }
  $r++;
}

// autosize
for ($i = 1; $i <= count($headers); $i++) {
  $col = Coordinate::stringFromColumnIndex($i);
  $sheet->getColumnDimension($col)->setAutoSize(true);
}

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
