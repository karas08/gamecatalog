<?php session_start(); ?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>GameCat | Вхід</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #0f0c29; color: white; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-card { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); padding: 30px; border-radius: 20px; width: 400px; }
    </style>
</head>
<body>
    <div class="login-card shadow-lg">
        <h3 class="text-center mb-4">Вхід у GameCat</h3>
        <form action="check-login.php" method="POST">
            <div class="mb-3">
                <label>Логін</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Пароль</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-bold">Увійти</button>
            <a href="register.php" class="btn btn-outline-info w-100 mt-2">Реєстрація</a>
        </form>
    </div>
</body>
</html>