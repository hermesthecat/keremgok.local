<?php
/**
 * Blog Yapılandırma Dosyası
 * 
 * @author A. Kerem Gök
 * @version 1.0
 */

// Veritabanı bağlantı bilgileri
define('DB_HOST', 'localhost');
define('DB_NAME', 'blog_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site ayarları
define('SITE_TITLE', 'Blog');
define('POSTS_PER_PAGE', 10);
define('EXCERPT_LENGTH', 200);

// Dosya yükleme ayarları
define('UPLOAD_DIR', 'uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

/**
 * Veritabanı bağlantısını oluştur
 * @return PDO
 */
function getDB() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $db = new PDO($dsn, DB_USER, DB_PASS);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $db;
    } catch (PDOException $e) {
        error_log("Veritabanı bağlantı hatası: " . $e->getMessage());
        die("Veritabanına bağlanılamadı. Lütfen daha sonra tekrar deneyiniz.");
    }
}

/**
 * Blog yazılarını getir
 * @param int $page Sayfa numarası
 * @param int $limit Sayfa başına gösterilecek yazı sayısı
 * @return array
 */
function getBlogPosts($page = 1, $limit = POSTS_PER_PAGE) {
    try {
        $db = getDB();
        $offset = ($page - 1) * $limit;
        
        $stmt = $db->prepare("
            SELECT posts.*, users.username as author
            FROM posts 
            JOIN users ON posts.author_id = users.id
            WHERE posts.status = 'published'
            ORDER BY posts.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Blog yazıları getirme hatası: " . $e->getMessage());
        return [];
    }
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