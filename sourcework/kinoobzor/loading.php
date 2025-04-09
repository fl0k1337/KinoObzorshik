<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Загрузка</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #E5E5DB;
            font-family: 'Roboto', sans-serif;
        }
        .loader {
            border: 16px solid #f3f3f3;
            border-top: 16px solid #DDB16B;
            border-radius: 50%;
            width: 120px;
            height: 120px;
            animation: spin 2s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="loader"></div>
    <script>
        // Перенаправление на главную страницу через 5-8 секунд
        setTimeout(function() {
            window.location.href = 'index.php';
        }, Math.floor(Math.random() * (8 - 5 + 1)) + 5 * 1000); // 5-8 секунд
    </script>
</body>
</html>
