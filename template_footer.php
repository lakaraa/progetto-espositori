<?php
error_reporting(E_ALL); // Report all errors and warnings
ini_set('display_errors', 1); // Display errors on the screen

include_once 'config.php';
?>
<!-- template_footer.php -->
<footer class="section footer-classic bg-default">
            <div class="container">
            <img src="<?php echo $base_path; ?>resources/images/motto.png" alt="About Us" width="20%"/>
                <div class="row row-15">
                    <div class="col-sm-6">
                        
                        <p class="rights"><span>Cultura Viva</span><span>&nbsp;</span><span>&copy;&nbsp;</span><span class="copyright-year"><?php echo date('Y'); ?></span><span>&nbsp;</span>All Rights Reserved \ Design by Sara Petrovska</p>
                    </div>                  
                    <div class="col-sm-6">
                        <div class="footer-contact"><a href="<?php echo $base_path; ?>pages/contacts.php">
                            <div class="icon novi-icon mdi mdi-email-outline"></div>Contact Us</a></div>
                            
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <div class="snackbars" id="form-output-global"></div>
    <script src="<?php echo $base_path; ?>resources/js/core.min.js"></script>
    <script src="<?php echo $base_path; ?>resources/js/script.js"></script>
</body>
</html>
