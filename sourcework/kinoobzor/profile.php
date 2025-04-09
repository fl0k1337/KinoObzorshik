<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Получаем данные пользователя
$stmt = $db->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Получаем количество отзывов
$review_stmt = $db->prepare("SELECT COUNT(*) AS review_count FROM reviews WHERE user_id = :user_id");
$review_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$review_stmt->execute();
$review_count = $review_stmt->fetch(PDO::FETCH_ASSOC)['review_count'];

// Получаем отзывы пользователя
$review_stmt = $db->prepare("
    SELECT reviews.*, movies.title AS movie_title, movies.id AS movie_id
    FROM reviews
    JOIN movies ON reviews.movie_id = movies.id
    WHERE reviews.user_id = :user_id
    ORDER BY reviews.created_at DESC
");
$review_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$review_stmt->execute();
$user_reviews = $review_stmt->fetchAll(PDO::FETCH_ASSOC);

// Получаем комментарии пользователя
$comment_stmt = $db->prepare("
    SELECT comments.*, movies.title AS movie_title, movies.id AS movie_id, 
           reviews.id AS review_id, reviews.review_text AS review_text
    FROM comments
    JOIN reviews ON comments.review_id = reviews.id
    JOIN movies ON reviews.movie_id = movies.id
    WHERE comments.user_id = :user_id
    ORDER BY comments.created_at DESC
");
$comment_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$comment_stmt->execute();
$user_comments = $comment_stmt->fetchAll(PDO::FETCH_ASSOC);

// Получаем количество комментариев
$comment_count = count($user_comments);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Профиль - <?= htmlspecialchars($user['username']) ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="profile-container">
        <div class="profile-main">
            <div class="profile-info">
                <h2>Профиль пользователя</h2>
                <p>Имя пользователя: <?= htmlspecialchars($user['username']) ?></p>
                <p>Email: <?= htmlspecialchars($user['email']) ?></p>
                <p>Дата регистрации: <?= htmlspecialchars($user['registration_date']) ?></p>
                <p>Количество отзывов: <?= htmlspecialchars($review_count) ?></p>
                <p>Количество комментариев: <?= htmlspecialchars($comment_count) ?></p>
                <a href="edit_profile.php" class="button-style">Редактировать профиль</a>
                <a href="bookmarks.php" class="button-style">Мои закладки</a>
            </div>

            <!-- Секция "Мои комментарии" -->
            <div class="profile-section">
                <h3>Мои комментарии</h3>
                <?php if ($user_comments): ?>
                    <ul class="comment-list">
                        <?php foreach ($user_comments as $comment): ?>
                            <li class="comment-item">
                                <div class="comment-meta">
                                    <span class="comment-movie">
                                        Фильм: <a href="movie.php?movie_id=<?= $comment['movie_id'] ?>">
                                            <?= htmlspecialchars($comment['movie_title']) ?>
                                        </a>
                                    </span>
                                    <span class="comment-date">
                                        <?= date('d.m.Y H:i', strtotime($comment['created_at'])) ?>
                                    </span>
                                </div>
                                <div class="comment-review">
                                    <strong>К отзыву:</strong>
                                    <?= htmlspecialchars(mb_substr($comment['review_text'], 0, 100)) ?>...
                                </div>
                                <div class="comment-text">
                                    <?= htmlspecialchars($comment['comment_text']) ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Вы еще не оставляли комментариев.</p>
                <?php endif; ?>
            </div>

            <!-- Секция "Мои отзывы" -->
            <div class="profile-section">
                <h3>Мои отзывы</h3>
                <?php if ($user_reviews): ?>
                    <ul class="review-list">
                        <?php foreach ($user_reviews as $review): ?>
                            <li class="review-item">
                                <strong><a href="movie.php?movie_id=<?= htmlspecialchars($review['movie_id']) ?>">
                                    <?= htmlspecialchars($review['movie_title']) ?>
                                </a></strong>
                                <p>Оценка: <?= htmlspecialchars($review['rating']) ?>/10</p>
                                <p><?= htmlspecialchars($review['review_text']) ?></p>
                                <small>Дата: <?= htmlspecialchars($review['created_at']) ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Вы еще не оставляли отзывы.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>