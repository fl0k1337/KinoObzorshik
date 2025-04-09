<?php
session_start();
include 'db.php';

// Сохраняем введенные данные (кроме пароля)
$_SESSION['form_data'] = [
    'username' => trim($_POST['username']),
    'email' => trim($_POST['email'])
];

$errors = [];

// Валидация имени
$username = trim($_POST['username']);
if (empty($username)) {
    $errors[] = "Имя пользователя обязательно для заполнения.";
} elseif (strlen($username) < 3 || strlen($username) > 20) {
    $errors[] = "Имя пользователя должно быть от 3 до 20 символов.";
} elseif (preg_match('/\s/', $username)) {
    $errors[] = "Имя пользователя не должно содержать пробелов.";
} elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    $errors[] = "Разрешены только латинские буквы, цифры и подчеркивания.";
}

// Валидация email
$email = trim($_POST['email']);
if (empty($email)) {
    $errors[] = "Email обязателен для заполнения.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Некорректный формат email.";
}

// Валидация пароля
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
if (empty($password)) {
    $errors[] = "Пароль обязателен для заполнения.";
} elseif ($password !== $confirm_password) {
    $errors[] = "Пароли не совпадают.";
} elseif (strlen($password) < 8) {
    $errors[] = "Пароль должен быть не менее 8 символов.";
} elseif (!preg_match('/\d/', $password)) {
    $errors[] = "Пароль должен содержать минимум одну цифру.";
} elseif (!preg_match('/[A-Z]/', $password)) {
    $errors[] = "Пароль должен содержать минимум одну заглавную букву.";
} elseif (!preg_match('/[a-z]/', $password)) {
    $errors[] = "Пароль должен содержать минимум одну строчную букву.";
}

$agree_terms = isset($_POST['agree_terms']) && $_POST['agree_terms'] === 'on';
if (!$agree_terms) {
    $errors[] = "Необходимо согласиться с условиями пользования.";
}

if (empty($errors)) {
    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $errors[] = "Это имя пользователя или email уже зарегистрированы.";
        $_SESSION['reg_errors'] = $errors;
        header("Location: register.php");
        exit;
    }

    // Регистрация через хранимую процедуру
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    try {
        $stmt = $db->prepare("CALL AddUser(:username, :email, :password)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->execute();
        $stmt->closeCursor(); // Закрываем курсор

        // Получаем ID нового пользователя
        $stmt = $db->query("SELECT LAST_INSERT_ID() AS user_id");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $user_id = $result['user_id'];

        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;

        header("Location: profile.php");
        exit;
    } catch (PDOException $e) {
        $errors[] = "Ошибка регистрации: " . $e->getMessage();
        $_SESSION['reg_errors'] = $errors;
        header("Location: register.php");
        exit;
    }
} else {
    $_SESSION['reg_errors'] = $errors;
    header("Location: register.php");
    exit;
}