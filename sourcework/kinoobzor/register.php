<?php 
session_start();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация - КиноОбзор</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'header.php'; ?>
    <div class="login-container">
    <div class="login-box">
        <h2>Регистрация</h2>
        
        <?php 
        if (isset($_SESSION['reg_errors']) && !empty($_SESSION['reg_errors'])): 
        ?>
            <div class="error-message">
                <?php foreach ($_SESSION['reg_errors'] as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
            <?php unset($_SESSION['reg_errors']); ?>
            <?php endif; ?>

        <form action="register_process.php" method="post">
            <input type="text" name="username" placeholder="Имя пользователя" 
                   value="<?php echo isset($_SESSION['form_data']['username']) ? htmlspecialchars($_SESSION['form_data']['username']) : ''; ?>" 
                   required>
            <input type="email" name="email" placeholder="Почта" 
                   value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>" 
                   required>
            <input type="password" name="password" placeholder="Пароль" required>
            <input type="password" name="confirm_password" placeholder="Повторите пароль" required>
            <label>
            <input type="checkbox" name="agree_terms" required>
                Я согласен с <a href="terms.php">условиями пользования</a>
            </label>
            <button type="submit">Зарегистрироваться</button>
            <p>Уже есть аккаунт? <a href="login.php">Войти</a></p>
        </form>
    </div>
</div>
<?php unset($_SESSION['form_data']); ?>
    </div>
</body>
</html>
