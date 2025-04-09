<?php
session_start(); // Добавляем и здесь для надежности

$host = 'localhost';
$dbname = 'kinoobzor';
$user = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['reg_errors'] = ["Ошибка подключения к базе данных"];
    header("Location: register.php");
    exit;
}

function hasPermission($permission) {
    global $db;
    
    if (!isset($_SESSION['user_id'])) return false;
    
    // Если роль изменилась в БД, но не в сессии - обновляем сессию
    $stmt = $db->prepare("SELECT role_id FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $current_role_id = $stmt->fetchColumn();
    
    if ($current_role_id != $_SESSION['role_id']) {
        $stmt = $db->prepare("SELECT name FROM roles WHERE id = ?");
        $stmt->execute([$current_role_id]);
        $_SESSION['role_name'] = $stmt->fetchColumn();
        $_SESSION['role_id'] = $current_role_id;
    }
    
    // Админы имеют все права
    if ($_SESSION['role_name'] === 'admin') return true;
    
    // Проверяем конкретные права
    $stmt = $db->prepare("SELECT permissions FROM roles WHERE id = ?");
    $stmt->execute([$_SESSION['role_id']]);
    $permissions = json_decode($stmt->fetchColumn(), true);
    
    return isset($permissions[$permission]) && $permissions[$permission];
}
?>

