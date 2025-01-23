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

// Kategori veya etiket filtreleme
$category = isset($_GET['category']) ? $_GET['category'] : null;
$tag = isset($_GET['tag']) ? $_GET['tag'] : null;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Parsedown örneği oluştur
$parsedown = new Parsedown();
$parsedown->setSafeMode(true);
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="blog.css">
    <script src="blog.js" defer></script>
</head>

<body>
    <main class="blog-container">
        <div class="blog-header">
            <h1>Blog Yazıları</h1>
            <a href="new-post.php" class="btn-primary">Yeni Yazı</a>
        </div>

        <!-- Arama kutusu -->
        <div class="search-container">
            <input type="text" class="blog-search" placeholder="Blog yazılarında ara...">
        </div>

        <!-- Kategori ve Etiket Menüsü -->
        <aside class="blog-sidebar">
            <div class="categories">
                <h3>Kategoriler</h3>
                <ul>
                    <?php
                    $categories = getAllCategories();
                    foreach ($categories as $cat => $count) {
                        $activeClass = ($category === $cat) ? ' class="active"' : '';
                        echo '<li><a href="?category=' . urlencode($cat) . '"' . $activeClass . '>';
                        echo htmlspecialchars($cat) . ' (' . $count . ')</a></li>';
                    }
                    ?>
                </ul>
            </div>

            <div class="tags">
                <h3>Etiketler</h3>
                <div class="tag-cloud">
                    <?php
                    $tags = getAllTags();
                    foreach ($tags as $t => $count) {
                        $activeClass = ($tag === $t) ? ' active' : '';
                        $size = 1 + min(1.5, $count / max($tags));
                        echo '<a href="?tag=' . urlencode($t) . '" class="tag' . $activeClass . '" ';
                        echo 'style="font-size: ' . $size . 'em">';
                        echo htmlspecialchars($t) . '</a>';
                    }
                    ?>
                </div>
            </div>
        </aside>

        <section class="blog-posts">
            <?php
            // Blog yazılarını getir
            if ($category) {
                $posts = getPostsByCategory($category, $page);
                echo '<div class="filter-info">Kategori: ' . htmlspecialchars($category) . '</div>';
            } elseif ($tag) {
                $posts = getPostsByTag($tag, $page);
                echo '<div class="filter-info">Etiket: ' . htmlspecialchars($tag) . '</div>';
            } else {
                $posts = getBlogPosts($page);
            }

            if ($posts) {
                foreach ($posts as $post) {
                    echo '<article class="blog-post" id="post-' . $post['id'] . '">';
                    echo '<h2>' . htmlspecialchars($post['title']) . '</h2>';
                    echo '<div class="post-meta">';
                    echo '<span class="post-date">' . date('d.m.Y', strtotime($post['created_at'])) . '</span>';
                    echo '<span class="post-author">' . htmlspecialchars($post['author']) . '</span>';
                    if (isset($post['category'])) {
                        echo '<span class="post-category">';
                        echo '<a href="?category=' . urlencode($post['category']) . '">';
                        echo htmlspecialchars($post['category']) . '</a></span>';
                    }
                    echo '</div>';

                    if (isset($post['tags']) && !empty($post['tags'])) {
                        echo '<div class="post-tags">';
                        foreach ($post['tags'] as $t) {
                            echo '<a href="?tag=' . urlencode($t) . '" class="tag">';
                            echo htmlspecialchars($t) . '</a>';
                        }
                        echo '</div>';
                    }

                    echo '<div class="post-excerpt">' . $post['excerpt'] . '</div>';
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