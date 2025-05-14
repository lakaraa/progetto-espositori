<?php
include_once '../../config.php';
include_once '../../queries.php';
include_once '../../session.php';

// Gestione delle richieste AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Disabilita l'output di errori PHP
    error_reporting(0);
    ini_set('display_errors', 0);
    ob_clean();
    
    // Imposta gli header per JSON
    header('Content-Type: application/json');
    header('Cache-Control: no-cache, must-revalidate');
    
    // Pulisci qualsiasi output precedente
    if (ob_get_length()) ob_clean();

    try {
        // Verifica che la richiesta sia AJAX
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
            throw new Exception('Richiesta non valida');
        }

        $categoria_id = filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT);
        if (!$categoria_id) {
            throw new Exception('ID categoria non valido');
        }

        $esposizioni = getEsposizioniByCategoria($pdo, $categoria_id);
        if ($esposizioni === false) {
            throw new Exception('Errore nel recupero delle esposizioni');
        }

        $categoria_selezionata = getCategoriaById($pdo, $categoria_id);
        if (!$categoria_selezionata) {
            throw new Exception('Categoria non trovata');
        }

        echo json_encode([
            'success' => true,
            'esposizioni' => $esposizioni,
            'categoria' => $categoria_selezionata
        ]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    } catch (Error $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Errore interno del server'
        ]);
    }
    exit;
}
include_once '../../template_header.php';

// Recupera tutte le categorie per il select
$categorie = getCategorie($pdo);
?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(/progetto-espositori/resources/images/sfondo.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Esposizioni per Categoria</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="/progetto-espositori/index.php">Home</a></li>
        <li class="active">Esposizioni per Categoria</li>
    </ul>
</section>

<!-- Main Content-->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h2>Esposizioni per Categoria</h2>
        <p>Visualizza tutte le esposizioni filtrate per categoria di appartenenza.</p>
        
        <!-- Form di selezione categoria -->
        <div class="row justify-content-center mb-4">
            <div class="col-md-6">
                <form id="form-categoria" class="form-inline justify-content-center">
                    <div class="form-group mx-sm-3 mb-2">
                        <label for="categoria_id" class="mr-2">Seleziona Categoria:</label>
                        <select name="categoria_id" id="categoria_id" class="form-control">
                            <option value="">Seleziona una categoria</option>
                            <?php foreach ($categorie as $categoria): ?>
                                <option value="<?= $categoria['Id_Categoria'] ?>">
                                    <?= htmlspecialchars($categoria['Nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary mb-2">Visualizza</button>
                </form>
            </div>
        </div>

        <!-- Messaggio di stato -->
        <div id="form-message"></div>

        <!-- Titolo categoria selezionata -->
        <h3 id="categoria-titolo" class="mb-4" style="display: none;"></h3>

        <!-- Tabella risultati -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Titolo</th>
                        <th>Espositore</th>
                        <th>Area</th>
                    </tr>
                </thead>
                <tbody id="tabella-esposizioni">
                    <tr>
                        <td colspan="3">Seleziona una categoria per visualizzare le esposizioni.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function() {
    $('#form-categoria').on('submit', function(e) {
        e.preventDefault();
        
        const categoriaId = $('#categoria_id').val();
        if (!categoriaId) {
            $('#form-message').html('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Seleziona una categoria.</div>');
            return;
        }

        // Mostra un indicatore di caricamento
        $('#form-message').html('<div class="alert alert-info"><i class="fas fa-spinner fa-spin"></i> Caricamento in corso...</div>');

        $.ajax({
            url: '',
            method: 'POST',
            data: { categoria_id: categoriaId },
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    // Aggiorna il titolo della categoria
                    if (response.categoria) {
                        $('#categoria-titolo')
                            .text('Esposizioni nella categoria: ' + response.categoria.Nome)
                            .show();
                    } else {
                        $('#categoria-titolo').hide();
                    }

                    // Aggiorna la tabella
                    const tbody = $('#tabella-esposizioni');
                    tbody.empty();

                    if (response.esposizioni && response.esposizioni.length > 0) {
                        response.esposizioni.forEach(function(esposizione) {
                            tbody.append(`
                                <tr>
                                    <td>${esposizione.titolo || 'Nessun titolo'}</td>
                                    <td>${esposizione.cognome} ${esposizione.nome}</td>
                                    <td>${esposizione.area || 'Nessuna area assegnata'}</td>
                                </tr>
                            `);
                        });
                    } else {
                        tbody.html(`
                            <tr>
                                <td colspan="3" class="text-center">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        Non ci sono esposizioni disponibili per questa categoria.
                                    </div>
                                </td>
                            </tr>
                        `);
                    }

                    $('#form-message').html('<div class="alert alert-success"><i class="fas fa-check-circle"></i> Dati aggiornati con successo.</div>');
                    setTimeout(() => {
                        $('#form-message').fadeOut(400, function() {
                            $(this).empty().show();
                        });
                    }, 3000);
                } else {
                    $('#form-message').html(`<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ${response.error || 'Errore nel recupero dei dati.'}</div>`);
                }
            },
            error: function(xhr, status, error) {
                console.error("Errore AJAX:", status, error);
                let errorMessage = 'Errore di comunicazione con il server.';
                
                if (xhr.responseText) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.error) {
                            errorMessage = response.error;
                        }
                    } catch (e) {
                        console.error("Errore nel parsing della risposta:", e);
                        // Se non è possibile parsare la risposta come JSON, mostra un messaggio generico
                        errorMessage = 'Errore nella comunicazione con il server. Riprova più tardi.';
                    }
                }
                
                $('#form-message').html(`<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ${errorMessage}</div>`);
            }
        });
    });
});
</script>

<?php include_once '../../template_footer.php'; ?> 