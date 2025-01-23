/**
 * Blog JavaScript Fonksiyonları
 * @author A. Kerem Gök
 */

document.addEventListener('DOMContentLoaded', () => {
    // Lazy loading için görsel yükleme
    const images = document.querySelectorAll('.blog-post img[data-src]');
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    observer.unobserve(img);
                }
            });
        });

        images.forEach(img => imageObserver.observe(img));
    }

    // Blog yazılarını filtreleme
    const searchInput = document.querySelector('.blog-search');
    if (searchInput) {
        searchInput.addEventListener('input', filterPosts);
    }

    // "Devamını Oku" butonları için smooth scroll
    const readMoreLinks = document.querySelectorAll('.read-more');
    readMoreLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            if (link.getAttribute('href').startsWith('#')) {
                e.preventDefault();
                const targetId = link.getAttribute('href').slice(1);
                const targetElement = document.getElementById(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    });
});

/**
 * Blog yazılarını filtrele
 * @param {Event} e - Input event
 */
function filterPosts(e) {
    const searchTerm = e.target.value.toLowerCase();
    const posts = document.querySelectorAll('.blog-post');
    
    posts.forEach(post => {
        const title = post.querySelector('h2').textContent.toLowerCase();
        const content = post.querySelector('p').textContent.toLowerCase();
        
        if (title.includes(searchTerm) || content.includes(searchTerm)) {
            post.style.display = '';
        } else {
            post.style.display = 'none';
        }
    });
}

/**
 * Blog yazısını beğen
 * @param {number} postId - Blog yazısı ID
 */
async function likePost(postId) {
    try {
        const response = await fetch('like-post.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ postId })
        });

        if (!response.ok) throw new Error('Beğeni işlemi başarısız');

        const data = await response.json();
        const likeButton = document.querySelector(`#post-${postId} .like-button`);
        const likeCount = document.querySelector(`#post-${postId} .like-count`);
        
        if (likeButton && likeCount) {
            likeButton.classList.toggle('liked');
            likeCount.textContent = data.likes;
        }
    } catch (error) {
        console.error('Hata:', error);
    }
} 