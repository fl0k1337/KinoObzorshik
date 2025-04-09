<header>
    <link rel="stylesheet" href="styles.css">
    <div class="top-bar">
        <div class="logo-search">
            <a href="index.php" class="logo">
                <span class="logo-cinema">Кино</span><span class="logo-review">Обзор</span>
            </a>
            <!-- Строка поиска -->
            <div class="search-bar">
                <form action="search.php" method="get">
                    <input type="text" name="query" placeholder="Поиск фильмов..." required>
                    <button type="submit">Поиск</button>
                </form>
            </div>
        </div>
        <div class="auth-section">
        <?php if (isset($_SESSION['username'])): ?>
        <div class="user-panel">
            <span>Привет, <?= htmlspecialchars($_SESSION['username']) ?>!</span>
            <a href="profile.php" class="button-style">Профиль</a>
            <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'admin'): ?>
                <a href="admin_panel.php" class="button-style admin-btn">Админ-панель</a>
            <?php endif; ?>
            <a href="logout.php" class="button-style">Выход</a>
        </div>
            <?php else: ?>
                <a href="login.php" class="button-style">Войти</a>
                <a href="register.php" class="button-style">Регистрация</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="genre-bar">
        <a href="genre.php?genre=Боевик">Боевик</a>
        <a href="genre.php?genre=Драма">Драма</a>
        <a href="genre.php?genre=Фэнтези">Фэнтези</a>
        <a href="genre.php?genre=Триллер">Триллер</a>
        <a href="genre.php?genre=Фантастика">Фантастика</a>
        <a href="genre.php?genre=Ужасы">Ужасы</a>
        <a href="genre.php?genre=Комедия">Комедия</a>
    </div>
</header>
<footer id="footer" class="footer-hidden">
    <div class="footer-container">
        <p>&copy; 2024 КиноОбзор. Все права защищены.</p>
        <div class="social-links">
            <a href="https://t.me/misterpenisito" target="_blank">Telegram</a>
            <a href="https://vk.com/fl0k1337" target="_blank">ВКонтакте</a>
        </div>
    </div>
</footer>
<script src="script.js"></script>
