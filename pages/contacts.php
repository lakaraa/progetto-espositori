<?php
include_once("../config.php");
include_once("../queries.php");
include_once("../session.php");
include_once("../template_header.php"); 

// Variabile per il messaggio di successo
$successMessage = "";
$errorMessage = "";

// Verifica se il form è stato inviato
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recupera i dati dal modulo
    $nome = htmlspecialchars($_POST['name']);
    $telefono = htmlspecialchars($_POST['phone']);
    $email = htmlspecialchars($_POST['email']);
    $messaggio = htmlspecialchars($_POST['message']);

    // Funzione per inserire i dati nel database
    if (insertMessaggio($pdo, $nome, $email, $telefono, $messaggio)) {
        $successMessage = "Messaggio inviato correttamente!";
    } else {
        $errorMessage = "C'è stato un errore nell'invio del messaggio. Riprova.";
    }
}
?>

<!-- Breadcrumbs-->
<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(../resources/images/sfondo.jpg);">
    <div class="container">
        <h2 class="breadcrumbs-custom-title">Contacts</h2>
    </div>
    <ul class="breadcrumbs-custom-path">
        <li><a href="index.html">Home</a></li>
        <li class="active">Contacts</li>
    </ul>
</section>

<!-- Main Content-->
<section class="section section-md bg-default">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-10">
                <h3>Contact details</h3>
                <p class="text-gray-600">On this page you can find all necessary contacts to get in touch with us if you would like to collaborate, book a live event or ask a question.</p>
            </div>
        </div>
        <div class="row row-50 justify-lg-content-center row-custom-border">
            <div class="col-lg-4 col-md-6">
                <div class="box-icon-classic">
                    <div class="icon novi-icon icon-primary icon-lg fl-bigmug-line-cellphone55"></div>
                    <h5 class="box-icon-classic-title">Phones</h5>
                    <ul class="box-icon-classic-list">
                        <li><a href="tel:#">Phone 01: +1 (409) 987–5874</a></li>
                        <li><a href="tel:#">Phone 02: +1 (409) 987–5874</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="box-icon-classic">
                    <div class="icon novi-icon icon-primary icon-lg fl-bigmug-line-big104"></div>
                    <h5 class="box-icon-classic-title">Address</h5>
                    <ul class="box-icon-classic-list">
                        <li><a href="#">6036 Richmond Hwy., <br> Alexandria, VA, 2230</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="box-icon-classic">
                    <div class="icon novi-icon icon-primary icon-md fl-bigmug-line-email64"></div>
                    <h5 class="box-icon-classic-title">E-mails</h5>
                    <ul class="box-icon-classic-list">
                        <li><a href="mailto:#">info@demolink.org</a></li>
                        <li><a href="mailto:#">mail@demolink.org</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mailform -->
<section class="section section-lg bg-default">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-10">
                <h3>Get in touch with us</h3>
                <p class="text-gray-600">Feel free to drop a line or ask a question using the contact form below.</p>
            </div>
        </div>
        
        <!-- Success or error message -->
        <?php if ($successMessage): ?>
            <div class="alert alert-success">
                <?php echo $successMessage; ?>
            </div>
        <?php elseif ($errorMessage): ?>
            <div class="alert alert-danger">
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>

        <!-- RD Mailform -->
        <form class="rd-form rd-mailform text-left" method="post" action="contacts.php">
            <div class="row row-40">
                <div class="col-lg-4">
                    <div class="form-wrap">
                        <label class="form-label" for="contact-name">Name</label>
                        <input class="form-input" id="contact-name" type="text" name="name" data-constraints="@Required">
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-wrap">
                        <label class="form-label" for="contact-phone">Phone</label>
                        <input class="form-input" id="contact-phone" type="text" name="phone" data-constraints="@Required @PhoneNumber">
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-wrap">
                        <label class="form-label" for="contact-email">E-Mail</label>
                        <input class="form-input" id="contact-email" type="email" name="email" data-constraints="@Required @Email">
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-wrap">
                        <label class="form-label" for="contact-message">Message</label>
                        <textarea class="form-input" id="contact-message" name="message" data-constraints="@Required"></textarea>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-wrap">
                        <button class="button button-primary" type="submit">Send Message</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<?php
include_once("../template_footer.php");
?>
