<?php
  include_once("../config.php");
  include_once("../queries.php");
  include_once("../session.php");
  include_once("../template_header.php");
  $currentYear = date("Y");
?>

      <!-- Breadcrumbs-->
      <section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(/progetto-espositori/resources/images/sfondo.jpg);">
         <div class="container">
          <h2 class="breadcrumbs-custom-title">About</h2>
        </div>
        <ul class="breadcrumbs-custom-path">
          <li><a href="index.php">Home</a></li>
          <li class="active">About</li>
        </ul>
      </section>
      <!-- About Section-->
      <section class="section section-lg bg-default">
        <div class="container">
          <div class="row row-50 flex-md-row-reverse">
            <div class="col-md-6 text-center">
              <div class="box-image-1"><img src="../resources/images/aboutus.jpg" alt="About Us" width="468" height="276"/>
              </div>
            </div>
            <div class="col-md-6">
              <div class="box-about">
                <div class="box-about-title-wrap">
                  <h2 class="box-about-mega-title">Cultura Viva</h2>
                  <h2 class="box-about-title">Chi Siamo</h2>
                </div>
                <h4 class="box-about-post-title">La Nostra Missione</h4>
                <p>La nostra associazione culturale è dedicata a promuovere la cultura e l'arte attraverso eventi pubblici e manifestazioni. Organizziamo una varietà di eventi che includono presentazioni, mostre e contributi da parte di espositori di diversi settori.</p>
                <p>Il nostro obiettivo è creare una piattaforma dove gli espositori possano condividere le loro opere e idee con il pubblico, favorendo lo scambio culturale e la crescita personale.</p>
                <a class="button button-primary" href="registration.php">Diventa un Espositore</a>
              </div>
            </div>
          </div>
        </div>
      </section>

<?php
include_once("../template_footer.php"); 
?>
