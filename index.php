<?php
$pageTitle = 'Home';
require_once __DIR__ . '/inc/link.php';
$base = rtrim(BASE_URL, '/');
$carousel_slides = getCarouselSlides($conn);
$site_info = getSiteInfo($conn);
$footer_contact = $site_info['contact'];
$footer_settings = $site_info['settings'];

if (empty($carousel_slides)) {
    $carousel_slides = [
        ['title' => 'Welcome to BSU Hostel', 'subtitle' => 'The perfect venue for your events. Spacious function rooms and comfortable guest rooms for meetings, celebrations, and group stays. Reserve your space today.', 'button_text' => 'View Rooms', 'button_url' => 'rooms_showcase.php', 'image_path' => 'hostel/hostel2.png'],
        ['title' => 'Book Your Function or Guest Room', 'subtitle' => 'Check availability and reserve your stay in minutes.', 'button_text' => 'Check Availability', 'button_url' => 'calendar.php', 'image_path' => 'hostel/hostel2.png'],
        ['title' => 'Stay With Us', 'subtitle' => 'Ideal for students, groups, and travelers visiting BSU.', 'button_text' => 'Get in Touch', 'button_url' => 'contact.php', 'image_path' => 'hostel/hostel2.png'],
    ];
}
$assets_base = $base . '/assets/images/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/x-icon" href="BSU_Logo.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? clean($pageTitle) . ' | ' : '' ?>BSU Hostel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/BSU_HRS/css/style.css" rel="stylesheet">
    
    <!-- Animate.css for additional animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    

