<?php
include_once '../../config.php';
include_once '../../queries.php';
include_once '../../session.php';
include_once '../../template_header.php';

$anno = $_GET['anno'] ?? date('Y');
$partecipanti = getQueryPrenotazioniPerData($pdo, $anno);
?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(/progetto-espositori/resources/images/sfondo.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Andamento Partecipanti</h2>
        <ul class="breadcrumbs-custom-path">
            <li><a href="/progetto-espositori/index.php">Home</a></li>
            <li class="active">Andamento Partecipanti</li>
        </ul>
    </div>
</section>

<!-- Main Content-->
<section class="section section-lg bg-default">
    <div class="container">
        <h2>Andamento Partecipanti</h2>
        <p>Visualizza l'andamento dei partecipanti per ogni anno.</p>
        <div class="dashboard-container">
            <!-- Form di selezione anno premium -->
            <div class="dashboard-card year-selection-card">
                <div class="form-header">
                    <div class="form-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3 class="form-title">Analisi Partecipanti</h3>
                </div>
                
                <form method="GET" class="year-form">
                    <div class="form-group">
                        <label for="anno" class="form-label">Seleziona l'anno da analizzare</label>
                        <div class="input-container">
                            <div class="input-icon">
                                <i class="fas fa-calendar-day" style="margin-left: 10px;"></i>
                            </div>
                            <input type="number" name="anno" id="anno" class="form-input" 
                                value="<?= htmlspecialchars($anno) ?>" min="2000" max="2100"
                                aria-label="Seleziona l'anno da visualizzare" style="width: 100px;">
                            
                        </div>
                        <button type="submit" class="submit-button input-container form-input" style="text-align: center;">
                            <i class="fas fa-chart-line" style="margin-left: 10px;"></i>
                            <span style="text-align: center;">Visualizza Dati</span>
                        </button>
                        <div class="form-hint">Seleziona un anno compreso tra il 2000 e il 2100</div>
                    </div>
                </form>
            </div>


            <!-- Grafico -->
            <div class="chart-container">
                <div class="chart-header">
                    <i class="fas fa-chart-line"></i>
                    <h4>Andamento Partecipanti <?= htmlspecialchars($anno) ?></h4>
                </div>
                <div class="chart-wrapper">
                    <canvas id="partecipantiChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</section>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('partecipantiChart').getContext('2d');
        
        // Dati dal PHP
        const dates = <?= json_encode(array_column($partecipanti, 'Data')) ?>;
        const counts = <?= json_encode(array_column($partecipanti, 'NumeroPartecipanti')) ?>;
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Numero Partecipanti',
                    data: counts,
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#3498db',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                }
            }
        });
    });
</script>

<style>
    .dashboard-container {
        padding: 2rem;
        max-width: 1400px;
        margin: 0 auto;
    }
    
    .year-selection-form {
        background: white;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
        margin-bottom: 2rem;
    }
    
    .year-selection-form:hover {
        transform: translateY(-5px);
    }
    
    .form-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        color: #2c3e50;
    }
    
    .form-header i {
        font-size: 1.5rem;
        color: #3498db;
    }
    
    .form-header h4 {
        margin: 0;
        font-size: 1.3rem;
    }
    
    .input-group {
        margin-bottom: 1rem;
    }
    
    .input-group-text {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        color: #6c757d;
    }
    
    .form-control {
        border: 1px solid #dee2e6;
        padding: 0.75rem;
        font-size: 1.1rem;
        transition: border-color 0.3s ease;
    }
    
    .form-control:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }
    
    .button {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 5px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .button-primary {
        background: #3498db;
        color: white;
    }
    
    .button-primary:hover {
        background: #2980b9;
        transform: translateY(-2px);
    }
    
    .form-footer {
        text-align: center;
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .chart-container {
        background: white;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .chart-container:hover {
        transform: translateY(-5px);
    }
    
    .chart-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        color: #2c3e50;
    }
    
    .chart-header i {
        font-size: 1.5rem;
        color: #3498db;
    }
    
    .chart-header h4 {
        margin: 0;
        font-size: 1.3rem;
    }
    
    .chart-wrapper {
        position: relative;
        height: 400px;
        width: 100%;
    }
    
    @media (max-width: 768px) {
        .dashboard-container {
            padding: 1rem;
        }
        
        .year-selection-form {
            padding: 1.5rem;
        }
        
        .chart-wrapper {
            height: 300px;
        }
    }

    /* Stile generale della card */
    .year-selection-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(5px);
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    .year-selection-card:hover {
        box-shadow: 0 14px 28px rgba(0, 0, 0, 0.12), 0 10px 10px rgba(0, 0, 0, 0.08);
        transform: translateY(-3px);
    }

    /* Header della form */
    .form-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .form-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #4e66f8 0%, #6f42c1 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        box-shadow: 0 4px 12px rgba(78, 102, 248, 0.3);
    }

    .form-title {
        margin: 0;
        color: #2c3e50;
        font-size: 1.5rem;
        font-weight: 600;
    }

    /* Stile del form */
    .year-form {
        width: 100%;
    }

    .form-group {
        margin-bottom: 0;
    }

    .form-label {
        display: block;
        margin-bottom: 0.75rem;
        color: #6c757d;
        font-size: 0.95rem;
        font-weight: 500;
    }

    .input-container {
        display: flex;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .input-container:focus-within {
        box-shadow: 0 4px 20px rgba(78, 102, 248, 0.2);
    }

    .input-icon {
        background-color: #f0f4ff;
        padding: 0 1rem;
        display: flex;
        align-items: center;
        color: #4e66f8;
        font-size: 1.2rem;
    }

    .form-input {
        flex: 1;
        padding: 1rem;
        border: none;
        font-size: 1.1rem;
        font-weight: 500;
        text-align: center;
        background-color: white;
        transition: all 0.3s ease;
    }

    .form-input:focus {
        outline: none;
        background-color: #fefeff;
    }

    .submit-button {
        background: linear-gradient(135deg, #4e66f8 0%, #6f42c1 100%);
        color: white;
        border: none;
        padding: 0 1.5rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .submit-button:hover {
        background: linear-gradient(135deg, #3a56e6 0%, #5d35b0 100%);
        transform: translateY(-1px);
    }

    .submit-button i {
        font-size: 1.1rem;
    }

    .form-hint {
        margin-top: 0.75rem;
        font-size: 0.85rem;
        color: #adb5bd;
        text-align: center;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .year-selection-card {
            padding: 1.5rem;
        }
        
        .input-container {
            flex-direction: column;
        }
        
        .input-icon {
            justify-content: center;
            padding: 0.5rem;
        }
        
        .form-input {
            padding: 0.75rem;
        }
        
        .submit-button {
            justify-content: center;
            padding: 0.75rem;
        }
    }
</style>

<?php include_once '../../template_footer.php'; ?>