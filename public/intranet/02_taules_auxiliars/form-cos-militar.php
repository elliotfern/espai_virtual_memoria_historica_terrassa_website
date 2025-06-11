<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';

use App\Config\DatabaseConnection;

$conn = DatabaseConnection::getConnection();

if (!$conn) {
    die("No se pudo establecer conexión a la base de datos.");
}

// Obtener la URL completa
$url2 = $_SERVER['REQUEST_URI'];

// Dividir la URL en partes usando '/' como delimitador
$urlParts = explode('/', $url2);

// Obtener la parte deseada (en este caso, la cuarta parte)
$pag = $urlParts[3] ?? '';

$id_old = "";
$cos_militar_ca_old = "";
$cos_militar_es_old = "";
$cos_militar_en_old = "";
$cos_militar_fr_old = "";
$cos_militar_it_old = "";
$cos_militar_pt_old = "";

$btnModificar = 1;

if ($pag === "modifica-cos-militar") {
    $btnModificar = 2;
    $id = $routeParams[0];

    // Verificar si la ID existe en la base de datos
    $query = "SELECT id, cos_militar_ca, cos_militar_es, cos_militar_en, cos_militar_fr, cos_militar_it, cos_militar_pt
    FROM aux_cossos_militars
    WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Acceder a las variables de la consulta
            $id_old = $row['id'] ?? "";
            $cos_militar_ca_old = $row['cos_militar_ca'] ?? "";
            $cos_militar_es_old = $row['cos_militar_es'] ?? "";
            $cos_militar_en_old = $row['cos_militar_en'] ?? "";
            $cos_militar_fr_old = $row['cos_militar_fr'] ?? "";
            $cos_militar_it_old = $row['cos_militar_it'] ?? "";
            $cos_militar_pt_old = $row['cos_militar_pt'] ?? "";
        }
    }
}
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="condicioForm">
        <div class="container">
            <div class="row g-3">
                <?php if ($btnModificar === 1) {
                    echo '<h2>Crear nou cos militar durant la Guerra Civil</h2>';
                } else {
                    echo '<h2>Modifica cos militar durant durant la Guerra Civil: ' . $cos_militar_ca_old . '</h2>';
                }

                ?>
                <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                    <div id="okText"></div>
                </div>

                <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                    <div id="errText"></div>
                </div>

                <input type="hidden" name="id" id="id" value="<?php echo $id_old; ?>">

                <div class="col-md-4 mb-4">
                    <label for="cos_militar_ca" class="form-label negreta">Cos militar (català):</label>
                    <input type="text" class="form-control" id="cos_militar_ca" name="cos_militar_ca" value="<?php echo $cos_militar_ca_old; ?>">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <?php if (isUserAdmin()) : ?>
                    <hr>

                    <div class="col-md-4 mb-4">
                        <label for="cos_militar_es" class="form-label negreta">Cos militar (castellà):</label>
                        <input type="text" class="form-control" id="cos_militar_es" name="cos_militar_es" value="<?php echo $cos_militar_es_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="cos_militar_en" class="form-label negreta">Cos militar (anglès):</label>
                        <input type="text" class="form-control" id="cos_militar_en" name="cos_militar_en" value="<?php echo $cos_militar_en_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="cos_militar_fr" class="form-label negreta">Cos militar (francès):</label>
                        <input type="text" class="form-control" id="cos_militar_fr" name="cos_militar_fr" value="<?php echo $cos_militar_fr_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="cos_militar_pt" class="form-label negreta">Cos militar (portuguès):</label>
                        <input type="text" class="form-control" id="cos_militar_pt" name="cos_militar_pt" value="<?php echo $cos_militar_pt_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="cos_militar_it" class="form-label negreta">Cos militar (italià):</label>
                        <input type="text" class="form-control" id="cos_militar_it" name="cos_militar_it" value="<?php echo $cos_militar_it_old; ?>">
                    </div>
                <?php endif; ?>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col"></div>

                    <div class="col d-flex justify-content-end align-items-center">

                        <?php
                        if ($btnModificar === 2) {
                            echo '<button class="btn btn-primary" type="submit">Modificar dades</button>';
                        } else {
                            echo '<button class="btn btn-primary" type="submit">Inserir dades</button>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>