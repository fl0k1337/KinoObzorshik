<?php
session_start();
include 'db.php';


if (!isset($_SESSION['role_name']) || $_SESSION['role_name'] !== 'admin') {
    header("Location: index.php?error=access_denied");
    exit;
}

if ($_SESSION['role_name'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Получение данных
try {
    $users = $db->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
    $reviews = $db->query("SELECT reviews.*, movies.title FROM reviews JOIN movies ON reviews.movie_id = movies.id")->fetchAll(PDO::FETCH_ASSOC);
    $comments = $db->query("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id")->fetchAll(PDO::FETCH_ASSOC);
    $movies = $db->query("SELECT *, DATE_FORMAT(created_at, '%d.%m.%Y %H:%i') as formatted_date FROM movies ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Ошибка базы данных: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ-панель</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="admin-container">
        <!-- Секция пользователей -->
        <section class="admin-section">
            <h2>Пользователи</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Имя</th>
                    <th>Email</th>
                    <th>Действия</th>
                    <th></th>
                </tr>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                        <select class="role-select" data-user-id="<?= $user['id'] ?>">
                    <?php 
                    $roles = $db->query("SELECT * FROM roles")->fetchAll();
                    foreach ($roles as $role): 
                    ?>
                    <option value="<?= $role['id'] ?>" <?= $user['role_id'] == $role['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($role['name']) ?>
                    </option>
                    <?php endforeach; ?>
                    </select>
                        <button class="save-role-btn" data-user-id="<?= $user['id'] ?>" data-current-user="<?= $_SESSION['user_id'] ?>">Сохранить</button>
                </td>
                <td>
                    <a href="view_profile.php?id=<?= $user['id'] ?>" class="profile-link">Просмотр</a>
                </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </section>

        <!-- Секция отзывов -->
        <section class="admin-section">
            <h2>Отзывы</h2>
            <?php foreach ($reviews as $review): ?>
            <div class="moderation-item">
                <p><?= htmlspecialchars($review['review_text']) ?></p>
                <small>Автор: <?= $review['user_id'] ?>, Фильм: <?= $review['title'] ?></small>
                <a href="delete_review.php?id=<?= $review['id'] ?>" onclick="return confirm('Удалить отзыв?')">Удалить</a>
            </div>
            <?php endforeach; ?>
        </section>

        <!-- Секция комментариев -->
        <section class="admin-section">
            <h2>Комментарии</h2>
            <?php foreach ($comments as $comment): ?>
            <div class="moderation-item">
                <p><?= htmlspecialchars($comment['comment_text']) ?></p>
                <small>Автор: <?= $comment['username'] ?></small>
                <a href="delete_comment.php?id=<?= $comment['id'] ?>" onclick="return confirm('Удалить комментарий?')">Удалить</a>
            </div>
            <?php endforeach; ?>
        </section>

        <!-- Секция фильмов -->
        <section class="admin-section">
            <div class="admin-section-header">
                <h2>Управление фильмами</h2>
                <a href="add_movie.php" class="button-style">Добавить новый фильм</a>
            </div>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Год</th>
                    <th>Жанр</th>
                    <th>Рейтинг</th>
                    <th>Дата добавления</th>
                    <th>Действия</th>
                </tr>
                <?php if (is_array($movies) && !empty($movies)): ?>
                    <?php foreach ($movies as $movie): ?>
                    <tr>
                        <td><?= htmlspecialchars($movie['id']) ?></td>
                        <td><?= htmlspecialchars($movie['title']) ?></td>
                        <td><?= htmlspecialchars($movie['release_year']) ?></td>
                        <td><?= htmlspecialchars($movie['genre']) ?></td>
                        <td><?= htmlspecialchars($movie['rating']) ?></td>
                        <td><?= htmlspecialchars($movie['formatted_date'] ?? 'N/A') ?></td>
                        <td>
                            <a href="edit_movie.php?movie_id=<?= $movie['id'] ?>" class="button-style">Редактировать</a>
                            <a href="delete_movie.php?movie_id=<?= $movie['id'] ?>" 
                               onclick="return confirm('Удалить фильм?')" 
                               class="button-style">Удалить</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">Фильмы не найдены</td>
                    </tr>
                <?php endif; ?>
            </table>
        </section>
    </div>
</body>
</html>