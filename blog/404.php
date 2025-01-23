<?php

/**
 * 404 Hata Sayfası
 * 
 * @author A. Kerem Gök
 * @version 1.0
 */

session_start();
require_once 'config.php';

// 404 HTTP durum kodunu gönder
header("HTTP/1.0 404 Not Found");

// Sayfa başlığı
$pageTitle = __('404_title');
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, follow">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="blog.css">
</head>

<body>
    <main class="blog-container error-page">
        <div class="blog-header">
            <h1><?php echo __('site_title'); ?></h1>
            <div class="header-actions">
                <div class="language-switcher">
                    <a href="<?php echo getLanguageUrl('tr'); ?>" class="<?php echo getCurrentLanguage() === 'tr' ? 'active' : ''; ?>">TR</a>
                    <a href="<?php echo getLanguageUrl('en'); ?>" class="<?php echo getCurrentLanguage() === 'en' ? 'active' : ''; ?>">EN</a>
                </div>
                <nav>
                    <a href="index.php" class="btn-primary"><?php echo __('back_to_blog'); ?></a>
                </nav>
            </div>
        </div>

        <div class="error-content">
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h2><?php echo __('404_title'); ?></h2>
            <p><?php echo __('404_message'); ?></p>
            <div class="error-actions">
                <a href="index.php" class="btn-primary">
                    <i class="fas fa-home"></i> <?php echo __('back_to_blog'); ?>
                </a>
            </div>
        </div>
    </main>
</body>

</html>