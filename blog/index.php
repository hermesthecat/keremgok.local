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
    <meta name="description" content="<?php echo htmlspecialchars(getMetaDescription()); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars(getMetaKeywords()); ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo getCanonicalUrl(); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars(getMetaDescription()); ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo getCanonicalUrl(); ?>">
    <meta property="twitter:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
    <meta property="twitter:description" content="<?php echo htmlspecialchars(getMetaDescription()); ?>">

    <link rel="canonical" href="<?php echo getCanonicalUrl(); ?>">
    <title><?php echo $pageTitle; ?></title>
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
                <a href="new-post.php" class="btn-primary"><?php echo __('new_post'); ?></a>
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
                        $activeClass = ($category === $cat) ? ' class="active"' : '';
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
                        $activeClass = ($tag === $t) ? ' active' : '';
                        $size = 1 + min(1.5, $count / max($tags));
                        echo '<a href="' . getTagUrl($t) . '" class="tag' . $activeClass . '" ';
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
                echo '<div class="filter-info">' . __('category_filter', htmlspecialchars($category)) . '</div>';
            } elseif ($tag) {
                $posts = getPostsByTag($tag, $page);
                echo '<div class="filter-info">' . __('tag_filter', htmlspecialchars($tag)) . '</div>';
            } else {
                $posts = getBlogPosts($page);
            }

            if ($posts) {
                foreach ($posts as $post) {
                    echo '<article class="blog-post" id="post-' . $post['id'] . '">';
                    echo '<h2><a href="' . getPostUrl($post['id'], $post['title']) . '">' . htmlspecialchars($post['title']) . '</a></h2>';
                    echo '<div class="post-meta">';
                    echo '<span class="post-author"><i class="fas fa-user"></i> ' . __('author') . ': ' . htmlspecialchars($post['author']) . '</span>';
                    echo '<span class="post-date"><i class="fas fa-calendar"></i> ' . __('date') . ': ' . date('d.m.Y', strtotime($post['created_at'])) . '</span>';
                    if (isset($post['category'])) {
                        $categories = is_array($post['category']) ? $post['category'] : [$post['category']];
                        foreach ($categories as $cat) {
                            echo '<span class="post-category">';
                            echo '<a href="' . getCategoryUrl(trim($cat)) . '">';
                            echo htmlspecialchars(trim($cat)) . '</a>';
                            echo '</span>';
                        }
                    }
                    echo '</div>';

                    echo '<div class="post-content">';
                    echo '<p>' . $post['excerpt'] . '</p>';
                    echo '</div>';
                    if (isset($post['tags']) && !empty($post['tags'])) {
                        echo '<div class="post-tags">';
                        foreach ($post['tags'] as $t) {
                            echo '<a href="' . getTagUrl($t) . '" class="tag">';
                            echo '<i class="fas fa-tag"></i> ' . htmlspecialchars($t) . '</a>';
                        }
                        echo '</div>';
                    }
                    echo '<div class="post-actions">';
                    echo '<a href="' . getPostUrl($post['id'], $post['title']) . '" class="read-more">' . __('read_more') . '</a>';
                    echo '<a href="edit-post.php?id=' . $post['id'] . '" class="edit-post">' . __('edit') . '</a>';
                    echo '</div>';
                    echo '</article>';
                }

                // Sayfalama
                $totalPosts = count(glob(POSTS_DIR . '*.md'));
                $totalPages = ceil($totalPosts / POSTS_PER_PAGE);

                if ($totalPages > 1) {
                    echo '<div class="pagination">';

                    // Önceki sayfa
                    if ($page > 1) {
                        $prevPage = $page - 1;
                        echo '<a href="' . getPageUrl($prevPage) . '" class="page-link">← ' . __('previous') . '</a>';
                    }

                    // Sayfa numaraları
                    for ($i = 1; $i <= $totalPages; $i++) {
                        $activeClass = ($i === $page) ? ' active' : '';
                        echo '<a href="' . getPageUrl($i) . '" class="page-link' . $activeClass . '">' . $i . '</a>';
                    }

                    // Sonraki sayfa
                    if ($page < $totalPages) {
                        $nextPage = $page + 1;
                        echo '<a href="' . getPageUrl($nextPage) . '" class="page-link">' . __('next') . ' →</a>';
                    }

                    echo '</div>';
                }
            } else {
                echo '<p class="no-posts">' . __('no_posts') . '</p>';
            }
            ?>
        </section>
    </main>
</body>

</html>