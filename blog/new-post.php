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

// Dosya yükleme işlemi için AJAX endpoint
if (isset($_FILES['image']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    try {
        $file = $_FILES['image'];
        if (isValidUpload($file)) {
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $newFileName = uniqid() . '.' . $extension;

            if (!file_exists(UPLOAD_DIR)) {
                mkdir(UPLOAD_DIR, 0777, true);
            }

            $destination = UPLOAD_DIR . $newFileName;
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                echo json_encode(['success' => 1, 'file' => ['url' => '/' . $destination]]);
                exit;
            }
        }
        echo json_encode(['success' => 0, 'message' => 'Dosya yüklenemedi.']);
        exit;
    } catch (Exception $e) {
        echo json_encode(['success' => 0, 'message' => $e->getMessage()]);
        exit;
    }
}

// Form gönderildi mi kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_FILES['image'])) {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $tags = trim($_POST['tags'] ?? '');

    // Validasyon
    if (empty($title)) {
        $error = 'Başlık alanı zorunludur.';
    } elseif (empty($content)) {
        $error = 'İçerik alanı zorunludur.';
    } elseif (empty($category)) {
        $error = 'Kategori alanı zorunludur.';
    } else {
        try {
            // Slug oluştur
            $slug = createSlug($title);
            $baseSlug = $slug;
            $counter = 1;

            while (file_exists(POSTS_DIR . $slug . '.md')) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            // Markdown içeriği oluştur
            $markdown = "---\n";
            $markdown .= "title: " . $title . "\n";
            $markdown .= "author: " . AUTHOR_NAME . "\n";
            $markdown .= "category: " . $category . "\n";

            if (!empty($tags)) {
                $tagArray = array_map('trim', explode(',', $tags));
                $markdown .= "tags: [" . implode(',', $tagArray) . "]\n";
            }

            $markdown .= "---\n\n";
            $markdown .= $content;

            // Dosyayı kaydet
            if (file_put_contents(POSTS_DIR . $slug . '.md', $markdown)) {
                $success = 'Blog yazısı başarıyla yayınlandı.';
                // Formu temizle
                $title = $content = $category = $tags = '';
            } else {
                $error = 'Yazı kaydedilirken bir hata oluştu.';
            }
        } catch (Exception $e) {
            $error = 'Bir hata oluştu: ' . $e->getMessage();
        }
    }
}

// Mevcut kategorileri ve etiketleri al
$categories = array_keys(getAllCategories());
$allTags = array_keys(getAllTags());

// Sayfa başlığı
$pageTitle = "Yeni Blog Yazısı";
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
    <link rel="stylesheet" href="blog.css">
    <script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>
    <script src="blog.js" defer></script>
</head>

<body>
    <main class="blog-container editor-page">
        <div class="blog-header">
            <h1><?php echo $pageTitle; ?></h1>
            <a href="index.php" class="btn-secondary">Blog'a Dön</a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form id="postForm" method="post" class="post-editor">
            <div class="form-group">
                <label for="title">Başlık</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="category">Kategori</label>
                <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($category ?? ''); ?>" list="categoryList">
                <datalist id="categoryList">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>">
                        <?php endforeach; ?>
                </datalist>
            </div>

            <div class="form-group">
                <label for="tags">Etiketler (virgülle ayırın)</label>
                <input type="text" id="tags" name="tags" value="<?php echo htmlspecialchars($tags ?? ''); ?>">
                <?php if (!empty($allTags)): ?>
                    <div class="tag-suggestions">
                        <?php foreach ($allTags as $tag): ?>
                            <span class="tag" onclick="addTag('<?php echo htmlspecialchars($tag); ?>')"><?php echo htmlspecialchars($tag); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="content">İçerik</label>
                <textarea id="content" name="content"><?php echo htmlspecialchars($content ?? ''); ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Yayınla</button>
                <a href="index.php" class="btn-secondary">İptal</a>
            </div>
        </form>
    </main>

    <script>
        // EasyMDE editörünü başlat
        var easyMDE = new EasyMDE({
            element: document.getElementById('content'),
            spellChecker: false,
            status: ['lines', 'words'],
            uploadImage: true,
            imageUploadEndpoint: 'new-post.php',
            imageMaxSize: <?php echo MAX_FILE_SIZE; ?>,
            imageAccept: '<?php echo '.' . implode(',.', ALLOWED_EXTENSIONS); ?>',
            toolbar: [
                'bold', 'italic', 'heading', '|',
                'quote', 'unordered-list', 'ordered-list', '|',
                'link', 'upload-image', '|',
                'preview', 'side-by-side', 'fullscreen', '|',
                'guide'
            ],
            imageUploadFunction: function(file, onSuccess, onError) {
                var formData = new FormData();
                formData.append('image', file);

                fetch('new-post.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            onSuccess(result.file.url);
                        } else {
                            onError(result.message);
                        }
                    })
                    .catch(error => {
                        onError('Dosya yüklenirken bir hata oluştu.');
                    });
            }
        });

        // Form gönderilmeden önce içeriği güncelle
        document.getElementById('postForm').addEventListener('submit', function(e) {
            document.getElementById('content').value = easyMDE.value();
        });

        // Etiket ekleme fonksiyonu
        function addTag(tag) {
            var tagsInput = document.getElementById('tags');
            var currentTags = tagsInput.value.split(',').map(t => t.trim()).filter(t => t);

            if (!currentTags.includes(tag)) {
                currentTags.push(tag);
                tagsInput.value = currentTags.join(', ');
            }
        }
    </script>
</body>

</html>