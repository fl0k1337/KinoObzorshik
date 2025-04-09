<?php
include 'db.php';
session_start();

if (!hasPermission('moderate_movies')) {
    die("У вас нет прав на удаление фильмов");
}

$movie_id = $_GET['movie_id'] ?? null;

if ($movie_id) {
    try {
        $db->prepare("DELETE FROM movies WHERE id = ?")->execute([$movie_id]);
        header("Location: admin_panel.php");
    } catch (PDOException $e) {
        echo "Ошибка: " . $e->getMessage();
    }
}
?>