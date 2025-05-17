<?php
include_once 'session.php';
include_once 'config.php';
?>
<!DOCTYPE html>
<html class="wide wow-animation" lang="en">
<head>
    <title>Associazione Culturale - Manifestazione Pubblica</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="<?php echo $base_path; ?>resources/images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Lato:400,700%7COpen+Sans:400,600,700%7CSource+Code+Pro:300,400,500,600,700,900%7CNothing+You+Could+Do%7CPoppins:400,500">
    <link rel="stylesheet" href="<?php echo $base_path; ?>resources/css/bootstrap.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>resources/css/fonts.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>resources/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@6.5.95/css/materialdesignicons.min.css">
    <style>
        .ie-panel{display: none;background: #212121;padding: 10px 0;box-shadow: 3px 3px 5px 0 rgba(0,0,0,.3);clear: both;text-align:center;position: relative;z-index: 1;} 
        html.ie-10 .ie-panel, html.lt-ie-10 .ie-panel {display: block;}
        .manifestazione-img { width: 100%; height: auto; margin-bottom: 10px; transition: transform 0.3s ease; }
        .manifestazione-img:hover { transform: scale(1.05); }
        .button { margin-top: 20px; }
        .rd-navbar-dropdown { display: none; background-color: #4ac4cf; }
        .rd-navbar-dropdown.open { display: block; background-color: #4ac4cf; }
        @media (min-width: 992px) {
            .rd-nav-item--has-dropdown:hover > .rd-navbar-dropdown { display: block; background-color: #4ac4cf; }
        }
    </style>
</head>

<!-- JS -->
<script src="<?php echo $base_path; ?>resources/js/core.min.js"></script>
<script src="<?php echo $base_path; ?>resources/js/script.js"></script>

<body>
    <div class="ie-panel"><a href="http://windows.microsoft.com/en-US/internet-explorer/"><img src="resources/images/ie8-panel/warning_bar_0000_us.jpg" height="42" width="820" alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today."></a></div>
    <div class="preloader">
        <div class="preloader-body">
            <div class="cssload-container">
                <div class="cssload-speeding-wheel"></div>
            </div>
        </div>
    </div>
    <div class="page">
<!-- Page Header-->
<header class="section page-header">
    <div class="rd-navbar-wrap" style="position: absolute">
        <nav class="rd-navbar rd-navbar-classic" 
            data-layout="rd-navbar-fixed"
            data-sm-layout="rd-navbar-fixed"
            data-md-layout="rd-navbar-fixed"
            data-lg-layout="rd-navbar-fixed"
            data-xl-layout="rd-navbar-static"
            data-xxl-layout="rd-navbar-static"
            data-lg-stick-up="true"
            data-xl-stick-up="true"
            data-xxl-stick-up="true"
            data-lg-stick-up-offset="46px"
            data-xl-stick-up-offset="46px"
            data-xxl-stick-up-offset="46px"
        >
            <div class="rd-navbar-collapse-toggle rd-navbar-fixed-element-1" data-rd-navbar-toggle=".rd-navbar-collapse"><span></span></div>
            <div class="rd-navbar-main-outer">
                <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
                <div class="rd-navbar-main">
                    <div class="rd-navbar-panel">
                        <button class="rd-navbar-toggle" data-rd-navbar-toggle=".rd-navbar-nav-wrap"><span></span></button>
                        <div class="rd-navbar-brand">
                            <a class="brand" href="<?php echo $base_path; ?>index.php">
                                <img src="<?php echo $base_path; ?>resources/images/logo-default-296x52.png" alt="" width="148" height="26"/>
                            </a>
                        </div>
                    </div>
                    <div class="rd-navbar-main-element">
                        <div class="rd-navbar-nav-wrap">
                            <ul class="rd-navbar-nav">
                                <li class="rd-nav-item <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                                    <a class="rd-nav-link" href="<?php echo $base_path; ?>index.php">Home</a>
                                </li>
                                <li class="rd-nav-item <?php echo $current_page == 'manifestazioni.php' ? 'active' : ''; ?>">
                                    <a class="rd-nav-link" href="<?php echo $base_path; ?>pages/manifestazioni.php">Manifestazioni</a>
                                </li>

                                <?php
                                if (!empty($_SESSION['ruolo'])) {
                                    $ruolo = $_SESSION['ruolo'];
                                    $ruoloLower = strtolower($ruolo);
                                    $dashboardPage = "dashboard_" . $ruoloLower . ".php";
                                    $dashboardPath = "$ruolo/$dashboardPage";
                                    $isDashboard = $current_page === $dashboardPage;
                                    ?>
                                    <li class="rd-nav-item <?php echo $isDashboard ? 'active' : ''; ?>">
                                        <a class="rd-nav-link" href="<?php echo $dashboardPath; ?>">Dashboard</a>
                                    </li>
                                <?php } ?>

                                <li class="rd-nav-item <?php echo $current_page == 'about.php' ? 'active' : ''; ?>">
                                    <a class="rd-nav-link" href="<?php echo $base_path; ?>pages/about.php">About</a>
                                </li>
                                <li class="rd-nav-item <?php echo $current_page == 'contacts.php' ? 'active' : ''; ?>">
                                    <a class="rd-nav-link" href="<?php echo $base_path; ?>pages/contacts.php">Contacts</a>
                                </li>

                                <?php if (isset($_SESSION['email'])): ?>
                                    <li class="rd-nav-item">
                                        <a class="rd-nav-link" href="auth/logout_handler.php">Logout</a>
                                    </li>
                                <?php else: ?>
                                    <li class="rd-nav-item <?php echo $current_page == 'login.php' ? 'active' : ''; ?>">
                                        <a class="rd-nav-link" href="<?php echo $base_path; ?>pages/login.php">Login</a>
                                    </li>
                                <?php endif; ?>

                                <li class="rd-nav-item rd-nav-item--has-dropdown">
                                    <a class="rd-nav-link" href="#">Query</a>
                                    <ul class="rd-navbar-dropdown">
                                        <li class="rd-nav-item"><a class="rd-nav-link" href="<?php echo $base_path; ?>pages/visualizzazioni/elenco_espositori.php" style="color: white;">Elenco Espositori</a></li>
                                        <li class="rd-nav-item"><a class="rd-nav-link" href="<?php echo $base_path; ?>pages/visualizzazioni/esposizioni_per_categoria.php" style="color: white;">Esposizioni per Categoria</a></li>
                                        <li class="rd-nav-item"><a class="rd-nav-link" href="<?php echo $base_path; ?>pages/visualizzazioni/elenco_categorie.php" style="color: white;">Elenco Categorie</a></li>
                                        <li class="rd-nav-item"><a class="rd-nav-link" href="<?php echo $base_path; ?>pages/visualizzazioni/andamento_partecipanti.php" style="color: white;">Andamento Partecipanti</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var dropdown = document.querySelector('.rd-nav-item--has-dropdown > .rd-nav-link');
  if(dropdown) {
    dropdown.addEventListener('click', function(e) {
      e.preventDefault();
      var menu = this.nextElementSibling;
      if(menu && menu.classList.contains('rd-navbar-dropdown')) {
        menu.classList.toggle('open');
      }
    });
  }
});
</script>
