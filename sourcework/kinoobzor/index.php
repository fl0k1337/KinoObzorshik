<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db.php';

$stmt = $db->prepare("
    SELECT reviews.*, movies.title AS movie_title, movies.id AS movie_id, users.username
    FROM reviews
    JOIN movies ON reviews.movie_id = movies.id
    JOIN users ON reviews.user_id = users.id
    ORDER BY reviews.rating DESC
    LIMIT 5
");
$stmt->execute();
$popular_reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->prepare("SELECT * FROM movies ORDER BY created_at DESC, release_year DESC LIMIT 5");
$stmt->execute();
$new_movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>КиноОбзор - Главная</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <main>
        <section class="popular-reviews">
            <h1>Популярные отзывы</h1>
            <?php if ($popular_reviews): ?>
                <ul>
                    <?php foreach ($popular_reviews as $review): ?>
                        <li class="review-item">
                            <strong><a href="movie.php?movie_id=<?= htmlspecialchars($review['movie_id']) ?>"><?= htmlspecialchars($review["movie_title"]) ?></a></strong>
                            <p>Оценка: <?= htmlspecialchars($review['rating']) ?>/10</p>
                            <p><?= htmlspecialchars($review["review_text"]) ?></p>
                            <small>Отзыв от: <?= htmlspecialchars($review['username']) ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Популярные отзывы отсутствуют.</p>
            <?php endif; ?>
        </section>

        <section class="new-movies">
            <h1>Новые фильмы</h1>
            <?php if ($new_movies): ?>
                <ul>
                    <?php foreach ($new_movies as $movie): ?>
                        <li class="movie-item">
                            <h3><a href="movie.php?movie_id=<?= htmlspecialchars($movie['id']) ?>"><?= htmlspecialchars($movie['title']) ?></a></h3>
                            <p>Год выпуска: <?= htmlspecialchars($movie['release_year']) ?></p>
                            <p>Рейтинг: <?= htmlspecialchars($movie['rating']) ?>/10</p>
                            <p><?= htmlspecialchars($movie['description']) ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Новые фильмы отсутствуют.</p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
