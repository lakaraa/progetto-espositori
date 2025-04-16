<?php
include_once("../../config.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $pdo->prepare("SELECT Username FROM utente WHERE Id_Utente = :id AND Ruolo = 'Espositore'");
    $stmt->execute(['id' => $id]);
    $espositore = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($espositore && !empty($espositore['Username'])) {
        $username = $espositore['Username'];
        $curriculumPath = "../../uploads/cv_" . $username . ".pdf";

        if (file_exists($curriculumPath)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="cv_' . $username . '.pdf"');
            readfile($curriculumPath);
            exit;
        } else {
            echo "CV non trovato.";
        }
    } else {
        echo "Espositore non trovato.";
    }
} else {
    echo "ID mancante.";
}
?>
