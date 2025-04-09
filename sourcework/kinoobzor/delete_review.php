<?php
include 'db.php';
session_start();

// Проверка прав администратора
if (!isset($_SESSION['role_name']) || $_SESSION['role_name'] !== 'admin') {
    header("Location: index.php?error=access_denied");
    exit;
}

// Получение данных из запроса
$review_id = $_POST['review_id'] ?? null;
$user_id = $_POST['user_id'] ?? null;

if (!$review_id || !$user_id) {
    header("HTTP/1.1 400 Bad Request");
    die("Неверные параметры запроса");
}

try {
    // Удаление отзыва
    $stmt = $db->prepare("DELETE FROM reviews WHERE id = ?");
    $stmt->execute([$review_id]);
    
    // Перенаправление обратно в профиль пользователя
    header("Location: view_profile.php?id=$user_id");
    exit;
} catch (PDOException $e) {
    header("HTTP/1.1 500 Internal Server Error");
    die("Ошибка при удалении отзыва: " . $e->getMessage());
}
?>