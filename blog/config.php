<?php

/**
 * Blog Yapılandırma Dosyası
 * 
 * @author A. Kerem Gök
 * @version 1.0
 */

// Parsedown'u dahil et
require_once __DIR__ . '/Parsedown.php';

// Site ayarları
define('SITE_TITLE', 'Blog');
define('POSTS_PER_PAGE', 5);
define('EXCERPT_LENGTH', 200);
define('POSTS_DIR', __DIR__ . '/posts/');
define('AUTHOR_NAME', 'A. Kerem Gök');

// Dosya yükleme ayarları
define('UPLOAD_DIR', 'uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

/**
 * Tüm kategorileri getir
 * @return array
 */
function getAllCategories()
{
    $categories = [];
    $files = glob(POSTS_DIR . '*.md');

    foreach ($files as $file) {
        $content = file_get_contents($file);
        $post = parseMarkdown($content);
        if (isset($post['category'])) {
            $postCategories = is_array($post['category']) ? $post['category'] : explode(',', str_replace(['[', ']', ' '], '', $post['category']));
            foreach ($postCategories as $category) {
                $category = trim($category);
                $categories[$category] = isset($categories[$category]) ? $categories[$category] + 1 : 1;
            }
        }
    }

    arsort($categories); // Yazı sayısına göre sırala
    return $categories;
}

/**
 * Tüm etiketleri getir
 * @return array
 */
function getAllTags()
{
    $tags = [];
    $files = glob(POSTS_DIR . '*.md');

    foreach ($files as $file) {
        $content = file_get_contents($file);
        $post = parseMarkdown($content);
        if (isset($post['tags'])) {
            $postTags = is_array($post['tags']) ? $post['tags'] : explode(',', str_replace(['[', ']', ' '], '', $post['tags']));
            foreach ($postTags as $tag) {
                $tags[$tag] = isset($tags[$tag]) ? $tags[$tag] + 1 : 1;
            }
        }
    }

    arsort($tags); // Kullanım sayısına göre sırala
    return $tags;
}

/**
 * Kategoriye göre yazıları getir
 * @param string $category
 * @param int $page
 * @param int $limit
 * @return array
 */
function getPostsByCategory($category, $page = 1, $limit = POSTS_PER_PAGE)
{
    $posts = [];
    $files = glob(POSTS_DIR . '*.md');

    foreach ($files as $file) {
        $content = file_get_contents($file);
        $post = parseMarkdown($content);
        if (isset($post['category'])) {
            $postCategories = is_array($post['category']) ? $post['category'] : explode(',', str_replace(['[', ']', ' '], '', $post['category']));
            if (in_array($category, array_map('trim', $postCategories))) {
                $post['id'] = basename($file, '.md');
                $post['created_at'] = date('Y-m-d H:i:s', filemtime($file));
                $posts[] = $post;
            }
        }
    }

    // Tarihe göre sırala
    usort($posts, function ($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });

    // Sayfalama
    $offset = ($page - 1) * $limit;
    return array_slice($posts, $offset, $limit);
}

/**
 * Etikete göre yazıları getir
 * @param string $tag
 * @param int $page
 * @param int $limit
 * @return array
 */
function getPostsByTag($tag, $page = 1, $limit = POSTS_PER_PAGE)
{
    $posts = [];
    $files = glob(POSTS_DIR . '*.md');

    foreach ($files as $file) {
        $content = file_get_contents($file);
        $post = parseMarkdown($content);
        if (isset($post['tags'])) {
            $postTags = is_array($post['tags']) ? $post['tags'] : explode(',', str_replace(['[', ']', ' '], '', $post['tags']));
            if (in_array($tag, $postTags)) {
                $post['id'] = basename($file, '.md');
                $post['created_at'] = date('Y-m-d H:i:s', filemtime($file));
                $posts[] = $post;
            }
        }
    }

    // Tarihe göre sırala
    usort($posts, function ($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });

    // Sayfalama
    $offset = ($page - 1) * $limit;
    return array_slice($posts, $offset, $limit);
}

/**
 * Blog yazılarını getir
 * @param int $page Sayfa numarası
 * @return array
 */
function getBlogPosts($page = 1)
{
    $posts = [];
    $files = glob(POSTS_DIR . '*.md');

    // Dosyaları tarihe göre sırala (en yeni en üstte)
    usort($files, function ($a, $b) {
        return filemtime($b) - filemtime($a);
    });

    // Sayfalama için başlangıç ve bitiş indekslerini hesapla
    $start = ($page - 1) * POSTS_PER_PAGE;
    $files = array_slice($files, $start, POSTS_PER_PAGE);

    foreach ($files as $file) {
        $content = file_get_contents($file);
        $post = parseMarkdown($content);

        if ($post) {
            $post['id'] = basename($file, '.md');
            $post['created_at'] = date('Y-m-d H:i:s', filemtime($file));
            $posts[] = $post;
        }
    }

    return $posts;
}

/**
 * Markdown içeriğini parse et
 * @param string $content
 * @return array
 */
function parseYaml($yaml)
{
    $data = [];
    $lines = explode("\n", $yaml);

    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;

        // Key-value ayır
        $parts = explode(':', $line, 2);
        if (count($parts) === 2) {
            $key = trim($parts[0]);
            $value = trim($parts[1]);

            // Etiketleri ve kategorileri dizi olarak işle
            if ($key === 'tags' || $key === 'category') {
                $value = str_replace(['[', ']', ' '], '', $value);
                $data[$key] = array_map('trim', explode(',', $value));
            } else {
                $data[$key] = $value;
            }
        }
    }

    return $data;
}

