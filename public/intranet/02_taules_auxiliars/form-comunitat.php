<?php

use App\Config\DatabaseConnection;

$conn = DatabaseConnection::getConnection();

if (!$conn) {
    die("No se pudo establecer conexión a la base de datos.");
}
require_once APP_ROOT . '/public/intranet/includes/header.php';

// Obtener la URL completa
$url2 = $_SERVER['REQUEST_URI'];

// Dividir la URL en partes usando '/' como delimitador
$urlParts = explode('/', $url2);

// Obtener la parte deseada (en este caso, la cuarta parte)
$categoriaId = $urlParts[3] ?? '';

$id_old = "";
$comunitat_old = "";
$comunitat_ca_old = "";
$modificaBtn = "";

if ($categoriaId === "modifica-comunitat") {
    $modificaBtn = 1;
    $id_old = $routeParams[0];

    $query = "SELECT c.id, c.comunitat, c.comunitat_ca
    FROM aux_dades_municipis_comunitat AS c
    WHERE c.id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id_old, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $comunitat_old = $row['comunitat'] ?? "";
            $id_old = $row['id'] ?? "";
            $comunitat_ca = $row['comunitat_ca'] ?? "";
        }
    }
} else {
    $modificaBtn = 2;
}
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="formComunitat">
        <div class="container">
            <?php if ($modificaBtn === 1) { ?>
                <h2>Modificació dades Comunitat autònoma / regió / Estat federal</h2>
                <h4 id="fitxa">Comunitat: <?php echo $comunitat_old; ?></h4>
            <?php } else { ?>
                <h2>Inserció dades nova Comunitat Autònoma/Regió/Estat federal</h2>
            <?php } ?>
            <div class="row g-3">

                <div class="alert alert-success" role="alert" id="okMessage" style="display:none">
                    <div id="okText"></div>
                </div>

                <div class="alert alert-danger" role="alert" id="errMessage" style="display:none">
                    <div id="errText"></div>
                </div>

                <div class="alert alert-info">
                    <h5>Sobre l'ús general de topònims en català</h5>
                    <ul>

                        <li>1. Els topònims de Catalunya s'utilitzen en la seva forma oficial i, sempre que sigui possible, en la seva forma íntegra.</li>
                        <li>2. Els topònims d'altres territoris de l'àrea lingüística catalana s'utilitzen en la forma en català.</li>
                        <li>3. Els exotopònims, és a dir, els topònims de fora de l'àrea lingüística catalana s'utilitzen en català quan hi ha una forma establerta amb ús tradicional, sens perjudici que hi pugui figurar també la denominació en altres llengües del territori corresponent.</li>
                        <li>4. Els topònims de l'àrea lingüística occitana de fora de Catalunya s'utilitzen en la forma tradicional en català o en occità, tret de l'Aran, on s'utilitza la forma tradicional en occità, i sens perjudici que hi pugui figurar també la denominació en altres llengües del territori corresponent.</li>
                    </ul>
                </div>

                <input type="hidden" id="id" name="id" value="<?php echo $id_old; ?>">

                <div class="col-md-4 mb-4">
                    <label for="comunitat" class="form-label negreta">Nom comunitat autònoma/regió (forma oficial):</label>
                    <input type="text" class="form-control" id="comunitat" name="comunitat" value="<?php echo $comunitat_old; ?>">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="comunitat" class="form-label negreta">Nom comunitat autònoma/regió (nom en català):</label>
                    <input type="text" class="form-control" id="comunitat_ca" name="comunitat_ca" value="<?php echo $comunitat_ca_old; ?>">
                    <div class="avis-form">
                        * Omplir en cas que disposem del nom de la comunitat en català
                    </div>
                </div>

                <div class="row espai-superior" style="border-top: 1px solid black;padding-top:25px">
                    <div class="col">
                    </div>

                    <div class="col d-flex justify-content-end align-items-center">
                        <?php
                        if ($modificaBtn === 1) {
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