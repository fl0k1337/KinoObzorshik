<?php
include 'db.php';
session_start();

// Заменить текущую проверку админа:
if (!hasPermission('delete_content')) {
    die("У вас нет прав на удаление");
}

// Получаем данные из формы или AJAX-запроса
$requestData = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $_GET;
$comment_id = $requestData['comment_id'] ?? null;
$user_id = $requestData['user_id'] ?? null;

// Валидация ID комментария
if (!$comment_id || !is_numeric($comment_id)) {
    header("HTTP/1.1 400 Bad Request");
    die(json_encode(['error' => 'Неверный ID комментария'], JSON_UNESCAPED_UNICODE));
}

try {
    // Начинаем транзакцию
    $db->beginTransaction();
    
    // 1. Удаляем оценки комментария (если есть)
    if ($db->query("SHOW TABLES LIKE 'comment_ratings'")->rowCount() > 0) {
        $db->prepare("DELETE FROM comment_ratings WHERE comment_id = ?")->execute([$comment_id]);
    }
    
    // 2. Удаляем сам комментарий
    $stmt = $db->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->execute([$comment_id]);
    
    // Проверяем, был ли удален комментарий
    if ($stmt->rowCount() === 0) {
        throw new PDOException("Комментарий не найден");
    }
    
    $db->commit();
    
    // Ответ в зависимости от типа запроса
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
    } else {
        header("Location: " . ($user_id ? "view_profile.php?id=$user_id" : "admin_panel.php"));
    }
    exit;

} catch (PDOException $e) {
    $db->rollBack();
    header("HTTP/1.1 500 Internal Server Error");
    die(json_encode(['error' => 'Ошибка: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE));
}
?>