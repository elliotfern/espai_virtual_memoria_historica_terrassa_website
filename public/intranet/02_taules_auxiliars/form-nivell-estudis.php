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
$estudi_ca_old = "";
$estudi_es_old = "";
$estudi_en_old = "";
$estudi_fr_old = "";
$estudi_it_old = "";
$estudi_pt_old = "";

$btnModificar = 1;

if ($pag === "modifica-nivell-estudis") {
    $btnModificar = 2;
    $id = $routeParams[0];

    // Verificar si la ID existe en la base de datos
    $query = "SELECT id, estudi_ca, estudi_es, estudi_en, estudi_it, estudi_fr, estudi_pt
    FROM aux_estudis
    WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Acceder a las variables de la consulta
            $id_old = $row['id'] ?? "";
            $estudi_ca_old = $row['estudi_ca'] ?? "";
            $estudi_es_old = $row['estudi_es'] ?? "";
            $estudi_en_old = $row['estudi_en'] ?? "";
            $estudi_it_old = $row['estudi_it'] ?? "";
            $estudi_fr_old = $row['estudi_fr'] ?? "";
            $estudi_pt_old = $row['estudi_pt'] ?? "";
        }
    }
}
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="nivellEstudisForm">
        <div class="container">
            <div class="row g-3">
                <?php if ($btnModificar === 1) {
                    echo '<h2>Crear nou nivell d\'estudis</h2>';
                } else {
                    echo '<h2>Modifica el nivell d\'estudis: ' . $estudi_cat_old . '</h2>';
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
                    <label for="estudi_cat" class="form-label negreta">Nivell d'estudis (català):</label>
                    <input type="text" class="form-control" id="estudi_ca" name="estudi_ca" value="<?php echo $estudi_ca_old; ?>">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <?php if (isUserAdmin()) : ?>
                    <hr>

                    <div class="col-md-4 mb-4">
                        <label for="estudi_es" class="form-label negreta">Nivell d'estudis (castellà):</label>
                        <input type="text" class="form-control" id="estudi_es" name="estudi_es" value="<?php echo $estudi_es_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="estudi_en" class="form-label negreta">Nivell d'estudis (anglès):</label>
                        <input type="text" class="form-control" id="estudi_en" name="estudi_en" value="<?php echo $estudi_en_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="estudi_fr" class="form-label negreta">Nivell d'estudis (francès):</label>
                        <input type="text" class="form-control" id="estudi_fr" name="estudi_fr" value="<?php echo $estudi_fr_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="estudi_pt" class="form-label negreta">Nivell d'estudis (portuguès):</label>
                        <input type="text" class="form-control" id="estudi_pt" name="estudi_pt" value="<?php echo $estudi_pt_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="estudi_it" class="form-label negreta">Nivell d'estudis (italià):</label>
                        <input type="text" class="form-control" id="estudi_it" name="estudi_it" value="<?php echo $estudi_it_old; ?>">
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