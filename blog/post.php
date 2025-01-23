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
    <meta name="description" content="<?php echo htmlspecialchars(getMetaDescription($post)); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars(getMetaKeywords($post)); ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="article">
    <meta property="og:url" content="<?php echo getCanonicalUrl('post.php?id=' . $postId); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($post['title']); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars(getMetaDescription($post)); ?>">
    <meta property="article:published_time" content="<?php echo date('c', strtotime($post['created_at'])); ?>">
    <meta property="article:author" content="<?php echo htmlspecialchars(AUTHOR_NAME); ?>">
    <?php if (isset($post['category'])):
        $categories = is_array($post['category']) ? $post['category'] : [$post['category']];
        foreach ($categories as $cat): ?>
            <meta property="article:section" content="<?php echo htmlspecialchars(trim($cat)); ?>">
    <?php endforeach;
    endif; ?>
    <?php if (isset($post['tags']) && !empty($post['tags'])):
        foreach ($post['tags'] as $tag): ?>
            <meta property="article:tag" content="<?php echo htmlspecialchars($tag); ?>">
    <?php endforeach;
    endif; ?>

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo getCanonicalUrl('post.php?id=' . $postId); ?>">
    <meta property="twitter:title" content="<?php echo htmlspecialchars($post['title']); ?>">
    <meta property="twitter:description" content="<?php echo htmlspecialchars(getMetaDescription($post)); ?>">

    <link rel="canonical" href="<?php echo getCanonicalUrl('post.php?id=' . $postId); ?>">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="blog.css">
    <script src="blog.js" defer></script>
</head>

<body>
    <main class="blog-container">
        <div class="blog-header">
            <h1><?php echo __('site_title'); ?></h1>
            <div class="header-actions">
                <div class="language-switcher">
                    <a href="<?php echo getLanguageUrl('tr'); ?>" class="<?php echo getCurrentLanguage() === 'tr' ? 'active' : ''; ?>">TR</a>
                    <a href="<?php echo getLanguageUrl('en'); ?>" class="<?php echo getCurrentLanguage() === 'en' ? 'active' : ''; ?>">EN</a>
                </div>
                <nav>
                    <a href="index.php" class="btn-secondary"><?php echo __('back_to_blog'); ?></a>
                    <a href="new-post.php" class="btn-primary"><?php echo __('new_post'); ?></a>
                </nav>
            </div>
        </div>

        <!-- Kategori ve Etiket Menüsü -->
        <aside class="blog-sidebar">
            <div class="categories">
                <h3><i class="fas fa-folder"></i> <?php echo __('categories'); ?></h3>
                <ul>
                    <?php
                    $categories = getAllCategories();
                    foreach ($categories as $cat => $count) {
                        $activeClass = (isset($post['category']) && $post['category'] === $cat) ? ' class="active"' : '';
                        echo '<li><a href="' . getCategoryUrl($cat) . '"' . $activeClass . '>';
                        echo htmlspecialchars($cat) . ' (' . $count . ')</a></li>';
                    }
                    ?>
                </ul>
            </div>

            <div class="tags">
                <h3><i class="fas fa-tags"></i> <?php echo __('tags'); ?></h3>
                <div class="tag-cloud">
                    <?php
                    $tags = getAllTags();
                    foreach ($tags as $t => $count) {
                        $activeClass = (isset($post['tags']) && in_array($t, $post['tags'])) ? ' active' : '';
                        $size = 1 + min(1.5, $count / max($tags));
                        echo '<a href="' . getTagUrl($t) . '" class="tag' . $activeClass . '" ';
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
                    <span class="post-author"><i class="fas fa-user"></i> <?php echo __('author'); ?>: <?php echo htmlspecialchars(AUTHOR_NAME); ?></span>
                    <span class="post-date"><i class="fas fa-calendar"></i> <?php echo __('date'); ?>: <?php echo date('d.m.Y', strtotime($post['created_at'])); ?></span>
                    <?php if (isset($post['category'])):
                        $categories = is_array($post['category']) ? $post['category'] : [$post['category']];
                        foreach ($categories as $cat): ?>
                            <span class="post-category">
                                <i class="fas fa-folder"></i> <?php echo __('category'); ?>: 
                                <a href="<?php echo getCategoryUrl(trim($cat)); ?>">
                                    <?php echo htmlspecialchars(trim($cat)); ?>
                                </a>
                            </span>
                    <?php endforeach;
                    endif; ?>
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
            <?php if (isset($post['tags']) && !empty($post['tags'])): ?>
                <div class="post-tags">
                    <i class="fas fa-tags"></i> <?php echo __('tags'); ?>:
                    <?php foreach ($post['tags'] as $tag): ?>
                        <a href="<?php echo getTagUrl($tag); ?>" class="tag">
                            <?php echo htmlspecialchars($tag); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="post-actions">
                <a href="index.php" class="btn-secondary"><?php echo __('back_to_blog'); ?></a>
                <a href="edit-post.php?id=<?php echo $postId; ?>" class="btn-primary"><?php echo __('edit'); ?></a>
            </div>
        </article>
    </main>
</body>

</html>