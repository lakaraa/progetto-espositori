<?php include __DIR__ . '/template_header.php'; ?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(resources/images/bg-breadcrumbs-04-1920x480.jpg);">
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
                <form class="rd-form rd-mailform" method="post" action="auth/login_handler.php">
                    <div class="row row-30">
                        <div class="col-md-12">
                            <div class="form-wrap">
                                <label class="form-label" for="login-email">Email</label>
                                <input class="form-input" id="login-email" type="email" name="email" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-wrap">
                                <label class="form-label" for="login-password">Password</label>
                                <input class="form-input" id="login-password" type="password" name="password" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button class="button button-primary" type="submit">Login</button>
                        </div>
                    </div>
                </form>
                <div class="text-center">
                    <p>Non hai un account? <a href="registration.php">Registrati</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/template_footer.php'; ?>