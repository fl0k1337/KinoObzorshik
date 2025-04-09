<?php
include 'db.php';
session_start();

// Проверка прав администратора
if (!isset($_SESSION['role_name']) || $_SESSION['role_name'] !== 'admin') {
    header("Location: index.php?error=access_denied");
    exit;
}

// Получение ID пользователя
$user_id = $_POST['user_id'] ?? null;
if (!$user_id) {
    header("HTTP/1.1 400 Bad Request");
    die("Не указан ID пользователя");
}

// Запрет на удаление самого себя
if ($user_id == $_SESSION['user_id']) {
    header("HTTP/1.1 400 Bad Request");
    die("Вы не можете удалить самого себя");
}

try {
    // Начало транзакции
    $db->beginTransaction();
    
    // Удаление зависимых записей (отзывы, комментарии, закладки)
    $db->prepare("DELETE FROM reviews WHERE user_id = ?")->execute([$user_id]);
    $db->prepare("DELETE FROM comments WHERE user_id = ?")->execute([$user_id]);
    $db->prepare("DELETE FROM bookmarks WHERE user_id = ?")->execute([$user_id]);
    
    // Удаление самого пользователя
    $db->prepare("DELETE FROM users WHERE id = ?")->execute([$user_id]);
    
    // Подтверждение транзакции
    $db->commit();
    
    // Перенаправление в админ-панель
    header("Location: admin_panel.php");
    exit;
} catch (PDOException $e) {
    // Откат транзакции при ошибке
    $db->rollBack();
    header("HTTP/1.1 500 Internal Server Error");
    die("Ошибка при удалении пользователя: " . $e->getMessage());
}
?>