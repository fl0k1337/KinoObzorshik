<?php
session_start();
include 'db.php';

// Сохраняем введенные данные (кроме пароля)
$_SESSION['form_data'] = [
    'username' => trim($_POST['username'])
];

$errors = [];

// Валидация имени пользователя
$username = trim($_POST['username']);
if (empty($username)) {
    $errors[] = "Имя пользователя обязательно для заполнения.";
} elseif (preg_match('/\s/', $username)) {
    $errors[] = "Имя пользователя не должно содержать пробелов.";
}

// Проверка наличия пользователя в базе данных
$stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
$stmt->bindParam(':username', $username);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $errors[] = "Пользователь с таким именем не найден.";
} elseif (!password_verify($_POST['password'], $user['password'])) {
    $errors[] = "Неверный пароль.";
}

if (empty($errors)) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role_id'] = $user['role_id'];
    $stmt = $db->prepare("SELECT name FROM roles WHERE id = ?");
    $stmt->execute([$user['role_id']]);
    $_SESSION['role_name'] = $stmt->fetchColumn();
    header("Location: loading.php");
    exit;
} else {
    $_SESSION['login_errors'] = $errors;
    header("Location: login.php");
    exit;
}
?>
