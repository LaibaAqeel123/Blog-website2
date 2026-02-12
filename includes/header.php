<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="<?php echo SITE_URL; ?>index.php">
                    <i class="fas fa-blog"></i> <?php echo SITE_NAME; ?>
                </a>
            </div>
            <ul class="nav-menu">
                <li><a href="<?php echo SITE_URL; ?>index.php">Home</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="<?php echo SITE_URL; ?>admin/dashboard.php">Dashboard</a></li>
                    <li><a href="<?php echo SITE_URL; ?>admin/logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="<?php echo SITE_URL; ?>admin/login.php">Admin</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>