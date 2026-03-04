<?php
$pageTitle = 'Our Rooms';
require_once __DIR__ . '/inc/link.php';
require_once __DIR__ . '/inc/db_config.php';
require_once __DIR__ . '/inc/header.php';

// Get all function rooms
$function_rooms = $conn->query("
    SELECT v.*, 
           (SELECT image_path FROM venue_images WHERE venue_id = v.id AND is_primary = 1 LIMIT 1) as primary_image,
           (SELECT COUNT(*) FROM venue_images WHERE venue_id = v.id) as image_count
    FROM venues v
    WHERE v.is_active = 1 
    AND v.name LIKE '%Function%'
    ORDER BY v.name
");

// Get all guest rooms
$guest_rooms = $conn->query("
    SELECT v.*, 
           (SELECT image_path FROM venue_images WHERE venue_id = v.id AND is_primary = 1 LIMIT 1) as primary_image,
           (SELECT COUNT(*) FROM venue_images WHERE venue_id = v.id) as image_count
    FROM venues v
    WHERE v.is_active = 1 
    AND v.name LIKE '%Guest%'
    ORDER BY v.name
");

// Get all banquet styles
$banquet_styles = $conn->query("
    SELECT * FROM banquet 
    ORDER BY name
");
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400;1,700&family=Outfit:wght@300;400;500;600&display=swap');

:root {
    --red:       #b71c1c;
    --red-dark:  #8b0000;
    --red-glow:  rgba(183,28,28,0.18);
    --gold:      #c9a84c;
    --ink:       #0f0f0f;
    --off-white: #f9f5f0;
    --warm-gray: #e8e2d9;
    --mid-gray:  #9e9990;
    --ease-expo: cubic-bezier(0.16, 1, 0.3, 1);
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'Outfit', sans-serif;
    background: var(--off-white);
    color: var(--ink);
    overflow-x: hidden;
}

/* ═══════════════ HERO ═══════════════ */
.rs-hero {
    height: 100vh;
    min-height: 600px;
    background: var(--ink);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.rs-hero-bg {
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse 80% 60% at 20% 50%, rgba(183,28,28,0.35) 0%, transparent 60%),
        radial-gradient(ellipse 60% 80% at 80% 80%, rgba(139,0,0,0.2) 0%, transparent 55%);
}

.rs-hero-grain {
    position: absolute;
    inset: -50%;
    width: 200%;
    height: 200%;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
    opacity: 0.4;
    animation: grain-shift 8s steps(2) infinite;
    pointer-events: none;
}

@keyframes grain-shift {
    0%   { transform: translate(0,0); }
    10%  { transform: translate(-2%,-3%); }
    20%  { transform: translate(2%,1%); }
    30%  { transform: translate(-1%,2%); }
    40%  { transform: translate(3%,-1%); }
    50%  { transform: translate(-2%,3%); }
    60%  { transform: translate(1%,-2%); }
    70%  { transform: translate(-3%,1%); }
    80%  { transform: translate(2%,2%); }
    90%  { transform: translate(-1%,-1%); }
    100% { transform: translate(0,0); }
}

.hero-shape {
    position: absolute;
    border: 1px solid rgba(201,168,76,0.12);
    border-radius: 50%;
    animation: float-shape 14s ease-in-out infinite;
    pointer-events: none;
}
.hero-shape-1 { width: 520px; height: 520px; top: -120px; right: -160px; }
.hero-shape-2 { width: 300px; height: 300px; bottom: 40px; left: -80px; animation-delay: -5s; }
.hero-shape-3 { width: 180px; height: 180px; top: 30%; right: 20%; animation-delay: -9s; border-color: rgba(183,28,28,0.18); }

@keyframes float-shape {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    33%       { transform: translateY(-22px) rotate(5deg); }
    66%       { transform: translateY(10px) rotate(-4deg); }
}

.rs-hero-content {
    position: relative;
    z-index: 2;
    text-align: center;
    padding: 2rem;
}

.hero-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.73rem;
    font-weight: 600;
    letter-spacing: 3.5px;
    text-transform: uppercase;
    color: var(--gold);
    margin-bottom: 1.75rem;
    opacity: 0;
    animation: hero-up 0.9s var(--ease-expo) 0.2s forwards;
}
.hero-eyebrow::before, .hero-eyebrow::after {
    content: '';
    width: 40px; height: 1px;
    background: var(--gold); opacity: 0.55;
}

.rs-hero h1 {
    font-family: 'Playfair Display', serif;
    font-size: clamp(3rem, 8vw, 7rem);
    font-weight: 900;
    color: white;
    line-height: 0.95;
    letter-spacing: -2px;
    margin-bottom: 1.5rem;
    opacity: 0;
    animation: hero-up 1s var(--ease-expo) 0.4s forwards;
}

.rs-hero h1 em {
    font-style: italic;
    color: transparent;
    -webkit-text-stroke: 2px var(--red);
    display: block;
}

.rs-hero-sub {
    font-size: 1.05rem;
    color: rgba(255,255,255,0.55);
    max-width: 480px;
    margin: 0 auto 2.5rem;
    line-height: 1.75;
    font-weight: 300;
    opacity: 0;
    animation: hero-up 1s var(--ease-expo) 0.6s forwards;
}

.hero-cta-row {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
    opacity: 0;
    animation: hero-up 1s var(--ease-expo) 0.8s forwards;
}

.btn-primary-hero {
    background: var(--red);
    color: white;
    text-decoration: none;
    padding: 0.9rem 2.25rem;
    border-radius: 4px;
    font-weight: 600;
    font-size: 0.9rem;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    display: inline-block;
}
.btn-primary-hero:hover { color: white; transform: translateY(-3px); box-shadow: 0 12px 30px rgba(183,28,28,0.45); }

.btn-ghost-hero {
    background: transparent;
    color: rgba(255,255,255,0.6);
    text-decoration: none;
    padding: 0.9rem 2.25rem;
    border-radius: 4px;
    font-weight: 500;
    font-size: 0.9rem;
    letter-spacing: 0.5px;
    border: 1px solid rgba(255,255,255,0.2);
    transition: all 0.3s ease;
    display: inline-block;
}
.btn-ghost-hero:hover { color: white; border-color: rgba(255,255,255,0.5); }

.hero-scroll-hint {
    position: absolute;
    bottom: 2.5rem;
    left: 50%;
    transform: translateX(-50%);
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    color: rgba(255,255,255,0.3);
    font-size: 0.68rem;
    letter-spacing: 2.5px;
    text-transform: uppercase;
    opacity: 0;
    animation: hero-up 1s var(--ease-expo) 1.2s forwards;
}

.scroll-line {
    width: 1px;
    height: 48px;
    background: linear-gradient(to bottom, rgba(255,255,255,0.4), transparent);
    animation: scroll-pulse 2s ease-in-out infinite;
}

@keyframes scroll-pulse {
    0%, 100% { opacity: 0.3; transform: scaleY(1) translateY(0); }
    50%       { opacity: 0.9; transform: scaleY(1.15) translateY(4px); }
}

@keyframes hero-up {
    from { opacity: 0; transform: translateY(28px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ═══════════════ SECTION INTROS ═══════════════ */
.section-intro {
    padding: 7rem 2rem 0;
    max-width: 1280px;
    margin: 0 auto;
}

.section-label {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 3.5px;
    text-transform: uppercase;
    color: var(--red);
    margin-bottom: 1.25rem;
    opacity: 0;
    transform: translateY(18px);
    transition: all 0.7s var(--ease-expo);
}
.section-label span { width: 28px; height: 2px; background: var(--red); display: inline-block; }
.section-intro.revealed .section-label { opacity: 1; transform: translateY(0); }

.section-headline {
    font-family: 'Playfair Display', serif;
    font-size: clamp(2.4rem, 5vw, 3.8rem);
    font-weight: 700;
    line-height: 1.1;
    color: var(--ink);
    margin-bottom: 1.25rem;
    opacity: 0;
    transform: translateY(28px);
    transition: all 0.85s var(--ease-expo) 0.1s;
}
.section-intro.revealed .section-headline { opacity: 1; transform: translateY(0); }

.section-sub {
    font-size: 1rem;
    color: var(--mid-gray);
    max-width: 500px;
    line-height: 1.8;
    font-weight: 300;
    opacity: 0;
    transform: translateY(18px);
    transition: all 0.85s var(--ease-expo) 0.2s;
}
.section-intro.revealed .section-sub { opacity: 1; transform: translateY(0); }

/* ═══════════════ FUNCTION ROOMS ═══════════════ */
.function-rooms-section {
    padding: 4rem 2rem 6rem;
    max-width: 1280px;
    margin: 0 auto;
}

.fr-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.75rem;
    margin-top: 3.5rem;
}

/* Cards slide up from below on scroll */
.fr-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 20px rgba(0,0,0,0.06);
    cursor: pointer;
    opacity: 0;
    transform: translateY(70px);
    transition:
        opacity 0.85s var(--ease-expo),
        transform 0.85s var(--ease-expo),
        box-shadow 0.4s ease;
}
.fr-card.revealed { opacity: 1; transform: translateY(0); }
.fr-card:hover    { box-shadow: 0 22px 55px rgba(183,28,28,0.14); }

.fr-img-wrap {
    height: 240px;
    overflow: hidden;
    position: relative;
}
.fr-img-wrap img {
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform 1s var(--ease-expo);
}
.fr-card:hover .fr-img-wrap img { transform: scale(1.09); }

.fr-img-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(15,15,15,0.5) 0%, transparent 55%);
}

.fr-badge {
    position: absolute;
    top: 1rem; left: 1rem;
    background: var(--red);
    color: white;
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    padding: 0.3rem 0.75rem;
    border-radius: 3px;
}

.fr-photo-count {
    position: absolute;
    bottom: 1rem; right: 1rem;
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(8px);
    color: white;
    font-size: 0.7rem;
    padding: 0.3rem 0.7rem;
    border-radius: 50px;
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

.fr-body { padding: 1.5rem 1.75rem 1.75rem; }

.fr-name {
    font-family: 'Playfair Display', serif;
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--ink);
    margin-bottom: 0.45rem;
}

.fr-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 0.85rem;
    flex-wrap: wrap;
}

