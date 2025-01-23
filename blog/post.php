<?php
/**
 * Blog Yazısı Sayfası
 * 
 * @author A. Kerem Gök
 * @version 1.0
 */

session_start();
require_once 'config.php';

// Yazı ID'sini al
$postId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$postId) {
    header('Location: index.php');
    exit;
}

// Yazı dosyasını oku
$postFile = POSTS_DIR . $postId . '.md';
if (!file_exists($postFile)) {
    header('Location: index.php');
    exit;
}

$content = file_get_contents($postFile);
$post = parseMarkdown($content);
$post['id'] = $postId;
$post['created_at'] = date('Y-m-d H:i:s', filemtime($postFile));

// Sayfa başlığı
$pageTitle = $post['title'] . " - Blog";

// Parsedown örneği oluştur
$parsedown = new Parsedown();
$parsedown->setSafeMode(true);

// Header'ı dahil et
include_once '../header.php';
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="blog.css">
    <script src="blog.js" defer></script>
</head>
<body>
    <main class="blog-container">
        <article class="blog-post full-post" id="post-<?php echo $post['id']; ?>">
            <h1><?php echo htmlspecialchars($post['title']); ?></h1>
            
            <div class="post-meta">
                <span class="post-date"><?php echo date('d.m.Y', strtotime($post['created_at'])); ?></span>
                <span class="post-author"><?php echo htmlspecialchars($post['author']); ?></span>
            </div>
            
            <div class="post-content">
                <?php echo $post['content']; ?>
            </div>
            
            <div class="post-navigation">
                <a href="index.php" class="back-to-blog">← Blog'a Dön</a>
            </div>
        </article>
    </main>
</body>
</html> 