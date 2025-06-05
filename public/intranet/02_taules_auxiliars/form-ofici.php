<?php

use App\Config\DatabaseConnection;

$conn = DatabaseConnection::getConnection();

if (!$conn) {
    die("No se pudo establecer conexión a la base de datos.");
}
require_once APP_ROOT . '/public/intranet/includes/header.php';

$id_old = "";
$ofici_cat_old = "";
$btnModificar = 1;

// Obtener la URL completa
$url2 = $_SERVER['REQUEST_URI'];

// Dividir la URL en partes usando '/' como delimitador
$urlParts = explode('/', $url2);

// Obtener la parte deseada (en este caso, la cuarta parte)
$pag = $urlParts[3] ?? '';

if ($pag === "modifica-ofici") {
    $btnModificar = 2;
    $id = $routeParams[0];

    $query = "SELECT id, ofici_cat, ofici_es, ofici_en
    FROM aux_oficis
    WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Acceder a las variables de la consulta
            $ofici_cat_old  = $row['ofici_cat'] ?? "";
            $ofici_es_old  = $row['ofici_es'] ?? "";
            $ofici_en_old  = $row['ofici_en'] ?? "";
            $id_old = $row['id'] ?? "";
        }
    }
}
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="oficiForm">
        <div class="container">
            <div class="row g-3">
                <?php if ($btnModificar === 1) {
                    echo '<h2>Creació nou Ofici</h2>';
                } else {
                    echo '<h2>Modifica ofici: ' . $ofici_cat_old . '</h2>';
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
                    <label for="ofici_cat" class="form-label negreta">Nom ofici (català):</label>
                    <input type="text" class="form-control" id="ofici_cat" name="ofici_cat" value="<?php echo $ofici_cat_old; ?>">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

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