<?php
include 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "Вы должны быть авторизованы, чтобы удалять фильмы из закладок.";
    exit;
}
$user_id = $_SESSION['user_id'];
$movie_id = $_POST['movie_id'];
try {
    $stmt = $db->prepare("DELETE FROM bookmarks WHERE user_id = :user_id AND movie_id = :movie_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':movie_id', $movie_id, PDO::PARAM_INT);
    $stmt->execute();
    header("Location: movie.php?movie_id=$movie_id");
} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage();
}
?>
