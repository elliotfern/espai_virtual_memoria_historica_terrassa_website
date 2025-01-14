<?php

$idPersona = $routeParams[0]; // Segundo, por ejemplo, 2
$idPersona = (int) $idPersona;

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

?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <div class="container">
        <div class="row">
            <h2>Gestió fonts documentals</h2>
            <h4 id="fitxaNomCognoms">Fitxa: <a href="https://memoriaterrassa.cat/fitxa/<?php echo $idPersona; ?>" target="_blank"><?php echo $nom . " " . $cognom1 . " " . $cognom2; ?></a></h4>

            <hr>
            <h4>Bibliografia:</h4>

            <div class="col-md-4" style="margin-top:20px;margin-bottom:20px">
                <a href="https://memoriaterrassa.cat/gestio/tots/fitxa/fonts-documentals/nou-llibre/<?php echo $idPersona; ?>" class="btn btn-success">Afegir bibliografia a la fitxa represaliat</a>
            </div>

            <?php
            $query = "SELECT 
            l.id,
            l.pagina,
            m.ciutat,
            ld.id AS idLlibre,
            ld.llibre,
            ld.autor,
            ld.editorial,
            ld.any,
            ld.volum
            FROM aux_bibliografia_llibres AS l
            LEFT JOIN aux_bibliografia_llibre_detalls AS ld ON l.llibre = ld.id
            LEFT JOIN aux_dades_municipis AS m ON ld.ciutat = m.id
            WHERE l.idRepresaliat = :idRepresaliat";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':idRepresaliat', $idPersona, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                echo '<table class="table" style="margin-top:25px;margin-bottom:30px">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Llibre/Article</th>';
                echo '<th>Pàgines</th>';
                echo '<th>Modifica</th>';
                echo '<th>Elimina</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $pagina = $row['pagina'] ?? '-';
                    $ciutat = $row['ciutat'] ?? null;
                    $idLlibre = $row['idLlibre'] ?? null;
                    $llibre = $row['llibre'] ?? null;
                    $autor = $row['autor'] ?? null;
                    $editorial = $row['editorial'] ?? null;
                    $any = $row['any'] ?? null;
                    $volum = $row['volum'] ?? null;

                    echo '<tr>';
                    echo '<td>' . $autor . '. <i>' . $llibre  . '.</i> ' . $ciutat . ', ' . $editorial . ' núm. ' . $volum . ', ' . $any . '</td>';
                    echo '<td>' . $pagina .  '</td>';
                    echo '<td><a href="https://memoriaterrassa.cat/gestio/tots/fitxa/fonts-documentals/modifica-llibre/' . $idPersona . "/" . $idLlibre . '" class="btn btn-primary">Modifica</a></td>';
                    echo '<td><a href="https://memoriaterrassa.cat/gestio/tots/fitxa/fonts-documentals/elimina/' . $idLlibre . '" class="btn btn-danger">Elimina</a></td>';
                    echo '</tr>';
                }

                echo '</tbody>';
                echo '</table>';
            } else {
                echo '<p>No s\'han trobat fonts bibliogràfiques.</p>';
            }
            ?>

            <hr>
            <h4>Fonts arxivístiques:</h4>

            <div class="col-md-4" style="margin-top:20px;margin-bottom:20px">
                <a href="https://memoriaterrassa.cat/gestio/tots/fitxa/fonts-documentals/nou-arxiu/<?php echo $idPersona; ?>" class="btn btn-success">Afegir font arxivística</a>
            </div>

            <?php
            $query = "SELECT 
            a.id,
            a.referencia,
            a.idRepresaliat,
            m.ciutat,
            c.arxiu,
            c.codi
            FROM aux_bibliografia_arxius AS a
            LEFT JOIN aux_bibliografia_arxius_codis AS c ON a.codi = c.id
            LEFT JOIN aux_dades_municipis AS m ON c.ciutat = m.id
            WHERE a.idRepresaliat = :idRepresaliat";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':idRepresaliat', $idPersona, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                echo '<table class="table" style="margin-top:25px;margin-bottom:30px">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Referència</th>';
                echo '<th>Arxiu</th>';
                echo '<th>Codi</th>';
                echo '<th>Ciutat arxiu</th>';
                echo '<th>Modifica</th>';
                echo '<th>Elimina</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $referencia = $row['referencia'] ?? '-';
                    $ciutat_arxiu = $row['ciutat'] ?? null;
                    $codi = $row['codi'] ?? null;
                    $arxiu = $row['arxiu'] ?? null;
                    $idArxiu = $row['id'] ?? null;

                    echo '<tr>';
                    echo '<td>' . $referencia . '</td>';
                    echo '<td>' . $arxiu .  '</td>';
                    echo '<td>' . $codi .  '</td>';
                    echo '<td>' . $ciutat_arxiu .  '</td>';
                    echo '<td><a href="https://memoriaterrassa.cat/gestio/tots/fitxa/fonts-documentals/modifica-arxiu/' . $idPersona . "/" . $idArxiu . '" class="btn btn-primary">Modifica</a></td>';
                    echo '<td><a href="https://memoriaterrassa.cat/gestio/tots/fitxa/fonts-documentals/elimina/' . $idArxiu . '" class="btn btn-danger">Elimina</a></td>';
                    echo '</tr>';
                }

                echo '</tbody>';
                echo '</table>';
            } else {
                echo '<p>No s\'han trobat fonts bibliogràfiques.</p>';
            }
            ?>

        </div>
    </div>
</div>