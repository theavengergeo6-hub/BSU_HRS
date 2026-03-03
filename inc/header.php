<?php
// Ensures BASE_URL is defined, pointing to the root of the application.
if (!defined('BASE_URL')) {
    // Dynamically determine BASE_URL from the script's location.
    $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
    $script_name = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $base_url .= ($script_name === '/') ? '' : $script_name;
    define('BASE_URL', rtrim($base_url, '/'));
}

$current_page = basename($_SERVER['PHP_SELF']);

// A single, consistent navigation structure for the entire site.
$nav_links = [
    ['href' => 'index.php', 'text' => 'Home', 'is_anchor' => false],
    ['href' => '#about', 'text' => 'About', 'is_anchor' => true],
    ['href' => '#faq', 'text' => 'FAQ', 'is_anchor' => true],
    ['href' => 'calendar.php', 'text' => 'Calendar', 'is_anchor' => false],
    ['href' => 'reservation.php', 'text' => 'Reservation', 'is_anchor' => false],
    ['href' => 'contact.php', 'text' => 'Contact', 'is_anchor' => false]
];

$logo_path_on_disk = __DIR__ . '/../assets/images/header_logo.png';
$logo_url = BASE_URL . '/assets/images/header_logo.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : '' ?>BSU Hostel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/css/style.css" rel="stylesheet">
    <link href="/BSU_HRS/css/style.css" rel="stylesheet">
    
    <?php if (isset($extraStyles)) echo $extraStyles; ?>
</head>
<body>

    <header class="site-header" id="siteHeader">
        <a href="<?= BASE_URL ?>/index.php" class="logo-container">
            <?php if (file_exists($logo_path_on_disk)): ?>
                <img src="<?= $logo_url ?>" alt="BSU Hostel Logo" class="logo-img">
            <?php else: ?>
                <span class="logo-text">BSU</span>
            <?php endif; ?>
            
            <!-- Brand text beside logo -->
            <div class="brand-text">
                <span class="brand-main">Batangas State University ARASOF-NASUGBU</span>
                <span class="brand-sub">BSU Hostel, Nasugbu Building</span>
            </div>
        </a>

        <button class="menu-toggle" id="menuToggle" aria-label="Toggle navigation" aria-expanded="false">
            <i class="bi bi-list"></i>
        </button>

        <nav class="nav-menu" id="navMenu">
            <ul class="nav-links">
                <?php foreach ($nav_links as $link): ?>
                    <?php
                        $is_active = ($current_page == $link['href']);
                        if ($link['is_anchor']) {
                            $url = ($current_page == 'index.php') ? $link['href'] : BASE_URL . '/index.php' . $link['href'];
                        } else {
                            $url = BASE_URL . '/' . $link['href'];
                        }
                    ?>
                    <li>
                        <a href="<?= $url ?>" class="<?= $is_active ? 'active' : '' ?>" data-anchor-target="<?= $link['is_anchor'] ? $link['href'] : '' ?>">
                            <?= $link['text'] ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </header>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const siteHeader = document.getElementById('siteHeader');
        const menuToggle = document.getElementById('menuToggle');
        const navMenu = document.getElementById('navMenu');
        const menuIcon = menuToggle.querySelector('i');
        const headerHeight = siteHeader.offsetHeight;
        const isHomepage = '<?= $current_page ?>' === 'index.php';

        menuToggle.addEventListener('click', () => {
            const isOpened = navMenu.classList.toggle('active');
            menuToggle.setAttribute('aria-expanded', isOpened);
            menuIcon.className = isOpened ? 'bi bi-x' : 'bi bi-list';
            document.body.style.overflow = isOpened ? 'hidden' : '';
        });

        let lastScrollTop = 0;
        window.addEventListener('scroll', function() {
            if (navMenu.classList.contains('active')) return;
            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            if (scrollTop > lastScrollTop && scrollTop > headerHeight) {
                siteHeader.style.top = `-${headerHeight}px`;
            } else {
                siteHeader.style.top = '0';
            }
            lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
        }, false);

        document.querySelectorAll('a[data-anchor-target]').forEach(link => {
            link.addEventListener('click', function(e) {
                const selector = this.getAttribute('data-anchor-target');
                if (!selector || !isHomepage) return;
                
                e.preventDefault();
                const targetElement = document.querySelector(selector);
                if (targetElement) {
                    if (navMenu.classList.contains('active')) {
                        navMenu.classList.remove('active');
                        menuIcon.className = 'bi bi-list';
                        menuToggle.setAttribute('aria-expanded', 'false');
                        document.body.style.overflow = '';
                    }
                    const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - headerHeight;
                    window.scrollTo({ top: targetPosition, behavior: 'smooth' });
                }
            });
        });

        if (isHomepage) {
            const homeLink = document.querySelector('a[href$="index.php"]:not([data-anchor-target])');
            const sections = Array.from(document.querySelectorAll('[id="about"], [id="faq"]'));

            window.addEventListener('scroll', () => {
                const scrollPosition = window.pageYOffset + headerHeight + 50;
                let currentSectionId = '';

                for (const section of sections) {
                    if (scrollPosition >= section.offsetTop) {
                        currentSectionId = section.id;
                    }
                }

                document.querySelectorAll('.nav-links a').forEach(a => a.classList.remove('active'));

                if (currentSectionId) {
                    const activeLink = document.querySelector(`a[data-anchor-target="#${currentSectionId}"]`);
                    if (activeLink) activeLink.classList.add('active');
                } else if (homeLink && window.pageYOffset < (sections[0]?.offsetTop || 0) - headerHeight) {
                    homeLink.classList.add('active');
                }
            });
        }
    });
    </script>
    <main> <!-- Opening main tag, to be closed in footer.php -->