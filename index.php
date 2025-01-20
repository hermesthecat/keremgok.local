<?php
// Create dynamic domain URL with HTTP/HTTPS check
// Dinamik domain URL'si oluştur (HTTP/HTTPS kontrolü ile)
$domain = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

// Social media links / Sosyal medya linkleri
$blog_url = "https://blog.keremgok.tr";
$twitter_username = "abdullahazad";
$github_username = "hermesthecat";
$googletag_id = "G-46GJVXYD2G";

?>
<!DOCTYPE html>
<html data-theme="dark" lang="en">

<head>
  <!-- Basic meta tags / Temel meta etiketleri -->
  <title data-lang-key="title">a. kerem gok..</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="A. Kerem Gök - Software Developer" />
  <meta
    name="keywords"
    content="kerem gök, software developer, web developer, javascript, php" />
  <meta name="author" content="A. Kerem Gök" />

  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website" />
  <meta property="og:url" content="<?php echo $domain; ?>/" />
  <meta property="og:title" content="A. Kerem Gök" />
  <meta
    property="og:description"
    content="A. Kerem Gök - Software Developer" />

  <!-- Twitter -->
  <meta name="twitter:card" content="summary" />
  <meta name="twitter:url" content="<?php echo $domain; ?>/" />
  <meta name="twitter:title" content="A. Kerem Gök" />
  <meta
    name="twitter:description"
    content="A. Kerem Gök - Software Developer" />
  <meta name="twitter:creator" content="@<?php echo $twitter_username; ?>" />

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

  <!-- Google tag (gtag.js) -->
  <script
    async
    src="https://www.googletagmanager.com/gtag/js?id=<?php echo $googletag_id; ?>"></script>
  <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
      dataLayer.push(arguments);
    }
    gtag("js", new Date());

    gtag("config", "<?php echo $googletag_id; ?>");
  </script>

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
      <div class="lang-option" data-lang="fr">Français</div>
      <div class="lang-option" data-lang="es">Español</div>
      <div class="lang-option" data-lang="de">Deutsch</div>
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
    <a href="<?php echo $blog_url; ?>">blog</a> •
    <a href="https://twitter.com/<?php echo $twitter_username; ?>">twitter</a> •
    <a href="https://github.com/<?php echo $github_username; ?>">github</a>
  </p>

  <script>
    // Translations for multilingual support / Çoklu dil desteği için çeviriler
    const translations = {
      tr: {
        title: "a. kerem gök..",
        intro: "Bu sayfayı görüyorsanız, hayatınızdan yaklaşık 10 saniye boşa harcandı demektir.",
        langButton: "EN",
      },
      en: {
        title: "a. kerem gok..",
        intro: "If you are seeing this page, it means that approximately 10 seconds of your life have been wasted.",
        langButton: "TR",
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
      if (urlLang && ["tr", "en", "fr", "es", "de", "it"].includes(urlLang)) return urlLang;

      // Check cookies / Cookie'den kontrol et
      const cookieLang = getCookie("lang");
      if (cookieLang) return cookieLang;

      // Check browser language / Tarayıcı dilini kontrol et
      const browserLang = navigator.language.split("-")[0];
      return ["tr", "en", "fr", "es", "de", "it"].includes(browserLang) ? browserLang : "en";
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