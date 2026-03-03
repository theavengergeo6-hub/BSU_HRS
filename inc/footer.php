</main>
        </div>
    </div>

    <!-- Footer Content -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-row">
                <div class="footer-col footer-about">
                    <h5 class="footer-title"><?= clean($footer_settings['site_name'] ?? $footer_settings['site_title'] ?? 'BSU Hostel') ?></h5>
                    <p class="footer-tagline"><?= clean($footer_settings['site_tagline'] ?? 'The BSU Hostel Reservation System simplifies booking for function rooms and guest rooms.') ?></p>
                    <div class="footer-social">
                        <?php if (!empty($footer_contact['facebook_url'])): ?>
                        <a href="<?= clean($footer_contact['facebook_url']) ?>" target="_blank" rel="noopener" class="social-link" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <?php endif; ?>
                        <a href="#" class="social-link" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="social-link" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h5 class="footer-title">Quick Links</h5>
                    <a href="<?= $base ?>/" class="footer-link">Home</a>
                    <a href="<?= $base ?>/rooms.php" class="footer-link">Rooms</a>
                    <a href="<?= $base ?>/facilities.php" class="footer-link">Amenities</a>
                    <a href="<?= $base ?>/contact.php" class="footer-link">Contact</a>
                </div>
                <div class="footer-col">
                    <h5 class="footer-title">Contact Info</h5>
                    <?php if (!empty($footer_contact['address'])): ?><p class="footer-link"><i class="bi bi-geo-alt-fill me-2"></i><?= clean($footer_contact['address']) ?></p><?php endif; ?>
                    <?php if (!empty($footer_contact['phone'])): ?><p class="footer-link"><i class="bi bi-telephone-fill me-2"></i><?= clean($footer_contact['phone']) ?></p><?php endif; ?>
                    <?php if (!empty($footer_contact['email'])): ?><p class="footer-link"><i class="bi bi-envelope-fill me-2"></i><a href="mailto:<?= clean($footer_contact['email']) ?>"><?= clean($footer_contact['email']) ?></a></p><?php endif; ?>
                </div>
            </div>
            <div class="footer-bottom">
                <p class="mb-0">© <?= date('Y') ?> <?= clean($footer_settings['site_name'] ?? 'BSU Hostel') ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button (Index page only) -->
    <?php
    $current_page = basename($_SERVER['PHP_SELF']);
    if ($current_page == 'index.php'):
    ?>
    <button id="scrollToTopBtn" class="scroll-to-top" aria-label="Scroll to top">
        <i class="bi bi-arrow-up-short"></i>
    </button>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Scroll to Top Button
        const scrollToTopBtn = document.getElementById('scrollToTopBtn');
        
        // Only run if button exists (only on index.php)
        if (!scrollToTopBtn) return;
        
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                scrollToTopBtn.classList.add('show');
            } else {
                scrollToTopBtn.classList.remove('show');
            }
        });
        
        scrollToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Initial check
        if (window.scrollY > 300) {
            scrollToTopBtn.classList.add('show');
        }
    });
    </script>
</body>
</html>