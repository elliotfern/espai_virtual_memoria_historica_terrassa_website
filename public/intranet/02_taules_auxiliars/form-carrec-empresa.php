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
$carrec_cat_old = "";
$carrec_es_old = "";
$carrec_en_old = "";
$carrec_fr_old = "";
$carrec_it_old = "";
$carrec_pt_old = "";

$btnModificar = 1;

if ($pag === "modifica-carrec-empresa") {
    $btnModificar = 2;
    $id = $routeParams[0];

    // Verificar si la ID existe en la base de datos
    $query = "SELECT id, carrec_cat, carrec_es, carrec_en, carrec_fr, carrec_pt, carrec_it	
    FROM aux_ofici_carrec
    WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Acceder a las variables de la consulta
            $id_old = $row['id'] ?? "";
            $carrec_cat_old = $row['carrec_cat'] ?? "";
            $carrec_es_old = $row['carrec_es'] ?? "";
            $carrec_en_old = $row['carrec_en'] ?? "";
            $carrec_fr_old = $row['carrec_fr'] ?? "";
            $carrec_it_old = $row['carrec_it'] ?? "";
            $carrec_pt_old = $row['carrec_pt'] ?? "";
        }
    }
}

?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="causaMortForm">
        <div class="container">
            <div class="row g-3">
                <?php if ($btnModificar === 1) {
                    echo '<h2>Crear nou càrrec d\'empresa</h2>';
                } else {
                    echo '<h2>Modifica càrrec d\'empresa: ' . $carrec_cat_old . '</h2>';
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
                    <label for="carrec_cat" class="form-label negreta">Càrrec empresa (català):</label>
                    <input type="text" class="form-control" id="carrec_cat" name="carrec_cat" value="<?php echo $carrec_cat_old; ?>">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <?php if (isUserAdmin()) : ?>
                    <hr>

                    <div class="col-md-4 mb-4">
                        <label for="carrec_es" class="form-label negreta">Càrrec empresa (castellà):</label>
                        <input type="text" class="form-control" id="carrec_es" name="carrec_es" value="<?php echo $carrec_es_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="carrec_en" class="form-label negreta">Càrrec empresa (anglès):</label>
                        <input type="text" class="form-control" id="carrec_en" name="carrec_en" value="<?php echo $carrec_en_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="carrec_fr" class="form-label negreta">Càrrec empresa (francès):</label>
                        <input type="text" class="form-control" id="carrec_fr" name="carrec_fr" value="<?php echo $carrec_fr_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="carrec_it" class="form-label negreta">Càrrec empresa (italià):</label>
                        <input type="text" class="form-control" id="carrec_it" name="carrec_it" value="<?php echo $carrec_it_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="carrec_pt" class="form-label negreta">Càrrec empresa (portugués):</label>
                        <input type="text" class="form-control" id="carrec_pt" name="carrec_pt" value="<?php echo $carrec_pt_old; ?>">
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