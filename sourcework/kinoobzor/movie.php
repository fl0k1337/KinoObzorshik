<?php
include 'db.php';
session_start();
$movie_id = $_GET['movie_id'] ?? null;

if (!$movie_id) {
    echo "Фильм не найден.";
    exit;
}

// Получаем данные фильма
$stmt = $db->prepare("SELECT *, DATE_FORMAT(created_at, '%d.%m.%Y %H:%i') as formatted_date FROM movies WHERE id = :movie_id");
$stmt->bindParam(':movie_id', $movie_id, PDO::PARAM_INT);
$stmt->execute();
$movie = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$movie) {
    echo "Фильм не найден.";
    exit;
}

$stmt = $db->prepare("SELECT reviews.*, users.username FROM reviews JOIN users ON reviews.user_id = users.id WHERE movie_id = :movie_id ORDER BY reviews.created_at DESC");
$stmt->bindParam(':movie_id', $movie_id, PDO::PARAM_INT);
$stmt->execute();
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получаем комментарии для каждого отзыва
$comments = [];
if ($reviews) {
    $review_ids = array_column($reviews, 'id');
    $placeholders = implode(',', array_fill(0, count($review_ids), '?'));
    $stmt = $db->prepare("
        SELECT comments.*, users.username 
        FROM comments 
        JOIN users ON comments.user_id = users.id 
        WHERE review_id IN ($placeholders) 
        ORDER BY comments.created_at ASC
    ");
    $stmt->execute($review_ids);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Группируем комментарии по review_id для удобства
    $grouped_comments = [];
    foreach ($comments as $comment) {
        $grouped_comments[$comment['review_id']][] = $comment;
    }
}

$rating_counts = [
    '1-2' => 0,
    '3-4' => 0,
    '5-6' => 0,
    '7-8' => 0,
    '9-10' => 0
];

foreach ($reviews as $review) {
    $rating = $review['rating'];
    if ($rating >= 1 && $rating <= 2) {
        $rating_counts['1-2']++;
    } elseif ($rating >= 3 && $rating <= 4) {
        $rating_counts['3-4']++;
    } elseif ($rating >= 5 && $rating <= 6) {
        $rating_counts['5-6']++;
    } elseif ($rating >= 7 && $rating <= 8) {
        $rating_counts['7-8']++;
    } elseif ($rating >= 9 && $rating <= 10) {
        $rating_counts['9-10']++;
    }
}


if (!isset($_SESSION['user_id'])) {
    $auth_error = "Вы должны быть авторизованы, чтобы оставить отзыв.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        $error = "Вы должны быть авторизованы, чтобы оставить отзыв.";
    } else {
        $user_id = $_SESSION['user_id'];
        $rating = $_POST['rating'] ?? null;
        $review_text = $_POST['review_text'] ?? "";

        if (!$rating || empty($review_text)) {
            $error = "Пожалуйста, заполните все поля формы.";
        } else {
            try {
                $stmt = $db->prepare("INSERT INTO reviews (movie_id, user_id, rating, review_text) VALUES (:movie_id, :user_id, :rating, :review_text)");
                $stmt->bindParam(':movie_id', $movie_id, PDO::PARAM_INT);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
                $stmt->bindParam(':review_text', $review_text, PDO::PARAM_STR);
                if ($stmt->execute()) {
                    header("Location: movie.php?movie_id=$movie_id"); // Перенаправление на страницу фильма
                    exit;
                } else {
                    $error = "Ошибка: не удалось сохранить отзыв.";
                }
            } catch (PDOException $e) {
                $error = "Ошибка базы данных: " . $e->getMessage();
            }
        }
    }
}

