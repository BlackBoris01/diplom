-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Апр 05 2025 г., 13:03
-- Версия сервера: 8.0.30
-- Версия PHP: 8.3.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `mfc`
--

-- --------------------------------------------------------

--
-- Структура таблицы `pay`
--

CREATE TABLE `pay` (
  `id` int UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `pay`
--

INSERT INTO `pay` (`id`, `title`) VALUES
(1, 'Налчинка'),
(2, 'Картой');

-- --------------------------------------------------------

--
-- Структура таблицы `request`
--

CREATE TABLE `request` (
  `id` int UNSIGNED NOT NULL,
  `authorId` int UNSIGNED NOT NULL,
  `payId` int UNSIGNED NOT NULL,
  `statusId` int UNSIGNED NOT NULL,
  `serviceId` int UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `photoUrl` varchar(1000) DEFAULT NULL,
  `denyReason` varchar(1000) DEFAULT NULL,
  `completePhotoUrl` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `request`
--

INSERT INTO `request` (`id`, `authorId`, `payId`, `statusId`, `serviceId`, `date`, `time`, `email`, `phone`, `photoUrl`, `denyReason`, `completePhotoUrl`) VALUES
(1, 6, 2, 1, 2, '2025-02-13', '12:04:00', '7527945@gmail.com', '9216163559', NULL, NULL, NULL),
(2, 6, 1, 1, 1, '2025-02-23', '16:04:00', '234243@gmail.com', '92146163559', NULL, NULL, NULL),
(3, 7, 2, 2, 2, '2025-02-13', '12:04:00', '7527945@gmail.com', '9216sdf163559', NULL, NULL, NULL),
(4, 7, 1, 1, 2, '2025-02-23', '16:04:00', '234123123243@gmail.com', '92146163559', NULL, NULL, NULL),
(5, 6, 2, 1, 2, '2025-02-13', '12:04:00', '7527945@gmail.com', '9216163559', NULL, NULL, NULL),
(6, 6, 1, 1, 1, '2025-02-23', '16:04:00', '234243@gmail.com', '92146163559', NULL, NULL, NULL),
(7, 6, 2, 1, 2, '2025-02-13', '12:04:00', '7527945@gmail.com', '9216sdf163559', NULL, NULL, NULL),
(8, 6, 1, 1, 2, '2025-02-23', '16:04:00', '234123123243@gmail.com', '92146163559', NULL, NULL, NULL),
(9, 6, 2, 1, 2, '2025-02-13', '12:04:00', '7527945@gmail.com', '9216163559', NULL, NULL, NULL),
(10, 6, 1, 1, 1, '2025-02-23', '16:04:00', '234243@gmail.com', '92146163559', NULL, NULL, NULL),
(11, 7, 2, 1, 2, '2025-02-13', '12:04:00', '7527945@gmail.com', '9216sdf163559', NULL, NULL, NULL),
(12, 7, 1, 1, 2, '2025-02-23', '16:04:00', '234123123243@gmail.com', '92146163559', NULL, NULL, NULL),
(13, 6, 2, 1, 2, '2025-02-13', '12:04:00', '7527945@gmail.com', '9216163559', NULL, NULL, NULL),
(14, 6, 1, 1, 1, '2025-02-23', '16:04:00', '234243@gmail.com', '92146163559', NULL, NULL, NULL),
(15, 6, 2, 1, 2, '2025-02-13', '12:04:00', '7527945@gmail.com', '9216sdf163559', NULL, NULL, NULL),
(16, 6, 1, 1, 2, '2025-02-23', '16:04:00', '234123123243@gmail.com', '92146163559', NULL, NULL, NULL),
(17, 6, 1, 1, 1, '2025-02-14', '12:59:00', '7527945@gmail.com', '9216163559', 'uploads/Ax6vi8PiTtw.jpg', NULL, NULL),
(18, 6, 1, 2, 1, '2025-02-23', '16:06:00', '7527945@gmail.com', '9216163559', 'uploads/5FsWW0HSxnjJaDADqdsSep6Al0tGzlJv.png', NULL, NULL),
(21, 6, 2, 2, 2, '2025-02-18', '02:08:00', '7527945@gmail.com', '9216163559', 'uploads/ps9kdHod1ehp8vQ-ZG8BhbKGCZ6aiGKg.jpg', NULL, NULL),
(24, 6, 2, 2, 1, '2025-02-21', '10:31:00', 'timoti-skl@mail.ru', '9216163559', NULL, NULL, NULL),
(25, 6, 2, 2, 2, '2025-02-18', '10:32:00', 'timoti-skl@mail.ru', '9216163559', NULL, NULL, NULL),
(26, 6, 1, 3, 1, '2025-02-18', '10:36:00', '7527945@gmail.com', '9216163559', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `service`
--

CREATE TABLE `service` (
  `id` int UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `service`
--

INSERT INTO `service` (`id`, `title`) VALUES
(1, 'Замена паспорта'),
(2, 'Получение справки'),
(3, 'asdadsads');

-- --------------------------------------------------------

--
-- Структура таблицы `status`
--

CREATE TABLE `status` (
  `id` int UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `status`
--

INSERT INTO `status` (`id`, `title`) VALUES
(1, 'Новая'),
(2, 'В работе '),
(3, 'Выполнено'),
(4, 'Отменено');

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE `user` (
  `id` int UNSIGNED NOT NULL,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullName` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `authKey` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `photoUrl` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `login`, `password`, `fullName`, `role`, `authKey`, `email`, `phone`, `photoUrl`) VALUES
(6, '1', '$2y$13$scFX7tqUr47/b0pnYLrAEOctq0X52ux3ghiwC0V04uNSkaHPHYwua', 'А а', '0', 'B5-_baCfPLDhw-45zXkVtJStGqdIqenh', '7527945@gmail.com', '+7(921)-616-37-58', 'uploads/photo_2023-04-16_16-10-29.jpg'),
(7, 'admin', '$2y$13$kRsTqoDv2FfrQJUtWh0OruuCLeZyqB.K/9B/KXD9BjWrHGte.5goS', 'а а', '1', '-rW3KE6ZXOU2P42ChA_3B_atjWJjfFlX', 'timoti-sk@mfail.ru', '+7(929)-615-37-55', 'uploads/photo_2024-04-29_12-17-08.jpg');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `pay`
--
ALTER TABLE `pay`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `request`
--
ALTER TABLE `request`
  ADD PRIMARY KEY (`id`),
  ADD KEY `authorId` (`authorId`),
  ADD KEY `payId` (`payId`),
  ADD KEY `request_ibfk_3` (`serviceId`),
  ADD KEY `statusId` (`statusId`);

--
-- Индексы таблицы `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `pay`
--
ALTER TABLE `pay`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `request`
--
ALTER TABLE `request`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT для таблицы `service`
--
ALTER TABLE `service`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `status`
--
ALTER TABLE `status`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `request`
--
ALTER TABLE `request`
  ADD CONSTRAINT `request_ibfk_1` FOREIGN KEY (`authorId`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `request_ibfk_2` FOREIGN KEY (`payId`) REFERENCES `pay` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `request_ibfk_3` FOREIGN KEY (`serviceId`) REFERENCES `service` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `request_ibfk_4` FOREIGN KEY (`statusId`) REFERENCES `status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
