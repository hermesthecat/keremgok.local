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
define('POSTS_PER_PAGE', 10);
define('EXCERPT_LENGTH', 200);
define('POSTS_DIR', __DIR__ . '/posts/');

// Dosya yükleme ayarları
define('UPLOAD_DIR', 'uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

/**
 * Blog yazılarını getir
 * @param int $page Sayfa numarası
 * @param int $limit Sayfa başına gösterilecek yazı sayısı
 * @return array
 */
function getBlogPosts($page = 1, $limit = POSTS_PER_PAGE) {
    $posts = [];
    $files = glob(POSTS_DIR . '*.md');
    
    // Dosyaları tarihe göre sırala (en yeni en üstte)
    usort($files, function($a, $b) {
        return filemtime($b) - filemtime($a);
    });
    
    // Sayfalama
    $offset = ($page - 1) * $limit;
    $files = array_slice($files, $offset, $limit);
    
    foreach ($files as $file) {
        $content = file_get_contents($file);
        $post = parseMarkdown($content);
        $post['id'] = basename($file, '.md');
        $post['created_at'] = date('Y-m-d H:i:s', filemtime($file));
        $posts[] = $post;
    }
    
    return $posts;
}

/**
 * Markdown içeriğini parse et
 * @param string $content
 * @return array
 */
function parseMarkdown($content) {
    $lines = explode("\n", $content);
    $post = [
        'title' => '',
        'author' => '',
        'excerpt' => '',
        'content' => ''
    ];
    
    // YAML front matter'ı parse et
    $yamlStart = false;
    $contentStart = false;
    $contentLines = [];
    
    foreach ($lines as $line) {
        if (trim($line) === '---') {
            if (!$yamlStart) {
                $yamlStart = true;
                continue;
            } else {
                $contentStart = true;
                continue;
            }
        }
        
        if (!$contentStart && $yamlStart) {
            $parts = explode(':', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1]);
                $post[$key] = $value;
            }
        }
        
        if ($contentStart) {
            $contentLines[] = $line;
        }
    }
    
    // Parsedown kullanarak içeriği HTML'e dönüştür
    $parsedown = new Parsedown();
    $parsedown->setSafeMode(true);
    
    $content = implode("\n", $contentLines);
    $post['content'] = $parsedown->text($content);
    $post['excerpt'] = createExcerpt(strip_tags($post['content']));
    
    return $post;
}

/**
 * Metin içeriğinden özet oluştur
 * @param string $text
 * @return string
 */
function createExcerpt($text) {
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
function isValidUpload($file) {
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