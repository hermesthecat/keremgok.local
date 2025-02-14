<?php
header('Content-Type: application/xml; charset=utf-8');

// Domain URL'sini oluştur
$domain = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

// Son güncelleme tarihini al
$lastmod = date('Y-m-d');

// XML başlangıcı
echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:xhtml="http://www.w3.org/1999/xhtml">
    <url>
        <loc><?php echo $domain; ?>/</loc>
        <lastmod><?php echo $lastmod; ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
        <?php
        // Dil dosyasını oku
        $lang_file = 'lang.json';
        if (file_exists($lang_file)) {
            $translations = json_decode(file_get_contents($lang_file), true);
            // Her dil için alternatif URL'leri ekle
            foreach ($translations as $lang_code => $lang_data) {
                echo '<xhtml:link rel="alternate" hreflang="' . $lang_code . '" href="' . $domain . '/?lang=' . $lang_code . '" />' . PHP_EOL;
            }
        }
        ?>
    </url>
</urlset>