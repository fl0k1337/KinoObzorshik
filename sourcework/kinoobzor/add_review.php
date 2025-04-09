<?php
include 'db.php';
session_start();

// Добавить в начало, после session_start():
if (!hasPermission('write_reviews')) {
    die("У вас нет прав для добавления отзывов");
}
// Проверяем, что пользователь авторизован
if (!isset($_SESSION['user_id'])) {
    echo "Вы должны быть авторизованы, чтобы оставить отзыв.";
    exit;
}
// Получаем данные из формы
$movie_id = $_POST['movie_id'] ?? null;
$user_id = $_SESSION['user_id'];
$rating = $_POST['rating'] ?? null;
$review_text = $_POST['review_text'] ?? "";

// Проверка, что все необходимые данные присутствуют
if (!$movie_id || !$rating || empty($review_text)) {
    echo "Пожалуйста, заполните все поля формы.";
    exit;
}
try {
    // Вставка отзыва в базу данных
    $stmt = $db->prepare("INSERT INTO reviews (movie_id, user_id, rating, review_text) VALUES (:movie_id, :user_id, :rating, :review_text)");
    $stmt->bindParam(':movie_id', $movie_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
    $stmt->bindParam(':review_text', $review_text, PDO::PARAM_STR);
    if ($stmt->execute()) {
        header("Location: movie.php?movie_id=" . $movie_id); // Перенаправление на страницу фильма
        exit;
    } else {
        echo "Ошибка: не удалось сохранить отзыв.";
    }
} catch (PDOException $e) {
    echo "Ошибка базы данных: " . $e->getMessage();
}
?>
