/**
 * Main Page JavaScript Functions
 * Ana Sayfa JavaScript Fonksiyonları
 *
 * This file contains client-side functionality for the main page.
 * Bu dosya, ana sayfanın istemci tarafı işlevselliğini içerir.
 *
 * Features:
 * - Language selector dropdown
 * - Click outside handling
 * - Arrow rotation animation
 *
 * Özellikler:
 * - Dil seçici açılır menü
 * - Dışarı tıklama kontrolü
 * - Ok rotasyon animasyonu
 *
 * @author A. Kerem Gök
 * @version 1.0
 */

// Language selector dropdown toggle functionality
// Dil seçici açılır menü geçiş işlevselliği
document
  .querySelector(".selected-language")
  .addEventListener("click", function () {
    // Toggle dropdown visibility / Açılır menü görünürlüğünü değiştir
    document.querySelector(".language-dropdown").classList.toggle("show");

    // Rotate arrow based on dropdown state / Açılır menü durumuna göre oku döndür
    document.querySelector(".arrow").style.transform = document
      .querySelector(".language-dropdown")
      .classList.contains("show")
      ? "rotate(180deg)" // When open / Açıkken
      : "rotate(0)"; // When closed / Kapalıyken
  });

// Close dropdown when clicking outside
// Dışarı tıklandığında açılır menüyü kapat
document.addEventListener("click", function (e) {
  // Check if click is outside the language selector
  // Tıklamanın dil seçici dışında olup olmadığını kontrol et
  if (!e.target.closest(".language-selector")) {
    // Hide dropdown / Açılır menüyü gizle
    document.querySelector(".language-dropdown").classList.remove("show");
    // Reset arrow rotation / Ok rotasyonunu sıfırla
    document.querySelector(".arrow").style.transform = "rotate(0)";
  }
});
