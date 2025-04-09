<?php
include 'db.php';
session_start();

if (!hasPermission('moderate_movies')) {
    die("У вас нет прав на добавление фильмов");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $year = trim($_POST['year']);
    $genre = trim($_POST['genre']);
    $description = trim($_POST['description']);
    $rating = trim($_POST['rating']);
    $created_at = date('Y-m-d H:i:s'); // Текущая дата и время

    // Валидация данных
    if (empty($title) || empty($year) || empty($genre) || empty($description) || empty($rating)) {
        $error = 'Все поля обязательны для заполнения';
    } elseif (!is_numeric($year) || $year < 1900 || $year > date('Y') + 5) {
        $error = 'Некорректный год выпуска';
    } elseif (!is_numeric($rating) || $rating < 0 || $rating > 10) {
        $error = 'Рейтинг должен быть от 0 до 10';
    } else {
        try {
            $stmt = $db->prepare("INSERT INTO movies 
                (title, release_year, genre, description, rating, created_at) 
                VALUES (:title, :year, :genre, :description, :rating, :created_at)");
            
            $stmt->execute([
                ':title' => $title,
                ':year' => $year,
                ':genre' => $genre,
                ':description' => $description,
                ':rating' => $rating,
                ':created_at' => $created_at
            ]);
            
            $success = 'Фильм успешно добавлен!';
            $_POST = array();
        } catch (PDOException $e) {
            $error = 'Ошибка при добавлении фильма: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить новый фильм</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="admin-container">
        <h1>Добавить новый фильм</h1>
        
        <?php if ($error): ?>
            <div class="error-message"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-message"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" class="movie-form">
            <div class="form-group">
                <label for="title">Название фильма:</label>
                <input type="text" id="title" name="title" 
                       value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="year">Год выпуска:</label>
                <input type="number" id="year" name="year" 
                       value="<?= htmlspecialchars($_POST['year'] ?? '') ?>" 
                       min="1900" max="<?= date('Y') + 5 ?>" required>
            </div>

            <div class="form-group">
                <label for="genre">Жанр:</label>
                <input type="text" id="genre" name="genre" 
                       value="<?= htmlspecialchars($_POST['genre'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="rating">Рейтинг (0-10):</label>
                <input type="number" id="rating" name="rating" 
                       value="<?= htmlspecialchars($_POST['rating'] ?? '') ?>" 
                       step="0.1" min="0" max="10" required>
            </div>

            <div class="form-group">
                <label for="description">Описание:</label>
                <textarea id="description" name="description" 
                          rows="5" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="button-style">Добавить фильм</button>
                <a href="admin_panel.php" class="button-style">Назад в админ-панель</a>
            </div>
        </form>
    </div>
</body>
</html>