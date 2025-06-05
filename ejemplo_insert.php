try {
$db = new Database();

$query = "INSERT INTO aux_dades_municipis (ciutat, ciutat_ca, comarca, provincia, comunitat, estat)
VALUES (:ciutat, :ciutat_ca, :comarca, :provincia, :comunitat, :estat)";

$params = [
'ciutat' => $data['ciutat'],
'ciutat_ca' => $data['ciutat_ca'] ?? null,
'comarca' => $data['comarca'] ?? null,
'provincia' => $data['provincia'] ?? null,
'comunitat' => $data['comunitat'] ?? null,
'estat' => $data['estat'] ?? null,
];

// Preparar y ejecutar la consulta
$stmt = $db->getConnection()->prepare($query);

foreach ($params as $key => $value) {
// binding con el prefijo :
$stmt->bindValue(':' . $key, $value);
}

$stmt->execute();

// Recuperar el ID insertado
$id = $db->getConnection()->lastInsertId();

Response::success(
MissatgesAPI::success('create'),
['id' => $id],
201
);

} catch (PDOException $e) {
Response::error(
MissatgesAPI::error('errorBD'),
[$e->getMessage()],
500
);
}