.fr-meta-item {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.78rem;
    color: var(--mid-gray);
    font-weight: 500;
}
.fr-meta-item i { color: var(--red); font-size: 0.82rem; }

.fr-desc {
    font-size: 0.86rem;
    color: #777;
    line-height: 1.7;
    margin-bottom: 1.25rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.fr-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--red);
    text-decoration: none;
    font-size: 0.82rem;
    font-weight: 600;
    letter-spacing: 0.3px;
    transition: gap 0.3s ease;
}
.fr-link:hover { gap: 0.85rem; color: var(--red-dark); }

/* ═══════════════ BANQUET STYLES ═══════════════ */
.banquet-section {
    background: var(--ink);
    padding: 6rem 0;
    margin: 4rem 0;
    overflow: hidden;
    position: relative;
}

.banquet-section::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 70% 80% at 0% 50%, rgba(183,28,28,0.22) 0%, transparent 60%);
    pointer-events: none;
}

.banquet-inner {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 2rem;
}

.banquet-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 2rem;
    margin-bottom: 3rem;
    flex-wrap: wrap;
}

.banquet-title {
    font-family: 'Playfair Display', serif;
    font-size: clamp(2rem, 4vw, 3rem);
    font-weight: 700;
    color: white;
    line-height: 1.15;
    opacity: 0;
    transform: translateX(-40px);
    transition: all 0.9s var(--ease-expo);
}
.banquet-title em { color: var(--gold); font-style: italic; }
.banquet-section.revealed .banquet-title { opacity: 1; transform: translateX(0); }

