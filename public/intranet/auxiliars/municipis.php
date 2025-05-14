<?php
require_once APP_ROOT . '/public/intranet/includes/header.php';
?>

<div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
    <div class="container">
        <div class="row">
            <h2>Gestió base de dades auxiliars</h2>
            <h4>Llistat de municipis</h4>

            <?php
            $query = "SELECT 
            m.id,
            m.ciutat,
            c.comarca,
            p.provincia,
            co.comunitat,
            e.estat
            FROM  aux_dades_municipis AS m
            LEFT JOIN aux_dades_municipis_comarca AS c ON m.comarca = c.id
            LEFT JOIN aux_dades_municipis_provincia AS p ON m.provincia = p.id
            LEFT JOIN aux_dades_municipis_comunitat AS co ON m.comunitat = co.id
            LEFT JOIN aux_dades_municipis_estat AS e ON m.estat = e.id
            ORDER BY m.ciutat ASC";

            $stmt = $conn->prepare($query);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                echo '<table class="table" style="margin-top:25px;margin-bottom:30px">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Municipi</th>';
                echo '<th>Comarca</th>';
                echo '<th>Provincia</th>';
                echo '<th>Comunitat autonòma</th>';
                echo '<th>Estat</th>';
                echo '<th>Accions</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $ciutat = $row['ciutat'] ?? "";
                    $comarca = $row['comarca'] ?? "";
                    $provincia = $row['provincia'] ?? "";
                    $comunitat = $row['comunitat'] ?? "";
                    $estat = $row['estat'] ?? "";
                    $idMunicipi = $row['id'] ?? "";

                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($ciutat) . '</td>';
                    echo '<td>' . htmlspecialchars($comarca) . '</td>';
                    echo '<td>' . htmlspecialchars($provincia) . '</td>';
                    echo '<td>' . htmlspecialchars($comunitat) . '</td>';
                    echo '<td>' . htmlspecialchars($estat) . '</td>';
                    echo '<td><a href="' . APP_SERVER . '/gestio/municipi/modifica/' . $idMunicipi . '" class="btn btn-primary">Modificar</a></td>';
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