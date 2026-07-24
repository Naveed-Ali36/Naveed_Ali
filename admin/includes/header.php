<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$stats = adminStats();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Admin') ?> — Portfolio Admin</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/admin.css">
    <?php if (!empty($loadCharts)): ?>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <?php endif; ?>
</head>
<body class="admin-body">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <i class='bx bx-layer'></i>
            <div>
                <strong>Portfolio</strong>
                <span>Admin Panel</span>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="index.php" class="<?= $currentPage === 'index' ? 'active' : '' ?>"><i class='bx bx-grid-alt'></i> Dashboard</a>
            <a href="hero.php" class="<?= $currentPage === 'hero' ? 'active' : '' ?>"><i class='bx bx-home-alt'></i> Hero Section</a>
            <a href="about.php" class="<?= $currentPage === 'about' ? 'active' : '' ?>"><i class='bx bx-user'></i> About</a>
            <a href="projects.php" class="<?= $currentPage === 'projects' ? 'active' : '' ?>"><i class='bx bx-briefcase'></i> Projects</a>
            <a href="journey.php" class="<?= $currentPage === 'journey' ? 'active' : '' ?>"><i class='bx bx-time'></i> Experience</a>
            <a href="skills.php" class="<?= $currentPage === 'skills' ? 'active' : '' ?>"><i class='bx bx-code-alt'></i> Skills</a>
            <a href="contact-settings.php" class="<?= $currentPage === 'contact-settings' ? 'active' : '' ?>"><i class='bx bx-envelope'></i> Contact</a>
            <a href="messages.php" class="<?= $currentPage === 'messages' ? 'active' : '' ?>"><i class='bx bx-message-dots'></i> Messages <?php if ($stats['unread_messages'] > 0): ?><em><?= (int) $stats['unread_messages'] ?></em><?php endif; ?></a>
            <a href="settings.php" class="<?= $currentPage === 'settings' ? 'active' : '' ?>"><i class='bx bx-cog'></i> Settings</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../index.php" target="_blank"><i class='bx bx-link-external'></i> View Site</a>
            <a href="logout.php"><i class='bx bx-log-out'></i> Logout</a>
        </div>
    </aside>

    <main class="admin-main">
        <header class="admin-topbar">
            <div>
                <h1><?= e($pageTitle ?? 'Dashboard') ?></h1>
                <p><?= e($pageSubtitle ?? 'Manage your portfolio content') ?></p>
            </div>
            <div class="admin-user">
                <i class='bx bx-shield-quarter'></i>
                <?= e($_SESSION[ADMIN_SESSION_KEY] ?? 'Admin') ?>
            </div>
        </header>

        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>

        <div class="admin-content">