.banquet-subtitle {
    color: rgb(255, 255, 255);
    font-size: 0.95rem;
    max-width: 280px;
    line-height: 1.7;
    font-weight: 300;
    opacity: 0;
    transform: translateX(40px);
    transition: all 0.9s var(--ease-expo) 0.15s;
}
.banquet-section.revealed .banquet-subtitle { opacity: 1; transform: translateX(0); }

.banquet-track {
    display: flex;
    gap: 1.5rem;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    padding-bottom: 1.25rem;
    scrollbar-width: thin;
    scrollbar-color: var(--red) rgba(255,255,255,0.07);
}
.banquet-track::-webkit-scrollbar { height: 3px; }
.banquet-track::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); border-radius: 10px; }
.banquet-track::-webkit-scrollbar-thumb { background: var(--red); border-radius: 10px; }

/* Banquet cards slide in from the right */
.b-card {
    min-width: 275px;
    max-width: 275px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 14px;
    overflow: hidden;
    scroll-snap-align: start;
    flex-shrink: 0;
    opacity: 0;
    transform: translateX(70px);
    transition:
        opacity 0.75s var(--ease-expo),
        transform 0.75s var(--ease-expo),
        background 0.3s ease,
        border-color 0.3s ease;
}
.b-card.revealed { opacity: 1; transform: translateX(0); }
.b-card:hover { background: rgba(255,255,255,0.09); border-color: rgba(183,28,28,0.4); }

