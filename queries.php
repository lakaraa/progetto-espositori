<?php

function getManifestazioni($pdo) {
    $sql = "SELECT * FROM manifestazione";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}