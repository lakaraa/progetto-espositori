<!-- template_header.php -->
<!DOCTYPE html>
<html class="wide wow-animation" lang="en">
<head>
    <title>Associazione Culturale - Manifestazione Pubblica</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="/progetto-espositori/resources/images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Lato:400,700%7COpen+Sans:400,600,700%7CSource+Code+Pro:300,400,500,600,700,900%7CNothing+You+Could+Do%7CPoppins:400,500">
    <link rel="stylesheet" href="/progetto-espositori/resources/css/bootstrap.css">
    <link rel="stylesheet" href="/progetto-espositori/resources/css/fonts.css">
    <link rel="stylesheet" href="/progetto-espositori/resources/css/style.css">
    <style>
        .ie-panel{display: none;background: #212121;padding: 10px 0;box-shadow: 3px 3px 5px 0 rgba(0,0,0,.3);clear: both;text-align:center;position: relative;z-index: 1;} 
        html.ie-10 .ie-panel, html.lt-ie-10 .ie-panel {display: block;}
        .manifestazione-img { width: 100%; height: auto; margin-bottom: 10px; transition: transform 0.3s ease; } /* Aumenta il margine inferiore */
        .manifestazione-img:hover { transform: scale(1.05); }
        .button { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="ie-panel"><a href="http://windows.microsoft.com/en-US/internet-explorer/"><img src="/progetto-espositori/resources/images/ie8-panel/warning_bar_0000_us.jpg" height="42" width="820" alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today."></a></div>
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
                <nav class="rd-navbar rd-navbar-classic" data-layout="rd-navbar-fixed" data-sm-layout="rd-navbar-fixed" data-md-layout="rd-navbar-fixed" data-md-device-layout="rd-navbar-fixed" data-lg-layout="rd-navbar-fixed" data-lg-device-layout="rd-navbar-fixed" data-xl-layout="rd-navbar-static" data-xl-device-layout="rd-navbar-static" data-xxl-layout="rd-navbar-static" data-xxl-device-layout="rd-navbar-static" data-lg-stick-up-offset="46px" data-xl-stick-up-offset="46px" data-xxl-stick-up-offset="46px" data-lg-stick-up="true" data-xl-stick-up="true" data-xxl-stick-up="true">
                    <div class="rd-navbar-collapse-toggle rd-navbar-fixed-element-1" data-rd-navbar-toggle=".rd-navbar-collapse"><span></span></div>
                    <div class="rd-navbar-main-outer">
                        <?php
                        $current_page = basename($_SERVER['PHP_SELF']);
                        ?>
                        <div class="rd-navbar-main">
                            <div class="rd-navbar-panel">
                                <button class="rd-navbar-toggle" data-rd-navbar-toggle=".rd-navbar-nav-wrap"><span></span></button>
                                <div class="rd-navbar-brand">
                                    <a class="brand" href="index.php"><img src="/progetto-espositori/resources/images/logo-default-296x52.png" alt="" width="148" height="26"/></a>
                                </div>
                            </div>
                            <div class="rd-navbar-main-element">
                                <div class="rd-navbar-nav-wrap">
                                    <ul class="rd-navbar-nav">
                                        <li class="rd-nav-item <?php echo $current_page == 'index.php' ? 'active' : ''; ?>"><a class="rd-nav-link" href="/progetto-espositori/index.php">Home</a></li>
                                        <li class="rd-nav-item <?php echo $current_page == 'manifestazioni.php' ? 'active' : ''; ?>"><a class="rd-nav-link" href="/progetto-espositori/pages/manifestazioni.php">Manifestazioni</a></li>
                                        <li class="rd-nav-item <?php echo $current_page == 'about.php' ? 'active' : ''; ?>"><a class="rd-nav-link" href="/progetto-espositori/pages/about.php">About</a></li>
                                        <li class="rd-nav-item <?php echo $current_page == 'contacts.php' ? 'active' : ''; ?>"><a class="rd-nav-link" href="/progetto-espositori/pages/contacts.php">Contacts</a></li>
                                        <li class="rd-nav-item <?php echo $current_page == 'login.php' ? 'active' : ''; ?>"><a class="rd-nav-link" href="/progetto-espositori/pages/login.php">Login</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>
        </header>
