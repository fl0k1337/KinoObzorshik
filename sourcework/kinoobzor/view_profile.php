<?php
include 'db.php';
session_start();

if (!isset($_SESSION['role_name']) || $_SESSION['role_name'] !== 'admin') {
    header("Location: index.php?error=access_denied");
    exit;
}

$user_id = $_GET['id'] ?? null;
if (!$user_id) die("Пользователь не указан.");

try {
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) die("Пользователь не найден.");

    // Получаем отзывы
    $reviews_stmt = $db->prepare("
        SELECT r.*, m.title, m.id as movie_id
        FROM reviews r
        JOIN movies m ON r.movie_id = m.id
        WHERE r.user_id = ?
        ORDER BY r.created_at DESC
    ");
    $reviews_stmt->execute([$user_id]);
    $reviews = $reviews_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Получаем закладки
    $bookmarks_stmt = $db->prepare("
        SELECT m.* 
        FROM bookmarks b
        JOIN movies m ON b.movie_id = m.id
        WHERE b.user_id = ?
    ");
    $bookmarks_stmt->execute([$user_id]);
    $bookmarks = $bookmarks_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Получаем комментарии
    $comments_stmt = $db->prepare("
        SELECT c.*, m.title as movie_title, m.id as movie_id, r.id as review_id
        FROM comments c
        LEFT JOIN reviews r ON c.review_id = r.id
        LEFT JOIN movies m ON r.movie_id = m.id
        WHERE c.user_id = ?
        ORDER BY c.created_at DESC
    ");
    $comments_stmt->execute([$user_id]);
    $comments = $comments_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Ошибка базы данных: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Профиль: <?= htmlspecialchars($user['username']) ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="admin-profile-container">
        <!-- Информация о пользователе -->
        <div class="admin-profile-section admin-user-info">
            <h1><?= htmlspecialchars($user['username']) ?></h1>
            <div class="user-details-grid">
                <div class="user-detail-item">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value"><?= htmlspecialchars($user['email']) ?></span>
                </div>
                <div class="user-detail-item">
                    <span class="detail-label">Дата регистрации:</span>
                    <span class="detail-value"><?= $user['registration_date'] ?></span>
                </div>
                <div class="user-detail-item">
                    <span class="detail-label">Статус:</span>
                    <span class="detail-value"><?= $user['is_admin'] ? 'Администратор' : 'Пользователь' ?></span>
                </div>
                <div class="user-detail-item">
                    <span class="detail-label">Отзывов:</span>
                    <span class="detail-value"><?= count($reviews) ?></span>
                </div>
                <div class="user-detail-item">
                    <span class="detail-label">Комментариев:</span>
                    <span class="detail-value"><?= count($comments) ?></span>
                </div>
                <div class="user-detail-item">
                    <span class="detail-label">Закладок:</span>
                    <span class="detail-value"><?= count($bookmarks) ?></span>
                </div>
            </div>
        </div>

        <!-- Комментарии -->
        <div class="admin-profile-section">
            <h2>Комментарии (<?= count($comments) ?>)</h2>
            <?php if ($comments): ?>
                <div class="content-list">
                    <?php foreach ($comments as $comment): ?>
                        <div class="content-item">
                            <div class="item-content">
                                <p><?= htmlspecialchars($comment['comment_text']) ?></p>
                                <div class="item-meta">
                                    <?php if ($comment['movie_title']): ?>
                                        <span>Фильм: <a href="movie.php?movie_id=<?= $comment['movie_id'] ?>"><?= htmlspecialchars($comment['movie_title']) ?></a></span>
                                    <?php endif; ?>
                                    <span class="item-date"><?= $comment['created_at'] ?></span>
                                </div>
                            </div>
                            <form action="delete_comment.php" method="post" class="item-action">
                                <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <button type="submit" class="btn-delete" onclick="return confirm('Удалить комментарий?')">
                                    Удалить
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-content">Пользователь ещё не оставлял комментариев.</p>
            <?php endif; ?>
        </div>

        <!-- Отзывы -->
        <div class="admin-profile-section">
            <h2>Отзывы (<?= count($reviews) ?>)</h2>
            <?php if ($reviews): ?>
                <div class="content-list">
                    <?php foreach ($reviews as $review): ?>
                        <div class="content-item">
                            <div class="item-content">
                                <h3><a href="movie.php?movie_id=<?= $review['movie_id'] ?>"><?= htmlspecialchars($review['title']) ?></a></h3>
                                <div class="item-rating">Оценка: <?= $review['rating'] ?>/10</div>
                                <p><?= htmlspecialchars($review['review_text']) ?></p>
                                <div class="item-meta">
                                    <span class="item-date"><?= $review['created_at'] ?></span>
                                </div>
                            </div>
                            <form action="delete_review.php" method="post" class="item-action">
                                <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <button type="submit" class="btn-delete" onclick="return confirm('Удалить отзыв?')">
                                    Удалить
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-content">Пользователь ещё не оставлял отзывов.</p>
            <?php endif; ?>
        </div>

        <!-- Закладки -->
        <div class="admin-profile-section">
            <h2>Закладки (<?= count($bookmarks) ?>)</h2>
            <?php if ($bookmarks): ?>
                <div class="bookmark-list">
                    <?php foreach ($bookmarks as $bookmark): ?>
                        <div class="bookmark-item">
                            <a href="movie.php?movie_id=<?= $bookmark['id'] ?>">
                                <?= htmlspecialchars($bookmark['title']) ?> (<?= $bookmark['release_year'] ?>)
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-content">У пользователя нет закладок.</p>
            <?php endif; ?>
        </div>

        <!-- Опасная зона -->
        <div class="admin-profile-section danger-zone">
            <h2>Опасная зона</h2>
            <form action="delete_user.php" method="post" onsubmit="return confirm('Вы уверены? Это действие нельзя отменить!');">
                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                <button type="submit" class="btn-delete btn-danger">
                    Удалить этого пользователя
                </button>
            </form>
        </div>
    </div>
</body>
</html>