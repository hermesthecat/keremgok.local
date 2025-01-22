<?php
// Create dynamic domain URL with HTTP/HTTPS check
// Dinamik domain URL'si oluştur (HTTP/HTTPS kontrolü ile)
$domain = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

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

?>
<!DOCTYPE html>
<html data-theme="dark" lang="en">

<head>
  <!-- Basic meta tags / Temel meta etiketleri -->
  <title data-lang-key="title">a. kerem gok..</title>
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
  <link
    rel="alternate"
    hreflang="tr"
    href="<?php echo $domain; ?>/?lang=tr" />
  <link
    rel="alternate"
    hreflang="en"
    href="<?php echo $domain; ?>/?lang=en" />
  <link
    rel="alternate"
    hreflang="ru"
    href="<?php echo $domain; ?>/?lang=ru" />
  <link
    rel="alternate"
    hreflang="ja"
    href="<?php echo $domain; ?>/?lang=ja" />
  <link
    rel="alternate"
    hreflang="zh"
    href="<?php echo $domain; ?>/?lang=zh" />
  <link
    rel="alternate"
    hreflang="ko"
    href="<?php echo $domain; ?>/?lang=ko" />
  <link
    rel="alternate"
    hreflang="de"
    href="<?php echo $domain; ?>/?lang=de" />
  <link
    rel="alternate"
    hreflang="es"
    href="<?php echo $domain; ?>/?lang=es" />
  <link
    rel="alternate"
    hreflang="fr"
    href="<?php echo $domain; ?>/?lang=fr" />
  <link
    rel="alternate"
    hreflang="it"
    href="<?php echo $domain; ?>/?lang=it" />

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

    /* Theme toggle button style / Tema değiştirme butonu stili */
    #theme-toggle {
      position: fixed;
      padding: 8px 16px;
      border: none;
      border-radius: 4px;
      background-color: var(--text-color);
      color: var(--bg-color);
      cursor: pointer;
      transition: all 0.3s ease;
      top: 20px;
      right: 20px;
    }

    #theme-toggle:hover {
      opacity: 0.8;
    }

    /* Language selector component styles / Dil seçici bileşeni stilleri */
    .language-selector {
      position: fixed;
      top: 20px;
      right: 90px;
      z-index: 1000;
    }

    .selected-language {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 8px 16px;
      background-color: var(--text-color);
      color: var(--bg-color);
      border-radius: 4px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .selected-language:hover {
      opacity: 0.8;
    }

    .arrow {
      font-size: 10px;
      transition: transform 0.3s ease;
    }

    .language-dropdown {
      position: absolute;
      top: 100%;
      right: 0;
      margin-top: 4px;
      background-color: var(--text-color);
      border-radius: 4px;
      overflow: hidden;
      display: none;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .language-dropdown.show {
      display: block;
    }

    .lang-option {
      padding: 8px 16px;
      color: var(--bg-color);
      cursor: pointer;
      transition: background-color 0.3s ease;
      white-space: nowrap;
    }

    .lang-option:hover {
      background-color: rgba(255, 255, 255, 0.1);
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
  <!-- Theme toggle button / Tema değiştirme butonu -->
  <button id="theme-toggle">🌙</button>

  <!-- Language selector dropdown menu / Dil seçici dropdown menü -->
  <div class="language-selector">
    <div class="selected-language" id="selected-language">
      <span class="lang-text">EN</span>
      <span class="arrow">▼</span>
    </div>
    <div class="language-dropdown" id="language-dropdown">
      <div class="lang-option" data-lang="tr">Türkçe</div>
      <div class="lang-option" data-lang="en">English</div>
      <div class="lang-option" data-lang="ru">Русский</div>
      <div class="lang-option" data-lang="ja">日本語</div>
      <div class="lang-option" data-lang="zh">中文</div>
      <div class="lang-option" data-lang="ko">한국어</div>
      <div class="lang-option" data-lang="de">Deutsch</div>
      <div class="lang-option" data-lang="es">Español</div>
      <div class="lang-option" data-lang="fr">Français</div>
      <div class="lang-option" data-lang="it">Italiano</div>
    </div>
  </div>
  <!-- Main content / Ana içerik -->
  <h1 data-lang-key="title">a. kerem gök..</h1>
  <p data-lang-key="intro">
    If you are seeing this page, it means that approximately 10 seconds of
    your life have been wasted.
  </p>

  <!-- Social media links / Sosyal medya linkleri -->
  <p>
    <?php
    $first = true;
    foreach ($links as $link) {
      if (!$first) {
        echo ' • ';
      }
      echo '<a href="' . htmlspecialchars($link['url']) . '">' . htmlspecialchars($link['name']) . '</a>';
      $first = false;
    }
    ?>
  </p>

  <script>
    // Translations for multilingual support / Çoklu dil desteği için çeviriler
    const translations = {
      tr: {
        title: "a. kerem gök..",
        intro: "Bu sayfayı görüyorsanız, hayatınızdan yaklaşık 10 saniye boşa harcandı demektir.",
        langButton: "EN"
      },
      en: {
        title: "a. kerem gok..",
        intro: "If you are seeing this page, it means that approximately 10 seconds of your life have been wasted.",
        langButton: "TR"
      },
      ru: {
        title: "a. kerem gok..",
        intro: "Если вы видите эту страницу, значит примерно 10 секунд вашей жизни потрачены впустую.",
        langButton: "RU"
      },
      ja: {
        title: "a. kerem gok..",
        intro: "このページを見ているということは、あなたの人生の約10秒が無駄になったということです。",
        langButton: "JA"
      },
      zh: {
        title: "a. kerem gok..",
        intro: "如果您看到此页面，这意味着您的生命中大约有10秒被浪费了。",
        langButton: "ZH"
      },
      ko: {
        title: "a. kerem gok..",
        intro: "이 페이지를 보고 계시다면, 귀하의 인생에서 약 10초가 낭비되었다는 의미입니다.",
        langButton: "KO"
      },
      fr: {
        title: "a. kerem gok..",
        intro: "Si vous voyez cette page, cela signifie qu'environ 10 secondes de votre vie ont été gaspillées.",
        langButton: "FR",
      },
      es: {
        title: "a. kerem gok..",
        intro: "Si ves esta página, significa que aproximadamente 10 segundos de tu vida han sido desperdiciados.",
        langButton: "ES",
      },
      de: {
        title: "a. kerem gok..",
        intro: "Wenn Sie diese Seite sehen, bedeutet das, dass etwa 10 Sekunden Ihres Lebens verschwendet wurden.",
        langButton: "DE",
      },
      it: {
        title: "a. kerem gok..",
        intro: "Se stai vedendo questa pagina, significa che circa 10 secondi della tua vita sono stati sprecati.",
        langButton: "IT",
      },
    };

    // Helper functions for cookie management / Cookie yönetimi için yardımcı fonksiyonlar
    function setCookie(name, value, days = 365) {
      const date = new Date();
      date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
      document.cookie = `${name}=${value};expires=${date.toUTCString()};path=/`;
    }

    function getCookie(name) {
      const value = `; ${document.cookie}`;
      const parts = value.split(`; ${name}=`);
      if (parts.length === 2) return parts.pop().split(";").shift();
      return null;
    }

    // Language change operations / Dil değiştirme işlemleri
    function setLanguage(lang) {
      document.documentElement.lang = lang;
      document.querySelectorAll("[data-lang-key]").forEach((element) => {
        const key = element.getAttribute("data-lang-key");
        element.textContent = translations[lang][key];
        if (element.tagName.toLowerCase() === "title") {
          document.title = translations[lang][key];
        }
      });
      selectedLanguage.querySelector('.lang-text').textContent = lang.toUpperCase();
      setCookie("lang", lang);
    }


    // Determine initial language (URL > Cookie > Browser language > Default)
    // Başlangıç dilini belirleme (URL > Cookie > Tarayıcı dili > Varsayılan)
    function getInitialLang() {
      // Check URL parameters / URL'den dil parametresini kontrol et
      const urlParams = new URLSearchParams(window.location.search);
      const urlLang = urlParams.get("lang");
      if (urlLang && ["tr", "en", "ru", "ja", "zh", "ko", "fr", "es", "de", "it"].includes(urlLang)) return urlLang;

      // Check cookies / Cookie'den kontrol et
      const cookieLang = getCookie("lang");
      if (cookieLang) return cookieLang;

      // Check browser language / Tarayıcı dilini kontrol et
      const browserLang = navigator.language.split("-")[0];
      return ["tr", "en", "ru", "ja", "zh", "ko", "fr", "es", "de", "it"].includes(browserLang) ? browserLang : "en";
    }

    // Theme change operations / Tema değiştirme işlemleri
    function getInitialTheme() {
      // First check cookie / Önce cookie'yi kontrol et
      const cookieTheme = getCookie("theme");
      if (cookieTheme) return cookieTheme;

      // If no cookie, check browser preference / Cookie yoksa tarayıcı tercihine bak
      if (window.matchMedia("(prefers-color-scheme: light)").matches) {
        return "light";
      }

      // Use dark as default (compatible with HTML) / Varsayılan olarak dark kullan (HTML ile uyumlu)
      return "dark";
    }

    function setTheme(theme) {
      html.dataset.theme = theme;
      setCookie("theme", theme);
      themeToggle.textContent = theme === "dark" ? "☀️" : "🌙";
    }

    // Language selector dropdown menu operations / Dil seçici dropdown menü işlemleri
    const selectedLanguage = document.getElementById('selected-language');
    const languageDropdown = document.getElementById('language-dropdown');
    const langOptions = document.querySelectorAll('.lang-option');

    // Toggle dropdown menu / Dropdown menüyü aç/kapat
    selectedLanguage.addEventListener('click', () => {
      languageDropdown.classList.toggle('show');
      selectedLanguage.querySelector('.arrow').style.transform =
        languageDropdown.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0)';
    });

    // Close menu when clicking outside / Dışarı tıklandığında menüyü kapat
    document.addEventListener('click', (e) => {
      if (!e.target.closest('.language-selector')) {
        languageDropdown.classList.remove('show');
        selectedLanguage.querySelector('.arrow').style.transform = 'rotate(0)';
      }
    });

    langOptions.forEach(option => {
      option.addEventListener('click', () => {
        const lang = option.dataset.lang;
        setLanguage(lang);
        selectedLanguage.querySelector('.lang-text').textContent = lang.toUpperCase();
        languageDropdown.classList.remove('show');
        selectedLanguage.querySelector('.arrow').style.transform = 'rotate(0)';

        // Update URL / URL'yi güncelle
        const url = new URL(window.location);
        url.searchParams.set("lang", lang);
        window.location.href = url;
      });
    });

    // Theme change operations / Tema değiştirme işlemleri
    const themeToggle = document.getElementById("theme-toggle");
    const html = document.documentElement;


    // Set initial theme / Başlangıç temasını ayarla
    const initialTheme = getInitialTheme();
    html.dataset.theme = initialTheme;
    themeToggle.textContent = initialTheme === "dark" ? "☀️" : "🌙";

    themeToggle.addEventListener("click", () => {
      const currentTheme = html.dataset.theme;
      setTheme(currentTheme === "dark" ? "light" : "dark");
    });

    // Apply initial settings when page loads / Sayfa yüklendiğinde başlangıç ayarlarını uygula
    function applyInitialSettings() {
      const initialLang = getInitialLang();
      setLanguage(initialLang);
      const initialTheme = getInitialTheme();
      setTheme(initialTheme);
    }

    applyInitialSettings();
  </script>
</body>

</html>