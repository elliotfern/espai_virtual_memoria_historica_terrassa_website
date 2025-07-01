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
$modalitat_ca_old = "";
$modalitat_es_old = "";
$modalitat_en_old = "";
$modalitat_fr_old = "";
$modalitat_it_old = "";
$modalitat_pt_old = "";

$btnModificar = 1;

if ($pag === "modifica-modalitat-preso") {
    $btnModificar = 2;
    $id = $routeParams[0];

    // Verificar si la ID existe en la base de datos
    $query = "SELECT id,
        modalitat_ca,
        modalitat_es,
        modalitat_en,
        modalitat_fr,
        modalitat_it,
        modalitat_pt
    FROM aux_modalitat_preso 
    WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Acceder a las variables de la consulta
            $id_old = $row['id'] ?? "";
            $modalitat_ca_old = $row['modalitat_ca'] ?? "";
            $modalitat_es_old = $row['modalitat_es'] ?? "";
            $modalitat_en_old = $row['modalitat_en'] ?? "";
            $modalitat_fr_old = $row['modalitat_fr'] ?? "";
            $modalitat_it_old = $row['modalitat_it'] ?? "";
            $modalitat_pt_old = $row['modalitat_pt'] ?? "";
        }
    }
}
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="modalitatPresoForm" action="" type="post">
        <div class="container">
            <div class="row g-3">
                <?php if ($btnModificar === 1) {
                    echo '<h2>Crear nova modalitat de presó</h2>';
                } else {
                    echo '<h2>Modifica modalitat de presó: ' . $modalitat_ca_old . '</h2>';
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
                    <label for="modalitat_ca" class="form-label negreta">Modalitat (català):</label>
                    <input type="text" class="form-control" id="modalitat_ca" name="modalitat_ca" value="<?php echo $modalitat_ca_old; ?>">

                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <?php if (isUserAdmin()) : ?>
                    <hr>
                    <div class="col-md-4 mb-4">
                        <label for="modalitat_es" class="form-label negreta">Modalidad (castellano):</label>
                        <input type="text" class="form-control" id="modalitat_es" name="modalitat_es" value="<?php echo $modalitat_es_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="modalitat_en" class="form-label negreta">Modality (English):</label>
                        <input type="text" class="form-control" id="modalitat_en" name="modalitat_en" value="<?php echo $modalitat_en_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="modalitat_fr" class="form-label negreta">Modalité (français):</label>
                        <input type="text" class="form-control" id="modalitat_fr" name="modalitat_fr" value="<?php echo $modalitat_fr_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="modalitat_it" class="form-label negreta">Modalità (italiano):</label>
                        <input type="text" class="form-control" id="modalitat_it" name="modalitat_it" value="<?php echo $modalitat_it_old; ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="modalitat_pt" class="form-label negreta">Modalidade (português):</label>
                        <input type="text" class="form-control" id="modalitat_pt" name="modalitat_pt" value="<?php echo $modalitat_pt_old; ?>">
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