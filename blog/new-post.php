<?php

/**
 * Yeni Blog Yazısı Oluşturma
 * 
 * @author A. Kerem Gök
 * @version 1.0
 */

session_start();
require_once 'config.php';

// Yazı gönderildi mi kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $tags = trim($_POST['tags'] ?? '');

    if ($title && $content) {
        // Dosya adını oluştur
        $slug = createSlug($title);
        $filename = $slug . '.md';
        $filepath = POSTS_DIR . $filename;

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

        // Dosyayı kaydet
        if (file_put_contents($filepath, $markdown)) {
            header('Location: post.php?id=' . basename($filename, '.md'));
            exit;
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

        <form method="post" class="post-editor">
            <div class="form-group">
                <label for="title">Başlık</label>
                <input type="text" id="title" name="title" required>
            </div>

            <div class="form-group">
                <label for="category">Kategori</label>
                <input type="text" id="category" name="category" list="categories" required>
                <datalist id="categories">
                    <?php foreach ($categories as $cat => $count): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>">
                        <?php endforeach; ?>
                </datalist>
            </div>

            <div class="form-group">
                <label for="tags">Etiketler (virgülle ayırın)</label>
                <input type="text" id="tags" name="tags" required>
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
                <textarea id="content" name="content" required></textarea>
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