<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(403);
        echo json_encode(['error' => 'Вы должны быть авторизованы, чтобы оценить комментарий.']);
        exit;
    }

    $comment_id = $_POST['comment_id'] ?? null;
    $rating = $_POST['rating'] ?? null;
    $user_id = $_SESSION['user_id'];

    if (!$comment_id || !is_numeric($rating)) {
        http_response_code(400);
        echo json_encode(['error' => 'Неверные данные.']);
        exit;
    }

    $stmt = $db->prepare("SELECT * FROM comment_ratings WHERE comment_id = :comment_id AND user_id = :user_id");
    $stmt->bindParam(':comment_id', $comment_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $existingRating = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingRating) {
        $stmt = $db->prepare("UPDATE comment_ratings SET rating = :rating WHERE id = :id");
        $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
        $stmt->bindParam(':id', $existingRating['id'], PDO::PARAM_INT);
    } else {
        $stmt = $db->prepare("INSERT INTO comment_ratings (comment_id, user_id, rating) VALUES (:comment_id, :user_id, :rating)");
        $stmt->bindParam(':comment_id', $comment_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
    }

    $stmt->execute();

    // Получаем общее количество оценок для комментария
    $stmt = $db->prepare("SELECT SUM(rating) as totalRating FROM comment_ratings WHERE comment_id = :comment_id");
    $stmt->bindParam(':comment_id', $comment_id, PDO::PARAM_INT);
    $stmt->execute();
    $totalRating = $stmt->fetch(PDO::FETCH_ASSOC)['totalRating'];

    echo json_encode(['totalRating' => $totalRating]);
}
?>
