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
$categoria_cat_old = "";
$categoria_cast_old = "";
$categoria_eng_old = "";
$categoria_fr_old = "";
$categoria_it_old = "";
$categoria_pt_old = "";

$btnModificar = 1;

if ($pag === "modifica-categoria-repressio") {
    $btnModificar = 2;
    $id = $routeParams[0];

    // Verificar si la ID existe en la base de datos
    $query = "SELECT id, categoria_cat, categoria_cast,	categoria_eng, categoria_fr, categoria_it, categoria_pt 	
    FROM aux_categoria
    WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Acceder a las variables de la consulta
            $id_old = $row['id'] ?? "";
            $categoria_cat_old = $row['categoria_cat'] ?? "";
            $categoria_cast_old = $row['categoria_cast'] ?? "";
            $categoria_eng_old = $row['categoria_eng'] ?? "";
            $categoria_fr_old = $row['categoria_fr'] ?? "";
            $categoria_it_old = $row['categoria_it'] ?? "";
            $categoria_pt_old = $row['categoria_pt'] ?? "";
        }
    }
}

?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <form id="categoriesForm" action="" type="post">
        <div class="container">
            <div class="row g-3">
                <?php if ($btnModificar === 1) {
                    echo '<h2>Crear nova categoria de repressió</h2>';
                } else {
                    echo '<h2>Modifica categoria de repressió: ' . $categoria_cat_old . '</h2>';
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
                    <label for="categoria_cat" class="form-label negreta">Categoria repressió (català):</label>
                    <input type="text" class="form-control" id="categoria_cat" name="categoria_cat" value="<?php echo $categoria_cat_old; ?>">
                    <div class="avis-form">
                        * Camp obligatori
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label for="categoria_cast" class="form-label negreta">Categoria repressió (castellà):</label>
                    <input type="text" class="form-control" id="categoria_cast" name="categoria_cast" value="<?php echo $categoria_cast_old; ?>">
                </div>

                <div class="col-md-4 mb-4">
                    <label for="categoria_eng" class="form-label negreta">Categoria repressió (anglès):</label>
                    <input type="text" class="form-control" id="categoria_eng" name="categoria_eng" value="<?php echo $categoria_eng_old; ?>">

                </div>

                <div class="col-md-4 mb-4">
                    <label for="categoria_fr" class="form-label negreta">Categoria repressió (francès):</label>
                    <input type="text" class="form-control" id="categoria_fr" name="categoria_fr" value="<?php echo $categoria_fr_old; ?>">

                </div>

                <div class="col-md-4 mb-4">
                    <label for="categoria_it" class="form-label negreta">Categoria repressió (italià):</label>
                    <input type="text" class="form-control" id="categoria_it" name="categoria_it" value="<?php echo $categoria_it_old; ?>">

                </div>

                <div class="col-md-4 mb-4">
                    <label for="categoria_pt" class="form-label negreta">Categoria repressió (portuguès):</label>
                    <input type="text" class="form-control" id="categoria_pt" name="categoria_pt" value="<?php echo $categoria_pt_old; ?>">
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