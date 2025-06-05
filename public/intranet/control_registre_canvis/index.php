<?php

use App\Config\DatabaseConnection;

$conn = DatabaseConnection::getConnection();

if (!$conn) {
    die("No se pudo establecer conexión a la base de datos.");
}

require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <div class="container">
        <div class="row">
            <h2>Control registre de canvis a les bases de dades</h2>

            <div class="col-md-4"><a href="<?php APP_SERVER; ?>/gestio/control-acces" class="btn btn-success" role="button">Veure registre control accés</a></div>

            <?php
            $query = "SELECT 
            c.id, detalls, c.taula_afectada, c.registre_id, c.dataHora, c.ip_usuari, c.user_agent, c.operacio,
            u.nom AS nomEditor
            FROM control_registre_canvis AS c
            LEFT JOIN auth_users AS u ON c.idUser = u.id
            ORDER BY c.id DESC";

            $stmt = $conn->prepare($query);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                echo '<table class="table" style="margin-top:25px;margin-bottom:30px">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Usuari</th>';
                echo '<th>Taula afectada</th>';
                echo '<th>Tipus operació</th>';
                echo '<th>Detalls</th>';
                if ($isAdmin):
                    echo '<th>IP usuari</th>';
                    echo '<th>Navegador</th>';
                endif;
                echo '<th>Dia i hora (hora local Barcelona)</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $idFitxa = $row['idFitxa'] ?? "";
                    $nom = $row['nom'] ?? "";
                    $cognom1 = $row['cognom1'] ?? "";
                    $cognom2 = $row['cognom2'] ?? "";
                    $tipusOperacio = $row['operacio'] ?? "";
                    $taula_afectada = $row['taula_afectada'] ?? "";
                    $detalls = $row['detalls'] ?? "";
                    $ip_usuari = $row['ip_usuari'] ?? "";
                    $user_agent = $row['user_agent'] ?? "";
                    $nomEditor = $row['nomEditor'] ?? "";
                    $dataHoraCanvi = $row['dataHora'] ?? "";
                    $dateTime = new DateTime($dataHoraCanvi);
                    $dataHoraCanviFormatada = $dateTime->format('d/m/Y H:i:s');
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($nomEditor) . '</td>';
                    echo '<td>' . htmlspecialchars($taula_afectada) . '</td>';
                    echo '<td>' . htmlspecialchars($tipusOperacio) . '</td>';
                    echo '<td>' . htmlspecialchars($detalls) . '</td>';
                    if ($isAdmin):
                        echo '<td>' . htmlspecialchars($ip_usuari) . '</td>';
                        echo '<td>' . htmlspecialchars($user_agent) . '</td>';
                    endif;
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