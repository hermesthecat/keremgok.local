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
    $categories_input = trim($_POST['categories'] ?? '');
    $tags = trim($_POST['tags'] ?? '');

    // Validasyon
    if (empty($title)) {
        $error = 'Başlık alanı zorunludur.';
    } elseif (empty($content)) {
        $error = 'İçerik alanı zorunludur.';
    } elseif (empty($categories_input)) {
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
            $markdown .= "category: " . $categories_input . "\n";

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
                $title = $content = $categories_input = $tags = '';
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github.min.css">
    <link rel="stylesheet" href="blog.css">
    <script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/php.min.js"></script>
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
                <label for="categories">Kategoriler (virgülle ayırın)</label>
                <input type="text" id="categories" name="categories" value="<?php echo htmlspecialchars($categories_input ?? ''); ?>">
                <?php if (!empty($categories)): ?>
                    <div class="category-suggestions">
                        <?php foreach ($categories as $cat): ?>
                            <span class="category-tag" onclick="addCategory('<?php echo htmlspecialchars($cat); ?>')"><?php echo htmlspecialchars($cat); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
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
            status: ['lines', 'words', 'cursor'],
            uploadImage: true,
            imageUploadEndpoint: 'new-post.php',
            imageMaxSize: <?php echo MAX_FILE_SIZE; ?>,
            imageAccept: '<?php echo '.' . implode(',.', ALLOWED_EXTENSIONS); ?>',
            toolbar: [
                'bold', 'italic', 'heading', 'strikethrough', '|',
                'quote', 'code', 'unordered-list', 'ordered-list', '|',
                'link', 'upload-image', '|',
                {
                    name: "table",
                    action: EasyMDE.drawTable,
                    className: "fa fa-table",
                    title: "Tablo Ekle",
                },
                {
                    name: "emoji",
                    action: function(editor) {
                        const emojiList = ["😀", "😃", "😄", "😁", "😅", "😂", "🤣", "😊", "😇",
                            "🙂", "🙃", "😉", "😌", "😍", "🥰", "😘", "😗", "😙",
                            "😚", "😋", "😛", "😝", "😜", "🤪", "🤨", "🧐", "🤓",
                            "😎", "🤩", "🥳", "😏", "😒", "😞", "😔", "😟", "😕",
                            "🙁", "☹️", "😣", "😖", "😫", "😩", "🥺", "😢", "😭"
                        ];

                        // Emoji seçici oluştur
                        const picker = document.createElement('div');
                        picker.className = 'emoji-picker';
                        picker.style.position = 'absolute';
                        picker.style.backgroundColor = 'white';
                        picker.style.border = '1px solid #ddd';
                        picker.style.borderRadius = '4px';
                        picker.style.padding = '10px';
                        picker.style.display = 'grid';
                        picker.style.gridTemplateColumns = 'repeat(10, 1fr)';
                        picker.style.gap = '5px';
                        picker.style.zIndex = '1000';

                        // Emojileri ekle
                        emojiList.forEach(emoji => {
                            const btn = document.createElement('button');
                            btn.textContent = emoji;
                            btn.style.border = 'none';
                            btn.style.background = 'none';
                            btn.style.cursor = 'pointer';
                            btn.style.fontSize = '20px';
                            btn.onclick = () => {
                                const pos = editor.codemirror.getCursor();
                                editor.codemirror.replaceRange(emoji, pos);
                                picker.remove();
                            };
                            picker.appendChild(btn);
                        });

                        // Editörün üzerine yerleştir
                        const toolbar = editor.gui.toolbar;
                        toolbar.appendChild(picker);

                        // Dışarı tıklandığında kapat
                        document.addEventListener('click', function closeEmoji(e) {
                            if (!picker.contains(e.target)) {
                                picker.remove();
                                document.removeEventListener('click', closeEmoji);
                            }
                        });
                    },
                    className: "fa fa-smile-o",
                    title: "Emoji Ekle",
                },
                '|',
                'preview', 'side-by-side', 'fullscreen', '|',
                'guide'
            ],
            previewRender: function(plainText, preview) {
                // Özel önizleme işleme
                setTimeout(function() {
                    preview.innerHTML = this.parent.markdown(plainText);

                    // Kod bloklarına syntax highlighting ekle
                    preview.querySelectorAll('pre code').forEach((block) => {
                        hljs.highlightElement(block);
                    });
                }.bind(this), 0);

                return "Yükleniyor...";
            },
            renderingConfig: {
                singleLineBreaks: false,
                codeSyntaxHighlighting: true,
            },
            tabSize: 4,
            promptURLs: true,
            promptTexts: {
                image: "Görsel URL'si girin:",
                link: "Bağlantı URL'si girin:"
            }
        });

        // Form gönderilmeden önce içeriği güncelle
        document.getElementById('postForm').addEventListener('submit', function(e) {
            document.getElementById('content').value = easyMDE.value();
        });

        // Kategori ekleme fonksiyonu
        function addCategory(category) {
            var categoriesInput = document.getElementById('categories');
            var currentCategories = categoriesInput.value.split(',').map(t => t.trim()).filter(t => t);

            if (!currentCategories.includes(category)) {
                currentCategories.push(category);
                categoriesInput.value = currentCategories.join(', ');
            }
        }

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