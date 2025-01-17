<?php

$idPersona = $routeParams[0]; // Segundo, por ejemplo, 2
$idPersona = (int) $idPersona;

// Verificar si es un número entero válido
if (!is_int($idPersona)) {
    // Si no es un número entero o es menor o igual a cero, detener la ejecución
    header("Location: /404");
    exit();
}

require_once APP_ROOT . '/public/intranet/includes/header.php';

$query = "SELECT 
    d.nom,
    d.cognom1,
    d.cognom2
    FROM db_dades_personals AS d
    WHERE d.id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $idPersona, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $nom = $row['nom'] ?? "";
        $cognom1 = $row['cognom1'] ?? "";
        $cognom2 = $row['cognom2'] ?? "";
    }
}

?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <div class="container">
        <div class="row">
            <h2>Familiars del represaliat</h2>
            <h4 id="fitxaNomCognoms">Fitxa: <a href="https://memoriaterrassa.cat/fitxa/<?php echo $idPersona; ?>" target="_blank"><?php echo $nom . " " . $cognom1 . " " . $cognom2; ?></a></h4>

            <div class="col-md-4" style="margin-top:20px;margin-bottom:20px">
                <a href="https://memoriaterrassa.cat/gestio/tots/fitxa/familiar/nou/<?php echo $idPersona; ?>" class="btn btn-success">Afegir familiar</a>
            </div>

            <?php
            $query = "SELECT 
            f.id,
            f.nom,
            f.cognom1,
            f.cognom2,
            f.anyNaixement,
            f.idParent,
            r.relacio_parentiu
            FROM aux_familiars AS f
            LEFT JOIN aux_familiars_relacio AS r ON f.relacio_parentiu = r.id
            WHERE f.idParent = :idParent";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':idParent', $idPersona, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                echo '<table class="table" style="margin-top:25px;margin-bottom:30px">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Nom</th>';
                echo '<th>Cognoms</th>';
                echo '<th>Any de naixement</th>';
                echo '<th>Relació de parentiu</th>';
                echo '<th>Modifica</th>';
                echo '<th>Elimina</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $id = $row['id'] ?? "";
                    $nom = $row['nom'] ?? "";
                    $cognom1 = $row['cognom1'] ?? "";
                    $cognom2 = $row['cognom2'] ?? "";
                    $anyNaixement = $row['anyNaixement'] ?? "";
                    $relacio_parentiu = $row['relacio_parentiu'] ?? "";
                    $idParent = $row['idParent'] ?? "";

                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($nom) . '</td>';
                    echo '<td>' . htmlspecialchars($cognom1 . ' ' . $cognom2) . '</td>';
                    echo '<td>' . htmlspecialchars($anyNaixement) . '</td>';
                    echo '<td>' . htmlspecialchars($relacio_parentiu) . '</td>';
                    echo '<td><a href="https://memoriaterrassa.cat/gestio/tots/fitxa/familiar/modifica/' . $idParent . "/" . $id . '" class="btn btn-primary">Modifica</a></td>';
                    echo '<td><a href="https://memoriaterrassa.cat/gestio/tots/fitxa/familiar/elimina/' . $id . '" class="btn btn-danger">Elimina</a></td>';
                    echo '</tr>';
                }

                echo '</tbody>';
                echo '</table>';
            } else {
                echo '<p>No s\'han trobat familiars.</p>';
            }
            ?>
        </div>
    </div>
</div>