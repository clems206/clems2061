<?php
// includes/footer.php
?>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> CréaMod3D. Tous droits réservés.</p>
            <div class="social-links">
                <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navToggle = document.querySelector('.nav-toggle');
            const mainNav = document.querySelector('.main-nav');

            if (navToggle && mainNav) {
                navToggle.addEventListener('click', function() {
                    mainNav.classList.toggle('active');
                    navToggle.classList.toggle('active');
                });
            }
            // Note: Pour les scripts spécifiques à chaque page, il est préférable de les laisser dans la page elle-même
            // ou de les charger conditionnellement après le footer.
        });
    </script>
</body>
</html>