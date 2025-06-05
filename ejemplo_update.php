try {
$db = new Database();

$dataToUpdate = [
'ciutat' => $data['ciutat'],
'ciutat_ca' => $data['ciutat_ca'] ?? null,
'comarca' => $data['comarca'] ?? null,
'provincia' => $data['provincia'] ?? null,
'comunitat' => $data['comunitat'] ?? null,
'estat' => $data['estat'] ?? null,
];

$where = "id = :id";
$whereParams = ['id' => $data['id']];

$updated = $db->updateData('aux_dades_municipis', $dataToUpdate, $where, $whereParams);

if ($updated) {
Response::success(
MissatgesAPI::success('update'),
['id' => $data['id']],
200
);
} else {
Response::error(
"No s'ha modificat cap registre.",
[],
404
);
}
} catch (Exception $e) {
Response::error(
MissatgesAPI::error('errorBD'),
[$e->getMessage()],
500
);
}