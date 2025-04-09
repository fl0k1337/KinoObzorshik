-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Апр 05 2025 г., 16:43
-- Версия сервера: 8.0.30
-- Версия PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `kinoobzor`
--

DELIMITER $$
--
-- Процедуры
--
CREATE DEFINER=`root`@`%` PROCEDURE `AddUser` (IN `p_username` VARCHAR(100), IN `p_email` VARCHAR(100), IN `p_password` VARCHAR(255))   BEGIN
    INSERT INTO users (username, email, password, registration_date)
    VALUES (p_username, p_email, p_password, NOW());
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Структура таблицы `bookmarks`
--

CREATE TABLE `bookmarks` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `movie_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `bookmarks`
--

INSERT INTO `bookmarks` (`id`, `user_id`, `movie_id`, `created_at`) VALUES
(12, 18, 11, '2025-04-05 13:21:09');

-- --------------------------------------------------------

--
-- Структура таблицы `comments`
--

CREATE TABLE `comments` (
  `id` int NOT NULL,
  `review_id` int NOT NULL,
  `user_id` int NOT NULL,
  `comment_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `comments`
--

INSERT INTO `comments` (`id`, `review_id`, `user_id`, `comment_text`, `created_at`) VALUES
(8, 6, 16, 'qwe', '2025-04-02 10:40:29'),
(9, 9, 19, 'qwe', '2025-04-05 13:31:58');

-- --------------------------------------------------------

--
-- Структура таблицы `comment_ratings`
--

