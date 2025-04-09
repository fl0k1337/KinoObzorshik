<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    $review_id = $_POST['review_id'] ?? null;
    $movie_id = $_POST['movie_id'] ?? null;
    $comment_text = $_POST['comment_text'] ?? "";
    $user_id = $_SESSION['user_id'];

    if (!$review_id || !$movie_id || empty($comment_text)) {
        echo "Ошибка: неверные данные.";
        exit;
    }

    try {
        $stmt = $db->prepare("INSERT INTO comments (review_id, user_id, comment_text) VALUES (:review_id, :user_id, :comment_text)");
        $stmt->bindParam(':review_id', $review_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':comment_text', $comment_text, PDO::PARAM_STR);
        $stmt->execute();

        header("Location: movie.php?movie_id=$movie_id");
        exit;
    } catch (PDOException $e) {
        echo "Ошибка базы данных: " . $e->getMessage();
    }
}
?>
