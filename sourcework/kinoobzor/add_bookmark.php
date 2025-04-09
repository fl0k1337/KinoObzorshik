<?php
include 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "Вы должны быть авторизованы, чтобы добавлять фильмы в закладки.";
    exit;
}
$user_id = $_SESSION['user_id'];
$movie_id = $_POST['movie_id'];
try {
    $stmt = $db->prepare("INSERT INTO bookmarks (user_id, movie_id) VALUES (:user_id, :movie_id)");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':movie_id', $movie_id, PDO::PARAM_INT);
    $stmt->execute();
    header("Location: movie.php?movie_id=$movie_id");
} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage();
}
?>
