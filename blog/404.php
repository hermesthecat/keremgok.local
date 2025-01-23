<?php

/**
 * 404 Hata Sayfası
 * 
 * @author A. Kerem Gök
 * @version 1.0
 */

require_once 'config.php';

// HTTP 404 header'ı gönder
header("HTTP/1.0 404 Not Found");

// Sayfa başlığı
$pageTitle = "Sayfa Bulunamadı - Blog";
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, follow">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="blog.css">
</head>

<body>
    <main class="blog-container">
        <div class="blog-header">
            <h1>Blog</h1>
            <nav>
                <a href="index.php" class="btn-secondary">← Ana Sayfa'ya Dön</a>
            </nav>
        </div>

        <div class="error-page">
            <div class="error-content">
                <h1><i class="fas fa-exclamation-triangle"></i> 404</h1>
                <h2>Aradığınız Sayfa Bulunamadı</h2>
                <p>Üzgünüz, aradığınız sayfa bulunamadı veya taşınmış olabilir.</p>
                <div class="error-actions">
                    <a href="index.php" class="btn-primary">Ana Sayfa'ya Dön</a>
                </div>
            </div>
        </div>
    </main>
</body>

</html>