.b-card-img {
    height: 175px;
    overflow: hidden;
}
.b-card-img img {
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform 0.7s var(--ease-expo);
    filter: brightness(0.85) saturate(0.9);
}
.b-card:hover .b-card-img img { transform: scale(1.08); filter: brightness(1) saturate(1.1); }

.b-card-body { padding: 1.2rem 1.4rem 1.4rem; }

.b-card-name {
    font-family: 'Playfair Display', serif;
    font-size: 1.1rem;
    font-weight: 700;
    color: white;
    margin-bottom: 0.45rem;
}

.b-card-desc {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.78);
    line-height: 1.65;
}

/* ═══════════════ GUEST ROOMS — SPLIT REVEAL ═══════════════ */
.guest-rooms-section {
    padding: 4rem 2rem 8rem;
    max-width: 1280px;
    margin: 0 auto;
}

.gr-list {
    display: flex;
    flex-direction: column;
    gap: 3.5rem;
    margin-top: 4rem;
}

.gr-card {
    display: grid;
    grid-template-columns: 1fr 1fr;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 8px 40px rgba(0,0,0,0.09);
    min-height: 400px;
}

/* Image slides in from LEFT (odd) or RIGHT (even) */
.gr-img {
    position: relative;
    overflow: hidden;
    opacity: 0;
    transform: translateX(-90px);
    transition: opacity 1s var(--ease-expo), transform 1s var(--ease-expo);
}

.gr-card.even .gr-img {
    order: 2;
    transform: translateX(90px);
}

.gr-card.revealed .gr-img {
    opacity: 1;
    transform: translateX(0);
}

.gr-img img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 1.1s var(--ease-expo);
}
.gr-card:hover .gr-img img { transform: scale(1.05); }

/* Text slides in from RIGHT (odd) or LEFT (even) */
.gr-body {
    background: white;
    padding: 2.75rem 3.25rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
    opacity: 0;
    transform: translateX(60px);
    transition: opacity 1s var(--ease-expo) 0.15s, transform 1s var(--ease-expo) 0.15s;
}

.gr-card.even .gr-body {
    order: 1;
    transform: translateX(-60px);
}

.gr-card.revealed .gr-body {
    opacity: 1;
    transform: translateX(0);
}

.gr-number {
    font-family: 'Playfair Display', serif;
    font-size: 5rem;
    font-weight: 900;
    color: var(--warm-gray);
    line-height: 1;
    margin-bottom: 0.4rem;
    letter-spacing: -3px;
    user-select: none;
}

.gr-name {
    font-family: 'Playfair Display', serif;
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--ink);
    line-height: 1.2;
    margin-bottom: 0.45rem;
}

.gr-floor {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    color: var(--red);
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    margin-bottom: 1.2rem;
}

.gr-desc {
    font-size: 0.915rem;
    color: #777;
    line-height: 1.8;
    margin-bottom: 1.4rem;
    font-weight: 300;
}

.gr-features {
    display: flex;
    flex-wrap: wrap;
    gap: 0.6rem;
    margin-bottom: 1.6rem;
}

