<?php
include_once '../config.php';
include_once '../session.php';
include_once '../queries.php';
include_once '../template_header.php';
?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(/progetto-espositori/resources/images/bg-breadcrumbs-07-1920x480.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">I Miei Contributi</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../index.php">Home</a></li>
        <li><a href="dashboard_espositore.php">Dashboard</a></li>
        <li class="active">Contributi</li>
    </ul>
</section>

<!-- Contributi Section-->
<section class="section section-lg bg-default">
    <div class="container">
        <div class="text-center">
            <h2 class="heading-decoration"><span class="text-primary">I Miei</span> Contributi</h2>
            <p class="subtitle">Visualizza e gestisci tutti i tuoi contributi</p>
        </div>

        <table class="custom-table">
            <thead>
                <tr>                                 
                    <th>Manifestazione</th>
                    <th>Data Manifestazione</th>
                    <th>Titolo</th>
                    <th>Sintesi</th>
                    <th>Stato</th>
                </tr>
            </thead>
            <tbody id="contributi">
                <!-- I contributi verranno caricate qui tramite AJAX -->
            </tbody>
        </table>
    </div>
</section>

<style>
.text-info {
    color: #17a2b8 !important;
}

.card {
    transition: transform 0.2s;
    margin-bottom: 20px;
    background-color: white;
}

.card-title, .card-text {
    color: rgb(34, 45, 79);
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.table th {
    background-color: #f8f9fa;
}
.custom-table {
    font-family: Arial, Helvetica, sans-serif;
    border-collapse: collapse;
    width: 100%;
    background-color: transparent;
    color: white;
}

.custom-table th,
.custom-table td {
    border: none;
    padding: 12px;
}

.custom-table thead th {
    background-color: transparent;
    color: white;
    font-weight: bold;
}

.custom-table tbody tr:nth-child(even) {
    background-color: rgb(34, 45, 79);
}

.custom-table tbody tr:nth-child(odd) {
    background-color: rgb(44, 56, 99);
}

.custom-table tbody tr:hover {
    background-color: rgb(166, 169, 181);
}

.btn-primary {
    background-color: #4CAF50;
    border: none;
    color: white;
    padding: 8px 16px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 14px;
    margin: 4px 2px;
    cursor: pointer;
    border-radius: 4px;
}

.btn-primary:hover {
    background-color: #45a049;
}

.text-success {
    color: #4CAF50 !important;
}

.text-warning {
    color: #ffc107 !important;
}

.text-danger {
    color: #dc3545 !important;
}

.text-info {
    color: #17a2b8 !important;
}

.bg-warning-light {
    background-color: #fff3cd;
}

.bg-success-light {
    background-color: #d4edda;
}

.bg-danger-light {
    background-color: #f8d7da;
}
</style>

<script>
$(document).ready(function() {
    // Carica i contributi
    $.ajax({
        url: 'get_contributi.php',
        method: 'GET',
        success: function(data) {
            $('#contributi').html(data);
        },
        error: function(xhr, status, error) {
            console.log("Errore AJAX contributi: ", error);
        }
    });
});

function toggleText(id, fullText) {
    const element = document.getElementById(id);
    const button = element.nextElementSibling;
    
    if (element.classList.contains('sintesi-expanded') || element.classList.contains('titolo-expanded')) {
        // Collapse
        element.textContent = fullText.substring(0, 10) + '...';
        element.classList.remove('sintesi-expanded', 'titolo-expanded');
        button.textContent = 'Leggi di più';
    } else {
        // Expand
        element.textContent = fullText;
        element.classList.add(id.startsWith('sintesi') ? 'sintesi-expanded' : 'titolo-expanded');
        button.textContent = 'Mostra meno';
    }
}
</script>

<?php
include_once('../template_footer.php');
?> 