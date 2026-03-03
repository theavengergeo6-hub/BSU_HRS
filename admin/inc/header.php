<?php
require_once __DIR__ . '/../../inc/essentials.php'; // Load global functions first
require_once __DIR__ . '/auth.php';
requireAdminLogin();

$current_page = basename($_SERVER['PHP_SELF']);
$admin = getAdminInfo($conn);

// Get pending reservations count for badge
$pending_count = 0;
$result = $conn->query("SELECT COUNT(*) as count FROM facility_reservations WHERE status = 'pending'");
if ($result) {
    $pending_count = $result->fetch_assoc()['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' : '' ?>Admin | BSU Hostel</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --bsu-red: #b71c1c;
            --bsu-red-dark: #8b0000;
            --sidebar-width: 260px;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
        }
        .admin-wrapper {
            display: flex;
        }
        
        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: white;
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            z-index: 100;
            transition: transform 0.3s ease;
        }
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .sidebar-header h2 {
            color: var(--bsu-red);
            font-size: 1.3rem;
            font-weight: 700;
        }
        .sidebar-header p {
            color: #666;
            font-size: 0.8rem;
        }
        .sidebar-nav {
            padding: 1rem 0;
        }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.8rem 1.5rem;
            color: #333;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
        }
        .sidebar-nav a i {
            width: 24px;
            color: #666;
            transition: color 0.3s ease;
        }
        .sidebar-nav a:hover {
            background: #fdeae8;
            color: var(--bsu-red);
        }
        .sidebar-nav a:hover i {
            color: var(--bsu-red);
        }
        .sidebar-nav a.active {
            background: #fdeae8;
            color: var(--bsu-red);
            font-weight: 600;
            border-left: 4px solid var(--bsu-red);
        }
        .sidebar-nav a.active i {
            color: var(--bsu-red);
        }
        .badge-count {
            background: var(--bsu-red);
            color: white;
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
            border-radius: 20px;
            margin-left: auto;
        }
        .nav-divider {
            height: 1px;
            background: #eee;
            margin: 1rem 0;
        }
        
        /* Mobile menu toggle button */
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--bsu-red);
            font-size: 1.8rem;
            cursor: pointer;
            z-index: 1001;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            background: #f8f9fa;
            transition: margin-left 0.3s ease;
        }
        .top-bar {
            background: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .page-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
        }
        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .user-info {
            text-align: right;
        }
        .user-name {
            font-weight: 600;
            color: #333;
            font-size: 0.9rem;
        }
        .user-role {
            color: #666;
            font-size: 0.8rem;
        }
        .btn-logout {
            background: #fdeae8;
            color: var(--bsu-red);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }
        .btn-logout:hover {
            background: var(--bsu-red);
            color: white;
        }
        .content-area {
            padding: 2rem;
        }
        
        /* Cards */
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border-left: 4px solid var(--bsu-red);
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(183,28,28,0.1);
        }
        .stat-title {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
        }
        .stat-icon {
            float: right;
            color: var(--bsu-red);
            font-size: 2rem;
            opacity: 0.2;
        }
        
        /* Status Badges */
        .badge {
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }
        .badge-approved {
            background: #d4edda;
            color: #155724;
        }
        .badge-denied {
            background: #f8d7da;
            color: #721c24;
        }
        
        /* Tables */
        .table-responsive {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }
        th {
            text-align: left;
            padding: 1rem;
            color: #666;
            font-weight: 600;
            font-size: 0.9rem;
            border-bottom: 2px solid #eee;
        }
        td {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            color: #333;
        }
        tr:hover {
            background: #f8f9fa;
        }
        
        /* Buttons */
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: var(--bsu-red);
            color: white;
        }
        .btn-primary:hover {
            background: var(--bsu-red-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(183,28,28,0.3);
        }
        .btn-outline {
            background: white;
            border: 2px solid var(--bsu-red);
            color: var(--bsu-red);
        }
        .btn-outline:hover {
            background: var(--bsu-red);
            color: white;
        }
        
        /* ========== ADMIN PANEL RESPONSIVE ========== */
        @media (max-width: 1200px) {
            :root {
                --sidebar-width: 220px;
            }
        }

        @media (max-width: 992px) {
            :root {
                --sidebar-width: 200px;
            }
            
            .sidebar-nav a {
                padding: 0.7rem 1rem;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }
            
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 1000;
                width: 280px;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .top-bar {
                padding: 1rem;
            }
            
            .content-area {
                padding: 1rem;
            }
            
            .user-info {
                display: none;
            }
            
            .stat-card {
                margin-bottom: 1rem;
            }
            
            /* Add overlay when sidebar is open */
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 999;
            }
            
            .sidebar-overlay.show {
                display: block;
            }
        }

        @media (max-width: 576px) {
            .btn-logout span {
                display: none;
            }
            
            .stat-number {
                font-size: 1.5rem;
            }
            
            .sidebar-header h2 {
                font-size: 1.1rem;
            }
            
            .sidebar-header p {
                font-size: 0.7rem;
            }
            
            .top-bar {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            
            .user-menu {
                width: 100%;
                justify-content: space-between;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar Overlay (for mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="admin-wrapper">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div>
                    <h2>BSU Hostel</h2>
                    <p>Admin Panel</p>
                </div>
                <button class="menu-toggle" id="closeSidebarBtn">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <nav class="sidebar-nav">
                <a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2"></i>
                    Dashboard
                </a>
                <a href="reservations.php" class="<?= $current_page == 'reservations.php' ? 'active' : '' ?>">
                    <i class="bi bi-calendar-check"></i>
                    Reservations
                    <?php if ($pending_count > 0): ?>
                        <span class="badge-count"><?= $pending_count ?></span>
                    <?php endif; ?>
                </a>
                <a href="rooms.php" class="<?= $current_page == 'rooms.php' ? 'active' : '' ?>">
                    <i class="bi bi-building"></i>
                    Rooms
                </a>
                <a href="banquet.php" class="<?= $current_page == 'banquet.php' ? 'active' : '' ?>">
                    <i class="bi bi-grid-3x3-gap"></i>
                    Banquet Styles
                </a>
                <a href="faq.php" class="<?= $current_page == 'faq.php' ? 'active' : '' ?>">
                    <i class="bi bi-question-circle"></i>
                    FAQs
                </a>
                <a href="offices.php" class="<?= $current_page == 'offices.php' ? 'active' : '' ?>">
                    <i class="bi bi-building"></i>
                    Offices
                </a>
                <div class="nav-divider"></div>
                <a href="carousel.php" class="<?= $current_page == 'carousel.php' ? 'active' : '' ?>">
                    <i class="bi bi-images"></i>
                    Carousel
                </a>
                <a href="settings.php" class="<?= $current_page == 'settings.php' ? 'active' : '' ?>">
                    <i class="bi bi-gear"></i>
                    Settings
                </a>
                <a href="reports.php" class="<?= $current_page == 'reports.php' ? 'active' : '' ?>">
                    <i class="bi bi-file-text"></i>
                    Reports
                </a>
                <div class="nav-divider"></div>
                <a href="logout.php">
                    <i class="bi bi-box-arrow-right"></i>
                    Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="top-bar">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <button class="menu-toggle" id="menuToggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="page-title"><?= $pageTitle ?? 'Dashboard' ?></div>
                </div>
                <div class="user-menu">
                    <div class="user-info">
                        <div class="user-name"><?= htmlspecialchars($admin['username'] ?? 'Admin') ?></div>
                        <div class="user-role"><?= htmlspecialchars($admin['role'] ?? 'Administrator') ?></div>
                    </div>
                    <a href="logout.php" class="btn-logout">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
            <div class="content-area">