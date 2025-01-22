/**
 * Admin Panel JavaScript Functions
 * Admin Panel JavaScript Fonksiyonları
 * 
 * This file contains all the JavaScript functionality for the admin panel.
 * Bu dosya admin panelinin tüm JavaScript işlevselliğini içerir.
 * 
 * Features:
 * - CRUD operations for links
 * - Modal dialog management
 * - Dynamic content loading
 * - API communication
 * 
 * Özellikler:
 * - Linkler için CRUD işlemleri
 * - Modal pencere yönetimi
 * - Dinamik içerik yükleme
 * - API iletişimi
 * 
 * @author A. Kerem Gök
 * @version 1.0
 */

/**
 * Loads all links from the server and displays them
 * Sunucudan tüm linkleri yükler ve görüntüler
 */
function loadLinks() {
  fetch("data.json")
    .then((response) => response.json())
    .then((data) => {
      const linkList = document.getElementById("linkList");
      linkList.innerHTML = "<h2>Mevcut Linkler</h2>";

      Object.entries(data.links).forEach(([id, link]) => {
        const item = document.createElement("div");
        item.className = "link-item";
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

/**
 * Adds a new link to the system
 * Sisteme yeni bir link ekler
 */
function addLink() {
  const name = document.getElementById("linkName").value;
  const url = document.getElementById("linkUrl").value;

  if (!name || !url) {
    alert("Lütfen hem isim hem de URL giriniz!");
    return;
  }

  sendRequest("add", null, name, url);
  document.getElementById("linkName").value = "";
  document.getElementById("linkUrl").value = "";
}

/**
 * Shows the edit modal with pre-filled data
 * Düzenleme modalını önceden doldurulmuş verilerle gösterir
 * 
 * @param {string} id - Link ID / Link ID'si
 * @param {string} name - Link name / Link adı
 * @param {string} url - Link URL / Link URL'si
 */
function showEditModal(id, name, url) {
  document.getElementById("editId").value = id;
  document.getElementById("editName").value = name;
  document.getElementById("editUrl").value = url;
  document.getElementById("editModal").style.display = "block";
}

/**
 * Closes the edit modal
 * Düzenleme modalını kapatır
 */
function closeModal() {
  document.getElementById("editModal").style.display = "none";
}

/**
 * Updates an existing link
 * Mevcut bir linki günceller
 */
function updateLink() {
  const id = document.getElementById("editId").value;
  const name = document.getElementById("editName").value;
  const url = document.getElementById("editUrl").value;

  if (!name || !url) {
    alert("Lütfen hem isim hem de URL giriniz!");
    return;
  }

  sendRequest("update", id, name, url);
  closeModal();
}

/**
 * Deletes a link after confirmation
 * Onaydan sonra bir linki siler
 * 
 * @param {string} id - Link ID to delete / Silinecek link ID'si
 */
function deleteLink(id) {
  if (confirm("Bu linki silmek istediğinizden emin misiniz?")) {
    sendRequest("delete", id);
  }
}

/**
 * Sends an API request to the server
 * Sunucuya API isteği gönderir
 * 
 * @param {string} action - Action type (add/update/delete) / İşlem türü (ekle/güncelle/sil)
 * @param {string|null} id - Link ID (optional) / Link ID'si (opsiyonel)
 * @param {string|null} name - Link name (optional) / Link adı (opsiyonel)
 * @param {string|null} url - Link URL (optional) / Link URL'si (opsiyonel)
 */
function sendRequest(action, id = null, name = null, url = null) {
  const data = {
    action,
  };
  if (id) data.id = id;
  if (name) data.name = name;
  if (url) data.url = url;

  fetch("admin.php?api=1", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        loadLinks();
      }
    });
}

// Initialize page / Sayfa başlangıcı
document.addEventListener("DOMContentLoaded", loadLinks);

// Close modal when clicking outside / Modal dışına tıklandığında kapat
window.onclick = function (event) {
  const modal = document.getElementById("editModal");
  if (event.target == modal) {
    closeModal();
  }
};
