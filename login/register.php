<?php include_once "../include/config.php"; ?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>GameCat | Реєстрація</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #0f0c29; color: white; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .register-card { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); padding: 30px; border-radius: 20px; width: 400px; }
    </style>
</head>
<body>
    <div class="register-card shadow-lg">
        <h3 class="text-center mb-4">Створити акаунт</h3>
        <form action="check-register.php" method="POST">
            <div class="mb-3">
                <label>Логін</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Пароль</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-info w-100 fw-bold">Зареєструватися</button>
            <p class="mt-3 text-center small">Вже є акаунт? <a href="index.php" class="text-info">Увійти</a></p>
        </form>
    </div>
</body>
</html>