function parseMarkdown($content)
{
    $parsedown = new Parsedown();
    $parsedown->setSafeMode(true);
    // Resimleri güvenli şekilde işle
    $parsedown->setUrlsLinked(true);
    $parsedown->setMarkupEscaped(false);

    // YAML front matter'ı ayır
    $parts = explode('---', $content);
    if (count($parts) >= 3) {
        $yaml = trim($parts[1]);
        $content = trim(implode('---', array_slice($parts, 2)));

        // YAML verilerini parse et
        $metadata = parseYaml($yaml);

        // Markdown içeriğindeki resim URL'lerini düzelt
        $content = preg_replace_callback(
            '/!\[([^\]]*)\]\(([^)]+)\)/',
            function ($matches) {
                $altText = $matches[1];
                $url = $matches[2];
                return '![' . $altText . '](' . fixImageUrl($url) . ')';
            },
            $content
        );

        // Markdown içeriğini HTML'e dönüştür
        $html = $parsedown->text($content);

        // HTML içindeki resim URL'lerini düzelt
        $html = preg_replace_callback(
            '/<img[^>]+src="([^"]+)"[^>]*>/',
            function ($matches) {
                return str_replace(
                    $matches[1],
                    fixImageUrl($matches[1]),
                    $matches[0]
                );
            },
            $html
        );

        // Özet oluştur
        $excerpt = createExcerpt(strip_tags($html));

        return array_merge($metadata, ['content' => $html, 'excerpt' => $excerpt]);
    }

    // YAML front matter yoksa sadece içeriği dönüştür
    return ['content' => $parsedown->text($content)];
}

/**
 * Metin içeriğinden özet oluştur
 * @param string $text
 * @return string
 */
function createExcerpt($text)
{
    $text = strip_tags($text);
    if (mb_strlen($text) <= EXCERPT_LENGTH) {
        return $text;
    }
    return mb_substr($text, 0, EXCERPT_LENGTH) . '...';
}

/**
 * Güvenli dosya yükleme kontrolü
 * @param array $file $_FILES array
 * @return bool
 */
function isValidUpload($file)
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    if ($file['size'] > MAX_FILE_SIZE) {
        return false;
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        return false;
    }

    return true;
}

/**
 * Başlıktan URL dostu slug oluştur
 * @param string $text
 * @return string
 */
function createSlug($text)
{
    // Türkçe karakterleri değiştir
    $text = str_replace(
        ['ı', 'ğ', 'ü', 'ş', 'ö', 'ç', 'İ', 'Ğ', 'Ü', 'Ş', 'Ö', 'Ç'],
        ['i', 'g', 'u', 's', 'o', 'c', 'i', 'g', 'u', 's', 'o', 'c'],
        $text
    );

    // Küçük harfe çevir
    $text = mb_strtolower($text);

    // Alfanumerik olmayan karakterleri tire ile değiştir
    $text = preg_replace('/[^a-z0-9-]/', '-', $text);

    // Birden fazla tireyi tek tireye indir
    $text = preg_replace('/-+/', '-', $text);

    // Baştaki ve sondaki tireleri kaldır
    return trim($text, '-');
}

// Resim URL'sini düzelt
function fixImageUrl($url)
{
    // Eğer URL tam bir URL değilse (http:// veya https:// ile başlamıyorsa)
    if (!preg_match('~^(?:f|ht)tps?://~i', $url)) {
        // Başındaki slash'ı kaldır
        $url = ltrim($url, '/');
        // Domain'i ekle
        return 'http://' . $_SERVER['HTTP_HOST'] . '/' . $url;
    }
    return $url;
}
