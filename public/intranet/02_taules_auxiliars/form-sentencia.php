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
$sentencia_ca_old = "";
$sentencia_es_old = "";
$sentencia_en_old = "";
$sentencia_fr_old = "";
$sentencia_it_old = "";
$sentencia_pt_old = "";

$btnModificar = 1;

if ($pag === "modifica-sentencia") {
    $btnModificar = 2;
    $id = $routeParams[0];

    // Verificar si la ID existe en la base de datos
    $query = "SELECT id, sentencia_ca, sentencia_es, sentencia_en, sentencia_fr, sentencia_it, sentencia_pt
    FROM aux_sentencies 
    WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Acceder a las variables de la consulta
            $id_old = $row['id'] ?? "";
            $sentencia_ca_old = $row['sentencia_ca'] ?? "";
            $sentencia_es_old = $row['sentencia_es'] ?? "";
            $sentencia_en_old = $row['sentencia_en'] ?? "";
            $sentencia_fr_old = $row['sentencia_fr'] ?? "";
            $sentencia_it_old = $row['sentencia_it'] ?? "";
            $sentencia_pt_old = $row['sentencia_pt'] ?? "";
        }
    }
}
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="sentenciaForm" action="" type="post">
        <div class="container">
            <div class="row g-3">
                <?php if ($btnModificar === 1) {
                    echo '<h2>Crear nova sentència</h2>';
                } else {
                    echo '<h2>Modifica sentència: ' . $sentencia_ca_old . '</h2>';
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
                    <label for="sentencia_ca" class="form-label negreta">Sentència (català):</label>
                    <input type="text" id="sentencia_ca" class="form-control" name="sentencia_ca" value="<?php echo $sentencia_ca_old; ?>">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <?php if (isUserAdmin()) : ?>
                    <hr>

                    <div class="col-md-4 mb-4">
                        <label for="sentencia_es" class="form-label negreta">Sentencia (castellano):</label>
                        <input type="text" class="form-control" id="sentencia_es" name="sentencia_es" value="<?php echo $sentencia_es_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="sentencia_en" class="form-label negreta">Sentence (English):</label>
                        <input type="text" class="form-control" id="sentencia_en" name="sentencia_en" value="<?php echo $sentencia_en_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="sentencia_fr" class="form-label negreta">Sentence (français):</label>
                        <input type="text" class="form-control" id="sentencia_fr" name="sentencia_fr" value="<?php echo $sentencia_fr_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="sentencia_it" class="form-label negreta">Sentenza (italiano):</label>
                        <input type="text" class="form-control" id="sentencia_it" name="sentencia_it" value="<?php echo $sentencia_it_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="sentencia_pt" class="form-label negreta">Sentença (português):</label>
                        <input type="text" class="form-control" id="sentencia_pt" name="sentencia_pt" value="<?php echo $sentencia_pt_old; ?>">
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