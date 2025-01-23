<?php

/**
 * Dinamik Sitemap Oluşturucu
 * 
 * @author A. Kerem Gök
 * @version 1.0
 */

require_once 'config.php';

// XML başlığını ayarla
header('Content-Type: application/xml; charset=utf-8');

// Son güncelleme tarihini al (en son eklenen yazının tarihi)
$lastmod = date('Y-m-d');
$files = glob(POSTS_DIR . '*.md');
if (!empty($files)) {
    $lastmod = date('Y-m-d', max(array_map('filemtime', $files)));
}

// XML çıktısını oluştur
echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Ana Sayfa -->
    <url>
        <loc>http://<?php echo $_SERVER['HTTP_HOST']; ?>/</loc>
        <lastmod><?php echo $lastmod; ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    <!-- Blog Yazıları -->
    <?php
    foreach ($files as $file) {
        $postId = basename($file, '.md');
        $modified = date('Y-m-d', filemtime($file));
        $content = file_get_contents($file);
        $post = parseMarkdown($content);
    ?>
        <url>
            <loc>http://<?php echo $_SERVER['HTTP_HOST']; ?>/post.php?id=<?php echo $postId; ?></loc>
            <lastmod><?php echo $modified; ?></lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
        </url>
    <?php } ?>

    <!-- Kategoriler -->
    <?php
    $categories = getAllCategories();
    foreach (array_keys($categories) as $category) {
    ?>
        <url>
            <loc>http://<?php echo $_SERVER['HTTP_HOST']; ?>/index.php?category=<?php echo urlencode($category); ?></loc>
            <lastmod><?php echo $lastmod; ?></lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.6</priority>
        </url>
    <?php } ?>

    <!-- Etiketler -->
    <?php
    $tags = getAllTags();
    foreach (array_keys($tags) as $tag) {
    ?>
        <url>
            <loc>http://<?php echo $_SERVER['HTTP_HOST']; ?>/index.php?tag=<?php echo urlencode($tag); ?></loc>
            <lastmod><?php echo $lastmod; ?></lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.6</priority>
        </url>
    <?php } ?>
</urlset>