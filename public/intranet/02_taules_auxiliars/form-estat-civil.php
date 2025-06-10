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
$estat_cat_old = "";
$estat_es_old = "";
$estat_en_old = "";
$estat_fr_old = "";
$estat_it_old = "";
$estat_pt_old = "";

$btnModificar = 1;

if ($pag === "modifica-estat-civil") {
    $btnModificar = 2;
    $id = $routeParams[0];

    // Verificar si la ID existe en la base de datos
    $query = "SELECT id, estat_cat, estat_es, estat_en, estat_fr, estat_it, estat_pt
    FROM aux_estat_civil
    WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Acceder a las variables de la consulta
            $id_old = $row['id'] ?? "";
            $estat_cat_old = $row['estat_cat'] ?? "";
            $estat_es_old = $row['estat_es'] ?? "";
            $estat_en_old = $row['estat_en'] ?? "";
            $estat_it_old = $row['estat_it'] ?? "";
            $estat_fr_old = $row['estat_fr'] ?? "";
            $estat_it_old = $row['estat_it'] ?? "";
        }
    }
}
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="estatCivilForm">
        <div class="container">
            <div class="row g-3">
                <?php if ($btnModificar === 1) {
                    echo '<h2>Crear nou estat civil</h2>';
                } else {
                    echo '<h2>Modifica estat civil: ' . $estat_cat_old . '</h2>';
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
                    <label for="estat_cat" class="form-label negreta">Estat civil (català):</label>
                    <input type="text" class="form-control" id="estat_cat" name="estat_cat" value="<?php echo $estat_cat_old; ?>">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <?php if (isUserAdmin()) : ?>
                    <hr>

                    <div class="col-md-4 mb-4">
                        <label for="estat_es" class="form-label negreta">Estat civil (castellà):</label>
                        <input type="text" class="form-control" id="estat_es" name="estat_es" value="<?php echo $estat_es_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="estat_en" class="form-label negreta">Estat civil (anglès):</label>
                        <input type="text" class="form-control" id="estat_en" name="estat_en" value="<?php echo $estat_en_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="estat_fr" class="form-label negreta">Estat civil (francès):</label>
                        <input type="text" class="form-control" id="estat_fr" name="estat_fr" value="<?php echo $estat_fr_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="estat_pt" class="form-label negreta">Estat civil (portuguès):</label>
                        <input type="text" class="form-control" id="estat_pt" name="estat_pt" value="<?php echo $estat_pt_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="estat_it" class="form-label negreta">Estat civil (italià):</label>
                        <input type="text" class="form-control" id="estat_it" name="estat_it" value="<?php echo $estat_it_old; ?>">
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