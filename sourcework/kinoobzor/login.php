<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Войти - КиноОбзор</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php
    session_start();
    include 'header.php';
    $errors = isset($_SESSION['login_errors']) ? $_SESSION['login_errors'] : [];
    unset($_SESSION['login_errors']);
    ?>
    <div class="login-container">
        <div class="login-box">
            <h2>Войти</h2>
            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <form action="login_process.php" method="post">
                <input type="text" name="username" placeholder="Имя пользователя" value="<?php echo htmlspecialchars($_SESSION['form_data']['username'] ?? ''); ?>" required>
                <input type="password" name="password" placeholder="Пароль" required>
                <a href="#">Забыли пароль?</a>
                <button type="submit">Войти</button>
                <p>Нет аккаунта? <a href="register.php">Регистрация</a></p>
            </form>
        </div>
    </div>
    <?php unset($_SESSION['form_data']); ?>
</body>
</html>
