<?php 
include_once '../session.php';
include_once '../template_header.php'; 
?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(../resources/images/sfondo.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Login</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="index.php">Home</a></li>
        <li class="active">Login</li>
    </ul>
</section>

<!-- Login Section-->
<section class="section section-lg bg-default">
    <div class="container">
        <div class="row row-50 justify-content-center">
            <div class="col-md-10 col-lg-8">
                <h3>Login</h3>
                <?php if (!empty($_GET['error'])): ?>
                    <p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
                <?php endif; ?>
                <form action="../auth/login_handler.php" method="post">
                    <div class="row row-30">
                        <div class="col-md-12">
                            <div class="form-wrap">
                                <label class="form-label" for="email">Email</label>
                                <input class="form-input" id="email" type="email" name="email" style="text-transform: none;" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-wrap">
                                <label class="form-label" for="password">Password</label>
                                <input class="form-input" id="password" type="password" name="password" style="text-transform: none;" required>
                            </div>
                        </div>
                        <div class="col-md-12 d-flex justify-content-center">
                            <button class="button button-primary" type="submit">Login</button><br>
                        </div>

                    </div>
                </form>
                <div class="col-md-12">
                    <div class="text-center">
                        <br>
                        <p>Non hai un account? <a href="registrazione_visitatore.php">Registrati come visitatore</a></p>
                        Sei un espositore? <a href="registrazione_espositore.php">Registrati come Espositore</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../template_footer.php'; ?>