if (isset($_SESSION['user_id'])) {
    $bookmark_stmt = $db->prepare("SELECT * FROM bookmarks WHERE user_id = :user_id AND movie_id = :movie_id");
    $bookmark_stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $bookmark_stmt->bindParam(':movie_id', $movie_id, PDO::PARAM_INT);
    $bookmark_stmt->execute();
    $is_bookmarked = $bookmark_stmt->rowCount() > 0;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($movie['title']) ?> - КиноОбзор</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="movie-container">
        <h1><?= htmlspecialchars($movie['title']) ?></h1>
        <p>Год выпуска: <?= htmlspecialchars($movie['release_year']) ?></p>
        <p>Рейтинг: <?= htmlspecialchars($movie['rating']) ?>/10</p>
        <div class="rating-summary">
            <canvas id="ratingChart" width="400" height="200"></canvas>
        </div>

        <p><?= htmlspecialchars($movie['description']) ?></p>
        <div class="movie-actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($is_bookmarked): ?>
                    <form action="remove_bookmark.php" method="post" style="display:inline;">
                        <input type="hidden" name="movie_id" value="<?= $movie_id ?>">
                        <button type="submit">Удалить из закладок</button>
                    </form>
                <?php else: ?>
                    <form action="add_bookmark.php" method="post" style="display:inline;">
                        <input type="hidden" name="movie_id" value="<?= $movie_id ?>">
                        <button type="submit">Добавить в закладки</button>
                    </form>
                <?php endif; ?>
            <?php else: ?>
                <p>Пожалуйста, <a href="login.php">войдите</a>, чтобы добавить фильм в закладки.</p>
            <?php endif; ?>
        </div>
        <?php if (isset($auth_error)): ?>
            <p style="color: red;"><?= $auth_error ?></p>
        <?php endif; ?>
        <?php if (isset($_SESSION['user_id'])): ?>
            <h2>Оставить отзыв</h2>
            <?php if (isset($error)): ?>
                <p style="color: red;"><?= $error ?></p>
            <?php endif; ?>
            <form action="movie.php?movie_id=<?= $movie_id ?>" method="post" class="review-form">
                <label for="rating">Оценка (1-10):</label>
                <input type="number" id="rating" name="rating" min="1" max="10" required>
                <label for="review_text">Ваш отзыв:</label>
                <textarea id="review_text" name="review_text" rows="4" required></textarea>
                <button type="submit">Отправить отзыв</button>
            </form>
        <?php else: ?>
            <p>Пожалуйста, <a href="login.php">войдите</a>, чтобы оставить отзыв.</p>
        <?php endif; ?>
        <h2>Отзывы</h2>
            <div class="reviews-container">
            <select id="sort" onchange="sortReviews()">
                <option value="newest">Новые</option>
                <option value="oldest">Старые</option>
                <option value="highest">Самый высокий рейтинг</option>
                <option value="lowest">Самый низкий рейтинг</option>
            </select>
                <?php if ($reviews): ?>
                    <ul class="review-list" id="review-list">
                        <?php foreach ($reviews as $review): ?>
                            <li class="review-item"> <!-- Изменил div на li для семантики -->
                                <div class="review-item-content">
                                    <strong><?= htmlspecialchars($review['username']) ?></strong>
                                    <p>Оценка: <?= htmlspecialchars($review['rating']) ?>/10</p>
                                    <p><?= htmlspecialchars($review['review_text']) ?></p>
                                    <small><?= htmlspecialchars($review['created_at']) ?></small>
                                </div>
                                
                                <div class="comments-section">
                                    <?php if (!empty($grouped_comments[$review['id']])): ?>
                                        <button class="toggle-comments" onclick="toggleComments(<?= $review['id'] ?>)">
                                            Комментарии (<?= count($grouped_comments[$review['id']]) ?>)
                                        </button>
                                        <div class="comments" id="comments-<?= $review['id'] ?>" style="display: none;">
                                            <ul class="comment-list">
                                                <?php foreach ($grouped_comments[$review['id']] as $comment): ?>
                                                    <li class="comment-item">
                                                        <strong><?= htmlspecialchars($comment['username']) ?></strong>
                                                        <p><?= htmlspecialchars($comment['comment_text']) ?></p>
                                                        <small><?= htmlspecialchars($comment['created_at']) ?></small>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php else: ?>
                                        <div class="no-comments">Комментариев пока нет</div>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <form action="add_comment.php" method="post" class="comment-form">
                                            <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                                            <input type="hidden" name="movie_id" value="<?= $movie_id ?>">
                                            <textarea name="comment_text" rows="2" placeholder="Напишите комментарий..." required></textarea>
                                            <button type="submit">Отправить</button>
                                        </form>
                                    <?php else: ?>
                                        <p><a href="login.php">Войдите</a>, чтобы оставить комментарий.</p>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Отзывы отсутствуют.</p>
                <?php endif; ?>
            </div>
    </div>
    <script src="script.js"></script>
    <script>
    const ctx = document.getElementById('ratingChart').getContext('2d');
    const ratingChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['1-2', '3-4', '5-6', '7-8', '9-10'],
            datasets: [{
                label: 'Количество отзывов',
                data: [<?= implode(', ', array_values($rating_counts)) ?>],
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
</body>
</html>
