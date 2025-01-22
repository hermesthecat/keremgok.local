<?php

/**
 * Personal Website Main Page
 * Kişisel Web Sitesi Ana Sayfası
 * 
 * This file serves as the main entry point for the personal website.
 * Bu dosya, kişisel web sitesinin ana giriş noktasıdır.
 * 
 * Features:
 * - Multi-language support
 * - Theme switching (dark/light)
 * - Dynamic link management
 * - SEO optimization
 * - Social media integration
 * - Google Analytics integration
 * 
 * Özellikler:
 * - Çoklu dil desteği
 * - Tema değiştirme (koyu/açık)
 * - Dinamik link yönetimi
 * - SEO optimizasyonu
 * - Sosyal medya entegrasyonu
 * - Google Analytics entegrasyonu
 * 
 * @author A. Kerem Gök
 * @version 1.0
 */

session_start(); // Start session for language preference / Dil tercihi için oturum başlat

// Create dynamic domain URL with HTTP/HTTPS check
// Dinamik domain URL'si oluştur (HTTP/HTTPS kontrolü ile)
$domain = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

/**
 * Language Management Functions
 * Dil Yönetimi Fonksiyonları
 */

/**
 * Loads translations from lang.json
 * lang.json dosyasından çevirileri yükler
 * 
 * @return array Translation data / Çeviri verileri
 */
function loadTranslations()
{
  $lang_file = 'lang.json';
  if (file_exists($lang_file)) {
    return json_decode(file_get_contents($lang_file), true);
  }
  return [];
}

/**
 * Determines the current language
 * Mevcut dili belirler
 * 
 * Priority order: URL > Session > Browser Language > Default (en)
 * Öncelik sırası: URL > Session > Tarayıcı Dili > Varsayılan (en)
 * 
 * @return string Language code (e.g., 'tr', 'en') / Dil kodu (örn: 'tr', 'en')
 */
function getCurrentLang()
{
  $translations = loadTranslations();
  $supported_languages = array_keys($translations);

  // 1. Check URL parameter / URL parametresini kontrol et
  if (isset($_GET['lang']) && in_array($_GET['lang'], $supported_languages)) {
    $_SESSION['lang'] = $_GET['lang'];
    return $_GET['lang'];
  }

  // 2. Check session / Session'ı kontrol et
  if (isset($_SESSION['lang']) && in_array($_SESSION['lang'], $supported_languages)) {
    return $_SESSION['lang'];
  }

  // 3. Check browser language / Tarayıcı dilini kontrol et
  $browser_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en', 0, 2);
  if (in_array($browser_lang, $supported_languages)) {
    $_SESSION['lang'] = $browser_lang;
    return $browser_lang;
  }

  // 4. Default language / Varsayılan dil
  return 'en';
}

/**
 * Returns translated text for given key
 * Verilen anahtar için çevrilmiş metni döndürür
 * 
 * @param string $key Translation key / Çeviri anahtarı
 * @return string Translated text / Çevrilmiş metin
 */
function translate($key)
{
  static $translations = null;
  static $current_lang = null;

  if ($translations === null) {
    $translations = loadTranslations();
    $current_lang = getCurrentLang();
  }

  return $translations[$current_lang][$key] ?? $key;
}

// Theme management - stored in cookie / Tema yönetimi - cookie'de saklanır
$theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'dark';
if (isset($_GET['theme']) && in_array($_GET['theme'], ['light', 'dark'])) {
  $theme = $_GET['theme'];
  setcookie('theme', $theme, time() + (86400 * 365), "/"); // Valid for 1 year / 1 yıl geçerli
}

// Get current language and translations / Mevcut dili ve çevirileri al
$current_lang = getCurrentLang();
$translations = loadTranslations();
$languages = [];
foreach ($translations as $code => $trans) {
  $languages[$code] = $trans['langText'] ?? $code;
}

// Read data from data.json / data.json'dan verileri oku
$json_file = 'data.json';
$data = [];
if (file_exists($json_file)) {
  $data = json_decode(file_get_contents($json_file), true);
}

/**
 * Reads value from data.json, returns default if not found
 * data.json'dan değer okur, yoksa varsayılan değeri döndürür
 * 
 * @param string $section Section name / Bölüm adı
 * @param string $key Key / Anahtar
 * @param mixed $default Default value / Varsayılan değer
 * @return mixed Read value / Okunan değer
 */
function getValue($section, $key, $default = '')
{
  global $data;
  return $data[$section][$key] ?? $default;
}

// Social media links / Sosyal medya linkleri
$links = $data['links'] ?? [];

