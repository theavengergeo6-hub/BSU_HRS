<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <?php require('inc/link.php');?>
    <title><?php echo $settings_r['site_title'] ?> HOME</title>
    
    <style>
    /* Hero Section Styles */
    .hero-section {
        position: relative;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 100px 0;
        overflow: hidden;
        min-height: 600px;
        display: flex;
        align-items: center;
    }

    /* Animated Background */
    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><path d="M30 0 L60 30 L30 60 L0 30 Z" fill="rgba(255,255,255,0.02)"/></svg>');
        background-size: 60px 60px;
        animation: backgroundMove 20s linear infinite;
        opacity: 0.5;
    }

    /* Floating Elements */
    .hero-section::after {
        content: '●';
        position: absolute;
        color: rgba(255,255,255,0.1);
        font-size: 300px;
        top: -50px;
        right: -50px;
        animation: float 8s ease-in-out infinite;
        transform: rotate(45deg);
    }

    /* Animated Circles */
    .hero-section .container {
        position: relative;
        z-index: 2;
    }

    /* Floating Circles Animation */
    @keyframes float {
        0%, 100% { transform: translateY(0) rotate(45deg); }
        50% { transform: translateY(-30px) rotate(55deg); }
    }

    @keyframes backgroundMove {
        0% { background-position: 0 0; }
        100% { background-position: 60px 60px; }
    }

    /* Title Animation */
    .animate-title {
        animation: slideDown 1s ease-out;
        color: white;
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 20px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        position: relative;
        display: inline-block;
    }

    /* Title Underline Animation */
    .title-underline {
        width: 0;
        height: 4px;
        background: linear-gradient(90deg, #fff, #ffd700);
        margin: 20px auto;
        border-radius: 2px;
        animation: expandWidth 1s ease-out 0.5s forwards;
    }

    @keyframes expandWidth {
        from { width: 0; }
        to { width: 150px; }
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Description Animation */
    .animate-description {
        animation: fadeInUp 1s ease-out 0.3s both;
        color: rgba(255,255,255,0.95);
        font-size: 1.2rem;
        line-height: 1.8;
        max-width: 800px;
        margin: 0 auto 30px;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Buttons Animation */
    .animate-buttons {
        animation: fadeIn 1s ease-out 0.6s both;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* Button Styles */
    .btn-primary-custom {
        background: white;
        color: #667eea;
        padding: 12px 30px;
        border-radius: 50px;
        font-weight: 600;
        border: 2px solid white;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .btn-primary-custom:hover {
        background: transparent;
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }

    .btn-outline-custom {
        background: transparent;
        color: white;
        padding: 12px 30px;
        border-radius: 50px;
        font-weight: 600;
        border: 2px solid white;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .btn-outline-custom:hover {
        background: white;
        color: #667eea;
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }

    /* Particle Effect */
    .particle {
        position: absolute;
        pointer-events: none;
        opacity: 0.5;
        animation: particleFloat 3s ease-in-out infinite;
    }

    @keyframes particleFloat {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(180deg); }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .hero-section {
            padding: 60px 0;
            min-height: 500px;
        }
        
        .animate-title {
            font-size: 2.5rem;
        }
        
        .animate-description {
            font-size: 1rem;
            padding: 0 20px;
        }
        
        .btn-primary-custom, .btn-outline-custom {
            padding: 10px 25px;
            margin: 5px;
        }
    }

    @media (max-width: 576px) {
        .animate-title {
            font-size: 2rem;
        }
        
        .hero-buttons {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        
        .btn-primary-custom, .btn-outline-custom {
            width: 200px;
            margin: 0;
        }
    }

    /* Optional: Add hover effect on title */
    .animate-title:hover {
        animation: glitch 0.3s ease-in-out;
    }

    @keyframes glitch {
        0% { transform: skew(0deg); }
        20% { transform: skew(10deg); }
        40% { transform: skew(-10deg); }
        60% { transform: skew(5deg); }
        80% { transform: skew(-5deg); }
        100% { transform: skew(0deg); }
    }
        /* Red and White Theme with Animations */
        :root {
            --primary-red: #c02510;
            --dark-red: #8b1e0c;
            --light-red: #fdeae8;
            --pure-white: #ffffff;
            --off-white: #fafafa;
            --gray-light: #f5f5f5;
            --text-dark: #2c3e50;
            --shadow-sm: 0 4px 6px rgba(192, 37, 16, 0.1);
            --shadow-md: 0 6px 12px rgba(192, 37, 16, 0.15);
            --shadow-lg: 0 10px 25px rgba(192, 37, 16, 0.2);
            --transition-smooth: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text-dark);
            background-color: var(--off-white);
            overflow-x: hidden;
            margin: 0;
            padding: 0;
        }

        /* Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes expandLine {
            from { width: 0; }
            to { width: 80px; }
        }

        @keyframes spinner {
            to { transform: rotate(360deg); }
        }

        /* Header/Navbar Styles */
        .navbar {
            background: var(--pure-white) !important;
            box-shadow: var(--shadow-sm);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            animation: slideIn 0.5s ease-out;
        }

        .navbar-brand {
            color: var(--primary-red) !important;
            font-weight: 700;
            font-size: 1.5rem;
        }

        .nav-link {
            color: var(--text-dark) !important;
            font-weight: 500;
            margin: 0 0.5rem;
            position: relative;
            transition: var(--transition-smooth);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: var(--primary-red);
            transition: var(--transition-smooth);
        }

        .nav-link:hover {
            color: var(--primary-red) !important;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .nav-link.active {
            color: var(--primary-red) !important;
        }

        .nav-link.active::after {
            width: 100%;
        }

        .btn-login {
            background: transparent;
            border: 2px solid var(--primary-red);
            color: var(--primary-red);
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-weight: 500;
            transition: var(--transition-smooth);
            margin-right: 0.5rem;
        }

        .btn-login:hover {
            background: var(--primary-red);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-register {
            background: var(--primary-red);
            border: 2px solid var(--primary-red);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-weight: 500;
            transition: var(--transition-smooth);
        }

        .btn-register:hover {
            background: var(--dark-red);
            border-color: var(--dark-red);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--primary-red), var(--dark-red));
            color: white;
            padding: 4rem 0;
            margin-bottom: 2rem;
            animation: fadeInUp 0.8s ease-out;
        }

        .hero-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .hero-description {
            font-size: 1.1rem;
            opacity: 0.9;
            line-height: 1.8;
        }

        /* Apply animations to sections */
        .availability-form {
            animation: slideIn 0.8s ease-out;
            margin-top: -50px;
            z-index: 2;
            position: relative;
            margin-bottom: 3rem;
        }

        .room-card {
            animation: scaleIn 0.6s ease-out;
            animation-fill-mode: both;
            height: 100%;
        }

        .room-card:nth-child(1) { animation-delay: 0.1s; }
        .room-card:nth-child(2) { animation-delay: 0.2s; }
        .room-card:nth-child(3) { animation-delay: 0.3s; }
        .room-card:nth-child(4) { animation-delay: 0.4s; }
        .room-card:nth-child(5) { animation-delay: 0.5s; }
        .room-card:nth-child(6) { animation-delay: 0.6s; }

        .facility-item {
            animation: fadeInUp 0.5s ease-out;
            animation-fill-mode: both;
            height: 100%;
        }

        .facility-item:nth-child(1) { animation-delay: 0.1s; }
        .facility-item:nth-child(2) { animation-delay: 0.2s; }
        .facility-item:nth-child(3) { animation-delay: 0.3s; }
        .facility-item:nth-child(4) { animation-delay: 0.4s; }
        .facility-item:nth-child(5) { animation-delay: 0.5s; }
        .facility-item:nth-child(6) { animation-delay: 0.6s; }

        .section-title {
            position: relative;
            display: inline-block;
            margin-bottom: 2rem;
            animation: fadeInLeft 0.6s ease-out;
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-red);
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 3px;
            background: var(--primary-red);
            animation: expandLine 0.8s ease-out forwards;
            animation-delay: 0.3s;
        }

        /* Red and White Theme Cards */
        .card {
            background: var(--pure-white);
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: var(--transition-smooth);
            box-shadow: var(--shadow-sm);
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: var(--shadow-lg);
        }

        .card-img-top {
            transition: var(--transition-smooth);
            height: 250px;
            object-fit: cover;
            width: 100%;
        }

        .card:hover .card-img-top {
            transform: scale(1.1);
        }

        .card-body {
            padding: 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .card-title {
            color: var(--primary-red);
            font-weight: 700;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .card-text {
            color: var(--text-dark);
            line-height: 1.6;
            flex: 1;
        }

        /* Custom Button Styles */
        .custom-bg {
            background: var(--primary-red) !important;
            transition: var(--transition-smooth);
            position: relative;
            overflow: hidden;
            z-index: 1;
            border: none;
            color: white;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 500;
        }

        .custom-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s ease;
            z-index: -1;
        }

        .custom-bg:hover::before {
            left: 100%;
        }

        .custom-bg:hover {
            background: var(--dark-red) !important;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-outline-red {
            border: 2px solid var(--primary-red);
            color: var(--primary-red);
            background: transparent;
            transition: var(--transition-smooth);
            position: relative;
            overflow: hidden;
            z-index: 1;
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 500;
        }

        .btn-outline-red::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--primary-red);
            transition: left 0.3s ease;
            z-index: -1;
        }

        .btn-outline-red:hover::before {
            left: 0;
        }

        .btn-outline-red:hover {
            color: var(--pure-white);
            border-color: var(--primary-red);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* Form Styling */
        .availability-form .bg-white {
            background: var(--pure-white) !important;
            border-radius: 15px;
            box-shadow: var(--shadow-lg) !important;
            transition: var(--transition-smooth);
            border: 2px solid transparent;
            padding: 2rem;
        }

        .availability-form .bg-white:hover {
            box-shadow: var(--shadow-lg) !important;
            border-color: var(--light-red);
            transform: translateY(-5px);
        }

        .form-control {
            border: 2px solid #eee;
            border-radius: 10px;
            padding: 12px;
            transition: var(--transition-smooth);
        }

        .form-control:focus {
            border-color: var(--primary-red);
            box-shadow: 0 0 0 0.2rem rgba(192, 37, 16, 0.25);
            transform: translateY(-2px);
        }

       /* Swiper Customization */
        .swiper-container {
            width: 100%;
            height: 400px;  
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            animation: fadeInUp 1s ease-out;
            margin-bottom: 2rem;
        }

        .swiper-slide {
            border-radius: 20px;
            overflow: hidden;
        }

        .swiper-slide img {
            transition: transform 0.8s ease;
            width: 100%;
            height: 400px;  /* Must match the container height */
            object-fit: cover;  /* This ensures images cover the area without distortion */
        }

        .swiper-slide:hover img {
            transform: scale(1.1);
        }

        .swiper-pagination-bullet {
            width: 9px;
            height: 12px;
            background: white;
            opacity: 0.7;
            transition: var(--transition-smooth);
        }

        .swiper-pagination-bullet-active {
            background: var(--primary-red) !important;
            opacity: 1;
            transform: scale(1.2);
            width: 30px;
            border-radius: 10px;
        }

        /* Facility Items */
        .facility-item {
            background: var(--pure-white);
            border-radius: 15px;
            padding: 2rem 1rem;
            transition: var(--transition-smooth);
            box-shadow: var(--shadow-sm);
            height: 100%;
            border: 2px solid transparent;
            text-align: center;
        }

        .facility-item:hover {
            transform: translateY(-10px) rotate(1deg);
            box-shadow: var(--shadow-lg);
            border-color: var(--light-red);
        }

        .facility-item img {
            transition: var(--transition-smooth);
            filter: brightness(1);
            width: 60px;
            height: 60px;
            object-fit: contain;
            margin-bottom: 1rem;
        }

        .facility-item:hover img {
            transform: scale(1.2) rotate(5deg);
            filter: brightness(1.1);
        }

        .facility-item h5 {
            color: var(--primary-red);
            font-weight: 600;
            margin: 0;
        }

        /* Testimonial Cards */
        .testimonial-card {
            background: var(--pure-white);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: var(--shadow-sm);
            transition: var(--transition-smooth);
            position: relative;
            overflow: hidden;
            border: 2px solid transparent;
            height: 100%;
        }

        .testimonial-card::before {
            content: '"';
            position: absolute;
            top: -20px;
            right: 20px;
            font-size: 150px;
            color: rgba(192, 37, 16, 0.1);
            font-family: serif;
            transition: var(--transition-smooth);
            line-height: 1;
        }

        .testimonial-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-lg);
            border-color: var(--light-red);
        }

        .testimonial-card:hover::before {
            transform: scale(1.2) rotate(5deg);
            color: rgba(192, 37, 16, 0.2);
        }

        .testimonial-card img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-red);
            transition: var(--transition-smooth);
        }

        .testimonial-card:hover img {
            transform: scale(1.1);
            border-color: var(--dark-red);
        }

        /* Modal Styling */
        .modal-content {
            border-radius: 20px;
            border: none;
            overflow: hidden;
            animation: scaleIn 0.3s ease-out;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-red), var(--dark-red));
            color: var(--pure-white);
            border: none;
            padding: 1.5rem;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.8;
            transition: var(--transition-smooth);
        }

        .modal-header .btn-close:hover {
            opacity: 1;
            transform: rotate(90deg);
        }

        /* Badge Styling */
        .badge.bg-light {
            background: var(--light-red) !important;
            color: var(--dark-red) !important;
            padding: 8px 12px;
            transition: var(--transition-smooth);
        }

        .badge.bg-light:hover {
            background: var(--primary-red) !important;
            color: white !important;
            transform: translateY(-2px);
        }

        /* Rating Stars */
        .rating i {
            transition: var(--transition-smooth);
            cursor: pointer;
        }

        .rating i:hover {
            transform: scale(1.2);
            color: gold;
        }

        /* Footer Styling */
        footer {
            background: var(--pure-white);
            border-top: 3px solid var(--primary-red);
            animation: fadeInUp 0.8s ease-out;
            margin-top: 4rem;
            padding: 3rem 0 2rem;
        }

        .footer-title {
            color: var(--primary-red);
            font-weight: 700;
            margin-bottom: 1.5rem;
            font-size: 1.25rem;
        }

        .footer-link {
            color: var(--text-dark);
            text-decoration: none;
            transition: var(--transition-smooth);
            display: block;
            margin-bottom: 0.75rem;
        }

        .footer-link:hover {
            color: var(--primary-red);
            transform: translateX(5px);
        }

        .social-link {
            display: inline-block;
            width: 40px;
            height: 40px;
            background: var(--light-red);
            color: var(--primary-red);
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
            margin-right: 0.5rem;
            transition: var(--transition-smooth);
        }

        .social-link:hover {
            background: var(--primary-red);
            color: white;
            transform: translateY(-3px);
        }

        .copyright {
            text-align: center;
            padding-top: 2rem;
            margin-top: 2rem;
            border-top: 1px solid #eee;
            color: #666;
        }

        /* Loading Spinner */
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid var(--light-red);
            border-top-color: var(--primary-red);
            border-radius: 50%;
            animation: spinner 0.8s linear infinite;
            margin: 20px auto;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .availability-form {
                margin-top: 20px;
                padding: 0 20px;
            }
            
            .room-card {
                animation: fadeInUp 0.5s ease-out;
            }
            
            .swiper-slide img {
                height: 300px;
            }
            
            .hero-title {
                font-size: 2rem;
            }
            
            .section-title {
                font-size: 1.75rem;
            }
        }

        @media (max-width: 576px) {
            .availability-form {
                margin-top: 25px;
                padding: 0 15px;
            }
            
            .card-img-top {
                height: 200px;
            }
            
            .hero-title {
                font-size: 1.75rem;
            }
            
            .hero-description {
                font-size: 1rem;
            }
        }

        /* Smooth hover effects for all clickable elements */
        a, button {
            transition: var(--transition-smooth);
        }

        .hover-text-danger:hover {
            color: var(--primary-red) !important;
        }

        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--off-white);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-red);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--dark-red);
        }

        /* Image error handling */
        .img-error {
            background: var(--light-red);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 200px;
            color: var(--primary-red);
            font-size: 14px;
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            border: 2px dashed var(--primary-red);
            width: 100%;
        }

        .img-error i {
            font-size: 48px;
            margin-bottom: 10px;
            color: var(--primary-red);
        }

        /* Container spacing */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        section {
            margin-bottom: 4rem;
        }

        /* Row fixes */
        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }

        .col-lg-4, .col-md-6, .col-lg-2, .col-md-4, .col-6 {
            position: relative;
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
        }

        @media (min-width: 768px) {
            .col-md-4 {
                flex: 0 0 33.333333%;
                max-width: 33.333333%;
            }
            
            .col-md-6 {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }

        @media (min-width: 992px) {
            .col-lg-2 {
                flex: 0 0 16.666667%;
                max-width: 16.666667%;
            }
            
            .col-lg-4 {
                flex: 0 0 33.333333%;
                max-width: 33.333333%;
            }
            
            .col-lg-8 {
                flex: 0 0 66.666667%;
                max-width: 66.666667%;
            }
        }

        /* Calendar Styles */
        .calendar-grid {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .calendar-header {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            background: var(--primary-red);
            color: white;
            text-align: center;
            padding: 10px;
            font-weight: 600;
        }

        .calendar-days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background: #eee;
            border: 1px solid #ddd;
        }

        .calendar-day {
            background: white;
            min-height: 100px;
            padding: 8px;
            cursor: pointer;
            transition: var(--transition-smooth);
            position: relative;
            overflow-y: auto;
        }

        .calendar-day:hover {
            background: var(--light-red);
            transform: scale(1.02);
            z-index: 2;
            box-shadow: var(--shadow-md);
        }

        .calendar-day.other-month {
            background: #f9f9f9;
            color: #999;
        }

        .calendar-day.available {
            border-left: 4px solid #28a745;
        }

        .calendar-day.booked {
            border-left: 4px solid #dc3545;
            background: #fff5f5;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .calendar-day.partial {
            border-left: 4px solid #ffc107;
            background: #fff9e6;
        }

        .calendar-day.selected {
            background: #cce5ff;
            border: 2px solid #007bff;
            transform: scale(1.02);
            z-index: 3;
        }

        .day-number {
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--text-dark);
        }

        .booked-indicator {
            font-size: 11px;
            margin-top: 5px;
        }

        .booked-room {
            background: #dc3545;
            color: white;
            padding: 2px 4px;
            border-radius: 3px;
            margin-bottom: 2px;
            font-size: 10px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .partial-room {
            background: #ffc107;
            color: #333;
            padding: 2px 4px;
            border-radius: 3px;
            margin-bottom: 2px;
            font-size: 10px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .calendar-legend {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            padding: 10px;
            background: white;
            border-radius: 8px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
        }

        .legend-item .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .legend-item.available .dot { background: #28a745; }
        .legend-item.booked .dot { background: #dc3545; }
        .legend-item.partial .dot { background: #ffc107; }
        .legend-item.selected .dot { background: #007bff; }

        .selected-dates-summary {
            background: var(--light-red);
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid var(--primary-red);
        }

        #selectedDatesList {
            margin-bottom: 10px;
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .calendar-day {
                min-height: 70px;
                padding: 4px;
                font-size: 12px;
            }
            
            .calendar-header {
                font-size: 12px;
                padding: 5px;
            }
            
            .booked-room, .partial-room {
                font-size: 8px;
                padding: 1px 2px;
            }
        }


    </style>
</head>
<body>
    <!-- Navbar/Header Section -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <span style="color: var(--primary-red);">BatStateU</span> HOSTEL
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="rooms.php">Rooms</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="banquet.php">Banquet</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="facilities.php">Facilities</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="login.php" class="btn btn-login me-2">Login</a>
                    <a href="register.php" class="btn btn-register">Register</a>
                </div>
            </div>
        </div>
    </nav>



    <!-- Hostel image slider -->
    <div class="container-fluid px-lg-4 mt-4">
        <div class="swiper swiper-container">
            <div class="swiper-wrapper">
                <?php 
                $res = selectAll('hostel');
                if ($res && mysqli_num_rows($res) > 0) {
                    while ($row = mysqli_fetch_assoc($res)) {
                        $image_path = "images/hostel/" . $row['image'];
                        $full_path = $_SERVER['DOCUMENT_ROOT'] . "/hostel/" . $image_path;
                        
                        if (file_exists($full_path) && !empty($row['image'])) {
                            echo <<<data
                            <div class="swiper-slide">
                                <img src="/hostel/$image_path" class="w-100 d-block" alt="Hostel Image">
                            </div>
                            data;
                        } else {
                            echo <<<data
                            <div class="swiper-slide">
                                <div class="img-error" style="height: 500px;">
                                    <div>
                                        <i class="bi bi-building"></i>
                                        <p>Hostel Image Coming Soon</p>
                                    </div>
                                </div>
                            </div>
                            data;
                        }
                    }
                } else {
                    echo <<<data
                    <div class="swiper-slide">
                        <div class="img-error" style="height: 500px;">
                            <div>
                                <i class="bi bi-building"></i>
                                <p>No Hostel Images Available</p>
                            </div>
                        </div>
                    </div>
                    data;
                }
                ?>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>

<!-- Availability Form with Calendar -->
<div class="container availability-form">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="bg-white shadow p-4 rounded">
                <h5 class="mb-4 text-center section-title">Check Room Availability</h5>
                
                <!-- Room Type Selection -->
                <div class="row mb-4">
                    <div class="col-md-6 mx-auto">
                        <select class="form-control" id="roomTypeSelect">
                            <option value="">Select Room Type</option>
                            <option value="guest">Guest Rooms</option>
                            <option value="function">Function Rooms</option>
                        </select>
                    </div>
                </div>

                <!-- Calendar Container -->
                <div id="calendarContainer" style="display: none;">
                    <!-- Month Navigation -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <button class="btn btn-outline-red" id="prevMonth">
                            <i class="bi bi-chevron-left"></i> Previous
                        </button>
                        <h4 id="currentMonthYear" class="mb-0"></h4>
                        <button class="btn btn-outline-red" id="nextMonth">
                            Next <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>

                    <!-- Calendar Grid -->
                    <div class="calendar-grid">
                        <!-- Day headers -->
                        <div class="calendar-header">
                            <div>Sun</div>
                            <div>Mon</div>
                            <div>Tue</div>
                            <div>Wed</div>
                            <div>Thu</div>
                            <div>Fri</div>
                            <div>Sat</div>
                        </div>
                        <!-- Calendar days will be inserted here -->
                        <div id="calendarDays" class="calendar-days"></div>
                    </div>

                    <!-- Legend -->
                    <div class="calendar-legend mt-3">
                        <span class="legend-item available">
                            <span class="dot"></span> Available
                        </span>
                        <span class="legend-item booked">
                            <span class="dot"></span> Booked
                        </span>
                        <span class="legend-item partial">
                            <span class="dot"></span> Partially Booked
                        </span>
                        <span class="legend-item selected">
                            <span class="dot"></span> Selected
                        </span>
                    </div>

                    <!-- Selected Dates Summary -->
                    <div id="selectedDatesSummary" class="selected-dates-summary mt-3" style="display: none;">
                        <h6>Selected Dates:</h6>
                        <p id="selectedDatesList"></p>
                        <button class="btn custom-bg" id="checkAvailabilityBtn">Check Availability</button>
                    </div>
                </div>

                <!-- Available Rooms Modal -->
                <div class="modal fade" id="availableRoomsModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">Available Rooms</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body" id="availableRoomsList">
                                <!-- Rooms will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




    <!-- Room Types Section -->
    <section>
        <div class="container">
            <h2 class="text-center section-title">ROOM TYPES</h2>
            <div class="row">
                <?php 
                $res = selectAll('types_room');
                
                if ($res && mysqli_num_rows($res) > 0) {
                    while($row = mysqli_fetch_assoc($res)) {
                        $image_path = "images/types_room/" . $row['image'];
                        $full_path = $_SERVER['DOCUMENT_ROOT'] . "/hostel/" . $image_path;
                        
                        echo '<div class="col-lg-6 col-md-6 mb-4">';
                        echo '<div class="card room-card">';
                        echo '<div class="overflow-hidden" style="height: 300px;">';
                        
                        if (file_exists($full_path) && !empty($row['image'])) {
                            echo '<img src="/hostel/' . $image_path . '" class="card-img-top" alt="' . $row['name'] . '">';
                        } else {
                            echo '<div class="img-error h-100">';
                            echo '<div><i class="bi bi-house-door"></i><p>' . $row['name'] . '</p></div>';
                            echo '</div>';
                        }
                        
                        echo '</div>';
                        echo '<div class="card-body">';
                        echo '<h5 class="card-title">' . $row['name'] . '</h5>';
                        echo '<p class="card-text">' . $row['description'] . '</p>';
                        echo '<div class="mt-3">';
                        echo '<a href="room.php?id=' . $row['id'] . '" class="btn btn-outline-red">';
                        echo '<i class="bi bi-eye me-2"></i>View Rooms';
                        echo '</a>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    // Default room types if none in database
                    $default_rooms = [
                        [
                            'name' => 'Function Rooms',
                            'description' => 'Our function room is a versatile space designed to accommodate a wide range of events, from corporate meetings and seminars to parties, celebrations, and social gatherings. Featuring a modern and elegant design, our rooms are designed with comfort and relaxation in mind.'
                        ],
                        [
                            'name' => 'Guest Rooms',
                            'description' => 'Our Guest Room is designed with comfort and relaxation in mind, providing the perfect space for individuals, couples, or small families seeking a cozy and private retreat.'
                        ]
                    ];
                    
                    foreach($default_rooms as $room) {
                        echo '<div class="col-lg-6 col-md-6 mb-4">';
                        echo '<div class="card room-card">';
                        echo '<div class="overflow-hidden" style="height: 300px;">';
                        echo '<div class="img-error h-100">';
                        echo '<div><i class="bi bi-house-door"></i><p>' . $room['name'] . '</p></div>';
                        echo '</div>';
                        echo '</div>';
                        echo '<div class="card-body">';
                        echo '<h5 class="card-title">' . $room['name'] . '</h5>';
                        echo '<p class="card-text">' . $room['description'] . '</p>';
                        echo '<div class="mt-3">';
                        echo '<a href="rooms.php" class="btn btn-outline-red">';
                        echo '<i class="bi bi-eye me-2"></i>View Details';
                        echo '</a>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                }
                ?>
                
                <div class="col-12 text-center mt-4">
                    <a href="room_details.php" class="btn btn-outline-red rounded-0 fw-bold shadow-none px-4 py-2">
                        View All Room Types <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Facilities Section -->
    <section>
        <div class="container">
            <h2 class="text-center section-title">OUR FACILITIES</h2>
            <div class="row justify-content-center">
                <?php 
                $res = mysqli_query($con, "SELECT * FROM `facilities` ORDER BY `id` DESC LIMIT 6");

                if ($res && mysqli_num_rows($res) > 0) {
                    while($row = mysqli_fetch_assoc($res)) {
                        $icon_path = "images/facilities/" . $row['icon'];
                        $full_path = $_SERVER['DOCUMENT_ROOT'] . "/hostel/" . $icon_path;
                        
                        echo '<div class="col-lg-2 col-md-4 col-6 mb-4">';
                        echo '<div class="facility-item">';
                        
                        if (file_exists($full_path) && !empty($row['icon'])) {
                            echo '<img src="/hostel/' . $icon_path . '" alt="' . $row['name'] . '">';
                        } else {
                            echo '<i class="bi bi-grid-3x3-gap-fill fs-1 text-danger"></i>';
                        }
                        
                        echo '<h5>' . $row['name'] . '</h5>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    $default_facilities = ['WiFi', 'Parking', 'Restaurant', 'Gym', 'Pool', 'Security'];
                    foreach($default_facilities as $facility) {
                        echo '<div class="col-lg-2 col-md-4 col-6 mb-4">';
                        echo '<div class="facility-item">';
                        echo '<i class="bi bi-check-circle-fill fs-1 text-danger"></i>';
                        echo '<h5>' . $facility . '</h5>';
                        echo '</div>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
            <div class="text-center mt-4">
                <a href="facilities.php" class="btn btn-outline-red rounded-0 fw-bold shadow-none px-4 py-2">
                    View All Facilities <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section>
        <div class="container">
            <h2 class="text-center section-title">TESTIMONIALS</h2>
            <div class="swiper swipertest">
                <div class="swiper-wrapper mb-5" id="review-container">
                    <div class="swiper-slide">
                        <div class="testimonial-card text-center">
                            <i class="bi bi-star fs-1 text-danger mb-3"></i>
                            <h5>Loading Testimonials...</h5>
                            <div class="loading-spinner"></div>
                        </div>
                    </div>
                </div>
                <div class="swiper-pagination"></div>
            </div>
            <div class="text-center mt-4">
                <a href="about.php" class="btn btn-outline-red rounded-0 fw-bold shadow-none px-4 py-2">
                    Read More Testimonials <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Reach Us Section -->
    <section>
        <div class="container">
            <h2 class="text-center section-title">REACH US</h2>
            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="bg-white p-4 rounded shadow h-100">
                        <?php if (!empty($contact_r['iframe'])): ?>
                        <iframe class="w-100 rounded" height="400" src="<?php echo $contact_r['iframe'] ?>" loading="lazy" allowfullscreen></iframe>
                        <?php else: ?>
                        <div class="img-error" style="height: 400px;">
                            <div>
                                <i class="bi bi-map fs-1"></i>
                                <p>Map Location Coming Soon</p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="bg-white p-4 rounded shadow mb-4">
                        <h5 class="text-danger fw-bold mb-3"><i class="bi bi-telephone-fill me-2"></i>Call Us</h5>
                        <?php if (!empty($contact_r['pn1'])): ?>
                        <a href="tel:+<?php echo $contact_r['pn1'] ?>" class="d-block mb-2 text-decoration-none text-dark hover-text-danger">
                            <i class="bi bi-phone me-2"></i> +<?php echo $contact_r['pn1'] ?>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($contact_r['pn2'])): ?>
                        <a href="tel:+<?php echo $contact_r['pn2'] ?>" class="d-block text-decoration-none text-dark hover-text-danger">
                            <i class="bi bi-phone me-2"></i> +<?php echo $contact_r['pn2'] ?>
                        </a>
                        <?php endif; ?>
                        <?php if (empty($contact_r['pn1']) && empty($contact_r['pn2'])): ?>
                        <p class="text-muted">Contact numbers not available</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="bg-white p-4 rounded shadow mb-4">
                        <h5 class="text-danger fw-bold mb-3"><i class="bi bi-envelope-fill me-2"></i>Email Us</h5>
                        <?php if (!empty($contact_r['email1'])): ?>
                        <a href="mailto:<?php echo $contact_r['email1'] ?>" class="d-block mb-2 text-decoration-none text-dark hover-text-danger">
                            <i class="bi bi-envelope me-2"></i> <?php echo $contact_r['email1'] ?>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($contact_r['email2'])): ?>
                        <a href="mailto:<?php echo $contact_r['email2'] ?>" class="d-block text-decoration-none text-dark hover-text-danger">
                            <i class="bi bi-envelope me-2"></i> <?php echo $contact_r['email2'] ?>
                        </a>
                        <?php endif; ?>
                        <?php if (empty($contact_r['email1']) && empty($contact_r['email2'])): ?>
                        <p class="text-muted">Email addresses not available</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="bg-white p-4 rounded shadow">
                        <h5 class="text-danger fw-bold mb-3"><i class="bi bi-facebook me-2"></i>Follow Us</h5>
                        <?php if (!empty($contact_r['fb'])): ?>
                        <a href="<?php echo $contact_r['fb'] ?>" target="_blank" class="btn btn-outline-red w-100">
                            <i class="bi bi-facebook me-2"></i> Facebook Page
                        </a>
                        <?php else: ?>
                        <p class="text-muted">Facebook page not available</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="footer-title">BatStateU HOSTEL</h5>
                    <p class="text-muted">The BatStateU ARASOF Nasugbu Hostel Reservation System is a web-based platform designed to simplify and streamline the reservation process for the university's hostel facilities.</p>
                    <div class="mt-3">
                        <a href="#" class="social-link"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="social-link"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="social-link"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="social-link"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>

                  <!-- Hero Section -->
   <!-- Hero Section with Animations -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="hero-title animate-title">
                    BatStateU ARASOF Nasugbu Hostel
                </h1>
                <div class="title-underline animate-underline"></div>
                <p class="hero-description animate-description">
                    The BatStateU ARASOF Nasugbu Hostel Reservation System is a web-based platform 
                    designed to simplify and streamline the reservation process for the university's 
                    hostel facilities. It caters to both internal clients, such as BatStateU students 
                    and faculty, and external clients looking to book accommodations for events. 
                    This system ensures a user-friendly experience by automating bookings, 
                    providing real-time updates, and promoting efficient hostel management.
                </p>
                <div class="hero-buttons animate-buttons">
                    <a href="#rooms" class="btn btn-primary-custom me-3">
                        <i class="bi bi-calendar-check me-2"></i>Book Now
                    </a>
                    <a href="#facilities" class="btn btn-outline-custom">
                        <i class="bi bi-info-circle me-2"></i>Learn More
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="footer-title">Quick Links</h5>
                    <a href="index.php" class="footer-link">Home</a>
                    <a href="rooms.php" class="footer-link">Rooms</a>
                    <a href="banquet.php" class="footer-link">Banquet</a>
                    <a href="facilities.php" class="footer-link">Facilities</a>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="footer-title">Information</h5>
                    <a href="about.php" class="footer-link">About Us</a>
                    <a href="contact.php" class="footer-link">Contact Us</a>
                    <a href="privacy.php" class="footer-link">Privacy Policy</a>
                    <a href="terms.php" class="footer-link">Terms & Conditions</a>
                </div>
                <div class="col-lg-3 mb-4">
                    <h5 class="footer-title">Contact Info</h5>
                    <p class="footer-link"><i class="bi bi-geo-alt-fill me-2"></i> BatStateU ARASOF Nasugbu</p>
                    <p class="footer-link"><i class="bi bi-telephone-fill me-2"></i> +63 123 456 7890</p>
                    <p class="footer-link"><i class="bi bi-envelope-fill me-2"></i> info@batstateu.edu.ph</p>
                </div>
            </div>
            <div class="copyright">
                <p class="mb-0">© <?php echo date('Y'); ?> BatStateU-ARASOF Nasugbu Hostel. All rights reserved. Developed by Batangas State University</p>
            </div>
        </div>
    </footer>
<!-- Modals and Scripts -->
<?php require('inc/footer.php');?>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    // Initialize Swiper for main slider
    var mainSwiper = new Swiper(".swiper-container", {
        spaceBetween: 30,
        effect: "fade",
        loop: true,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false,
        },
        fadeEffect: {
            crossFade: true
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        speed: 1000,
    });

    // Initialize Swiper for testimonials
    var testimonialSwiper = new Swiper(".swipertest", {
        effect: "coverflow",
        grabCursor: true,
        centeredSlides: true,
        slidesPerView: "auto",
        loop: true,
        coverflowEffect: {
            rotate: 30,
            stretch: 0,
            depth: 100,
            modifier: 1,
            slideShadows: false,
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
        breakpoints: {
            320: { slidesPerView: 1 },
            640: { slidesPerView: 1 },
            768: { slidesPerView: 2 },
            1024: { slidesPerView: 3 },
        }
    });

    // Availability form submission
    $('#availabilityForm').submit(function(event) {
        event.preventDefault();

        let checkinDate = $('#checkin_date').val();
        let checkoutDate = $('#checkout_date').val();

        if (checkinDate && checkoutDate) {
            let btn = $(this).find('button[type="submit"]');
            let originalText = btn.html();
            btn.html('<span class="spinner-border spinner-border-sm me-2"></span>Checking...').prop('disabled', true);
            
            $.ajax({
                url: 'ajax/index_check_availability.php',
                method: 'POST',
                data: {
                    checkin_date: checkinDate,
                    checkout_date: checkoutDate
                },
                success: function(response) {
                    if ($('#availabilityModal').length === 0) {
                        $('body').append(`
                            <div class="modal fade" id="availabilityModal" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Available Rooms</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body" id="availableRoomsList"></div>
                                    </div>
                                </div>
                            </div>
                        `);
                    }
                    
                    $('#availableRoomsList').html(response);
                    $('#availabilityModal').modal('show');
                    btn.html(originalText).prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    alert('Error checking availability: ' + error);
                    btn.html(originalText).prop('disabled', false);
                }
            });
        } else {
            alert("Please fill in all fields.");
        }
    });

    // MAIN DOCUMENT READY - All initialization code goes here
    document.addEventListener('DOMContentLoaded', function() {
        
        // ========== FETCH REVIEWS ==========
        fetch('fetch_reviews.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error(data.error);
                    return;
                }

                const reviewContainer = document.getElementById('review-container');
                if (!reviewContainer) return;

                if (data && data.length > 0) {
                    reviewContainer.innerHTML = data.map(review => {
                        const profileImg = review.profile ? 
                            `/hostel/images/users/${review.profile}` : 
                            '/hostel/images/users/default.jpg';
                        
                        return `
                        <div class="swiper-slide">
                            <div class="testimonial-card">
                                <div class="d-flex align-items-center mb-4">
                                    <img src="${profileImg}" 
                                         loading="lazy" 
                                         width="70" 
                                         height="70" 
                                         class="rounded-circle me-3 border border-3 border-danger"
                                         onerror="this.src='/hostel/images/users/default.jpg'; this.onerror=null;">
                                    <div>
                                        <h5 class="fw-bold mb-1">${review.name || 'Anonymous'}</h5>
                                        <div class="rating">
                                            ${[...Array(5)].map((_, i) => 
                                                `<i class="bi ${i < (review.rating || 5) ? 'bi-star-fill text-warning' : 'bi-star text-secondary'} fs-5 me-1"></i>`
                                            ).join('')}
                                        </div>
                                    </div>
                                </div>
                                <p class="text-muted fst-italic">"${review.review || 'No review text'}"</p>
                            </div>
                        </div>
                    `}).join('');
                    
                    testimonialSwiper.update();
                } else {
                    reviewContainer.innerHTML = `
                        <div class="swiper-slide">
                            <div class="testimonial-card text-center">
                                <i class="bi bi-chat-square-text fs-1 text-danger mb-3"></i>
                                <h5>No Testimonials Yet</h5>
                                <p class="text-muted">Be the first to leave a review!</p>
                            </div>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error fetching reviews:', error);
                const reviewContainer = document.getElementById('review-container');
                if (reviewContainer) {
                    reviewContainer.innerHTML = `
                        <div class="swiper-slide">
                            <div class="testimonial-card text-center">
                                <i class="bi bi-exclamation-triangle fs-1 text-danger mb-3"></i>
                                <h5>Unable to Load Testimonials</h5>
                                <p class="text-muted">Please try again later</p>
                            </div>
                        </div>
                    `;
                }
            });

        // ========== CALENDAR FUNCTIONALITY ==========
        
        // Only initialize calendar if elements exist
        if (document.getElementById('roomTypeSelect')) {
            
            // Calendar variables
            let currentDate = new Date();
            let currentMonth = currentDate.getMonth();
            let currentYear = currentDate.getFullYear();
            let selectedDates = [];
            let bookedData = {};

            // Room type selection handler
            document.getElementById('roomTypeSelect').addEventListener('change', function() {
                if (this.value) {
                    const calendarContainer = document.getElementById('calendarContainer');
                    if (calendarContainer) {
                        calendarContainer.style.display = 'block';
                        loadBookedData(this.value);
                    }
                } else {
                    const calendarContainer = document.getElementById('calendarContainer');
                    if (calendarContainer) {
                        calendarContainer.style.display = 'none';
                    }
                }
            });

            // Load booked data from database
            function loadBookedData(roomType) {
                fetch(`ajax/get_booked_dates.php?room_type=${roomType}`)
                    .then(response => response.json())
                    .then(data => {
                        bookedData = data;
                        renderCalendar(currentMonth, currentYear);
                    })
                    .catch(error => {
                        console.error('Error loading booked data:', error);
                        bookedData = {};
                        renderCalendar(currentMonth, currentYear);
                    });
            }

            // Render calendar
            function renderCalendar(month, year) {
                const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'];
                
                const monthYearElement = document.getElementById('currentMonthYear');
                if (monthYearElement) {
                    monthYearElement.textContent = `${monthNames[month]} ${year}`;
                }
                
                const firstDay = new Date(year, month, 1).getDay();
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                const daysInPrevMonth = new Date(year, month, 0).getDate();
                
                let calendarHTML = '';
                
                // Previous month days
                for (let i = firstDay - 1; i >= 0; i--) {
                    const day = daysInPrevMonth - i;
                    const dateStr = formatDate(new Date(year, month - 1, day));
                    calendarHTML += renderDay(day, true, dateStr, month - 1, year);
                }
                
                // Current month days
                for (let day = 1; day <= daysInMonth; day++) {
                    const dateStr = formatDate(new Date(year, month, day));
                    calendarHTML += renderDay(day, false, dateStr, month, year);
                }
                
                // Next month days
                const totalDays = calendarHTML.split('<div class="calendar-day').length - 1;
                const nextMonthDays = 42 - totalDays;
                
                for (let day = 1; day <= nextMonthDays; day++) {
                    const dateStr = formatDate(new Date(year, month + 1, day));
                    calendarHTML += renderDay(day, true, dateStr, month + 1, year);
                }
                
                const calendarDays = document.getElementById('calendarDays');
                if (calendarDays) {
                    calendarDays.innerHTML = calendarHTML;
                }
            }

            // Render individual day
            function renderDay(day, isOtherMonth, dateStr, month, year) {
                const isSelected = selectedDates.includes(dateStr);
                const dayData = bookedData[dateStr] || { status: 'available', rooms: [] };
                
                let statusClass = 'available';
                if (dayData.status === 'booked') statusClass = 'booked';
                else if (dayData.status === 'partial') statusClass = 'partial';
                
                let bookedHTML = '';
                if (dayData.rooms && dayData.rooms.length > 0) {
                    dayData.rooms.slice(0, 3).forEach(room => {
                        bookedHTML += `<div class="${statusClass}-room" title="${room.name}">${room.name}</div>`;
                    });
                    if (dayData.rooms.length > 3) {
                        bookedHTML += `<div class="text-muted small">+${dayData.rooms.length - 3} more</div>`;
                    }
                }
                
                const selectedClass = isSelected ? ' selected' : '';
                const otherMonthClass = isOtherMonth ? ' other-month' : '';
                const clickHandler = !isOtherMonth && dayData.status !== 'booked' ? 
                    `onclick="toggleDateSelection('${dateStr}', '${dayData.status}')"` : '';
                
                return `
                    <div class="calendar-day ${statusClass}${selectedClass}${otherMonthClass}" 
                         ${clickHandler}
                         data-date="${dateStr}">
                        <div class="day-number">${day}</div>
                        <div class="booked-indicator">
                            ${bookedHTML}
                        </div>
                    </div>
                `;
            }

            // Toggle date selection
            window.toggleDateSelection = function(dateStr, status) {
                if (status === 'booked') return;
                
                const index = selectedDates.indexOf(dateStr);
                if (index === -1) {
                    selectedDates.push(dateStr);
                } else {
                    selectedDates.splice(index, 1);
                }
                
                selectedDates.sort();
                updateSelectedDatesSummary();
                renderCalendar(currentMonth, currentYear);
            };

            // Update selected dates summary
            function updateSelectedDatesSummary() {
                const summaryDiv = document.getElementById('selectedDatesSummary');
                const listElement = document.getElementById('selectedDatesList');
                
                if (summaryDiv && listElement) {
                    if (selectedDates.length > 0) {
                        const formattedDates = selectedDates.map(date => {
                            const d = new Date(date);
                            return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                        });
                        listElement.textContent = formattedDates.join(' → ');
                        summaryDiv.style.display = 'block';
                    } else {
                        summaryDiv.style.display = 'none';
                    }
                }
            }

            // Format date as YYYY-MM-DD
            function formatDate(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            // Month navigation
            const prevMonthBtn = document.getElementById('prevMonth');
            if (prevMonthBtn) {
                prevMonthBtn.addEventListener('click', function() {
                    if (currentMonth === 0) {
                        currentMonth = 11;
                        currentYear--;
                    } else {
                        currentMonth--;
                    }
                    renderCalendar(currentMonth, currentYear);
                });
            }

            const nextMonthBtn = document.getElementById('nextMonth');
            if (nextMonthBtn) {
                nextMonthBtn.addEventListener('click', function() {
                    if (currentMonth === 11) {
                        currentMonth = 0;
                        currentYear++;
                    } else {
                        currentMonth++;
                    }
                    renderCalendar(currentMonth, currentYear);
                });
            }

            // Check availability button
            const checkBtn = document.getElementById('checkAvailabilityBtn');
            if (checkBtn) {
                checkBtn.addEventListener('click', function() {
                    const roomType = document.getElementById('roomTypeSelect').value;
                    if (selectedDates.length === 0) {
                        alert('Please select at least one date');
                        return;
                    }
                    
                    const checkinDate = selectedDates[0];
                    const checkoutDate = selectedDates[selectedDates.length - 1];
                    
                    // Show loading
                    this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Checking...';
                    this.disabled = true;
                    
                    // Fetch available rooms
                    fetch('ajax/get_available_rooms.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            room_type: roomType,
                            checkin_date: checkinDate,
                            checkout_date: checkoutDate,
                            selected_dates: selectedDates
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        displayAvailableRooms(data);
                        this.innerHTML = 'Check Availability';
                        this.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error checking availability');
                        this.innerHTML = 'Check Availability';
                        this.disabled = false;
                    });
                });
            }

            // Display available rooms
            window.displayAvailableRooms = function(rooms) {
                const container = document.getElementById('availableRoomsList');
                if (!container) return;
                
                if (rooms.length === 0) {
                    container.innerHTML = '<p class="text-center text-muted">No rooms available for selected dates</p>';
                } else {
                    let html = '<div class="row">';
                    rooms.forEach(room => {
                        html += `
                            <div class="col-md-6 mb-3">
                                <div class="card room-card">
                                    <div class="card-body">
                                        <h5 class="card-title">${room.name}</h5>
                                        <p class="card-text">${room.description || ''}</p>
                                        <p><strong>Price:</strong> ₱${room.price}/night</p>
                                        <p><strong>Available:</strong> ${room.quantity} rooms</p>
                                        <button class="btn custom-bg btn-sm" onclick="bookRoom(${room.id})">
                                            Book Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                    container.innerHTML = html;
                }
                
                $('#availableRoomsModal').modal('show');
            };

            // Book room function
            window.bookRoom = function(roomId) {
                const checkin = selectedDates[0];
                const checkout = selectedDates[selectedDates.length - 1];
                window.location.href = `book_room.php?room_id=${roomId}&checkin=${checkin}&checkout=${checkout}`;
            };

            // Initial calendar render
            renderCalendar(currentMonth, currentYear);
        }
    });
</script>
</body>
</html>