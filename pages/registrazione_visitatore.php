<?php 
include_once '../session.php';
include_once '../template_header.php'; 
?>
<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(images/bg-breadcrumbs-03-1920x480.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Registrazione Visitatore</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="index.php">Home</a></li>
        <li class="active">Registrazione Visitatore</li>
    </ul>
</section>

<!-- Registration Section-->
<section class="section section-lg bg-default">
    <div class="container">
        <div class="row row-50 justify-content-center">
            <div class="col-md-10 col-lg-8">
                <h3>Registrati come Visitatore</h3>
                <?php if (!empty($message)): ?>
                    <p style="color: red;"><?php echo $message; ?></p>
                <?php endif; ?>
                <form method="post" action="../auth/registrazione_visitatore_handler.php">
                    <div class="row row-30">
                        <div class="col-md-6">
                            <div class="form-wrap">
                                <label class="form-label" for="registration-username">Username</label>
                                <input class="form-input" id="registration-username" type="text" name="username" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-wrap">
                                <label class="form-label" for="registration-password">Password</label>
                                <input class="form-input" id="registration-password" type="password" name="password" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-wrap">
                                <label class="form-label" for="registration-first-name">Nome</label>
                                <input class="form-input" id="registration-first-name" type="text" name="first_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-wrap">
                                <label class="form-label" for="registration-last-name">Cognome</label>
                                <input class="form-input" id="registration-last-name" type="text" name="last_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-wrap">
                                <label class="form-label" for="registration-email">Email</label>
                                <input class="form-input" id="registration-email" type="email" name="email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-wrap">
                                <label class="form-label" for="registration-phone">Telefono</label>
                                <input class="form-input" id="registration-phone" type="text" name="phone" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button class="button button-primary" type="submit">Registrati</button>
                        </div>
                    </div>
                </form>
                <div class="text-center">
                    <p>Hai già un account? <a href="login.php">Accedi</a></p>
                    <p>Sei un espositore? <a href="registrazione_espositore.php">Registrati come espositore</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php 
include_once '../template_footer.php'; 
?>