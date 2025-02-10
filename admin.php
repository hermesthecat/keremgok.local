<?php

/**
 * Admin Panel Backend
 * Admin Panel Arka Uç
 * 
 * This file handles the admin panel authentication and API endpoints.
 * Bu dosya admin paneli kimlik doğrulaması ve API uç noktalarını yönetir.
 * 
 * Features:
 * - User authentication
 * - Session management
 * - CRUD operations for links
 * - JSON data handling
 * 
 * Özellikler:
 * - Kullanıcı kimlik doğrulama
 * - Oturum yönetimi
 * - Linkler için CRUD işlemleri
 * - JSON veri işleme
 * 
 * Security Note:
 * - Password should be hashed in production
 * - Session should be secured with additional measures
 * 
 * Güvenlik Notu:
 * - Şifre gerçek ortamda hash'lenmelidir
 * - Oturum ek önlemlerle güvenli hale getirilmelidir
 * 
 * @author A. Kerem Gök
 * @version 1.0
 */

session_start();
header('Content-Type: text/html; charset=utf-8');

// Authentication credentials / Kimlik doğrulama bilgileri
$admin_username = "admin";
$admin_password = "1234"; // Should use hashing in production / Gerçek ortamda hash kullanılmalı

// Session check / Oturum kontrolü
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Check login form submission / Giriş formu gönderimini kontrol et
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
        // Validate credentials / Kimlik bilgilerini doğrula
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

/**
 * Displays the login form
 * Giriş formunu görüntüler
 * 
 * This function generates a simple HTML form for admin login
 * Bu fonksiyon admin girişi için basit bir HTML formu oluşturur
 */
function showLoginForm()
{
?>
    <!DOCTYPE html>
    <html lang="tr">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Girişi</title>
        <link rel="stylesheet" href="admin-login.css">
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

// API endpoint for JSON operations / JSON işlemleri için API uç noktası
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['api'])) {
    header('Content-Type: application/json');

    // Read JSON input / JSON girişini oku
    $input = json_decode(file_get_contents('php://input'), true);

    // Load data file / Veri dosyasını yükle
    $json_file = 'data.json';
    $data = json_decode(file_get_contents($json_file), true);

    // Process API actions / API işlemlerini işle
    if (isset($input['action'])) {
        switch ($input['action']) {
            case 'updateAnalytics':
                // Update Analytics ID / Analytics ID'yi güncelle
                $data['analytics'] = [
                    'name' => 'analytics',
                    'url' => $input['analyticsId']
                ];
                break;

            case 'add':
                // Find new ID / Yeni ID bul
                $maxId = 0;
                foreach ($data['links'] as $id => $link) {
                    $maxId = max($maxId, intval($id));
                }
                $newId = $maxId + 1;

                // Add new link / Yeni linki ekle
                $data['links'][$newId] = [
                    'name' => $input['name'],
                    'url' => $input['url']
                ];
                break;

            case 'update':
                // Update existing link / Mevcut linki güncelle
                if (isset($input['id']) && isset($data['links'][$input['id']])) {
                    $data['links'][$input['id']] = [
                        'name' => $input['name'],
                        'url' => $input['url']
                    ];
                }
                break;

            case 'delete':
                // Delete link / Linki sil
                if (isset($input['id'])) {
                    unset($data['links'][$input['id']]);
                }
                break;
        }

        // Save changes and return response / Değişiklikleri kaydet ve yanıt döndür
        file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo json_encode(['success' => true]);
        exit;
    }
}

// Admin panel interface / Admin panel arayüzü
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Yönetimi</title>
    <link rel="stylesheet" href="admin.css">
</head>

<body>
    <div class="container">
        <h1>Link Yönetimi</h1>

        <!-- Google Analytics Settings / Google Analytics Ayarları -->
        <div class="form-group">
            <h2>Google Analytics Ayarları</h2>
            <label for="analyticsId">Analytics ID:</label>
            <input type="text" id="analyticsId" placeholder="Örnek: G-XXXXXXXXXX" value="<?php echo $data['analytics']['url'] ?? ''; ?>">
            <button onclick="updateAnalytics()">Analytics ID Güncelle</button>
        </div>

        <!-- Add new link form / Yeni link ekleme formu -->
        <div class="form-group">
            <h2>Yeni Link Ekle</h2>
            <label for="linkName">Link Adı:</label>
            <input type="text" id="linkName" placeholder="Örnek: Blog">

            <label for="linkUrl">Link URL:</label>
            <input type="text" id="linkUrl" placeholder="Örnek: https://blog.keremgok.tr">

            <button onclick="addLink()">Link Ekle</button>
        </div>

        <!-- Link list container / Link listesi kapsayıcısı -->
        <div id="linkList" class="link-list">
            <!-- Links will be loaded here by JavaScript / Linkler JavaScript ile buraya yüklenecek -->
        </div>
    </div>

    <!-- Edit modal dialog / Düzenleme modal penceresi -->
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

    <!-- Include JavaScript file / JavaScript dosyasını dahil et -->
    <script src="admin.js"></script>

    <script>
        // Update Analytics ID / Analytics ID'yi güncelle
        function updateAnalytics() {
            const analyticsId = document.getElementById("analyticsId").value;

            if (!analyticsId) {
                alert("Lütfen Analytics ID giriniz!");
                return;
            }

            // Send update request / Güncelleme isteği gönder
            fetch("admin.php?api=1", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({
                        action: "updateAnalytics",
                        analyticsId: analyticsId
                    }),
                })
                .then((response) => response.json())
                .then((result) => {
                    if (result.success) {
                        alert("Analytics ID başarıyla güncellendi!");
                    }
                });
        }
    </script>
</body>

</html>