.gr-feat {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    background: var(--off-white);
    border: 1px solid var(--warm-gray);
    padding: 0.32rem 0.85rem;
    border-radius: 50px;
    font-size: 0.77rem;
    color: #555;
    font-weight: 500;
}
.gr-feat i { color: var(--red); font-size: 0.78rem; }

.gr-price-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    padding-top: 1.2rem;
    border-top: 1px solid var(--warm-gray);
}

.gr-price {
    font-family: 'Playfair Display', serif;
    font-size: 1.55rem;
    font-weight: 700;
    color: var(--ink);
}

.gr-price sub {
    font-family: 'Outfit', sans-serif;
    font-size: 0.78rem;
    color: var(--mid-gray);
    font-weight: 400;
}

.btn-book {
    background: var(--red);
    color: white;
    text-decoration: none;
    padding: 0.75rem 1.65rem;
    border-radius: 4px;
    font-weight: 600;
    font-size: 0.84rem;
    letter-spacing: 0.4px;
    transition: all 0.3s ease;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}
.btn-book:hover {
    color: white;
    background: var(--red-dark);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(183,28,28,0.38);
}

/* ═══════════════ CTA ═══════════════ */
.cta-section {
    margin: 2rem 2rem 6rem;
    border-radius: 22px;
    background: linear-gradient(130deg, var(--ink) 0%, #1c0505 100%);
    padding: 5rem 4rem;
    text-align: center;
    position: relative;
    overflow: hidden;
    opacity: 0;
    transform: translateY(40px);
    transition: all 1s var(--ease-expo);
}
.cta-section.revealed { opacity: 1; transform: translateY(0); }

.cta-glow {
    position: absolute;
    width: 600px; height: 600px;
    background: radial-gradient(circle, rgba(183,28,28,0.22), transparent 70%);
    top: 50%; left: 50%;
    transform: translate(-50%,-50%);
    pointer-events: none;
    animation: cta-breathe 5s ease-in-out infinite;
}
@keyframes cta-breathe {
    0%, 100% { transform: translate(-50%,-50%) scale(1); opacity: 1; }
    50%       { transform: translate(-50%,-50%) scale(1.25); opacity: 0.65; }
}

.cta-eyebrow {
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 3.5px;
    text-transform: uppercase;
    color: var(--gold);
    margin-bottom: 1rem;
    position: relative; z-index: 2;
}

.cta-headline {
    font-family: 'Playfair Display', serif;
    font-size: clamp(2rem, 4.5vw, 3.5rem);
    font-weight: 900;
    color: white;
    line-height: 1.1;
    margin-bottom: 1.25rem;
    position: relative; z-index: 2;
}

.cta-sub {
    color: rgba(255,255,255,0.45);
    font-size: 1rem;
    margin-bottom: 2.5rem;
    font-weight: 300;
    position: relative; z-index: 2;
}

.cta-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
    position: relative; z-index: 2;
}

.btn-cta-primary {
    background: var(--red);
    color: white;
    text-decoration: none;
    padding: 1rem 2.75rem;
    border-radius: 4px;
    font-weight: 600;
    font-size: 0.95rem;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    display: inline-block;
}
.btn-cta-primary:hover {
    color: white;
    background: #d32f2f;
    transform: translateY(-3px);
    box-shadow: 0 14px 35px rgba(183,28,28,0.5);
}

.btn-cta-ghost {
    background: transparent;
    color: rgba(255,255,255,0.6);
    text-decoration: none;
    padding: 1rem 2.75rem;
    border-radius: 4px;
    font-weight: 500;
    font-size: 0.95rem;
    border: 1px solid rgba(255,255,255,0.18);
    transition: all 0.3s ease;
    display: inline-block;
}
.btn-cta-ghost:hover { color: white; border-color: rgba(255,255,255,0.5); }

/* ═══════════════ RESPONSIVE ═══════════════ */
@media (max-width: 1024px) {
    .fr-grid { grid-template-columns: repeat(2, 1fr); }

    /* On smaller screens, guest cards stack vertically */
    .gr-card, .gr-card.even { grid-template-columns: 1fr; min-height: auto; }
    .gr-card.even .gr-img { order: 0; }
    .gr-card.even .gr-body { order: 1; transform: translateY(40px); }
    .gr-img { height: 280px; transform: translateY(60px) !important; }
    .gr-card.even .gr-img { transform: translateY(60px) !important; }
    .gr-body { transform: translateY(40px) !important; padding: 2rem 2.25rem; }
    .gr-card.revealed .gr-img,
    .gr-card.revealed .gr-body { transform: translateY(0) !important; }
}

