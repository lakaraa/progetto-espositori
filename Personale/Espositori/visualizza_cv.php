<?php
error_reporting(E_ALL); // Report all errors and warnings
ini_set('display_errors', 1); // Display errors on the screen

include_once("../../config.php");
include_once("../../queries.php");
include_once("../../template_header.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $username = getUsername($pdo, $id);

    if ($username) {
        $curriculumPath = "../../uploads/cv/cv_" . $username . ".pdf";

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
<?php
include_once("../../template_footer.php");
?>