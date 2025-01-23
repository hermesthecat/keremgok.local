<?php

/**
 * Blog Yazısı Görüntüleme Sayfası
 * 
 * @author A. Kerem Gök
 * @version 1.0
 */

session_start();
require_once 'config.php';

// Yazı ID'sini al
$postId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$postId || !file_exists(POSTS_DIR . $postId . '.md')) {
    header('Location: index.php');
    exit;
}

// Yazıyı oku
$content = file_get_contents(POSTS_DIR . $postId . '.md');
$post = parseMarkdown($content);
$post['id'] = $postId;
$post['created_at'] = date('Y-m-d H:i:s', filemtime(POSTS_DIR . $postId . '.md'));

// Parsedown örneği oluştur
$parsedown = new Parsedown();
$parsedown->setSafeMode(true);

// Sayfa başlığı
$pageTitle = $post['title'] . " - Blog";
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="blog.css">
    <script src="blog.js" defer></script>
</head>

<body>
    <main class="blog-container">
        <div class="blog-header">
            <h1>Blog</h1>
            <a href="new-post.php" class="btn-primary">Yeni Yazı</a>
        </div>

        <!-- Kategori ve Etiket Menüsü -->
        <aside class="blog-sidebar">
            <div class="categories">
                <h3><i class="fas fa-folder"></i> Kategoriler</h3>
                <ul>
                    <?php
                    $categories = getAllCategories();
                    foreach ($categories as $cat => $count) {
                        $activeClass = (isset($post['category']) && $post['category'] === $cat) ? ' class="active"' : '';
                        echo '<li><a href="index.php?category=' . urlencode($cat) . '"' . $activeClass . '>';
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
                        $activeClass = (isset($post['tags']) && in_array($t, $post['tags'])) ? ' active' : '';
                        $size = 1 + min(1.5, $count / max($tags));
                        echo '<a href="index.php?tag=' . urlencode($t) . '" class="tag' . $activeClass . '" ';
                        echo 'style="font-size: ' . $size . 'em">';
                        echo htmlspecialchars($t) . '</a>';
                    }
                    ?>
                </div>
            </div>
        </aside>

        <article class="blog-post">
            <div class="post-header">
                <h1><?php echo htmlspecialchars($post['title']); ?></h1>
                <div class="post-meta">
                    <span class="post-author"><?php echo htmlspecialchars($post['author']); ?></span>
                    <span class="post-date"> <?php echo date('d.m.Y', strtotime($post['created_at'])); ?></span>
                    <?php if (isset($post['category'])): ?>
                        <span class="post-category"> <?php
                                                        $categories = is_array($post['category']) ? $post['category'] : [$post['category']];
                                                        foreach ($categories as $i => $category) {
                                                            if ($i > 0) echo ', ';
                                                            echo '<a href="index.php?category=' . urlencode($category) . '">' .
                                                                htmlspecialchars($category) . '</a>';
                                                        }
                                                        ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="post-content">
                <?php
                // Resimlerin URL'lerini düzelt
                $content = $post['content'];
                $content = preg_replace_callback(
                    '/<img[^>]+src="([^"]+)"[^>]*>/',
                    function ($matches) {
                        return str_replace(
                            $matches[1],
                            fixImageUrl($matches[1]),
                            $matches[0]
                        );
                    },
                    $content
                );
                echo $content;
                ?>
            </div>
            <?php if (!empty($post['tags'])): ?>
                <div class="post-tags">
                    <?php foreach ($post['tags'] as $tag): ?>
                        <a href="index.php?tag=<?php echo urlencode($tag); ?>" class="tag">
                            <i class="fas fa-tag"></i> <?php echo htmlspecialchars($tag); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="post-navigation">
                <a href="index.php" class="btn-secondary">Blog'a Dön</a>
                <a href="edit-post.php?id=<?php echo $postId; ?>" class="btn-primary">Düzenle</a>
            </div>
        </article>
    </main>
</body>

</html>