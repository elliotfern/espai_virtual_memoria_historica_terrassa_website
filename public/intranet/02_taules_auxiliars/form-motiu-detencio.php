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
$motiuEmpresonament_ca_old = "";
$motiuEmpresonament_es_old = "";
$motiuEmpresonament_en_old = "";
$motiuEmpresonament_fr_old = "";
$motiuEmpresonament_it_old = "";
$motiuEmpresonament_pt_old = "";

$btnModificar = 1;

if ($pag === "modifica-motiu-detencio") {
    $btnModificar = 2;
    $id = $routeParams[0];

    // Verificar si la ID existe en la base de datos
    $query = "SELECT id, motiuEmpresonament_ca,	motiuEmpresonament_es, motiuEmpresonament_en, motiuEmpresonament_fr, motiuEmpresonament_it, motiuEmpresonament_pt
    FROM aux_motius_empresonament 
    WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Acceder a las variables de la consulta
            $id_old = $row['id'] ?? "";
            $motiuEmpresonament_ca_old = $row['motiuEmpresonament_ca'] ?? "";
            $motiuEmpresonament_es_old = $row['motiuEmpresonament_es'] ?? "";
            $motiuEmpresonament_en_old = $row['motiuEmpresonament_en'] ?? "";
            $motiuEmpresonament_fr_old = $row['motiuEmpresonament_fr'] ?? "";
            $motiuEmpresonament_it_old = $row['motiuEmpresonament_it'] ?? "";
            $motiuEmpresonament_pt_old = $row['motiuEmpresonament_pt'] ?? "";
        }
    }
}
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="motiuDetencioForm" action="" type="post">
        <div class="container">
            <div class="row g-3">
                <?php if ($btnModificar === 1) {
                    echo '<h2>Crear nou motiu de detenció/empresonament</h2>';
                } else {
                    echo '<h2>Modifica motiu de detenció/empresonament: ' . $motiuEmpresonament_ca_old . '</h2>';
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
                    <label for="motiuEmpresonament_ca" class="form-label negreta">Motiu empresonament (català):</label>
                    <input type="text" class="form-control" id="motiuEmpresonament_ca" name="motiuEmpresonament_ca" value="<?php echo $motiuEmpresonament_ca_old; ?>">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <?php if (isUserAdmin()) : ?>
                    <hr>

                    <div class="col-md-4 mb-4">
                        <label for="motiuEmpresonament_es" class="form-label negreta">Motivo de encarcelamiento (castellano):</label>
                        <input type="text" class="form-control" id="motiuEmpresonament_es" name="motiuEmpresonament_es" value="<?php echo $motiuEmpresonament_es_old; ?>">
                    </div>
                    <div class="col-md-4 mb-4">
                        <label for="motiuEmpresonament_en" class="form-label negreta">Reason for imprisonment (English):</label>
                        <input type="text" class="form-control" id="motiuEmpresonament_en" name="motiuEmpresonament_en" value="<?php echo $motiuEmpresonament_en_old; ?>">
                    </div>
                    <div class="col-md-4 mb-4">
                        <label for="motiuEmpresonament_fr" class="form-label negreta">Raison de l'emprisonnement (français):</label>
                        <input type="text" class="form-control" id="motiuEmpresonament_fr" name="motiuEmpresonament_fr" value="<?php echo $motiuEmpresonament_fr_old; ?>">
                    </div>
                    <div class="col-md-4 mb-4">
                        <label for="motiuEmpresonament_it" class="form-label negreta">Motivo dell'incarcerazione (italiano):</label>
                        <input type="text" class="form-control" id="motiuEmpresonament_it" name="motiuEmpresonament_it" value="<?php echo $motiuEmpresonament_it_old; ?>">
                    </div>
                    <div class="col-md-4 mb-4">
                        <label for="motiuEmpresonament_pt" class="form-label negreta">Motivo de encarceramento (português):</label>
                        <input type="text" class="form-control" id="motiuEmpresonament_pt" name="motiuEmpresonament_pt" value="<?php echo $motiuEmpresonament_pt_old; ?>">
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