<?php

/**
 * Yeni Blog Yazısı Oluşturma
 * 
 * @author A. Kerem Gök
 * @version 1.0
 */

session_start();
require_once 'config.php';

$error = '';
$success = '';

// Debug için
error_reporting(E_ALL);
ini_set('display_errors', 1);

// POST verilerini kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug için
    error_log("POST verileri: " . print_r($_POST, true));
    
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $tags = trim($_POST['tags'] ?? '');
    
    // Debug için
    error_log("İşlenen veriler:");
    error_log("Başlık: $title");
    error_log("İçerik: $content");
    error_log("Kategori: $category");
    error_log("Etiketler: $tags");
    
    if (!$title) {
        $error = 'Başlık boş olamaz.';
    } elseif (!$content) {
        $error = 'İçerik boş olamaz.';
    } elseif (!$category) {
        $error = 'Kategori boş olamaz.';
    } elseif (!$tags) {
        $error = 'En az bir etiket ekleyin.';
    } else {
        try {
            // Dosya adını oluştur
            $slug = createSlug($title);
            $filename = $slug . '.md';
            $filepath = POSTS_DIR . $filename;
            
            // Debug için
            error_log("Oluşturulacak dosya: $filepath");
            
            // Dosya zaten varsa slug'a sayı ekle
            $counter = 1;
            while (file_exists($filepath)) {
                $filename = $slug . '-' . $counter . '.md';
                $filepath = POSTS_DIR . $filename;
                $counter++;
            }
            
            // Markdown içeriğini oluştur
            $markdown = "---\n";
            $markdown .= "title: " . $title . "\n";
            $markdown .= "author: " . AUTHOR_NAME . "\n";
            $markdown .= "date: " . date('Y-m-d') . "\n";
            $markdown .= "category: " . $category . "\n";
            $markdown .= "tags: [" . $tags . "]\n";
            $markdown .= "---\n\n";
            $markdown .= $content;
            
            // Debug için
            error_log("Oluşturulan markdown:");
            error_log($markdown);
            
            // posts klasörünü kontrol et ve oluştur
            if (!file_exists(POSTS_DIR)) {
                error_log("Posts klasörü oluşturuluyor...");
                if (!mkdir(POSTS_DIR, 0777, true)) {
                    throw new Exception('Posts klasörü oluşturulamadı.');
                }
            }
            
            // Dosyayı kaydet
            if (file_put_contents($filepath, $markdown) === false) {
                throw new Exception('Dosya kaydedilemedi.');
            }
            
            error_log("Dosya başarıyla kaydedildi: $filepath");
            
            $success = 'Yazı başarıyla yayınlandı!';
            header('Location: post.php?id=' . basename($filename, '.md'));
            exit;
        } catch (Exception $e) {
            error_log("Hata oluştu: " . $e->getMessage());
            $error = 'Hata: ' . $e->getMessage();
        }
    }
}

// Mevcut kategorileri al
$categories = getAllCategories();
$allTags = getAllTags();

// Sayfa başlığı
$pageTitle = "Yeni Yazı - Blog";
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="blog.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
    <script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>
    <script src="blog.js" defer></script>
</head>

<body>
    <main class="blog-container editor-page">
        <h1>Yeni Blog Yazısı</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <form method="post" class="post-editor" id="postForm" onsubmit="return submitForm(event)">
            <div class="form-group">
                <label for="title">Başlık</label>
                <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="category">Kategori</label>
                <input type="text" id="category" name="category" list="categories" required value="<?php echo htmlspecialchars($_POST['category'] ?? ''); ?>">
                <datalist id="categories">
                    <?php foreach ($categories as $cat => $count): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
            
            <div class="form-group">
                <label for="tags">Etiketler (virgülle ayırın)</label>
                <input type="text" id="tags" name="tags" required value="<?php echo htmlspecialchars($_POST['tags'] ?? ''); ?>">
                <div class="tag-suggestions">
                    <?php foreach ($allTags as $tag => $count): ?>
                        <span class="tag" onclick="addTag(this.textContent)">
                            <?php echo htmlspecialchars($tag); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="form-group">
                <label for="content">İçerik</label>
                <textarea id="content" name="content"><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-primary">Yazıyı Yayınla</button>
                <a href="index.php" class="btn-secondary">İptal</a>
            </div>
        </form>
    </main>
    
    <script>
        // EasyMDE editörünü başlat
        const easyMDE = new EasyMDE({
            element: document.getElementById('content'),
            spellChecker: false,
            autosave: {
                enabled: true,
                uniqueId: 'blog-post-content',
                delay: 1000,
            },
            toolbar: [
                'bold', 'italic', 'heading', '|',
                'quote', 'unordered-list', 'ordered-list', '|',
                'link', 'image', '|',
                'preview', 'side-by-side', 'fullscreen', '|',
                'guide'
            ],
            placeholder: 'Markdown formatında yazınızı buraya yazın...',
            status: ['autosave', 'lines', 'words', 'cursor']
        });
        
        // Form submit fonksiyonu
        function submitForm(e) {
            e.preventDefault();
            
            // İçeriği al ve textarea'ya kopyala
            const content = easyMDE.value();
            document.getElementById('content').value = content;
            
            // Form verilerini kontrol et
            const title = document.getElementById('title').value.trim();
            const category = document.getElementById('category').value.trim();
            const tags = document.getElementById('tags').value.trim();
            
            if (!title) {
                alert('Başlık boş olamaz.');
                return false;
            }
            
            if (!category) {
                alert('Kategori boş olamaz.');
                return false;
            }
            
            if (!tags) {
                alert('En az bir etiket ekleyin.');
                return false;
            }
            
            if (!content) {
                alert('İçerik boş olamaz.');
                return false;
            }
            
            // Form'u manuel olarak gönder
            const form = document.getElementById('postForm');
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                body: formData
            }).then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                }
            }).catch(error => {
                console.error('Hata:', error);
                alert('Bir hata oluştu. Lütfen tekrar deneyin.');
            });
            
            return false;
        }
        
        // Etiket ekleme fonksiyonu
        function addTag(tag) {
            const tagsInput = document.getElementById('tags');
            const currentTags = tagsInput.value.split(',').map(t => t.trim()).filter(t => t);
            
            if (!currentTags.includes(tag)) {
                currentTags.push(tag);
                tagsInput.value = currentTags.join(', ');
            }
        }
    </script>
</body>

</html>