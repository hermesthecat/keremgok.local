<?php
$domain = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
?>
<!DOCTYPE html>
<html data-theme="dark" lang="en">

<head>
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
  <meta property="og:url" content="" />
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
  <meta name="twitter:creator" content="@abdullahazad" />

  <!-- Robots -->
  <meta name="robots" content="index, follow" />

  <!-- Language Alternatives -->
  <link
    rel="alternate"
    hreflang="tr"
    href="<?php echo $domain; ?>/?lang=tr" />
  <link
    rel="alternate"
    hreflang="en"
    href="<?php echo $domain; ?>/?lang=en" />
  <link rel="canonical" href="<?php echo $domain; ?>/" />

  <style>
    :root[data-theme="light"] {
      --bg-color: #ffffff;
      --text-color: #000000;
    }

    :root[data-theme="dark"] {
      --bg-color: #1a1a1a;
      --text-color: #ffffff;
    }

    html {
      color-scheme: light dark;
    }

    body {
      width: 35em;
      margin: 0 auto;
      font-family: Tahoma, Verdana, Arial, sans-serif;
      background-color: var(--bg-color);
      color: var(--text-color);
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    #theme-toggle,
    #lang-toggle {
      position: fixed;
      padding: 8px 16px;
      border: none;
      border-radius: 4px;
      background-color: var(--text-color);
      color: var(--bg-color);
      cursor: pointer;
      transition: all 0.3s ease;
    }

    #theme-toggle {
      top: 20px;
      right: 20px;
    }

    #lang-toggle {
      top: 20px;
      right: 90px;
    }

    #theme-toggle:hover,
    #lang-toggle:hover {
      opacity: 0.8;
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
    src="https://www.googletagmanager.com/gtag/js?id=G-46GJVXYD2G"></script>
  <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
      dataLayer.push(arguments);
    }
    gtag("js", new Date());

    gtag("config", "G-46GJVXYD2G");
  </script>
</head>

<body>
  <button id="theme-toggle">🌙</button>
  <button id="lang-toggle">TR</button>
  <button id="lang-toggle">EN</button>
  <button id="lang-toggle">FR</button>
  <button id="lang-toggle">ES</button>
  <button id="lang-toggle">DE</button>
  <button id="lang-toggle">IT</button>
  <h1 data-lang-key="title">a. kerem gök..</h1>
  <p data-lang-key="intro">
    If you are seeing this page, it means that approximately 10 seconds of
    your life have been wasted.
  </p>

  <p>
    <a href="https://twitter.com/abdullahazad">twitter</a> •
    <a href="https://github.com/hermesthecat">github</a>
  </p>

  <script>
    // Dil verileri
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

    // Cookie işlemleri için yardımcı fonksiyonlar
    const setCookie = (name, value, days = 365) => {
      const date = new Date();
      date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
      document.cookie = `${name}=${value};expires=${date.toUTCString()};path=/`;
    };

    const getCookie = (name) => {
      const value = `; ${document.cookie}`;
      const parts = value.split(`; ${name}=`);
      if (parts.length === 2) return parts.pop().split(";").shift();
      return null;
    };

    // Dil değiştirme fonksiyonu
    const setLanguage = (lang) => {
      document.documentElement.lang = lang;
      document.querySelectorAll("[data-lang-key]").forEach((element) => {
        const key = element.getAttribute("data-lang-key");
        element.textContent = translations[lang][key];
        if (element.tagName.toLowerCase() === "title") {
          document.title = translations[lang][key];
        }
      });
      document.getElementById("lang-toggle").textContent =
        translations[lang].langButton;
      setCookie("lang", lang);
    };

    // Başlangıç dilini belirle
    const getInitialLang = () => {
      // 1. URL'den dil parametresini kontrol et
      const urlParams = new URLSearchParams(window.location.search);
      const urlLang = urlParams.get("lang");
      if (urlLang && ["tr", "en", "fr", "es", "de", "it"].includes(urlLang)) return urlLang;

      // 2. Cookie'den kontrol et
      const cookieLang = getCookie("lang");
      if (cookieLang) return cookieLang;

      // 3. Tarayıcı dilini kontrol et
      const browserLang = navigator.language.split("-")[0];
      return ["tr", "en", "fr", "es", "de", "it"].includes(browserLang) ? browserLang : "en";
    };

    // Dil değiştirme butonu
    const langToggle = document.getElementById("lang-toggle");
    langToggle.addEventListener("click", () => {
      const currentLang = document.documentElement.lang;
      const newLang = currentLang === "tr" ? "en" :
        currentLang === "en" ? "fr" :
        currentLang === "fr" ? "es" :
        currentLang === "es" ? "de" :
        currentLang === "de" ? "it" : "tr";
      // URL'yi güncelle
      const url = new URL(window.location);
      url.searchParams.set("lang", newLang);
      window.history.pushState({}, "", url);
      setLanguage(newLang);
    });

    // Theme toggle functionality
    const themeToggle = document.getElementById("theme-toggle");
    const html = document.documentElement;

    const getInitialTheme = () => {
      // 1. Önce cookie'yi kontrol et
      const cookieTheme = getCookie("theme");
      if (cookieTheme) return cookieTheme;

      // 2. Cookie yoksa tarayıcı tercihine bak
      if (window.matchMedia("(prefers-color-scheme: light)").matches) {
        return "light";
      }

      // 3. Varsayılan olarak dark kullan (HTML ile uyumlu)
      return "dark";
    };

    // Başlangıç temasını ayarla
    const initialTheme = getInitialTheme();
    html.dataset.theme = initialTheme;
    themeToggle.textContent = initialTheme === "dark" ? "☀️" : "🌙";

    themeToggle.addEventListener("click", () => {
      const currentTheme = html.dataset.theme;
      const newTheme = currentTheme === "dark" ? "light" : "dark";

      html.dataset.theme = newTheme;
      setCookie("theme", newTheme);
      themeToggle.textContent = newTheme === "dark" ? "☀️" : "🌙";
    });

    // Başlangıç ayarları
    const initialLang = getInitialLang();
    setLanguage(initialLang);
  </script>
</body>

</html>