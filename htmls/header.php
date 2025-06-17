<!DOCTYPE html>
<html lang="he" dir="rtl" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ביטוח פלוס - פלטפורמת השוואות</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="js/main.js"></script>
</head>
<body class="page-<?php echo basename($_SERVER['PHP_SELF'], '.php'); ?>">
<div class="overlay" id="overlay"></div>
<div class="app-container">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo-container">
                <i class="fas fa-shield-alt logo-icon"></i>
                <div class="logo-text">
                    <span class="logo-stream">ביטוח פלוס</span>
                    <span class="logo-university">פלטפורמת השוואות</span>
                </div>
            </div>
            <button class="close-sidebar" id="close-sidebar">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <a href="index.php" class="nav-item">
                    <i class="fas fa-th-large"></i>
                    <span>דשבורד</span>
                </a>
                <a href="clients.php" class="nav-item">
                    <i class="fas fa-users"></i>
                    <span>לקוחות</span>
                </a>
                <a href="activity.php" class="nav-item">
                    <i class="fas fa-chart-line"></i>
                    <span>פעילות</span>
                </a>
                <a href="comparison.php" class="nav-item">
                    <i class="fas fa-balance-scale"></i>
                    <span>השוואת פוליסות</span>
                </a>
            </div>

            <div class="nav-section">
                <h3 class="nav-title">ניהול</h3>

                <?php if(isset($currentUser) && $currentUser['role']) == 'admin'): ?>
                <a href="management.php" class="nav-item admin-only">
                    <i class="fas fa-user-cog"></i>
                    <span>ניהול משתמשים</span>
                </a>
                <?php endif; ?>

                <a href="profile.php" class="nav-item">
                    <i class="fas fa-user"></i>
                    <span>פרופיל</span>
                </a>
            </div>

            <div class="nav-section">
                <h3 class="nav-title">חשבון</h3>
                <a href="#" class="nav-item logout-link" id="sidebar-logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>התנתקות</span>
                </a>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header class="main-header">
            <button class="menu-toggle" id="menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="search-container">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="חיפוש לקוחות..." id="global-search">
                </div>
            </div>
            <div class="header-actions">
                <button class="icon-button notification-button">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </button>
                <?= (isset($header_action) ? $header_action : ''); ?>
                <div class="user-menu-container">
                    <input type="checkbox" id="user-menu-toggle" class="user-menu-toggle">
                    <label for="user-menu-toggle" class="user-menu-toggle-label">
                        <div class="user-avatar" id="user-avatar" tabindex="0"></div>
                    </label>
                    <label for="user-menu-toggle" class="menu-overlay"></label>
                    <div class="account-dropdown">
                        <div class="account-dropdown-header">
                            <div class="dropdown-user-avatar"></div>
                            <div class="dropdown-user-info">
                                <div class="dropdown-user-name"></div>
                                <div class="dropdown-user-email"></div>
                            </div>
                        </div>
                        <div class="account-dropdown-items">
                            <a href="profile.php" class="account-dropdown-item">
                                <i class="fas fa-user"></i>
                                <span>פרופיל</span>
                            </a>
                            <a href="management.php" class="account-dropdown-item admin-only">
                                <i class="fas fa-cog"></i>
                                <span>ניהול</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="account-dropdown-item logout-item" id="account-logout">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>התנתקות</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>
