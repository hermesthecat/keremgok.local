<?php
session_start(); // Dil seçimini saklamak için session kullanacağız

// Create dynamic domain URL with HTTP/HTTPS check
// Dinamik domain URL'si oluştur (HTTP/HTTPS kontrolü ile)
$domain = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

// Dil yönetimi için fonksiyonlar
function loadTranslations()
{
  $lang_file = 'lang.json';
  if (file_exists($lang_file)) {
    return json_decode(file_get_contents($lang_file), true);
  }
  return [];
}

function getCurrentLang()
{
  $translations = loadTranslations();
  $supported_languages = array_keys($translations);

  // 1. URL'den dil kontrolü
  if (isset($_GET['lang']) && in_array($_GET['lang'], $supported_languages)) {
    $_SESSION['lang'] = $_GET['lang'];
    return $_GET['lang'];
  }

  // 2. Session'dan dil kontrolü
  if (isset($_SESSION['lang']) && in_array($_SESSION['lang'], $supported_languages)) {
    return $_SESSION['lang'];
  }

  // 3. Tarayıcı dilini kontrol et
  $browser_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en', 0, 2);
  if (in_array($browser_lang, $supported_languages)) {
    $_SESSION['lang'] = $browser_lang;
    return $browser_lang;
  }

  // 4. Varsayılan dil
  return 'en';
}

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

// Tema yönetimi
$theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'dark';
if (isset($_GET['theme']) && in_array($_GET['theme'], ['light', 'dark'])) {
  $theme = $_GET['theme'];
  setcookie('theme', $theme, time() + (86400 * 365), "/");
}

// Mevcut dili al
$current_lang = getCurrentLang();
$translations = loadTranslations();
$languages = [];
foreach ($translations as $code => $trans) {
  $languages[$code] = $trans['langText'] ?? $code;
}

// Read data from data.json
$json_file = 'data.json';
$data = [];
if (file_exists($json_file)) {
  $data = json_decode(file_get_contents($json_file), true);
}

// Get values with defaults
function getValue($section, $key, $default = '')
{
  global $data;
  return $data[$section][$key] ?? $default;
}

// Social media links / Sosyal medya linkleri
$links = $data['links'] ?? [];

// Twitter username'i bul
$twitter_link = null;
foreach ($links as $link) {
  if (strtolower($link['name']) === 'twitter') {
    $twitter_link = $link;
    break;
  }
}
$twitter_username = '';
if ($twitter_link) {
  // URL'den kullanıcı adını çıkar
  preg_match('/twitter\.com\/([^\/]+)/', $twitter_link['url'], $matches);
  if (isset($matches[1])) {
    $twitter_username = $matches[1];
  }
}

// Google Analytics ID'sini bul
$googletag_id = '';
foreach ($links as $link) {
  if (strtolower($link['name']) === 'analytics') {
    $googletag_id = $link['url'];
    break;
  }
}

