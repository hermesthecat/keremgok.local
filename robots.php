<?php
header("Content-Type: text/plain");

include 'config.php';

echo "User-agent: *\n";

echo "Allow: /\n";
echo "Allow: /sitemap.xml\n";

echo "Disallow: /admin.php\n";
echo "Disallow: /config.php\n";
echo "Disallow: /config.sample.php\n";
echo "Disallow: /bash/\n";

echo "Sitemap: " . $domain . "/sitemap.xml\n";