// Find Twitter username / Twitter kullanıcı adını bul
$twitter_link = null;
foreach ($links as $link) {
  if (strtolower($link['name']) === 'twitter') {
    $twitter_link = $link;
    break;
  }
}
$twitter_username = '';
if ($twitter_link) {
  // Extract username from URL / URL'den kullanıcı adını çıkar
  preg_match('/twitter\.com\/([^\/]+)/', $twitter_link['url'], $matches);
  if (isset($matches[1])) {
    $twitter_username = $matches[1];
  }
}

// Find Google Analytics ID / Google Analytics ID'sini bul
$googletag_id = $data['analytics']['url'] ?? '';

/**
 * Generates language alternatives for SEO
 * SEO için dil alternatiflerini oluşturur
 * 
 * @param string $domain Site domain / Site domaini
 * @param array $languages Language list / Dil listesi
 */
function generateLanguageAlternatives($domain, $languages)
{
  foreach ($languages as $code => $name) {
    echo '<link rel="alternate" hreflang="' . htmlspecialchars($code) . '" href="' .
      htmlspecialchars($domain) . '/?lang=' . htmlspecialchars($code) . '" />' . PHP_EOL;
  }
}
?>
<!DOCTYPE html>
<html data-theme="<?php echo htmlspecialchars($theme); ?>" lang="<?php echo htmlspecialchars($current_lang); ?>">

<head>
  <!-- Basic meta tags / Temel meta etiketleri -->
  <title><?php echo htmlspecialchars(translate('title')); ?></title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="A. Kerem Gök - Software Developer" />
  <meta name="keywords" content="kerem gök, software developer, web developer, javascript, php" />
  <meta name="author" content="A. Kerem Gök" />

  <!-- Open Graph / Facebook meta tags -->
  <meta property="og:type" content="website" />
  <meta property="og:url" content="<?php echo $domain; ?>/" />
  <meta property="og:title" content="A. Kerem Gök" />
  <meta property="og:description" content="A. Kerem Gök - Software Developer" />

  <!-- Twitter meta tags -->
  <meta name="twitter:card" content="summary" />
  <meta name="twitter:url" content="<?php echo $domain; ?>/" />
  <meta name="twitter:title" content="A. Kerem Gök" />
  <meta name="twitter:description" content="A. Kerem Gök - Software Developer" />
  <?php if ($twitter_username): ?>
    <meta name="twitter:creator" content="@<?php echo htmlspecialchars($twitter_username); ?>" />
  <?php endif; ?>

  <!-- SEO meta tags -->
  <meta name="robots" content="index, follow" />
  <link rel="canonical" href="<?php echo $domain; ?>/" />

  <!-- Language alternatives / Dil alternatifleri -->
  <?php generateLanguageAlternatives($domain, $languages); ?>

  <!-- Styles / Stiller -->
  <link rel="stylesheet" href="style.css">

  <?php if ($googletag_id): ?>
    <!-- Google Analytics tracking code / Google Analytics takip kodu -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo htmlspecialchars($googletag_id); ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];

      function gtag() {
        dataLayer.push(arguments);
      }
      gtag("js", new Date());
      gtag("config", "<?php echo htmlspecialchars($googletag_id); ?>");
    </script>
  <?php endif; ?>
</head>

<body>
  <!-- Theme toggle button / Tema değiştirme butonu -->
  <a href="?<?php
            $params = $_GET;
            $params['theme'] = ($theme === 'dark') ? 'light' : 'dark';
            echo http_build_query($params);
            ?>" id="theme-toggle" style="text-decoration: none;">
    <?php echo $theme === 'dark' ? '☀️' : '🌙'; ?>
  </a>

  <!-- Language selector dropdown / Dil seçici açılır menü -->
  <div class="language-selector">
    <div class="selected-language">
      <span class="lang-text"><?php echo htmlspecialchars($languages[$current_lang]); ?></span>
      <span class="arrow">▼</span>
    </div>
    <div class="language-dropdown">
      <?php foreach ($languages as $code => $name): ?>
        <a href="?<?php
                  $params = $_GET;
                  $params['lang'] = $code;
                  echo http_build_query($params);
                  ?>" class="lang-option">
          <?php echo htmlspecialchars($name); ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Main content / Ana içerik -->
  <h1><?php echo htmlspecialchars(translate('title')); ?></h1>
  <p><?php echo htmlspecialchars(translate('intro')); ?></p>

  <!-- Social media links / Sosyal medya linkleri -->
  <p>
    <?php
    $first = true;
    foreach ($links as $link) {
      if (!$first) echo ' • ';
      echo '<a href="' . htmlspecialchars($link['url']) . '">' .
        htmlspecialchars($link['name']) . '</a>';
      $first = false;
    }
    ?>
  </p>

  <!-- JavaScript for dropdown functionality / Açılır menü için JavaScript -->
  <script src="script.js"></script>
</body>

</html>