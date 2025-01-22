<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

// Basit bir güvenlik kontrolü
$admin_username = "admin";
$admin_password = "1234"; // Gerçek uygulamada hash kullanılmalı

// Oturum kontrolü
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
        if ($_POST['username'] === $admin_username && $_POST['password'] === $admin_password) {
            $_SESSION['admin_logged_in'] = true;
        } else {
            echo '<script>alert("Hatalı kullanıcı adı veya şifre!");</script>';
            showLoginForm();
            exit;
        }
    } else {
        showLoginForm();
        exit;
    }
}

function showLoginForm() {
    ?>
    <!DOCTYPE html>
    <html lang="tr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Girişi</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f0f0f0; }
            .login-form { max-width: 400px; margin: 50px auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
            input { width: 100%; padding: 8px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; }
            button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
            button:hover { background: #0056b3; }
        </style>
    </head>
    <body>
        <div class="login-form">
            <h2>Admin Girişi</h2>
            <form method="POST">
                <input type="text" name="username" placeholder="Kullanıcı Adı" required>
                <input type="password" name="password" placeholder="Şifre" required>
                <button type="submit">Giriş Yap</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// JSON işlemleri için API endpoint
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['api'])) {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    
    $json_file = 'data.json';
    $data = json_decode(file_get_contents($json_file), true);
    
    if (isset($input['action'])) {
        switch ($input['action']) {
            case 'add':
                // Yeni ID'yi bul
                $maxId = 0;
                foreach ($data['links'] as $id => $link) {
                    $maxId = max($maxId, intval($id));
                }
                $newId = $maxId + 1;
                
                // Yeni linki ekle
                $data['links'][$newId] = [
                    'name' => $input['name'],
                    'url' => $input['url']
                ];
                break;

            case 'update':
                if (isset($input['id']) && isset($data['links'][$input['id']])) {
                    $data['links'][$input['id']] = [
                        'name' => $input['name'],
                        'url' => $input['url']
                    ];
                }
                break;

            case 'delete':
                if (isset($input['id'])) {
                    unset($data['links'][$input['id']]);
                }
                break;
        }
        
        file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo json_encode(['success' => true]);
        exit;
    }
}

// Admin panel arayüzü
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Yönetimi</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f0f0f0; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input { width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { padding: 10px 15px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; margin-right: 5px; }
        button:hover { background: #0056b3; }
        .link-list { margin-top: 20px; }
        .link-item { background: #f8f9fa; padding: 15px; margin-bottom: 10px; border-radius: 4px; border: 1px solid #dee2e6; }
        .link-item:hover { background: #e9ecef; }
        .link-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .link-title { font-size: 1.1em; font-weight: bold; }
        .link-controls { display: flex; gap: 10px; }
        .delete-btn { background: #dc3545; }
        .delete-btn:hover { background: #c82333; }
        .edit-btn { background: #28a745; }
        .edit-btn:hover { background: #218838; }
        .link-url { color: #6c757d; word-break: break-all; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-content { background: white; padding: 20px; border-radius: 8px; max-width: 500px; margin: 50px auto; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .close-btn { font-size: 24px; cursor: pointer; }
        .close-btn:hover { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Link Yönetimi</h1>
        
        <!-- Yeni Link Ekleme -->
        <div class="form-group">
            <h2>Yeni Link Ekle</h2>
            <label for="linkName">Link Adı:</label>
            <input type="text" id="linkName" placeholder="Örnek: Blog">
            
            <label for="linkUrl">Link URL:</label>
            <input type="text" id="linkUrl" placeholder="Örnek: https://blog.keremgok.tr">
            
            <button onclick="addLink()">Link Ekle</button>
        </div>

        <div id="linkList" class="link-list">
            <!-- Linkler JavaScript ile buraya yüklenecek -->
        </div>
    </div>

    <!-- Düzenleme Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Link Düzenle</h2>
                <span class="close-btn" onclick="closeModal()">&times;</span>
            </div>
            <input type="hidden" id="editId">
            <label for="editName">Link Adı:</label>
            <input type="text" id="editName">
            <label for="editUrl">Link URL:</label>
            <input type="text" id="editUrl">
            <button onclick="updateLink()">Güncelle</button>
        </div>
    </div>

    <script>
        // Linkleri yükle
        function loadLinks() {
            fetch('data.json')
                .then(response => response.json())
                .then(data => {
                    const linkList = document.getElementById('linkList');
                    linkList.innerHTML = '<h2>Mevcut Linkler</h2>';
                    
                    Object.entries(data.links).forEach(([id, link]) => {
                        const item = document.createElement('div');
                        item.className = 'link-item';
                        item.innerHTML = `
                            <div class="link-header">
                                <div class="link-title">${link.name}</div>
                                <div class="link-controls">
                                    <button class="edit-btn" onclick="showEditModal('${id}', '${link.name}', '${link.url}')">Düzenle</button>
                                    <button class="delete-btn" onclick="deleteLink('${id}')">Sil</button>
                                </div>
                            </div>
                            <div class="link-url">${link.url}</div>
                        `;
                        linkList.appendChild(item);
                    });
                });
        }

        // Yeni link ekle
        function addLink() {
            const name = document.getElementById('linkName').value;
            const url = document.getElementById('linkUrl').value;
            
            if (!name || !url) {
                alert('Lütfen hem isim hem de URL giriniz!');
                return;
            }

            sendRequest('add', null, name, url);
            document.getElementById('linkName').value = '';
            document.getElementById('linkUrl').value = '';
        }

        // Link düzenleme modalını göster
        function showEditModal(id, name, url) {
            document.getElementById('editId').value = id;
            document.getElementById('editName').value = name;
            document.getElementById('editUrl').value = url;
            document.getElementById('editModal').style.display = 'block';
        }

        // Modalı kapat
        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Link güncelle
        function updateLink() {
            const id = document.getElementById('editId').value;
            const name = document.getElementById('editName').value;
            const url = document.getElementById('editUrl').value;
            
            if (!name || !url) {
                alert('Lütfen hem isim hem de URL giriniz!');
                return;
            }

            sendRequest('update', id, name, url);
            closeModal();
        }

        // Link sil
        function deleteLink(id) {
            if (confirm('Bu linki silmek istediğinizden emin misiniz?')) {
                sendRequest('delete', id);
            }
        }

        // API isteği gönder
        function sendRequest(action, id = null, name = null, url = null) {
            const data = { action };
            if (id) data.id = id;
            if (name) data.name = name;
            if (url) data.url = url;

            fetch('admin.php?api=1', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    loadLinks();
                }
            });
        }

        // Sayfa yüklendiğinde linkleri yükle
        document.addEventListener('DOMContentLoaded', loadLinks);

        // Modal dışına tıklandığında kapat
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html> 