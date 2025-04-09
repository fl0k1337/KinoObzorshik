<?php
include 'db.php';
session_start();

if (!hasPermission('moderate_movies')) {
    die("У вас нет прав на модерацию фильмов");
}

$movie_id = $_GET['movie_id'] ?? null;

if (!$movie_id) {
    die("Фильм не найден");
}

// Исправленный запрос с правильным использованием prepare/execute/fetch
$stmt = $db->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->execute([$movie_id]);
$movie = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$movie) {
    die("Фильм не найден");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $year = $_POST['year'];
    $genre = $_POST['genre'];
    $description = $_POST['description'];
    $rating = $_POST['rating'];

    try {
        $stmt = $db->prepare("UPDATE movies SET 
        title = :title, 
        release_year = :year, 
        genre = :genre, 
        description = :description, 
        rating = :rating,
        created_at = :created_at
        WHERE id = :id");
        
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':genre', $genre);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':rating', $rating);
        $stmt->bindParam(':id', $movie_id);
        
        $stmt->execute();
        header("Location: admin_panel.php");
        exit;
    } catch (PDOException $e) {
        echo "Ошибка: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать фильм</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="admin-container">
        <h1>Редактировать фильм: <?= htmlspecialchars($movie['title']) ?></h1>
        
        <form method="POST" class="movie-edit-form">
            <div class="form-group">
                <label for="title">Название:</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($movie['title']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="year">Год выпуска:</label>
                <input type="number" id="year" name="year" value="<?= htmlspecialchars($movie['release_year']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="genre">Жанр:</label>
                <input type="text" id="genre" name="genre" value="<?= htmlspecialchars($movie['genre']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="rating">Рейтинг:</label>
                <input type="number" step="0.1" id="rating" name="rating" min="0" max="10" 
                       value="<?= htmlspecialchars($movie['rating']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="description">Описание:</label>
                <textarea id="description" name="description" rows="5" required><?= htmlspecialchars($movie['description']) ?></textarea>
            </div>
            
            <button type="submit" class="button-style">Сохранить изменения</button>
            <a href="admin_panel.php" class="button-style">Отмена</a>
        </form>
    </div>
</body>
</html>