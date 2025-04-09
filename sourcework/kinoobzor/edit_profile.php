<?php
include 'db.php';
session_start();
// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];
// Получаем данные пользователя
$stmt = $db->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
// Обработка данных формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $current_password = $_POST['current_password'] ?? "";
    $new_password = $_POST['new_password'] ?? "";
    $confirm_password = $_POST['confirm_password'] ?? "";
    // Проверка текущего пароля
    if ($current_password && !password_verify($current_password, $user['password'])) {
        $error = "Неверный текущий пароль.";
    } elseif ($new_password && $new_password !== $confirm_password) {
        $error = "Новые пароли не совпадают.";
    } else {
        // Обновление данных пользователя
        $stmt = $db->prepare("UPDATE users SET username = :username, email = :email WHERE id = :user_id");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        // Обновление пароля, если он был изменен
        if ($new_password) {
            $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);
            $stmt = $db->prepare("UPDATE users SET password = :password WHERE id = :user_id");
            $stmt->bindParam(':password', $new_password_hash);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
        }
        header("Location: profile.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать профиль - КиноОбзор</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="profile-container">
        <div class="profile-main">
            <div class="profile-info">
                <h2>Редактировать профиль</h2>
                <?php if (isset($error)): ?>
                    <p style="color: red;"><?= $error ?></p>
                <?php endif; ?>
                <form action="edit_profile.php" method="post">
                    <label for="username">Имя пользователя:</label>
                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    <label for="current_password">Текущий пароль:</label>
                    <input type="password" id="current_password" name="current_password">
                    <label for="new_password">Новый пароль:</label>
                    <input type="password" id="new_password" name="new_password">
                    <label for="confirm_password">Подтвердите новый пароль:</label>
                    <input type="password" id="confirm_password" name="confirm_password">
                    <button type="submit">Сохранить изменения</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
