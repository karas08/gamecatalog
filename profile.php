<?php
require_once "include/config.php";
require_once "include/auth_check.php";
include_once "include/header.php";

// Перевірка авторизації
if (!isset($_SESSION['user_id'])) {
    header("Location: login/index.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// 1. ОБРОБКА ОНОВЛЕННЯ ПРОФІЛЮ (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bio = mysqli_real_escape_string($conn, trim($_POST['bio']));
    
    // Отримуємо поточну аватарку з бази
    $user_q = mysqli_query($conn, "SELECT avatar FROM users WHERE id = $user_id");
    $user_data = mysqli_fetch_assoc($user_q);
    $avatar_filename = $user_data['avatar'] ?? 'default_avatar.png';

    // Обробка завантаження аватарки
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['avatar']['tmp_name'];
        $file_name = $_FILES['avatar']['name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        
        // Дозволені формати
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($file_ext), $allowed)) {
            $new_avatar_name = "avatar_" . $user_id . "_" . time() . "." . $file_ext;
            $upload_dir = "img/avatars/";

            // Створюємо папку, якщо її немає
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            if (move_uploaded_file($file_tmp, $upload_dir . $new_avatar_name)) {
                // Видаляємо стару аватарку, якщо це не дефолтна
                if ($avatar_filename !== 'default_avatar.png' && file_exists($upload_dir . $avatar_filename)) {
                    @unlink($upload_dir . $avatar_filename);
                }
                $avatar_filename = $new_avatar_name;
            }
        }
    }

    // Оновлюємо дані в БД
    $update_query = "UPDATE users SET bio = '$bio', avatar = '$avatar_filename' WHERE id = $user_id";
    if (mysqli_query($conn, $update_query)) {
        echo "<script>window.location.href='profile.php?status=success';</script>";
        exit();
    }
}

// 2. ОТРИМАННЯ ДАНИХ КОРИСТУВАЧА ДЛЯ ВИВЕДЕННЯ
$res = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($res);

$avatar_path = "img/avatars/" . ($user['avatar'] ?? 'default_avatar.png');
if (!file_exists($avatar_path) || empty($user['avatar'])) {
    $avatar_path = "https://cdn-icons-png.flaticon.com/512/149/149071.png"; // Тимчасова дефолтна заглушка з мережі
}

// 3. ОТРИМАННЯ ОБРАНИХ ІГОР ДЛЯ ВІДОБРАЖЕННЯ В ПРОФІЛІ
$fav_table = "favorites";
$check_fav = mysqli_query($conn, "SHOW TABLES LIKE 'favorites'");
if (mysqli_num_rows($check_fav) == 0) { $fav_table = "favorite"; }

$games_query = "SELECT g.* FROM games g JOIN $fav_table f ON g.id = f.game_id WHERE f.user_id = $user_id ORDER BY f.id DESC";
$games_res = mysqli_query($conn, $games_query);
?>

<div class="container mt-5 text-white">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="glass-card p-4 text-center">
                <h3 class="text-info mb-3">Мій Профіль</h3>
                
                <img src="<?= $avatar_path ?>" class="rounded-circle img-thumbnail mb-3" style="width: 150px; height: 150px; object-fit: cover; background: #24243e; border: 2px solid #00d2ff;">
                
                <h4 class="text-white"><?= htmlspecialchars($user['username']) ?></h4>
                <p class="text-white-50 small">Зареєстрований: <?= $user['created_at'] ?? 'Невідомо' ?></p>

                <hr class="border-secondary">

                <form action="profile.php" method="POST" enctype="multipart/form-data" class="text-start">
                    <div class="mb-3">
                        <label class="form-label text-white-50 small fw-bold">Змінити аватарку</label>
                        <input type="file" name="avatar" accept="image/*" class="form-control form-control-sm bg-dark text-white border-secondary">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white-50 small fw-bold">Про себе (Опис)</label>
                        <textarea name="bio" rows="4" class="form-control bg-dark text-white border-secondary" placeholder="Розкажи про свої улюблені жанри чи ігри..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-info btn-sm w-100 fw-bold">💾 Зберегти зміни</button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="glass-card p-4">
                <h3 class="text-info mb-4"><i class="fas fa-gamepad text-warning"></i> Моя колекція (Обрані ігри)</h3>
                
                <?php if (mysqli_num_rows($games_res) == 0): ?>
                    <p class="text-white-50 lead text-center py-4">Ви ще не додали жодної гри до своєї колекції.</p>
                    <div class="text-center">
                        <a href="index.php" class="btn btn-outline-info btn-sm">Перейти до каталогу</a>
                    </div>
                <?php else: ?>
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
                        <?php while ($game = mysqli_fetch_assoc($games_res)): ?>
                            <div class="col">
                                <div class="card h-100 bg-dark border-secondary text-white style-card" style="border-radius: 10px; overflow: hidden;">
                                    <img src="img/<?= htmlspecialchars($game['image']) ?>" class="card-img-top" style="height: 140px; object-fit: cover;">
                                    <div class="card-body p-2 d-flex flex-column justify-content-between">
                                        <h6 class="card-title text-truncate m-0 text-info"><?= htmlspecialchars($game['title']) ?></h6>
                                        <a href="post.php?id=<?= $game['id'] ?>" class="btn btn-sm btn-outline-light w-100 btn-xs mt-2" style="font-size: 0.8rem; padding: 2px 5px;">Відкрити</a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
    <div class="alert alert-success position-fixed bottom-0 end-0 m-3" style="z-index: 9999;"> Профіль успішно оновлено! </div>
<?php endif; ?>