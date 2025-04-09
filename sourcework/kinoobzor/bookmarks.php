<?php
include 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];
// Получаем закладки пользователя
$stmt = $db->prepare("
    SELECT movies.*
    FROM bookmarks
    JOIN movies ON bookmarks.movie_id = movies.id
    WHERE bookmarks.user_id = :user_id
");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$bookmarks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Мои закладки</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="bookmarks-container">
    <h2>Мои закладки</h2>
    
    <?php if ($bookmarks): ?>
        <ul class="bookmark-list">
            <?php foreach ($bookmarks as $bookmark): ?>
                <li class="bookmark-item">
                    <a href="movie.php?movie_id=<?= htmlspecialchars($bookmark['id']) ?>">
                        <?= htmlspecialchars($bookmark['title']) ?>
                    </a>
                    <div class="bookmark-meta">
                        <span>Год: <?= htmlspecialchars($bookmark['release_year']) ?></span>
                        <span>Рейтинг: <?= htmlspecialchars($bookmark['rating']) ?>/10</span>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="no-bookmarks">
            <p>У вас пока нет закладок.</p>
            <p>Найдите интересный фильм и добавьте его в закладки!</p>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
