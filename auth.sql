-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 21, 2025 at 01:16 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `auth`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(11) NOT NULL,
  `password` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `author` varchar(100) DEFAULT NULL,
  `available` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `available`) VALUES
(1, 'Pride and Prejudice', 'Jane Austen', 1),
(2, 'Moby-Dick', 'Herman Melville', 1),
(3, 'War and Peace', 'Leo Tolstoy', 1),
(4, 'The Catcher in the Rye', 'J.D. Salinger', 1),
(5, 'The Hobbit', 'J.R.R. Tolkien', 1),
(6, 'Brave New World', 'Aldous Huxley', 1),
(7, 'The Odyssey', 'Homer', 1),
(8, 'Crime and Punishment', 'Fyodor Dostoevsky', 1),
(9, 'The Divine Comedy', 'Dante Alighieri', 1),
(10, 'Frankenstein', 'Mary Shelley', 1),
(11, 'Jane Eyre', 'Charlotte Bront?', 1),
(12, 'The Lord of the Rings', 'J.R.R. Tolkien', 1),
(13, 'The Iliad', 'Homer', 1),
(14, 'Catch-22', 'Joseph Heller', 1),
(15, 'Animal Farm', 'George Orwell', 1),
(16, 'The Brothers Karamazov', 'Fyodor Dostoevsky', 1),
(17, 'The Adventures of Huckleberry Finn', 'Mark Twain', 1),
(18, 'The Great Expectations', 'Charles Dickens', 1),
(19, 'Les Mis?rables', 'Victor Hugo', 1),
(20, 'Dracula', 'Bram Stoker', 1);

-- --------------------------------------------------------

--
-- Table structure for table `borrowed_books`
--

CREATE TABLE `borrowed_books` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `book_id` int(11) DEFAULT NULL,
  `borrow_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrowed_books`
--

INSERT INTO `borrowed_books` (`id`, `user_id`, `book_id`, `borrow_date`) VALUES
(0, 4, 10, '2025-04-21'),
(1, 9, 2, '2024-11-03'),
(2, 9, 1, '2024-11-03'),
(3, 9, 3, '2024-11-03'),
(5, 13, 12, '2024-11-04'),
(6, 11, 6, '2024-11-04'),
(7, 16, 4, '2024-11-08'),
(9, 18, 9, '2024-11-11'),
(10, 18, 5, '2024-11-11'),
(11, 18, 7, '2024-11-11');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `ip_address` varchar(20) NOT NULL,
  `attempt_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `username`, `ip_address`, `attempt_time`) VALUES
(1, 'vevien', '::1', '2025-04-21 13:58:23'),
(2, 'vevien', '::1', '2025-04-21 13:58:27'),
(3, 'vevien', '::1', '2025-04-21 13:59:40'),
(4, 'vevien', '::1', '2025-04-21 14:04:17'),
(5, 'vevien', '::1', '2025-04-21 14:04:21'),
(6, 'vevien', '::1', '2025-04-21 14:22:30'),
(7, 'vevien', '::1', '2025-04-21 14:22:32'),
(8, 'vevien', '::1', '2025-04-21 14:22:35'),
(9, 'vevien', '::1', '2025-04-21 14:26:51'),
(10, 'vevien', '::1', '2025-04-21 14:26:56'),
(11, 'vevien', '::1', '2025-04-21 14:26:58'),
(12, 'vevien', '::1', '2025-04-21 14:27:01'),
(13, 'vevien', '::1', '2025-04-21 14:27:07'),
(14, 'vevien', '::1', '2025-04-21 14:35:22'),
(15, 'vevien', '::1', '2025-04-21 14:35:25'),
(16, 'vevien', '::1', '2025-04-21 14:35:27'),
(17, 'vevien', '::1', '2025-04-21 14:40:51'),
(18, 'vevien', '::1', '2025-04-21 14:43:20'),
(19, 'vevien', '::1', '2025-04-21 14:43:23'),
(20, 'vevien', '::1', '2025-04-21 14:43:25'),
(21, 'welmar', '::1', '2025-04-21 14:51:59'),
(22, 'welmar', '::1', '2025-04-21 14:52:02'),
(23, 'welmar', '::1', '2025-04-21 14:52:04'),
(24, 'welmar', '::1', '2025-04-21 14:52:08'),
(25, 'welmar', '::1', '2025-04-21 14:57:00'),
(26, 'welmar', '::1', '2025-04-21 14:57:10'),
(27, 'welmar', '::1', '2025-04-21 14:58:11'),
(28, 'rena', '::1', '2025-04-21 15:01:40'),
(29, 'rena', '::1', '2025-04-21 15:01:50'),
(30, 'rena', '::1', '2025-04-21 15:02:47'),
(31, 'rena', '::1', '2025-04-21 15:02:57'),
(32, 'rena', '::1', '2025-04-21 15:06:47'),
(33, 'rena', '::1', '2025-04-21 15:06:57'),
(34, 'rena', '::1', '2025-04-21 15:07:50'),
(35, 'rena', '::1', '2025-04-21 15:11:55'),
(36, 'rena', '::1', '2025-04-21 15:13:40'),
(37, 'rena', '::1', '2025-04-21 15:13:50'),
(38, 'rena', '::1', '2025-04-21 15:20:01'),
(39, 'rena', '::1', '2025-04-21 15:20:11'),
(40, 'rena', '::1', '2025-04-21 18:11:52'),
(41, 'vevien', '::1', '2025-04-21 18:54:43'),
(42, 'maricel', '::1', '2025-04-21 19:00:52');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `reset_token` varchar(100) DEFAULT NULL,
  `requested_at` datetime DEFAULT current_timestamp(),
  `is_used` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `phone`, `email`, `created_at`) VALUES
(1, 'vevien', '$2y$10$l8rXUmTjf3B1XTJbYLgkGuNAjLd2PyhkF9nG6DZvsbJDE9nYh7X5y', '', NULL, '2025-04-21 06:40:27'),
(2, 'althia', '$2y$10$qGbu2q3d0qZFSZrzP2ZRK.aQBVg1sbK0/IeYsO4fqLCDVQcmhPqxm', '09511959950', '', '2025-04-21 06:47:45'),
(3, 'welmar', '$2y$10$wtzMSBsgc1z8WOneUo6zAeUscp5ZT4KiJdXYfXZ6h/JLQh3rOQSOy', '09511959950', '', '2025-04-21 06:51:44'),
(4, 'rena', '$2y$10$UyjNPY4T.FkunyptPue1..i5hBx/SR./JfHYtQjHL3f9Kqg8ZxrRe', '09511959950', '', '2025-04-21 07:01:28'),
(5, 'maricel', '$2y$10$UnQtKufcLg03hJl4z9L3TubMksgqtVMUvPhyv05uvgutY3CRwifA2', '09511959950', '', '2025-04-21 10:58:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `borrowed_books`
--
ALTER TABLE `borrowed_books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