</head>
<body>
    <?php require_once __DIR__ . '/inc/header.php'; ?>

    <!-- Hero Carousel -->
    <div id="heroCarousel" class="carousel slide hero-carousel hero-section" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-indicators">
            <?php foreach ($carousel_slides as $i => $s): ?>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?= $i ?>" <?= $i === 0 ? 'class="active" aria-current="true"' : '' ?>></button>
            <?php endforeach; ?>
        </div>
        <div class="carousel-inner">
            <?php foreach ($carousel_slides as $i => $slide):
                $img_url = $assets_base . (strpos($slide['image_path'], '/') === 0 ? ltrim($slide['image_path'], '/') : $slide['image_path']);
            ?>
            <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                <div class="carousel-image" style="background-image: url('<?= htmlspecialchars($img_url) ?>');"></div>
                <div class="carousel-overlay"></div>
                <div class="carousel-caption">
                    <h1 class="animate__animated animate__fadeInDown float-element"><?= clean($slide['title']) ?></h1>
                    <p class="animate__animated animate__fadeInUp animate__delay-1s float-element-slow"><?= clean($slide['subtitle'] ?? '') ?></p>
                    <a href="<?= $base ?>/<?= clean($slide['button_url'] ?? 'rooms_showcase.php') ?>" class="btn-view-rooms animate__animated animate__fadeInUp animate__delay-2s float-element-delayed"><?= clean($slide['button_text'] ?? 'View Rooms') ?></a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <!-- About Us Section -->
    <section id="about" class="about-section section-fade-top">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 scroll-animate slide-left">
                    <h6 class="section-subtitle">About Us</h6>
                    <h2 class="section-title float-element-slow">Empowering Communities Through Lifelong Learning</h2>
                    <p class="mission-vision-text">
                        We are dedicated to creating empowered and inclusive communities by providing equitable access to modern, gender-responsive training and 
                        education opportunities that foster sustainable development and self-sufficiency.</p>
                    <a href="#" class="btn-learn-more">Learn More About Us →</a>
                </div>
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-md-6 mb-4 scroll-animate slide-right delay-1">
                            <div class="vision-card float-element">
                                <div class="icon-box"><i class="bi bi-eye-fill"></i></div>
                                <h4>Our Vision</h4>
                                <p class="mission-vision-text">Empowered and inclusive communities thrive through equitable access to modern, gender-responsive livelihood training and lifelong learning opportunities.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4 scroll-animate slide-right delay-2">
                            <div class="mission-card float-element-slow">
                                <div class="icon-box"><i class="bi bi-flag-fill"></i></div>
                                <h4>Our Mission</h4>
                                <p class="mission-vision-text">To deliver gender-responsive, skills-focused training that promotes self-sufficiency, equality, and sustainable development.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="faq-section">
        <div class="container">
            <div class="text-center mb-5 scroll-animate fade-up">
                <h6 class="section-subtitle">SUPPORT</h6>
                <h2 class="section-title float-element">Frequently Asked Questions</h2>
                <div style="width: 80px; height: 2px; background: var(--bsu-red); margin: 1rem auto; opacity: 0.5;"></div>
                <p class="lead">Find answers to common questions about facility use, scheduling, reservations, and policies of the Livelihood Training Center.</p>
            </div>

            <?php
            // Fetch FAQs from the database
            $faqs_query = "SELECT * FROM `faq` ORDER BY `sort_order` ASC";
            $faqs_result = $conn->query($faqs_query);
            
            // Map questions to icons
            $faq_icons = [
                'Who may request' => 'bi-person-circle',
                'Are Extension Services' => 'bi-calendar-check',
                'How can we submit' => 'bi-question-circle',
                'What general rules' => 'bi-shield-check',
            ];

            function get_faq_icon($question, $icon_map) {
                foreach ($icon_map as $key => $icon) {
                    if (strpos($question, $key) !== false) {
                        return $icon;
                    }
                }
                return 'bi-patch-question';
            }
            ?>

            <div class="accordion" id="faqAccordion">
                <?php
                if ($faqs_result && $faqs_result->num_rows > 0) {
                    $count = 0;
                    while ($faq = $faqs_result->fetch_assoc()) {
                        $count++;
                        $icon_class = get_faq_icon($faq['question'], $faq_icons);
                ?>
                        <div class="accordion-item scroll-animate fade-up delay-<?= min($count, 4) ?> float-element-slow">
                            <h2 class="accordion-header" id="heading<?= $count ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $count ?>" aria-expanded="false" aria-controls="collapse<?= $count ?>">
                                    <span class="faq-icon-wrapper">
                                        <i class="bi <?= $icon_class ?>"></i>
                                    </span>
                                    <?= htmlspecialchars($faq['question']) ?>
                                </button>
                            </h2>
                            <div id="collapse<?= $count ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $count ?>" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <?= nl2br(htmlspecialchars($faq['answer'])) ?>
                                </div>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo '<p class="text-center">No frequently asked questions found.</p>';
                }
                ?>
            </div>

            <!-- Still have questions? -->
            <div class="contact-prompt-box scroll-animate scale-in delay-3 float-element">
                <div class="icon-box"><i class="bi bi-chat-dots-fill"></i></div>
                <h3 class="section-title">Still have questions?</h3>
                <p class="lead" style="color: #666; font-size: 1.1rem;">If you cannot find an answer to your question in our FAQ, you can always contact us. We will answer you shortly!</p>
                <a href="contact.php" class="btn-learn-more mt-3">Contact Us</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
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
                    <a href="<?= $base ?>/rooms_showcase.php" class="footer-link">Rooms</a>
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

    <!-- Scroll Fade Effect Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile menu toggle
        var toggle = document.getElementById('navToggle');
        var mobileMenu = document.getElementById('mobileMenu');
        if (toggle && mobileMenu) {
            toggle.addEventListener('click', function() {
                mobileMenu.classList.toggle('show');
                var icon = toggle.querySelector('i');
                if (icon) {
                    icon.classList.toggle('bi-list');
                    icon.classList.toggle('bi-x-lg');
                }
            });
        }
        
        // Initialize carousel
        var carouselEl = document.getElementById('heroCarousel');
        if (carouselEl && typeof bootstrap !== 'undefined') {
            new bootstrap.Carousel(carouselEl, { 
                interval: 5000,
                wrap: true
            });
        }
        
        // Intersection Observer for scroll animations
        const animatedElements = document.querySelectorAll('.scroll-animate');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                }
            });
        }, {
            threshold: 0.2,
            rootMargin: '0px'
        });
        
        animatedElements.forEach(el => {
            observer.observe(el);
        });
        
        // ===== SCROLL FADE EFFECT =====
        const heroSection = document.querySelector('.hero-carousel');
        const aboutSection = document.querySelector('#about');
        
        if (!heroSection || !aboutSection) return;
        
        function updateFadeOnScroll() {
            const scrollY = window.scrollY;
            const windowHeight = window.innerHeight;
            
            // Get hero section position
            const heroRect = heroSection.getBoundingClientRect();
            const heroTop = heroRect.top + scrollY;
            const heroHeight = heroRect.height;
            
            // Calculate how far we've scrolled through the hero section (0 to 1)
            // Start fading when we're 100px into the hero section
            const heroProgress = Math.min(1, Math.max(0, (scrollY - heroTop - 100) / (heroHeight * 0.5)));
            
            // Hero fades from 1 to 0.4
            const heroOpacity = Math.max(0.4, 1 - heroProgress * 0.6);
            
            // About section fades from 0.4 to 1
            const aboutOpacity = Math.min(1, 0.4 + heroProgress * 0.6);
            
            // Apply to hero section
            heroSection.style.opacity = heroOpacity;
            
            // Make overlay slightly darker as we scroll
            const heroOverlay = heroSection.querySelector('.carousel-overlay');
            if (heroOverlay) {
                const overlayOpacity = 0.45 + (heroProgress * 0.2);
                heroOverlay.style.backgroundColor = `rgba(0, 0, 0, ${overlayOpacity})`;
            }
            
            // Apply to about section
            aboutSection.style.opacity = aboutOpacity;
            aboutSection.style.transform = `translateY(${heroProgress * 20}px)`;
        }
        
        // Throttle scroll events for performance
        let ticking = false;
        window.addEventListener('scroll', function() {
            if (!ticking) {
                window.requestAnimationFrame(function() {
                    updateFadeOnScroll();
                    ticking = false;
                });
                ticking = true;
            }
        });
        
        // Initial call
        updateFadeOnScroll();
    });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Scroll to Top Button -->
        <button id="scrollToTopBtn" class="scroll-to-top" aria-label="Scroll to top">
        <i class="bi bi-arrow-up-short"></i>
</button>
    <script>
        // Scroll to Top Button functionality
        document.addEventListener('DOMContentLoaded', function() {
            const scrollToTopBtn = document.getElementById('scrollToTopBtn');
            
            if (!scrollToTopBtn) return;
            
            // Show button when user scrolls down 300px
            window.addEventListener('scroll', function() {
                if (window.scrollY > 300) {
                    scrollToTopBtn.classList.add('show');
                } else {
                    scrollToTopBtn.classList.remove('show');
                }
            });
    
        // Smooth scroll to top when clicked
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