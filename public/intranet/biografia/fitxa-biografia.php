<?php

$idPersona = $routeParams[0]; // Segundo, por ejemplo, 2
$idPersona = (int) $idPersona;
$biografiaCa = "";
$biografiaEs = "";
$biografiaEn = "";

// Verificar si es un número entero válido
if (!is_int($idPersona)) {
    // Si no es un número entero o es menor o igual a cero, detener la ejecución
    header("Location: /gestio");
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

$query = "SELECT 
            b.id, b.idRepresaliat, b.biografiaCa, b.biografiaEs, b.biografiaEn 	
            FROM db_biografies AS b
            WHERE b.idRepresaliat = :idRepresaliat";
$stmt = $conn->prepare($query);
$stmt->bindParam(':idRepresaliat', $idPersona, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $biografiaCa = $row['biografiaCa'] ?? null;
        $biografiaEs =  $row['biografiaEs'] ?? null;
        $biografiaEn =  $row['biografiaEn'] ?? null;
        $idBiografia = $row['id'] ?? null;
    }
}

?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <div class="container">
        <div class="row d-flex flex-column">
            <h2>Gestió biografia</h2>
            <h4 id="fitxaNomCognoms">Fitxa: <a href="https://memoriaterrassa.cat/fitxa/<?php echo $idPersona; ?>" target="_blank"><?php echo $nom . " " . $cognom1 . " " . $cognom2; ?></a></h4>
            <hr>
            <?php
            if (!$biografiaCa) {
            ?>
                <div class="col-md-12 " style="margin-top:20px;margin-bottom:20px">
                    <a href="https://memoriaterrassa.cat/gestio/tots/fitxa/biografia/nova-biografia/<?php echo $idPersona; ?>" class="btn btn-success">Afegir biografia</a>
                </div>
                <p>Biografia sense publicar en català</p>

            <?php
            } else {
            ?>
                <div class="col-md-12" style="margin-top:20px;margin-bottom:20px">
                    <a href="https://memoriaterrassa.cat/gestio/tots/fitxa/biografia/modifica-biografia/<?php echo $idBiografia; ?>/<?php echo $idPersona; ?>" class="btn btn-success">Modificar biografia</a>
                </div>
                <hr>
                <h4>Biografia en català:</h4>
                <div class="col-md-12" style="margin-top:20px;margin-bottom:20px">
                    <?php echo $biografiaCa; ?>
                </div>

            <?php
            }

            ?>

            <?php
            if (!$biografiaEs) {
            ?>
                <p>Biografia sense publicar en castellà</p>
            <?php
            } else {
            ?>
                <hr>
                <h4>Biografia en castellà:</h4>
                <div class="col-md-12" style="margin-top:20px;margin-bottom:20px">
                    <?php echo $biografiaEs; ?>
                </div>
            <?php
            }

            ?>

        </div>
    </div>
</div>