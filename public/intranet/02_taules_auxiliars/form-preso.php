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
$nom_preso_old        = "";
$municipi_preso_old   = "";
$nom_preso_es_old     = "";
$nom_preso_en_old     = "";
$nom_preso_fr_old     = "";
$nom_preso_it_old     = "";
$nom_preso_pt_old     = "";

$btnModificar = 1;

if ($pag === "modifica-preso") {
    $btnModificar = 2;
    $id = $routeParams[0];

    // Verificar si la ID existe en la base de datos
    $query = "SELECT id, nom_preso, municipi_preso, nom_preso_es, nom_preso_en, nom_preso_fr, nom_preso_it, nom_preso_pt
    FROM aux_presons 
    WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Acceder a las variables de la consulta
            $id_old = $row['id'] ?? "";
            $nom_preso_old        = $row['nom_preso'] ?? "";
            $municipi_preso_old   = $row['municipi_preso'] ?? "";
            $nom_preso_es_old     = $row['nom_preso_es'] ?? "";
            $nom_preso_en_old     = $row['nom_preso_en'] ?? "";
            $nom_preso_fr_old     = $row['nom_preso_fr'] ?? "";
            $nom_preso_it_old     = $row['nom_preso_it'] ?? "";
            $nom_preso_pt_old     = $row['nom_preso_pt'] ?? "";
        }
    }
}
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="presoForm">
        <div class="container">
            <div class="row g-3">
                <?php if ($btnModificar === 1) {
                    echo '<h2>Crear nova presó</h2>';
                } else {
                    echo '<h2>Modifica presó: ' . $nom_preso_old . '</h2>';
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
                    <label for="nom_preso" class="form-label negreta">Nom presó (català):</label>
                    <input type="text" class="form-control" id="nom_preso" name="nom_preso" value="<?php echo $nom_preso_old ?>">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="municipi_preso" class="form-label negreta">Municipi de la presó:</label>
                    <select class="form-select" id="municipi_preso" value="" name="municipi_preso">
                    </select>
                    <div class="mt-2">
                        <a href="<?php echo APP_WEB . APP_INTRANET . $url['auxiliars'] ?>/nou-municipi" target="_blank" class="btn btn-secondary btn-sm" id="afegir1">Afegir municipi</a>
                        <button id="refreshButton1" class="btn btn-primary btn-sm">Actualitzar llistat</button>
                    </div>
                </div>

                <?php if (isUserAdmin()) : ?>
                    <hr>

                    <div class="col-md-4 mb-4">
                        <label for="nom_preso_es" class="form-label negreta">Nom presó (castellà):</label>
                        <input type="text" class="form-control" id="nom_preso_es" name="nom_preso_es" value="<?php echo $nom_preso_es_old ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="nom_preso_en" class="form-label negreta">Nom presó (anglès):</label>
                        <input type="text" class="form-control" id="nom_preso_en" name="nom_preso_en" value="<?php echo $nom_preso_en_old ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="nom_preso_fr" class="form-label negreta">Nom presó (francès):</label>
                        <input type="text" class="form-control" id="nom_preso_fr" name="nom_preso_fr" value="<?php echo $nom_preso_fr_old ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="nom_preso_it" class="form-label negreta">Nom presó (italià):</label>
                        <input type="text" class="form-control" id="nom_preso_it" name="nom_preso_it" value="<?php echo $nom_preso_it_old ?>">
                    </div>

                    <div class="col-md-4 mb-4">
                        <label for="nom_preso_pt" class="form-label negreta">Nom presó (portuguès):</label>
                        <input type="text" class="form-control" id="nom_preso_pt" name="nom_preso_pt" value="<?php echo $nom_preso_pt_old ?>">
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