CREATE TABLE `comment_ratings` (
  `id` int NOT NULL,
  `comment_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `movies`
--

CREATE TABLE `movies` (
  `id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `genre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `release_year` int NOT NULL,
  `rating` decimal(3,1) NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `movies`
--

INSERT INTO `movies` (`id`, `title`, `genre`, `release_year`, `rating`, `description`, `created_at`) VALUES
(1, 'Интерстеллар', 'Фантастика', 2014, '8.6', 'Фильм о путешествии через червоточину в поисках нового дома для человечества.', '2025-04-03 09:30:11'),
(2, 'Начало', 'Фантастика', 2010, '8.8', 'Фильм о воре, который может проникать в подсознание людей и красть их идеи.', '2025-04-03 09:30:11'),
(3, 'Темный рыцарь', 'Боевик', 2008, '9.0', 'Фильм о Бэтмене, который сталкивается с Джокером, хаотичным преступником.', '2025-04-03 09:30:11'),
(5, 'Крестный отец', 'Драма', 1972, '9.2', 'Фильм о мафиозной семье Корлеоне и их борьбе за власть.', '2025-04-03 09:30:11'),
(6, 'Криминальное чтиво', 'Триллер', 1994, '8.9', 'Фильм о переплетении историй нескольких преступников в Лос-Анджелесе.', '2025-04-03 09:30:11'),
(7, 'Форрест Гамп', 'Драма', 1994, '8.8', 'Фильм о жизни Форреста Гампа, человека с низким IQ, который достигает великих успехов.', '2025-04-03 09:30:11'),
(8, 'Список Шиндлера', 'Драма', 1993, '9.0', 'Фильм о немецком промышленнике, который спасает более тысячи евреев во время Холокоста.', '2025-04-03 09:30:11'),
(9, 'Бойцовский клуб', 'Триллер', 1999, '8.8', 'Фильм о мужчине, который создает подпольный бойцовский клуб и сталкивается с собственным альтер эго.', '2025-04-03 09:30:11'),
(10, 'Гладиатор', 'Боевик', 2000, '8.5', 'Фильм о римском генерале, который становится гладиатором и борется за месть.', '2025-04-03 09:30:11'),
(11, 'Властелин колец: Возвращение короля', 'Фэнтези', 2003, '9.0', 'Фильм о заключительной битве за Средиземье между силами добра и зла.', '2025-04-03 09:30:11'),
(12, 'Храброе сердце', 'Драма', 1995, '8.4', 'Фильм о шотландском повстанце Уильяме Уоллесе, который борется за независимость Шотландии.', '2025-04-03 09:30:11'),
(13, 'Леон', 'Триллер', 1994, '8.5', 'Фильм о профессиональном убийце, который берет под опеку молодую девушку.', '2025-04-03 09:30:11'),
(14, 'Семь', 'Триллер', 1995, '8.6', 'Фильм о двух детективах, которые расследуют серию убийств, основанных на семи смертных грехах.', '2025-04-03 09:30:11');

-- --------------------------------------------------------

--
-- Структура таблицы `reviews`
--

CREATE TABLE `reviews` (
  `id` int NOT NULL,
  `movie_id` int NOT NULL,
  `user_id` int NOT NULL,
  `rating` int DEFAULT NULL,
  `review_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `reviews`
--

INSERT INTO `reviews` (`id`, `movie_id`, `user_id`, `rating`, `review_text`, `created_at`) VALUES
(6, 7, 16, 7, 'qwe', '2025-04-02 10:40:26'),
(7, 6, 16, 5, 'уцйцйу', '2025-04-03 09:51:30'),
(8, 11, 18, 5, '213', '2025-04-05 13:23:05'),
(9, 5, 19, 6, 'qwe', '2025-04-05 13:31:51');

-- --------------------------------------------------------

--
-- Структура таблицы `roles`
--

CREATE TABLE `roles` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `permissions` text NOT NULL COMMENT 'JSON с правами'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `roles`
--

INSERT INTO `roles` (`id`, `name`, `permissions`) VALUES
(1, 'admin', '{\"all\": true}'),
(2, 'moderator', '{\"moderate_movies\": true}'),
(3, 'user', '{\"write_reviews\": true, \"write_comments\": true, \"add_bookmarks\": true}');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `registration_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `is_admin` tinyint(1) DEFAULT '0',
  `role_id` int NOT NULL DEFAULT '3'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `registration_date`, `is_admin`, `role_id`) VALUES
(3, 'testuser', 'test@gmail.com', '$2y$10$L72H4SIIhusIJK/Huaq6peEetgwqphyGX.gT1osVg/LACQEi6pqyu', '2024-11-28 12:15:47', 0, 3),
(4, 'testuser', 'testuser@gmail.com', '$2y$10$4p45s.ecG8qW1gUogeknUO5a1H.GooI/pStweahsIPwElY636FX9i', '2024-11-28 12:15:47', 0, 3),
(5, 'testuser', 'test@mail.ru', '$2y$10$7WbEfUQiGRjtuqpGYqLJE.Im3zgyYo1/s02JUrPczw00G0UnZDUau', '2024-12-04 11:05:02', 0, 3),
(6, 'testuser', 'pobedildo@gmail.com', '$2y$10$1eF7YRwnFtEyxYWcDuY/NuawhBfky.QusuZkhXhhBLcx6GFzZVYxa', '2024-12-04 11:05:48', 0, 3),
(7, 'Reygor', 'testurl@gmail.com', '$2y$10$hqGOphZenzvFypvFoWILC.F9FCm5lWl3bRVg7t8W/gcZ0ucNUgJe.', '2024-12-11 10:17:54', 0, 3),
(9, 'penis', 'penis@gmail.com', '123123', '2024-12-11 11:05:43', 0, 3),
(10, '123', '213@f.c', 'авыаыв', '2025-03-13 11:35:08', 0, 3),
(12, '1235', '123@qwe.com', '123', '2025-03-13 11:37:15', 0, 3),
(13, '123455', '12@m.n', '$2y$10$rqh67mQB9uCyDTuzlMrUFuXxaPUgIVFEe3JlOcw.TcBCuw7eqOlja', '2025-03-13 11:57:55', 0, 3),
(14, '111', '112@a.a', '$2y$10$qATFmg842Zto3w2EODr7PekVDuWOzA/NoVT9r8M9YEDvbwh49027a', '2025-03-18 10:59:04', 0, 3),
(15, 'wqeqwe', 'ewq@wq.ewq', '$2y$10$Dv7jeei/w0bLVaRubCA.8e3VDH9wXa9Kinib.Us.YwZ1HvtrhP08.', '2025-03-27 10:25:14', 0, 3),
(16, 'Penisito', 'qwerty@gmail.com', '$2y$10$eTfDiFsofsZfbFe5RzvrqOcA7UhnAnxsHOqHKsa/2A/ydk2.Zecii', '2025-04-02 10:14:17', 1, 1),
(18, 'Qweeqwqwew', '123@das.f', '$2y$10$bkViOkqnlvxPr8I.B2wJSO0e5ZSLQKjKBAwOQF5PkHkfvevNVXuu6', '2025-04-05 16:18:19', 0, 3),
(19, 'eqwqwe', 'ewqewq@weqqew.q', '$2y$10$yNzTgkrp2lV7hx7W5DA0wukUEGGZCwsRPrRqNQpcN6y2ilbfhZ43m', '2025-04-05 16:26:30', 0, 1),
(20, 'qweeqwqwe', 'ewqwqw@eqw.q', '$2y$10$Texo4fSAEU24sjG8Ls6c3OEEiEUSCpJhz7GdztmXEQAq9DVcJGZ3y', '2025-04-05 16:36:04', 0, 3),
(21, 'weqeqweq', 'eqwqew@dsa.a', '$2y$10$3ggFBsZurnea5S3OQXq/G.Qw/lnprznDIdzrQvfHzPMXlsgN1eLMu', '2025-04-05 16:42:42', 0, 3);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`movie_id`),
  ADD KEY `movie_id` (`movie_id`);

--
-- Индексы таблицы `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `review_id` (`review_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `comment_ratings`
--
ALTER TABLE `comment_ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comment_id` (`comment_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movie_id` (`movie_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `bookmarks`
--
ALTER TABLE `bookmarks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `comment_ratings`
--
ALTER TABLE `comment_ratings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT для таблицы `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD CONSTRAINT `bookmarks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookmarks_ibfk_2` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `comment_ratings`
--
ALTER TABLE `comment_ratings`
  ADD CONSTRAINT `comment_ratings_ibfk_1` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`),
  ADD CONSTRAINT `comment_ratings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
