<?php
/**
 * Blog Ana Sayfası
 * 
 * @author A. Kerem Gök
 * @version 1.0
 */

session_start();
require_once 'config.php';

// Sayfa başlığı
$pageTitle = "Blog";

// Header'ı dahil et
include_once '../header.php';
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="blog.css">
    <script src="blog.js" defer></script>
</head>
<body>
    <main class="blog-container">
        <h1>Blog Yazıları</h1>
        <section class="blog-posts">
            <?php
            // Blog yazılarını getir
            $posts = getBlogPosts();
            
            if ($posts) {
                foreach ($posts as $post) {
                    echo '<article class="blog-post">';
                    echo '<h2>' . htmlspecialchars($post['title']) . '</h2>';
                    echo '<div class="post-meta">';
                    echo '<span class="post-date">' . date('d.m.Y', strtotime($post['created_at'])) . '</span>';
                    echo '<span class="post-author">' . htmlspecialchars($post['author']) . '</span>';
                    echo '</div>';
                    echo '<p>' . htmlspecialchars($post['excerpt']) . '</p>';
                    echo '<a href="post.php?id=' . $post['id'] . '" class="read-more">Devamını Oku</a>';
                    echo '</article>';
                }
            } else {
                echo '<p class="no-posts">Henüz blog yazısı bulunmamaktadır.</p>';
            }
            ?>
        </section>
    </main>
</body>
</html> 