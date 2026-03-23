</main>
        </div>
    </div>

    <?php
    // Ensure footer data is available even if not pre-fetched in the main script
    if (!isset($footer_contact) || !isset($footer_settings)) {
        if (isset($conn)) {
            $site_info = getSiteInfo($conn);
            $footer_contact = $site_info['contact'];
            $footer_settings = $site_info['settings'];
        }
    }
    $base = defined('BASE_URL') ? rtrim(BASE_URL, '/') : '';
    ?>
    <style>
        .site-footer {
            background-color: #ffffff;
            color: #333;
            padding-top: 4rem;
            padding-bottom: 2rem;
            position: relative;
            border-top: 1px solid #eee;
            overflow: hidden;
        }

        /* Animations - Subtle Floating Background Elements */
        .footer-bg-animation {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
            opacity: 0.6;
        }
        .floating-circle {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(183,28,28,0.06) 0%, rgba(183,28,28,0) 70%);
            animation: float 10s infinite ease-in-out alternate;
        }
        .circle-1 { width: 350px; height: 350px; top: -100px; left: -100px; animation-duration: 12s;}
        .circle-2 { width: 450px; height: 450px; bottom: -150px; right: -100px; animation-duration: 15s; animation-delay: -5s;}
        .circle-3 { width: 250px; height: 250px; top: 10%; left: 45%; animation-duration: 9s; animation-delay: -3s;}

        @keyframes float {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(25px, -20px) scale(1.05); }
            100% { transform: translate(-15px, 15px) scale(0.95); }
        }

        .footer-content-wrapper {
            position: relative;
            z-index: 1;
        }

        .footer-col {
            margin-bottom: 2rem;
        }

        .footer-title {
            color: #b71c1c;
            font-weight: 700;
            margin-bottom: 1.5rem;
            font-size: 1.15rem;
            position: relative;
            display: inline-block;
        }

        .footer-title::after {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 0;
            width: 30px;
            height: 2px;
            background-color: #b71c1c;
            transition: width 0.3s ease;
        }

        .footer-col:hover .footer-title::after {
            width: 50px;
        }

        .footer-tagline {
            color: #555;
            font-size: 0.95rem;
            line-height: 1.7;
            margin-bottom: 1.5rem;
        }

        .footer-link-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1rem;
            color: #555;
            font-size: 0.95rem;
        }

        .footer-link-item i {
            color: #b71c1c;
            margin-right: 12px;
            font-size: 1.1rem;
            margin-top: 2px;
            transition: transform 0.3s ease;
        }

        .footer-link-item:hover i {
            transform: scale(1.15);
        }

        .footer-link-item a {
            color: #555;
            text-decoration: none;
            transition: color 0.3s ease, transform 0.3s ease;
            display: inline-block;
        }

        .footer-link-item a:hover {
            color: #b71c1c;
            transform: translateX(4px);
        }

        .social-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            background-color: #fdeae8;
            color: #b71c1c;
            border-radius: 50%;
            margin-right: 10px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 1.1rem;
        }

        .social-link:hover {
            background-color: #b71c1c;
            color: white;
            transform: translateY(-4px);
            box-shadow: 0 4px 10px rgba(183,28,28,0.25);
        }

        .footer-bottom {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #eee;
            text-align: center;
            color: #888;
            font-size: 0.85rem;
        }

        .scroll-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 45px;
            height: 45px;
            background: #b71c1c;
            color: #fff;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            z-index: 1000;
            box-shadow: 0 5px 15px rgba(183, 28, 28, 0.3);
            font-size: 1.5rem;
        }

        .scroll-to-top.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .scroll-to-top:hover {
            background: #8b0000;
            transform: translateY(-5px) scale(1.1);
        }
    </style>

    <footer class="site-footer">
        <!-- Floating Animated Background Entities -->
        <div class="footer-bg-animation">
            <div class="floating-circle circle-1"></div>
            <div class="floating-circle circle-2"></div>
            <div class="floating-circle circle-3"></div>
        </div>

        <div class="container footer-content-wrapper">
            <div class="row">
                <!-- Column 1: About -->
                <div class="col-lg-4 col-md-12 footer-col pr-lg-4">
                    <h5 class="footer-title"><?= clean($footer_settings['site_name'] ?? $footer_settings['site_title'] ?? 'BatStateU HOSTEL') ?></h5>
                    <p class="footer-tagline"><?= clean($footer_settings['site_tagline'] ?? 'The BSU Hostel Reservation System simplifies booking for function rooms and guest rooms.') ?></p>
                    
                    <div class="mt-4">
                        <?php if (!empty($footer_contact['facebook_url'])): ?>
                            <a href="<?= clean($footer_contact['facebook_url']) ?>" target="_blank" rel="noopener" class="social-link" aria-label="Facebook">
                                <i class="bi bi-facebook"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Column 2: Quick Links -->
                <div class="col-lg-4 col-md-6 footer-col">
                    <h5 class="footer-title">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="footer-link-item">
                            <i class="bi bi-chevron-right" style="font-size: 0.8rem;"></i>
                            <a href="<?= $base ?>/index.php">Home</a>
                        </li>
                        <li class="footer-link-item">
                            <i class="bi bi-chevron-right" style="font-size: 0.8rem;"></i>
                            <a href="<?= $base ?>/rooms_showcase.php">Our Rooms</a>
                        </li>
                        <li class="footer-link-item">
                            <i class="bi bi-chevron-right" style="font-size: 0.8rem;"></i>
                            <a href="<?= $base ?>/calendar.php">Availability</a>
                        </li>
                    </ul>
                </div>

                <!-- Column 3: Contact Info (Right Side) -->
                <div class="col-lg-4 col-md-6 footer-col">
                    <h5 class="footer-title">Get In Touch</h5>
                    
                    <?php if (!empty($footer_contact['address'])): ?>
                        <div class="footer-link-item">
                            <i class="bi bi-geo-alt-fill"></i>
                            <div><?= clean($footer_contact['address']) ?></div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($footer_contact['phone'])): ?>
                        <div class="footer-link-item">
                            <i class="bi bi-telephone-fill"></i>
                            <a href="tel:<?= clean($footer_contact['phone']) ?>"><?= clean($footer_contact['phone']) ?></a>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($footer_contact['email'])): ?>
                        <div class="footer-link-item">
                            <i class="bi bi-envelope-fill"></i>
                            <a href="mailto:<?= clean($footer_contact['email']) ?>"><?= clean($footer_contact['email']) ?></a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="footer-bottom">
                <p class="mb-0">© <?= date('Y') ?> <?= clean($footer_settings['site_name'] ?? 'BatStateU HOSTEL') ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button id="scrollToTopBtn" class="scroll-to-top" aria-label="Scroll to top">
        <i class="bi bi-arrow-up-short"></i>
    </button>

    <!-- Global Loading Overlay -->
    <style>
        .global-loading-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(4px);
            z-index: 99999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        .global-loading-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        .global-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #fdeae8;
            border-top: 4px solid #b71c1c; 
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 15px;
        }
        .global-loader-text {
            color: #b71c1c;
            font-weight: 600;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
    <div id="globalLoadingOverlay" class="global-loading-overlay">
        <div class="global-spinner"></div>
        <div id="globalLoaderText" class="global-loader-text">Processing, please wait...</div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.showGlobalLoader = function(msg) {
            document.getElementById('globalLoaderText').innerText = msg || 'Processing, please wait...';
            document.getElementById('globalLoadingOverlay').classList.add('show');
        };
        window.hideGlobalLoader = function() {
            document.getElementById('globalLoadingOverlay').classList.remove('show');
        };
    document.addEventListener('DOMContentLoaded', function() {
        const scrollToTopBtn = document.getElementById('scrollToTopBtn');
        
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
    });
    </script>
</body>
</html>