<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <div class="container">
        <div class="row">
            <h2>Control d'accés a la intranet</h2>

            <?php
            $query = "SELECT 
            c.id,
            c.idUser,
            c.tipusOperacio,
            c.dataAcces,
            u.nom AS nomEditor
            FROM auth_users_control_acces AS c
            LEFT JOIN auth_users AS u ON c.idUser = u.id
            ORDER BY c.id DESC";

            $stmt = $conn->prepare($query);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                echo '<table class="table" style="margin-top:25px;margin-bottom:30px">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Nom usuari</th>';
                echo '<th>Tipus operació</th>';
                echo '<th>Dia i hora (hora local Barcelona)</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $tipusOperacio = $row['tipusOperacio'] ?? "";

                    if ($tipusOperacio == 1) {
                        $operacio = '<button type="button" class="btn btn-primary">Accés a la intranet</button>';
                    } else {
                        $operacio = '<button type="button" class="btn btn-danger">Error en la contrasenya</button>';
                    }
                    $nomEditor = $row['nomEditor'] ?? "";
                    $dataHoraCanvi = $row['dataAcces'] ?? "";
                    $dateTime = new DateTime($dataHoraCanvi);
                    $dataHoraCanviFormatada = $dateTime->format('d/m/Y H:i:s');

                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($nomEditor) . '</td>';
                    echo '<td>' . $operacio . '</td>';
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