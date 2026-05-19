<?php
require_once "include/config.php";
include_once "include/header.php";

$id = (int)$_GET['id'];
$game = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM games WHERE id = $id"));

if (!$game) { die("<div class='container mt-5 text-white'><h3>Гру не знайдено!</h3></div>"); }

$avg_rating = round(mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(rating) as avg_r FROM ratings WHERE game_id = $id"))['avg_r'], 1);
$steam_final_link = !empty($game['steam_link']) ? $game['steam_link'] : "https://store.steampowered.com/search/?term=" . urlencode($game['title']);

function getYoutubeEmbedUrl($url) {
    if (empty($url)) return '';
    preg_match('/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches);
    return !empty($matches[1]) ? 'https://www.youtube.com/embed/' . $matches[1] : '';
}
$embed_trailer = getYoutubeEmbedUrl($game['trailer_url'] ?? '');
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-4 text-center mb-4">
            <img src="img/<?php echo htmlspecialchars($game['image']); ?>" class="img-fluid rounded-4 shadow-lg mb-3" style="width: 100%; max-width: 350px; height: 430px; object-fit: cover;">
            <div class="d-flex gap-2">
                <a href="<?php echo $steam_final_link; ?>" target="_blank" class="btn btn-primary flex-grow-1" style="background-color: #101822; border-color: #171a21; font-weight: bold;">
                    <i class="fab fa-steam"></i> В Steam
                </a>
                <a href="toggle_fav.php?game_id=<?php echo $id; ?>" class="btn btn-outline-danger" title="В улюблене">
                    <i class="fas fa-heart"></i>
                </a>
            </div>
        </div>

        <div class="col-md-8">
            <div class="glass-card p-4">
                <h1 class="text-info"><?php echo htmlspecialchars($game['title']); ?></h1>
                <div class="mb-3"><span class="text-warning h4">★ <?php echo $avg_rating; ?>/5</span></div>
                
                <div class="d-flex flex-wrap gap-4 mb-4" style="font-size: 0.95rem;">
                    <div class="d-flex flex-column">
                        <span class="text-white-50 small">Розробник</span>
                        <span class="fw-bold text-white"><?php echo htmlspecialchars($game['developer'] ?? '—'); ?></span>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="text-white-50 small">Жанр</span>
                        <span class="fw-bold text-white"><?php echo htmlspecialchars($game['genre'] ?? '—'); ?></span>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="text-white-50 small">Рік випуску</span>
                        <span class="fw-bold text-white"><?php echo htmlspecialchars($game['release_year'] ?? '—'); ?></span>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="text-white-50 small">Вік</span>
                        <span class="fw-bold text-white"><?php echo htmlspecialchars($game['age_rating'] ?? '0'); ?>+</span>
                    </div>
                </div>

                <hr class="border-secondary my-3">
                <p class="lead" style="font-size: 1rem; line-height: 1.6;"><?php echo nl2br(htmlspecialchars($game['description'] ?? '')); ?></p>

                <?php if (!empty($embed_trailer)): ?>
                    <div class="mt-4">
                        <h5 class="text-info mb-3"><i class="fab fa-youtube"></i> Офіційний трейлер</h5>
                        <div class="ratio ratio-16x9 shadow-lg rounded-3 overflow-hidden border border-secondary">
                            <iframe src="<?= $embed_trailer ?>" title="YouTube video player" frameborder="0" allowfullscreen></iframe>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mt-4 p-3 rounded border border-secondary" style="background-color: rgba(0,0,0,0.2);">
                    <h6 class="text-white-50 mb-2">Ваша оцінка гри:</h6>
                    <form action="save_rating.php" method="POST" class="d-flex gap-2">
                        <input type="hidden" name="game_id" value="<?php echo $id; ?>">
                        <?php for($i=1; $i<=5; $i++): ?>
                            <button name="rate" value="<?php echo $i; ?>" class="btn btn-sm btn-outline-warning">
                                <?php echo $i; ?> ★
                            </button>
                        <?php endfor; ?>
                    </form>
                </div> 
            </div>
        </div>
    </div>
</div>

<?php if (file_exists("include/footer.php")) { include_once "include/footer.php"; } ?>