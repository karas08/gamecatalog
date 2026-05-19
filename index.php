<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once "include/config.php";
require_once "include/auth_check.php";
include_once "include/header.php";

$limit = 8; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;
// ----------------------------

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : "";
$genre = isset($_GET['genre']) ? mysqli_real_escape_string($conn, trim($_GET['genre'])) : "";
$age_rating = isset($_GET['age_rating']) ? trim($_GET['age_rating']) : "";
$min_rating = isset($_GET['min_rating']) ? (float)$_GET['min_rating'] : "";

$query = "SELECT g.*, COALESCE(AVG(r.rating), 0) as avg_rating 
          FROM games g 
          LEFT JOIN ratings r ON g.id = r.game_id";

$where_clauses = [];
if (!empty($search)) $where_clauses[] = "(g.`title` LIKE '%$search%' OR g.`genre` LIKE '%$search%')";
if (!empty($genre)) $where_clauses[] = "g.`genre` = '$genre'";
if (!empty($age_rating)) $where_clauses[] = "g.`age_rating` <= " . (int)$age_rating;

if (count($where_clauses) > 0) $query .= " WHERE " . implode(" AND ", $where_clauses);
$query .= " GROUP BY g.id";
if (!empty($min_rating)) $query .= " HAVING avg_rating >= $min_rating";

// --- ДОДАНО ДЛЯ ПАГІНАЦІЇ (підрахунок загальної кількості для кнопок) ---
$total_res = mysqli_query($conn, $query);
$total_rows = mysqli_num_rows($total_res);
$total_pages = ceil($total_rows / $limit);
// ----------------------------

$query .= " ORDER BY g.id DESC LIMIT $limit OFFSET $offset";
$res = mysqli_query($conn, $query);

$genres_query = "SELECT DISTINCT genre FROM games WHERE genre IS NOT NULL AND genre != ''";
$genres_res = mysqli_query($conn, $genres_query);

// ==========================================
// 1. ЗБИРАЄМО ДАНІ З ПОШУКУ ТА ФІЛЬТРІВ
// ==========================================
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : "";
$genre = isset($_GET['genre']) ? mysqli_real_escape_string($conn, trim($_GET['genre'])) : "";
$age_rating = isset($_GET['age_rating']) ? trim($_GET['age_rating']) : "";
$min_rating = isset($_GET['min_rating']) ? (float)$_GET['min_rating'] : "";

// Базовий запит з LEFT JOIN для автоматичного підрахунку середнього рейтингу
// Спочатку будуємо базу запиту
$query = "SELECT g.*, COALESCE(AVG(r.rating), 0) as avg_rating 
          FROM games g 
          LEFT JOIN ratings r ON g.id = r.game_id";

// Додаємо умови WHERE
if (count($where_clauses) > 0) {
    $query .= " WHERE " . implode(" AND ", $where_clauses);
}

// ГРУПУВАННЯ — це найважливіше, щоб прибрати дублі
$query .= " GROUP BY g.id";

// HAVING для рейтингу
if (!empty($min_rating)) {
    $query .= " HAVING avg_rating >= $min_rating";
}

// Спершу рахуємо загальну кількість для пагінації (без LIMIT!)
$count_query = "SELECT COUNT(*) as total FROM ($query) as sub";
$total_res = mysqli_fetch_assoc(mysqli_query($conn, $count_query));
$total_rows = $total_res['total'];
$total_pages = ceil($total_rows / $limit);

// А тепер додаємо сортування та LIMIT для виводу
$query .= " ORDER BY g.id DESC LIMIT $limit OFFSET $offset";
$res = mysqli_query($conn, $query);

// Список унікальних жанрів для випадаючого списку
$genres_query = "SELECT DISTINCT genre FROM games WHERE genre IS NOT NULL AND genre != ''";
$genres_res = mysqli_query($conn, $genres_query);
?>

<style>
    .game-hover-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease !important;
        cursor: pointer;
    }
    
    .game-hover-card:hover {
        transform: translateY(-8px) scale(1.02);
        background: rgba(255, 255, 255, 0.12) !important;
        box-shadow: 0 12px 24px rgba(13, 202, 240, 0.35) !important;
    }
</style>

