<?php
require_once "include/config.php";
include_once "include/header.php";

// Перевіряємо, чи користувач авторизований
if (!isset($_SESSION['user_id'])) {
    echo "<div class='container mt-5 text-white text-center'>
            <h3>Щоб переглядати обране, потрібно авторизуватися!</h3>
            <a href='login.php' class='btn btn-info mt-3'>Увійти</a>
          </div>";
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// Автовизначення таблиці обраного (шукаємо favorites або favorite)
$fav_table = "favorites";
$check_fav = mysqli_query($conn, "SHOW TABLES LIKE 'favorites'");
if (mysqli_num_rows($check_fav) == 0) {
    $check_fav_alt = mysqli_query($conn, "SHOW TABLES LIKE 'favorite'");
    if (mysqli_num_rows($check_fav_alt) > 0) {
        $fav_table = "favorite";
    }
}

// Запит, який об'єднує таблицю обраного та таблицю ігор
$query = "SELECT g.* FROM games g 
          JOIN $fav_table f ON g.id = f.game_id 
          WHERE f.user_id = $user_id 
          ORDER BY f.id DESC";

$res = mysqli_query($conn, $query);
?>

<div class="container mt-5 text-white">
    <h1 class="mb-4 text-info"><i class="fas fa-heart text-danger"></i> Моє обране</h1>

    <?php if (mysqli_num_rows($res) == 0): ?>
        <div class="glass-card p-5 text-center" style="background: rgba(255,255,255,0.05); border-radius: 15px; backdrop-filter: blur(10px);">
            <p class="lead text-white-50">У вашому списку обраного поки порожньо.</p>
            <a href="index.php" class="btn btn-primary mt-2">Переглянути каталог ігор</a>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
            <?php while ($game = mysqli_fetch_assoc($res)): ?>
                <div class="col">
                    <div class="card h-100 border-0 shadow-lg text-white" style="background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(10px); border-radius: 15px; overflow: hidden; transition: transform 0.2s;">
                        <img src="img/<?php echo htmlspecialchars($game['image']); ?>" class="card-img-top" alt="Poster" style="height: 250px; object-fit: cover;">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <h5 class="card-title text-info mb-1"><?php echo htmlspecialchars($game['title']); ?></h5>
                                <p class="text-white-50 small mb-2"><?php echo htmlspecialchars($game['genre'] ?? ''); ?></p>
                            </div>
                            <div class="d-flex flex-column gap-2 mt-3">
                                <a href="post.php?id=<?php echo $game['id']; ?>" class="btn btn-sm btn-info w-100" style="font-weight: bold;">
                                    Детальніше
                                </a>
                                <a href="toggle_fav.php?game_id=<?php echo $game['id']; ?>" class="btn btn-sm btn-outline-danger w-100" style="font-weight: bold;">
                                    ❌ Видалити з обраного
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<?php
if (file_exists("include/footer.php")) {
    include_once "include/footer.php";
}
?>