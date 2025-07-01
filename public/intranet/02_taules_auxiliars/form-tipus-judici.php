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
$tipusJudici_ca_old = "";
$tipusJudici_es_old = "";
$tipusJudici_en_old = "";
$tipusJudici_fr_old = "";
$tipusJudici_it_old = "";
$tipusJudici_pt_old = "";

$btnModificar = 1;

if ($pag === "modifica-tipus-judici") {
    $btnModificar = 2;
    $id = $routeParams[0];

    // Verificar si la ID existe en la base de datos
    $query = "SELECT id, tipusJudici_ca, tipusJudici_es, tipusJudici_en, tipusJudici_fr, tipusJudici_it, tipusJudici_pt
    FROM aux_tipus_judici
    WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Acceder a las variables de la consulta
            $id_old = $row['id'] ?? "";
            $tipusJudici_ca_old = $row['tipusJudici_ca'] ?? "";
            $tipusJudici_es_old = $row['tipusJudici_es'] ?? "";
            $tipusJudici_en_old = $row['tipusJudici_en'] ?? "";
            $tipusJudici_fr_old = $row['tipusJudici_fr'] ?? "";
            $tipusJudici_it_old = $row['tipusJudici_it'] ?? "";
            $tipusJudici_pt_old = $row['tipusJudici_pt'] ?? "";
        }
    }
}
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="tipusJudiciForm" action="" type="post">
        <div class="container">
            <div class="row g-3">
                <?php if ($btnModificar === 1) {
                    echo '<h2>Crear nou tipus de judici</h2>';
                } else {
                    echo '<h2>Modifica tipus de judici: ' . $tipusJudici_ca_old . '</h2>';
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
                    <label for="tipusJudici_ca" class="form-label negreta">Tipus judici (català):</label>
                    <input type="text" class="form-control" id="tipusJudici_ca" name="tipusJudici_ca" value="<?php echo $tipusJudici_ca_old; ?>">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <?php if (isUserAdmin()) : ?>
                    <hr>

                    <div class="col-md-4 mb-4">
                        <label for="tipusJudici_es" class="form-label negreta">Tipus judici (castellà):</label>
                        <input type="text" class="form-control" id="tipusJudici_es" name="tipusJudici_es" value="<?php echo $tipusJudici_es_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="tipusJudici_en" class="form-label negreta">Tipus judici (anglès):</label>
                        <input type="text" class="form-control" id="tipusJudici_en" name="tipusJudici_en" value="<?php echo $tipusJudici_en_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="tipusJudici_fr" class="form-label negreta">Tipus judici (francès):</label>
                        <input type="text" class="form-control" id="tipusJudici_fr" name="tipusJudici_fr" value="<?php echo $tipusJudici_fr_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="tipusJudici_it" class="form-label negreta">Tipus judici (italià):</label>
                        <input type="text" class="form-control" id="tipusJudici_it" name="tipusJudici_it" value="<?php echo $tipusJudici_it_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="tipusJudici_pt" class="form-label negreta">Tipus judici (portuguès):</label>
                        <input type="text" class="form-control" id="tipusJudici_pt" name="tipusJudici_pt" value="<?php echo $tipusJudici_pt_old; ?>">
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