<div class="container mt-4">

    <?php if (empty($search) && empty($genre) && empty($age_rating) && empty($min_rating)): ?>
        <?php
        // Витягуємо ігри з позначкою рекомендованих
        $slider_query = mysqli_query($conn, "SELECT * FROM games WHERE is_featured = 1 LIMIT 5");
        if (mysqli_num_rows($slider_query) > 0): 
        ?>
            <div id="featuredGamesCarousel" class="carousel slide mb-5 shadow" data-bs-ride="carousel">
                <div class="carousel-inner" style="border-radius: 20px;">
                    <?php 
                    $is_first = true;
                    while ($slider_game = mysqli_fetch_assoc($slider_query)): 
                        $active_class = $is_first ? 'active' : '';
                        $is_first = false;
                    ?>
                        <div class="carousel-item <?= $active_class ?>" data-bs-interval="4000">
                            <img src="img/<?= htmlspecialchars($slider_game['image']) ?>" class="d-block w-100" alt="<?= htmlspecialchars($slider_game['title']) ?>" style="height: 380px; object-fit: cover; filter: brightness(0.55);">
                            
                            <div class="carousel-caption d-block text-start" style="bottom: 40px; left: 5%; right: 5%;">
                                <span class="badge bg-danger mb-2 fw-bold text-uppercase" style="letter-spacing: 1px;">🔥 Рекомендуємо</span>
                                <h1 class="fw-bold text-white display-5"><?= htmlspecialchars($slider_game['title']) ?></h1>
                                <p class="text-white-50 text-truncate d-none d-md-block mb-4" style="max-width: 650px; font-size: 1.1rem;">
                                    <?= htmlspecialchars($slider_game['description']) ?>
                                </p>
                                <a href="post.php?id=<?= $slider_game['id'] ?>" class="btn btn-info btn-lg fw-bold text-dark px-4 py-2" style="border-radius: 10px; background-color: #00d2ff; border: none; box-shadow: 0 4px 15px rgba(0, 210, 255, 0.4);">
                                    Дивитися зараз
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <button class="carousel-control-prev" type="button" data-bs-target="#featuredGamesCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Попередній</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#featuredGamesCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Наступний</span>
                </button>
            </div>
        <?php endif; ?>
    <?php endif; ?>


    <div class="row">
        <div class="col-12">
            <h2 class="mb-4 text-info fw-bold">
                <?php if (!empty($search)): ?>
                    🔍 Результати пошуку за запитом: "<?= htmlspecialchars($search) ?>"
                <?php else: ?>
                    🎮 Всі доступні ігри
                <?php endif; ?>
            </h2>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="p-3 mb-2" style="background: rgba(255, 255, 255, 0.04); backdrop-filter: blur(10px); border-radius: 15px; border: 1px solid rgba(255, 255, 255, 0.05);">
                <form method="GET" action="index.php" class="row g-3 align-items-end">
                    
                    <?php if(!empty($search)): ?>
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <?php endif; ?>

                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="form-label text-white-50 small fw-bold">Жанр</label>
                        <select name="genre" class="form-select bg-dark text-white border-secondary" style="border-radius: 8px;">
                            <option value="">Всі жанри</option>
                            <?php while($g_row = mysqli_fetch_assoc($genres_res)): ?>
                                <option value="<?= htmlspecialchars($g_row['genre']) ?>" <?= $genre == $g_row['genre'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($g_row['genre']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="form-label text-white-50 small fw-bold">Доступно для віку</label>
                        <select name="age_rating" class="form-select bg-dark text-white border-secondary" style="border-radius: 8px;">
                            <option value="">Будь-який вік</option>
                            <option value="0" <?= $age_rating === '0' ? 'selected' : '' ?>>0+ (Для всіх)</option>
                            <option value="3" <?= $age_rating == '3' ? 'selected' : '' ?>>3+</option>
                            <option value="12" <?= $age_rating == '12' ? 'selected' : '' ?>>12+</option>
                            <option value="16" <?= $age_rating == '16' ? 'selected' : '' ?>>16+</option>
                            <option value="18" <?= $age_rating == '18' ? 'selected' : '' ?>>18+</option>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="form-label text-white-50 small fw-bold">Мінімальний рейтинг (★)</label>
                        <select name="min_rating" class="form-select bg-dark text-white border-secondary" style="border-radius: 8px;">
                            <option value="">Будь-який рейтинг</option>
                            <option value="4.5" <?= $min_rating == 4.5 ? 'selected' : '' ?>>⭐ 4.5 і вище</option>
                            <option value="4.0" <?= $min_rating == 4.0 ? 'selected' : '' ?>>⭐ 4.0 і вище</option>
                            <option value="3.0" <?= $min_rating == 3.0 ? 'selected' : '' ?>>⭐ 3.0 і вище</option>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-info w-100 fw-bold text-dark" style="border-radius: 8px; padding: 7px; background-color: #00d2ff; border: none;">
                            Застосувати
                        </button>
                        <?php if(!empty($genre) || !empty($age_rating) || !empty($min_rating) || !empty($search)): ?>
                            <a href="index.php" class="btn btn-outline-secondary text-white border-secondary fw-bold d-flex align-items-center justify-content-center" style="border-radius: 8px; width: 45px;" title="Скинути фільтри">
                                ✖
                            </a>
                        <?php endif; ?>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mb-5">
        <?php if (mysqli_num_rows($res) == 0): ?>
            <div class="col-12 text-center py-5">
                <div class="glass-card p-5 d-inline-block" style="background: rgba(255, 255, 255, 0.04); border-radius: 15px; padding: 3rem !important;">
                    <h4 class="text-warning mb-2">😢 Нічого не знайдено</h4>
                    <p class="text-white-50 m-0">Спробуйте змінити критерії фільтрації або пошукове слово.</p>
                    <a href="index.php" class="btn btn-sm btn-info mt-3 fw-bold text-dark">Скинути всі фільтри</a>
                </div>
            </div>
        <?php else: ?>
            <?php while ($game = mysqli_fetch_assoc($res)): ?>
                <div class="col">
                    <div class="card h-100 border-0 shadow-lg text-white game-hover-card" onclick="location.href='post.php?id=<?= $game['id'] ?>'" style="background: rgba(255, 255, 255, 0.06); backdrop-filter: blur(10px); border-radius: 15px; overflow: hidden;">
                        
                        <div class="position-relative" style="height: 270px; width: 100%;">
                            <img src="img/<?= htmlspecialchars($game['image']) ?>" class="w-100 h-100" alt="Poster" style="object-fit: cover; object-position: center 20%;">
                            
                            <?php if (isset($game['age_rating'])): ?>
                                <span class="position-absolute top-0 end-0 bg-danger px-2 py-1 m-2 rounded small fw-bold" style="font-size: 0.75rem; z-index: 5;">
                                    <?= htmlspecialchars($game['age_rating']) ?>+
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="card-body d-flex flex-column justify-content-between p-3">
                            <div>
                                <h5 class="card-title text-info mb-1 text-truncate fw-bold" title="<?= htmlspecialchars($game['title']) ?>">
                                    <?= htmlspecialchars($game['title']) ?>
                                </h5>
                                
                                <div class="d-flex justify-content-between align-items-center text-white-50 small mb-2 gap-2">
                                    <span class="text-truncate flex-grow-1" title="<?= htmlspecialchars($game['genre'] ?? 'Не вказано') ?>">
                                        <?= htmlspecialchars($game['genre'] ?? 'Не вказано') ?>
                                    </span>
                                    
                                    <span class="text-end text-warning fw-bold flex-shrink-0" style="min-width: 45px;">
                                        <?= $game['avg_rating'] > 0 ? '⭐ ' . round($game['avg_rating'], 1) : '⭐ 0.0' ?>
                                    </span>

                                    <span class="text-end flex-shrink-0" style="min-width: 50px;">
                                        <?= (int)($game['release_year'] ?? 0) ?> р.
                                    </span>
                                </div>
                            </div>
                            
                            <div class="mt-2">
                                <a href="post.php?id=<?= $game['id'] ?>" class="btn btn-sm btn-info w-100 fw-bold text-dark" style="font-size: 0.85rem; padding: 6px; background-color: #00d2ff; border: none;">
                                    Детальніше
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>
<div class="container mt-4">
    <?php if ($total_pages > 1): ?>
        <nav class="mt-4 mb-5">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link bg-dark text-info border-secondary" href="?page=<?= $page - 1 ?><?= !empty($_GET['search']) ? '&search='.$search : '' ?>">Назад</a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link bg-dark text-info border-secondary" href="?page=<?= $i ?><?= !empty($_GET['search']) ? '&search='.$search : '' ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                    <a class="page-link bg-dark text-info border-secondary" href="?page=<?= $page + 1 ?><?= !empty($_GET['search']) ? '&search='.$search : '' ?>">Вперед</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include_once "include/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php
if (file_exists("include/footer.php")) {
    include_once "include/footer.php";
}
?>