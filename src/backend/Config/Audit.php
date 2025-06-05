<?php

namespace App\Config;

use PDO;
use PDOException;

class Audit
{
    public static function registrarCanvi(PDO $conn, int $idUser, string $operacio, string $detalls, string $taulaAfectada, int $registreId): bool
    {
        $dataHora = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

        $sql = "INSERT INTO control_registre_canvis (
                    idUser, operacio, detalls, taula_afectada, registre_id, dataHora, ip_usuari, user_agent
                ) VALUES (
                    :idUser, :operacio, :detalls, :taulaAfectada, :registreId, :dataHora, :ipUsuari, :userAgent
                )";

        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
            $stmt->bindParam(':operacio', $operacio, PDO::PARAM_STR);
            $stmt->bindParam(':detalls', $detalls, PDO::PARAM_STR);
            $stmt->bindParam(':taulaAfectada', $taulaAfectada, PDO::PARAM_STR);
            $stmt->bindParam(':registreId', $registreId, PDO::PARAM_INT);
            $stmt->bindParam(':dataHora', $dataHora, PDO::PARAM_STR);
            $stmt->bindParam(':ipUsuari', $ip, PDO::PARAM_STR);
            $stmt->bindParam(':userAgent', $userAgent, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error auditant canvi: " . $e->getMessage());
            return false;
        }
    }
}
