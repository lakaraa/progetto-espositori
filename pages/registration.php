<?php include __DIR__ . '/template_header.php'; ?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(images/bg-breadcrumbs-03-1920x480.jpg);">
        <div class="container">
            <h2 class="breadcrumbs-custom-title">Registration</h2>
        </div>
        <ul class="breadcrumbs-custom-path">
            <li><a href="index.php">Home</a></li>
            <li class="active">Registration</li>
        </ul>
    </section>

    <!-- Registration Section-->
    <section class="section section-lg bg-default">
        <div class="container">
            <div class="row row-50 justify-content-center">
                <div class="col-md-10 col-lg-8">
                    <h3>Register as an Exhibitor</h3>
                    <?php if (!empty($message)): ?>
                        <p style="color: red;"><?php echo $message; ?></p>
                    <?php endif; ?>
                    <form class="rd-form rd-mailform" method="post" enctype="multipart/form-data" action="auth/register_handler.php">
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
                                    <label class="form-label" for="registration-first-name">First Name</label>
                                    <input class="form-input" id="registration-first-name" type="text" name="first_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-wrap">
                                    <label class="form-label" for="registration-last-name">Last Name</label>
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
                                    <label class="form-label" for="registration-phone">Phone</label>
                                    <input class="form-input" id="registration-phone" type="text" name="phone" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-wrap">
                                    <label class="form-label" for="registration-qualification">Qualification</label>
                                    <select class="form-input" id="registration-qualification" name="qualification" required>
                                        <option value="professional">Professional</option>
                                        <option value="amateur">Amateur</option>
                                        <option value="expert">Expert Non-Professional</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-wrap">
                                    <label class="form-label" for="registration-cv">Curriculum Vitae (PDF)</label>
                                    <input class="form-input" id="registration-cv" type="file" name="cv" accept=".pdf" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button class="button button-primary" type="submit">Register</button>
                            </div>
                        </div>
                    </form>
                    <div class="text-center">
                        <p style="color: black;">Hai già un account? <a href="login.php">Sì</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include __DIR__ . '/template_footer.php'; ?>