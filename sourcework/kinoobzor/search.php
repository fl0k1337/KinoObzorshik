<?php
include 'db.php';
session_start();
// Получаем поисковый запрос
$search_query = $_GET['query'] ?? "";
// Выполняем поиск фильмов
if ($search_query) {
    $stmt = $db->prepare("SELECT *, DATE_FORMAT(created_at, '%d.%m.%Y %H:%i') as created_date FROM movies WHERE title LIKE :search_query ORDER BY created_at DESC");
    $stmt->bindValue(':search_query', '%' . $search_query . '%', PDO::PARAM_STR);
    $stmt->execute();
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $movies = [];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Результаты поиска</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="search-results">
        <h2>Результаты поиска для: "<?= htmlspecialchars($search_query) ?>"</h2>
        <?php if ($movies): ?>
            <ul>
                <?php foreach ($movies as $movie): ?>
                    <li>
                        <a href="movie.php?movie_id=<?= htmlspecialchars($movie['id']) ?>">
                            <?= htmlspecialchars($movie['title']) ?> (<?= htmlspecialchars($movie['release_year']) ?>)
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Фильмы не найдены.</p>
        <?php endif; ?>
    </div>
</body>
</html>
