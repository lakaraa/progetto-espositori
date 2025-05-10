<?php
include_once("../../config.php");
include_once("../../queries.php");
include_once("../../template_header.php");
if (isset($_GET['username'])) {
    $username = $_GET['username'];
    $filepath = "../../uploads/cv/cv_" . $username . ".pdf";
    
    if (file_exists($filepath)) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="cv_' . $username . '.pdf"');
        header('Content-Length: ' . filesize($filepath));
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        header('Access-Control-Allow-Origin: *');
        
        readfile($filepath);
        exit;
    }
}
http_response_code(404);
echo "File non trovato.";
?> 
<?php
include_once("../../template_footer.php");
?>