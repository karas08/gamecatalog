<?php
require_once "include/config.php";

if (isset($_GET['query']) && !empty(trim($_GET['query']))) {
    $search = mysqli_real_escape_string($conn, trim($_GET['query']));
    
    // Шукаємо перші 5 збігів
    $query = "SELECT id, title, image, genre FROM games WHERE title LIKE '%$search%' LIMIT 5";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        while ($game = mysqli_fetch_assoc($result)) {
            echo "
            <a href='post.php?id={$game['id']}' class='d-flex align-items-center text-decoration-none p-2 border-bottom live-search-item'>
                <img src='img/{$game['image']}' class='rounded me-2' style='width: 40px; height: 50px; object-fit: cover; min-width: 40px;'>
                <div>
                    <div class='fw-bold text-white search-title'>".htmlspecialchars($game['title'])."</div>
                    <small class='search-muted' style='color: rgba(255, 255, 255, 0.65); display: block;'>".htmlspecialchars($game['genre'])."</small>
                </div>
            </a>";
        }
    } else {
        echo "<div class='p-3 text-center search-no-results' style='color: rgba(255, 255, 255, 0.8); font-weight: 500;'>Нічого не знайдено 🔍</div>";
    }
}
?>