@media (max-width: 768px) {
    .fr-grid { grid-template-columns: 1fr; }
    .banquet-section { padding: 4rem 0; }
    .cta-section { padding: 3.5rem 1.75rem; margin: 2rem 1rem 4rem; }
    .rs-hero h1 em { -webkit-text-stroke-width: 1px; }
    .gr-body { padding: 1.75rem; }
    .gr-number { font-size: 3.5rem; }
}
</style>

<main>

    <!-- ══════════════ HERO ══════════════ -->
    <section class="rs-hero">
        <div class="rs-hero-bg"></div>
        <div class="rs-hero-grain"></div>
        <div class="hero-shape hero-shape-1"></div>
        <div class="hero-shape hero-shape-2"></div>
        <div class="hero-shape hero-shape-3"></div>

        <div class="rs-hero-content">
            <div class="hero-eyebrow">BSU Hostel &nbsp;·&nbsp; Spaces &amp; Accommodations</div>
            <h1>
                Exceptional
                <em>Spaces</em>
            </h1>
            <p class="rs-hero-sub">
                From grand function rooms built for memorable events to restful guest accommodations — every space is designed to inspire.
            </p>
            <div class="hero-cta-row">
                <a href="reservation.php" class="btn-primary-hero">Reserve a Space</a>
                <a href="#function-rooms" class="btn-ghost-hero">Explore Rooms</a>
            </div>
        </div>

        <div class="hero-scroll-hint">
            <span>Scroll</span>
            <div class="scroll-line"></div>
        </div>
    </section>

    <!-- ══════════════ FUNCTION ROOMS ══════════════ -->
    <div class="section-intro" id="function-rooms">
        <div class="section-label"><span></span> Function Rooms</div>
        <h2 class="section-headline">Spaces That<br>Set the Stage</h2>
        <p class="section-sub">Versatile, fully-equipped venues for seminars, assemblies, workshops, and every celebration in between.</p>
    </div>

    <section class="function-rooms-section">
        <div class="fr-grid">
            <?php
            $fr_index = 0;
            if ($function_rooms && $function_rooms->num_rows > 0):
                while ($room = $function_rooms->fetch_assoc()):
                    $fr_index++;
            ?>
            <div class="fr-card" style="transition-delay: <?= ($fr_index - 1) * 0.1 ?>s">
                <div class="fr-img-wrap">
                    <img src="<?= $room['primary_image'] ? '../assets/images/rooms/' . htmlspecialchars($room['primary_image']) : 'https://images.unsplash.com/photo-1511578314322-379afb476865?w=700&q=80' ?>"
                         alt="<?= htmlspecialchars($room['name']) ?>"
                         loading="lazy"
                         onerror="this.src='https://images.unsplash.com/photo-1511578314322-379afb476865?w=700&q=80'">
                    <div class="fr-img-overlay"></div>
                    <div class="fr-badge">Function Room</div>
                    <?php if ($room['image_count'] > 0): ?>
                        <div class="fr-photo-count">
                            <i class="bi bi-images"></i> <?= $room['image_count'] ?> photos
                        </div>
                    <?php endif; ?>
                </div>
                <div class="fr-body">
                    <div class="fr-name"><?= htmlspecialchars($room['name']) ?></div>
                    <div class="fr-meta">
                        <div class="fr-meta-item">
                            <i class="bi bi-people-fill"></i>
                            Up to <?= htmlspecialchars($room['capacity'] ?? '50') ?> guests
                        </div>
                        <?php if (!empty($room['floor'])): ?>
                        <div class="fr-meta-item">
                            <i class="bi bi-layers-fill"></i>
                            <?= htmlspecialchars($room['floor']) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <p class="fr-desc"><?= htmlspecialchars($room['description'] ?? 'A versatile space perfect for your events.') ?></p>
                    <a href="reservation.php?room=<?= $room['id'] ?>" class="fr-link">
                        Reserve this room <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            <?php endwhile; else: ?>
                <div style="grid-column:1/-1;text-align:center;padding:3rem;color:#999;">
                    <i class="bi bi-building" style="font-size:2.5rem;display:block;margin-bottom:0.75rem;"></i>
                    No function rooms available at the moment.
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ══════════════ BANQUET STYLES ══════════════ -->
    <section class="banquet-section" id="banquet-styles">
        <div class="banquet-inner">
            <div class="banquet-header">
                <div class="banquet-title">
                    Available<br><em>Banquet Styles</em>
                </div>
                <p class="banquet-subtitle">
                    Craft the perfect atmosphere. Choose a layout that matches your event's character.
                </p>
            </div>
            <div class="banquet-track">
                <?php
                $b_index = 0;
                if ($banquet_styles && $banquet_styles->num_rows > 0):
                    while ($style = $banquet_styles->fetch_assoc()):
                        $b_index++;
                ?>
                <div class="b-card" style="transition-delay: <?= ($b_index - 1) * 0.12 ?>s">
                    <div class="b-card-img">
                        <img src="<?= $style['image'] ? '../assets/images/banquet/' . htmlspecialchars($style['image']) : 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?w=500&q=80' ?>"
                             alt="<?= htmlspecialchars($style['name']) ?>"
                             loading="lazy"
                             onerror="this.src='https://images.unsplash.com/photo-1519167758481-83f550bb49b3?w=500&q=80'">
                    </div>
                    <div class="b-card-body">
                        <div class="b-card-name"><?= htmlspecialchars($style['name']) ?></div>
                        <p class="b-card-desc"><?= htmlspecialchars($style['description'] ?? 'Perfect for your event setup') ?></p>
                    </div>
                </div>
                <?php endwhile; endif; ?>
            </div>
        </div>
    </section>

    <!-- ══════════════ GUEST ROOMS ══════════════ -->
    <div class="section-intro" id="guest-rooms">
        <div class="section-label"><span></span> Guest Rooms</div>
        <h2 class="section-headline">Rest Well,<br>Stay Inspired</h2>
        <p class="section-sub">Thoughtfully designed accommodations where comfort meets convenience — the perfect home away from home.</p>
    </div>

    <section class="guest-rooms-section">
        <div class="gr-list">
            <?php
            $gr_index = 0;
            if ($guest_rooms && $guest_rooms->num_rows > 0):
                while ($room = $guest_rooms->fetch_assoc()):
                    $gr_index++;
                    $is_even = ($gr_index % 2 === 0);
            ?>
            <div class="gr-card <?= $is_even ? 'even' : '' ?>">
                <div class="gr-img">
                    <img src="<?= $room['primary_image'] ? '../assets/images/rooms/' . htmlspecialchars($room['primary_image']) : 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800&q=80' ?>"
                         alt="<?= htmlspecialchars($room['name']) ?>"
                         loading="lazy"
                         onerror="this.src='https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800&q=80'">
                </div>
                <div class="gr-body">
                    <div class="gr-number">0<?= $gr_index ?></div>
                    <div class="gr-name"><?= htmlspecialchars($room['name']) ?></div>
                    <div class="gr-floor">
                        <i class="bi bi-geo-alt-fill"></i>
                        <?= htmlspecialchars($room['floor'] ?? 'Ground Floor') ?>
                    </div>
                    <p class="gr-desc">
                        <?= htmlspecialchars(substr($room['description'] ?? 'A comfortable and well-appointed room for your stay.', 0, 160)) ?><?= strlen($room['description'] ?? '') > 160 ? '…' : '' ?>
                    </p>
                    <div class="gr-features">
                        <span class="gr-feat"><i class="bi bi-people-fill"></i> <?= htmlspecialchars($room['capacity'] ?? '2') ?> guests</span>
                        <span class="gr-feat"><i class="bi bi-wifi"></i> Free WiFi</span>
                        <span class="gr-feat"><i class="bi bi-snow"></i> Air-conditioned</span>
                        <span class="gr-feat"><i class="bi bi-moon-stars"></i> Overnight stay</span>
                    </div>
                    <div class="gr-price-row">
                        <div class="gr-price">₱2,500 <sub>/ night</sub></div>
                        <a href="reservation.php?room=<?= $room['id'] ?>" class="btn-book">
                            Book Now <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; else: ?>
                <div style="text-align:center;padding:3rem;color:#999;">
                    <i class="bi bi-door-open" style="font-size:2.5rem;display:block;margin-bottom:0.75rem;"></i>
                    No guest rooms available at the moment.
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ══════════════ CTA ══════════════ -->
    <div class="cta-section">
        <div class="cta-glow"></div>
        <div class="cta-eyebrow">Ready to plan your event?</div>
        <h2 class="cta-headline">Book Your Perfect Space Today</h2>
        <p class="cta-sub">Simple online reservation, fast confirmation — we handle the rest.</p>
        <div class="cta-buttons">
            <a href="reservation.php" class="btn-cta-primary">Make a Reservation</a>
            <a href="faqs.php" class="btn-cta-ghost">View FAQs</a>
        </div>
    </div>

