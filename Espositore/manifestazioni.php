<?php
include_once '../config.php';
include_once '../session.php';
include_once '../queries.php';
include_once '../template_header.php';
?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(/progetto-espositori/resources/images/sfondo.jpg);">
       <div class="container">
        <h2 class="breadcrumbs-custom-title">Manifestazioni Disponibili</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="../index.php">Home</a></li>
        <li><a href="dashboard_espositore.php">Dashboard</a></li>
        <li class="active">Manifestazioni</li>
    </ul>
</section>

<!-- Manifestazioni Section-->
<section class="section section-lg bg-default">
    <div class="container">
        <div class="text-center">
            <h2 class="heading-decoration"><span class="text-primary">Manifestazioni</span> Disponibili</h2>
            <p class="subtitle">Visualizza e candidati alle manifestazioni disponibili</p>
        </div>

        <table class="custom-table">
            <thead>
                <tr>                                 
                    <th>Nome</th>
                    <th>Data Inizio</th>
                    <th>Durata</th>
                    <th>Descrizione</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody id="manifestazioni">
                <!-- Le manifestazioni verranno caricate qui tramite AJAX -->
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
    // Carica le manifestazioni
    $.ajax({
        url: 'get_manifestazioni.php',
        method: 'GET',
        success: function(data) {
            $('#manifestazioni').html(data);
        },
        error: function(xhr, status, error) {
            console.log("Errore AJAX manifestazioni: ", error);
        }
    });
});

function toggleText(id, fullText) {
    const element = document.getElementById(id);
    const button = element.nextElementSibling;
    
    if (element.classList.contains('descrizione-expanded')) {
        // Collapse
        element.textContent = fullText.substring(0, 10) + '...';
        element.classList.remove('descrizione-expanded');
        button.textContent = 'Leggi di più';
    } else {
        // Expand
        element.textContent = fullText;
        element.classList.add('descrizione-expanded');
        button.textContent = 'Mostra meno';
    }
}

function candidati(idManifestazione) {
    if (confirm('Sei sicuro di voler candidarti a questa manifestazione?')) {
        $.ajax({
            url: 'candidati_manifestazione.php',
            method: 'POST',
            data: { id_manifestazione: idManifestazione },
            success: function(response) {
                if (response.success) {
                    alert('Candidatura inviata con successo!');
                    // Ricarica la tabella
                    $.ajax({
                        url: 'get_manifestazioni.php',
                        method: 'GET',
                        success: function(data) {
                            $('#manifestazioni').html(data);
                        }
                    });
                } else {
                    alert(response.message || 'Errore durante l\'invio della candidatura');
                }
            },
            error: function(xhr, status, error) {
                alert('Errore durante l\'invio della candidatura');
                console.log("Errore AJAX candidatura: ", error);
            }
        });
    }
}
</script>

<?php
include_once('../template_footer.php');
?> 