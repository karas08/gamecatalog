<?php 
// Підтягуємо змінну $users_online, якщо раптом файл не підключився раніше
if (!isset($users_online)) {
    include_once "track_online.php";
}
?>

<footer class="footer mt-auto py-3 text-center text-white-50 small">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
        <span>&copy; <?= date('Y') ?> GameCat. Усі права захищено.</span>
        <span class="badge bg-success d-flex align-items-center gap-1 p-2" style="font-size: 0.85rem;">
            <span class="spinner-grow spinner-grow-sm text-light" role="status" style="width: 8px; height: 8px;"></span>
            Зараз на сайті: <strong class="text-white ms-1"><?= $users_online ?></strong>
        </span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

<script>
// Логіка перемикання теми "День / Ніч"
function applyTheme(theme) {
    const root = document.documentElement;
    const btn = document.getElementById('themeToggleBtn');
    const searchBox = document.getElementById('liveSearchResults');
    
    if (theme === 'light') {
        root.style.setProperty('--site-bg', '#fafafa');
        root.style.setProperty('--site-text', '#121212');
        root.style.setProperty('--card-glass', 'rgba(0, 0, 0, 0.03)');
        root.style.setProperty('--card-border', 'rgba(0, 0, 0, 0.08)');
        if(btn) btn.innerHTML = "☀️";
        if(searchBox) { searchBox.style.background = "#ffffff"; searchBox.style.border = "1px solid #ccc"; }
        
        // Коригуємо пошукове вікно для СВІТЛОЇ теми
        document.querySelectorAll('.search-title').forEach(el => el.style.setProperty('color', '#121212', 'important'));
        document.querySelectorAll('.search-muted').forEach(el => el.style.setProperty('color', '#6c757d', 'important'));
        document.querySelectorAll('.search-no-results').forEach(el => el.style.setProperty('color', '#212529', 'important'));
        
        document.querySelectorAll('.text-white-50').forEach(el => el.classList.replace('text-white-50', 'text-muted'));
    } else {
        root.style.setProperty('--site-bg', 'linear-gradient(to right, #24243e, #302b63, #0f0c29)');
        root.style.setProperty('--site-text', '#ffffff');
        root.style.setProperty('--card-glass', 'rgba(255, 255, 255, 0.05)');
        root.style.setProperty('--card-border', 'rgba(255, 255, 255, 0.1)');
        if(btn) btn.innerHTML = "🌙";
        if(searchBox) { searchBox.style.background = "#1e1b3a"; searchBox.style.border = "1px solid rgba(255,255,255,0.1)"; }
        
        // Коригуємо пошукове вікно для ТЕМНОЇ теми
        document.querySelectorAll('.search-title').forEach(el => el.style.setProperty('color', '#ffffff', 'important'));
        document.querySelectorAll('.search-muted').forEach(el => el.style.setProperty('color', 'rgba(255, 255, 255, 0.65)', 'important'));
        document.querySelectorAll('.search-no-results').forEach(el => el.style.setProperty('color', 'rgba(255, 255, 255, 0.8)', 'important'));
        
        document.querySelectorAll('.text-muted').forEach(el => el.classList.replace('text-muted', 'text-white-50'));
    }
}

function toggleTheme() {
    let currentTheme = localStorage.getItem('site-theme') === 'light' ? 'dark' : 'light';
    localStorage.setItem('site-theme', currentTheme);
    applyTheme(currentTheme);
}

document.addEventListener("DOMContentLoaded", function() {
    let savedTheme = localStorage.getItem('site-theme') || 'dark';
    applyTheme(savedTheme);

    // ==========================================
    // ФІЧА 3: СКРИПТ ЖИВОГО ПОШУКУ (LIVE SEARCH)
    // ==========================================
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('liveSearchResults');

    if (searchInput && searchResults) {
        searchInput.addEventListener('input', function() {
            let query = this.value.trim();
            if (query.length > 1) { 
                fetch('search_ajax.php?query=' + encodeURIComponent(query))
                    .then(response => response.text())
                    .then(data => {
                        searchResults.innerHTML = data;
                        searchResults.classList.remove('d-none');
                        
                        // Динамічно оновлюємо кольори щойно завантаженого контенту під поточну тему
                        applyTheme(localStorage.getItem('site-theme') || 'dark');
                    });
            } else {
                searchResults.classList.add('d-none');
            }
        });

        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.add('d-none');
            }
        });
    }

    // ==========================================
    // ФІЧА 1: ОБРОБКА СТАТУСІВ + СУПЕР КОНФЕТІ 🎉
    // ==========================================
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const isDark = (savedTheme === 'dark');

    const swalConfig = {
        background: isDark ? '#1e1b3a' : '#ffffff',
        color: isDark ? '#ffffff' : '#121212',
        confirmButtonColor: '#00d2ff',
        timer: 2500,
        timerProgressBar: true
    };

    if (status) {
        switch(status) {
            case 'fav_added':
                confetti({ particleCount: 100, spread: 70, origin: { y: 0.6 } });
                Swal.fire({ ...swalConfig, title: 'Успішно!', text: 'Гру додано в обране! ❤️', icon: 'success' });
                break;
            case 'fav_removed':
                Swal.fire({ ...swalConfig, title: 'Видалено', text: 'Гру прибрано з вашого обраного.', icon: 'info' });
                break;
            case 'rate_success':
                Swal.fire({ ...swalConfig, title: 'Дякуємо!', text: 'Вашу оцінку успішно враховано! ⭐', icon: 'success' });
                break;
            case 'added':
                Swal.fire({ ...swalConfig, title: 'Додано!', text: 'Нова гра успішно з’явилася в каталозі! 🎮', icon: 'success' });
                break;
            case 'updated':
                Swal.fire({ ...swalConfig, title: 'Оновлено!', text: 'Дані гри успішно змінено! 📝', icon: 'success' });
                break;
        }
        window.history.replaceState({}, document.title, window.location.pathname + window.location.search.replace(/[?&]status=[^&]+/g, ''));
    }
});
</script>
</body>
</html>