</main>

<script>
(function () {
    'use strict';

    /* ── Smooth anchor scroll ── */
    document.querySelectorAll('a[href^="#"]').forEach(function (a) {
        a.addEventListener('click', function (e) {
            var target = document.querySelector(this.getAttribute('href'));
            if (!target) return;
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    /* ── Generic reveal observer ── */
    function observe(selector, options) {
        var obs = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    obs.unobserve(entry.target);
                }
            });
        }, Object.assign({ threshold: 0.12, rootMargin: '0px 0px -50px 0px' }, options));

        document.querySelectorAll(selector).forEach(function (el) { obs.observe(el); });
    }

    /* Section title text reveals */
    observe('.section-intro',    { threshold: 0.2 });

    /* Function room cards — upward stagger */
    observe('.fr-card',          { threshold: 0.1 });

    /* Banquet section headings and cards */
    observe('.banquet-section',  { threshold: 0.15 });
    observe('.b-card',           { threshold: 0.08 });

    /* Guest room split reveals — image from left/right, text from opposite side */
    observe('.gr-card',          { threshold: 0.1 });

    /* CTA */
    observe('.cta-section',      { threshold: 0.18 });

    /* ── Subtle hero fade on scroll ── */
    var hero = document.querySelector('.rs-hero');
    if (hero) {
        window.addEventListener('scroll', function () {
            var progress = Math.min(window.scrollY / (window.innerHeight * 0.75), 1);
            hero.style.opacity = 1 - progress * 0.35;
        }, { passive: true });
    }

    /* ── Magnetic hover on CTA buttons ── */
    document.querySelectorAll('.btn-book, .btn-primary-hero, .btn-cta-primary').forEach(function (btn) {
        btn.addEventListener('mousemove', function (e) {
            var r = btn.getBoundingClientRect();
            var x = (e.clientX - r.left - r.width / 2) * 0.2;
            var y = (e.clientY - r.top - r.height / 2) * 0.2;
            btn.style.transform = 'translate(' + x + 'px,' + (y - 2) + 'px)';
        });
        btn.addEventListener('mouseleave', function () {
            btn.style.transform = '';
        });
    });

    /* ── Count-up animation for capacity numbers on fr-cards ── */
    var countObs = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (!entry.isIntersecting) return;
            var el = entry.target;
            var target = parseInt(el.getAttribute('data-target'), 10);
            var start = 0;
            var duration = 1200;
            var startTime = null;
            function step(ts) {
                if (!startTime) startTime = ts;
                var progress = Math.min((ts - startTime) / duration, 1);
                var eased = 1 - Math.pow(1 - progress, 3);
                el.textContent = Math.floor(eased * target);
                if (progress < 1) requestAnimationFrame(step);
                else el.textContent = target;
            }
            requestAnimationFrame(step);
            countObs.unobserve(el);
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('[data-target]').forEach(function (el) {
        countObs.observe(el);
    });

})();
</script>

<?php require_once __DIR__ . '/inc/footer.php'; ?>