// Language Alternatives bölümünü dinamik olarak oluştur
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

  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website" />
  <meta property="og:url" content="<?php echo $domain; ?>/" />
  <meta property="og:title" content="A. Kerem Gök" />
  <meta property="og:description" content="A. Kerem Gök - Software Developer" />

  <!-- Twitter -->
  <meta name="twitter:card" content="summary" />
  <meta name="twitter:url" content="<?php echo $domain; ?>/" />
  <meta name="twitter:title" content="A. Kerem Gök" />
  <meta name="twitter:description" content="A. Kerem Gök - Software Developer" />
  <?php if ($twitter_username): ?>
    <meta name="twitter:creator" content="@<?php echo htmlspecialchars($twitter_username); ?>" />
  <?php endif; ?>

  <!-- Robots -->
  <meta name="robots" content="index, follow" />

  <link rel="canonical" href="<?php echo $domain; ?>/" />

  <!-- Language Alternatives / Dil Alternatifleri -->
  <?php generateLanguageAlternatives($domain, $languages); ?>

  <style>
    /* Theme color variables / Tema renk değişkenleri */
    :root[data-theme="light"] {
      --bg-color: #ffffff;
      --text-color: #000000;
    }

    :root[data-theme="dark"] {
      --bg-color: #1a1a1a;
      --text-color: #ffffff;
    }

    /* Basic style definitions / Temel stil tanımlamaları */
    html {
      color-scheme: light dark;
    }

    /* Main content container style / Ana içerik container stili */
    body {
      width: 35em;
      margin: 0 auto;
      font-family: Tahoma, Verdana, Arial, sans-serif;
      background-color: var(--bg-color);
      color: var(--text-color);
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    /* Ortak kontrol stilleri */
    .top-control {
      height: 35px;
      padding: 0 12px;
      border: 1px solid var(--text-color);
      border-radius: 4px;
      background-color: var(--bg-color);
      color: var(--text-color);
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      font-size: 14px;
      display: flex;
      align-items: center;
    }

    .top-control:hover {
      background-color: var(--text-color);
      color: var(--bg-color);
      transform: translateY(-1px);
    }

    /* Ortak buton özellikleri */
    .top-button {
      height: 35px;
      border: 1px solid var(--text-color);
      border-radius: 4px;
      background-color: var(--bg-color);
      color: var(--text-color);
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      font-size: 14px;
      display: flex;
      align-items: center;
      position: fixed;
      top: 20px;
      box-sizing: border-box;
    }

    .top-button:hover {
      background-color: var(--text-color);
      color: var(--bg-color);
      transform: translateY(-1px);
    }

    /* Language selector component styles */
    .language-selector {
      height: 35px;
      border: 1px solid var(--text-color);
      border-radius: 4px;
      background-color: var(--bg-color);
      color: var(--text-color);
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      font-size: 14px;
      display: flex;
      align-items: center;
      position: fixed;
      top: 20px;
      right: 70px;
      z-index: 1000;
      width: 120px;
      box-sizing: border-box;
    }

    .language-selector:hover {
      background-color: var(--text-color);
      color: var(--bg-color);
      transform: translateY(-1px);
    }

    .selected-language {
      padding: 0 12px;
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
    }

    .language-dropdown {
      position: absolute;
      top: 100%;
      right: 0;
      margin-top: 4px;
      background-color: var(--bg-color);
      border: 1px solid var(--text-color);
      border-radius: 4px;
      overflow: hidden;
      display: none;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      width: 100%;
      box-sizing: border-box;
      opacity: 0;
      transform: translateY(-10px);
      transition: opacity 0.2s ease, transform 0.2s ease;
    }

    .language-dropdown.show {
      display: block;
      opacity: 1;
      transform: translateY(0);
    }

    .arrow {
      font-size: 8px;
      transition: transform 0.3s ease;
      display: inline-block;
    }

    .lang-option {
      display: block;
      padding: 10px 16px;
      color: var(--text-color);
      cursor: pointer;
      transition: all 0.2s ease;
      white-space: nowrap;
      text-decoration: none;
      border-bottom: 1px solid var(--text-color);
    }

    .lang-option:hover {
      background-color: var(--text-color);
      color: var(--bg-color);
      padding-left: 20px;
    }

    /* Tema değiştirme butonu */
    #theme-toggle {
      height: 35px;
      border: 1px solid var(--text-color);
      border-radius: 4px;
      background-color: var(--bg-color);
      color: var(--text-color);
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      font-size: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      position: fixed;
      top: 20px;
      right: 20px;
      text-decoration: none;
      width: 35px;
      padding: 0;
      box-sizing: border-box;
    }

    #theme-toggle:hover {
      background-color: var(--text-color);
      color: var(--bg-color);
      transform: translateY(-1px);
    }

    .lang-text {
      font-size: 14px;
    }

    a {
      color: #0066cc;
    }

    :root[data-theme="dark"] a {
      color: #66b3ff;
    }
  </style>

  <?php if ($googletag_id): ?>
    <!-- Google tag (gtag.js) -->
    <script
      async
      src="https://www.googletagmanager.com/gtag/js?id=<?php echo htmlspecialchars($googletag_id); ?>"></script>
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
  <!-- Tema değiştirme butonu -->
  <a href="?<?php
            $params = $_GET;
            $params['theme'] = ($theme === 'dark') ? 'light' : 'dark';
            echo http_build_query($params);
            ?>" id="theme-toggle" style="text-decoration: none;">
    <?php echo $theme === 'dark' ? '☀️' : '🌙'; ?>
  </a>

  <!-- Dil seçici dropdown menü -->
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

  <!-- Minimal JavaScript - sadece dropdown için -->
  <script>
    // Dil seçici dropdown menüsü için basit toggle
    document.querySelector('.selected-language').addEventListener('click', function() {
      document.querySelector('.language-dropdown').classList.toggle('show');
      document.querySelector('.arrow').style.transform =
        document.querySelector('.language-dropdown').classList.contains('show') ?
        'rotate(180deg)' : 'rotate(0)';
    });

    // Dışarı tıklandığında menüyü kapat
    document.addEventListener('click', function(e) {
      if (!e.target.closest('.language-selector')) {
        document.querySelector('.language-dropdown').classList.remove('show');
        document.querySelector('.arrow').style.transform = 'rotate(0)';
      }
    });
  </script>
</body>

</html>