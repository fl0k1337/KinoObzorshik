<?php
include 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$genre = $_GET['genre'] ?? "";
if ($genre) {
    $stmt = $db->prepare("SELECT *, DATE_FORMAT(created_at, '%d.%m.%Y %H:%i') as created_date FROM movies WHERE genre = :genre ORDER BY created_at DESC");
    $stmt->bindParam(':genre', $genre);
    $stmt->execute();
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "Жанр не выбран!";
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Фильмы жанра: <?= htmlspecialchars($genre) ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="genre-container">
        <h2>Фильмы жанра: <?= htmlspecialchars($genre) ?></h2>
        <?php if ($movies): ?>
            <ul class="movie-list">
                <?php foreach ($movies as $movie): ?>
                    <li class="movie-item">
                        <h3><a href="movie.php?movie_id=<?= htmlspecialchars($movie['id']) ?>"><?= htmlspecialchars($movie['title']) ?></a></h3>
                        <p>Год выпуска: <?= htmlspecialchars($movie['release_year']) ?></p>
                        <p>Рейтинг: <?= htmlspecialchars($movie['rating']) ?>/10</p>
                        <p><?= htmlspecialchars($movie['description']) ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Фильмы данного жанра не найдены.</p>
        <?php endif; ?>
    </div>
</body>
</html>
