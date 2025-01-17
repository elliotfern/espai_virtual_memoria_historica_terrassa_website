<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <div class="container">
        <div class="row">
            <h2>Control registre de canvis a les bases de dades</h2>

            <div class="col-md-4"><a href="<?php APP_SERVER; ?>/gestio/control-acces" class="btn btn-success" role="button">Veure registre control accés</a></div>

            <?php
            $query = "SELECT 
            c.id,
            c.tipusOperacio,
            c.dataHoraCanvi,
            u.nom AS nomEditor,
            r.id AS idFitxa,
            r.nom,
            r.cognom1,
            r.cognom2
            FROM control_registre_canvis AS c
            LEFT JOIN db_dades_personals AS r ON c.idPersonaFitxa = r.id
            LEFT JOIN auth_users AS u ON c.idUser = u.id
            ORDER BY c.id DESC";

            $stmt = $conn->prepare($query);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                echo '<table class="table" style="margin-top:25px;margin-bottom:30px">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Fitxa represaliat</th>';
                echo '<th>Editor canvis</th>';
                echo '<th>Tipus operació</th>';
                echo '<th>Dia i hora (hora local Barcelona)</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $idFitxa = $row['idFitxa'] ?? "";
                    $nom = $row['nom'] ?? "";
                    $cognom1 = $row['cognom1'] ?? "";
                    $cognom2 = $row['cognom2'] ?? "";
                    $tipusOperacio = $row['tipusOperacio'] ?? "";
                    $nomEditor = $row['nomEditor'] ?? "";
                    $dataHoraCanvi = $row['dataHoraCanvi'] ?? "";
                    $dateTime = new DateTime($dataHoraCanvi);
                    $dataHoraCanviFormatada = $dateTime->format('d/m/Y H:i:s');

                    echo '<tr>';
                    echo '<td><a href="https://memoriaterrassa.cat/fitxa/' . $idFitxa . '" target=_blank>' . htmlspecialchars($nom) . ' ' . htmlspecialchars($cognom1 . ' ' . $cognom2) . '</a></td>';
                    echo '<td>' . htmlspecialchars($nomEditor) . '</td>';
                    echo '<td>' . htmlspecialchars($tipusOperacio) . '</td>';
                    echo '<td>' . htmlspecialchars($dataHoraCanviFormatada) . '</td>';
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