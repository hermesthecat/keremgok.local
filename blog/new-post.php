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

            // Kategorileri dizi olarak kaydet
            $categoryArray = array_map('trim', explode(',', $categories_input));
            $markdown .= "category: [" . implode(',', $categoryArray) . "]\n";

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="blog.css">
    <script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/php.min.js"></script>
    <script src="blog.js" defer></script>
</head>

<body>
    <main class="blog-container editor-page">
        <div class="blog-header">
            <h1><?php echo __('new_post'); ?></h1>
            <div class="header-actions">
                <div class="language-switcher">
                    <a href="<?php echo getLanguageUrl('tr'); ?>" class="<?php echo getCurrentLanguage() === 'tr' ? 'active' : ''; ?>">TR</a>
                    <a href="<?php echo getLanguageUrl('en'); ?>" class="<?php echo getCurrentLanguage() === 'en' ? 'active' : ''; ?>">EN</a>
                </div>
                <a href="index.php" class="btn-secondary"><?php echo __('back_to_blog'); ?></a>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo __($error); ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo __($success); ?></div>
        <?php endif; ?>

        <form id="postForm" class="post-editor" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title"><?php echo __('title'); ?> *</label>
                <input type="text" id="title" name="title" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="category"><?php echo __('categories'); ?> *</label>
                <input type="text" id="category" name="category" value="<?php echo isset($_POST['category']) ? htmlspecialchars($_POST['category']) : ''; ?>" required>
                <div class="category-suggestions">
                    <?php foreach (getAllCategories() as $cat => $count): ?>
                        <span class="category-tag" onclick="addCategory('<?php echo htmlspecialchars($cat); ?>')"><?php echo htmlspecialchars($cat); ?></span>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="tags"><?php echo __('tags'); ?></label>
                <input type="text" id="tags" name="tags" value="<?php echo isset($_POST['tags']) ? htmlspecialchars($_POST['tags']) : ''; ?>">
                <div class="tag-suggestions">
                    <?php foreach (getAllTags() as $t => $count): ?>
                        <span class="tag" onclick="addTag('<?php echo htmlspecialchars($t); ?>')"><?php echo htmlspecialchars($t); ?></span>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="content"><?php echo __('content'); ?> *</label>
                <textarea id="content" name="content" required><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary"><?php echo __('publish'); ?></button>
                <a href="index.php" class="btn-secondary"><?php echo __('cancel'); ?></a>
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
                            onError(result.message || 'Dosya yüklenirken bir hata oluştu.');
                        }
                    })
                    .catch(error => {
                        console.error('Yükleme hatası:', error);
                        onError('Dosya yüklenirken bir hata oluştu.');
                    });
            },
            toolbar: [
                'bold', 'italic', 'heading', 'strikethrough', '|',
                'quote', 'code', 'unordered-list', 'ordered-list', '|',
                'link', 'upload-image', '|',
                {
                    name: "table",
                    action: EasyMDE.drawTable,
                    className: "fas fa-table",
                    title: "Tablo Ekle",
                },
                {
                    name: "emoji",
                    action: function(editor) {
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
                        picker.style.boxShadow = '0 2px 5px rgba(0,0,0,0.2)';
                        picker.style.top = '100%';
                        picker.style.left = '0';

                        const emojiList = ["😀", "😃", "😄", "😁", "😅", "😂", "🤣", "😊", "😇",
                            "🙂", "🙃", "😉", "😌", "😍", "🥰", "😘", "😗", "😙",
                            "😚", "😋", "😛", "😝", "😜", "🤪", "🤨", "🧐", "🤓",
                            "😎", "🤩", "🥳", "😏", "😒", "😞", "😔", "😟", "😕",
                            "🙁", "☹️", "😣", "😖", "😫", "😩", "🥺", "😢", "😭"
                        ];

                        // Emojileri ekle
                        emojiList.forEach(emoji => {
                            const btn = document.createElement('button');
                            btn.textContent = emoji;
                            btn.style.border = 'none';
                            btn.style.background = 'none';
                            btn.style.cursor = 'pointer';
                            btn.style.fontSize = '20px';
                            btn.style.padding = '5px';
                            btn.style.width = '100%';
                            btn.style.height = '100%';
                            btn.onclick = (e) => {
                                e.preventDefault();
                                e.stopPropagation();
                                const pos = editor.codemirror.getCursor();
                                editor.codemirror.replaceRange(emoji, pos);
                                picker.remove();
                            };
                            picker.appendChild(btn);
                        });

                        // Editörün toolbar'ına ekle
                        const toolbarButton = editor.gui.toolbar.getElementsByClassName('fas fa-face-smile')[0];
                        if (toolbarButton) {
                            const buttonRect = toolbarButton.getBoundingClientRect();
                            picker.style.position = 'fixed';
                            picker.style.left = buttonRect.left + 'px';
                            picker.style.top = (buttonRect.bottom + 5) + 'px';
                            document.body.appendChild(picker);
                        }

                        // Dışarı tıklandığında kapat
                        const closeEmoji = function(e) {
                            if (!picker.contains(e.target) && !toolbarButton.contains(e.target)) {
                                picker.remove();
                                document.removeEventListener('click', closeEmoji);
                            }
                        };

                        // Bir tick bekleyip event listener'ı ekle
                        setTimeout(() => {
                            document.addEventListener('click', closeEmoji);
                        }, 0);
                    },
                    className: "fas fa-face-smile",
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
            var categoriesInput = document.getElementById('category');
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