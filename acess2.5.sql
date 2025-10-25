-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 24, 2025 at 06:07 PM
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
-- Database: `acess`
--

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clubs`
--

CREATE TABLE `clubs` (
  `id` int(11) NOT NULL,
  `club_name` varchar(100) NOT NULL,
  `adviser` varchar(100) NOT NULL,
  `group_chat` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `president_id` int(11) DEFAULT NULL,
  `vice_president_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clubs`
--

INSERT INTO `clubs` (`id`, `club_name`, `adviser`, `group_chat`, `description`, `president_id`, `vice_president_id`, `created_at`) VALUES
(7, 'Chess', 'Jose Gabriel T. Ignacio', 'Chess', NULL, NULL, NULL, '2025-10-21 13:30:20'),
(11, 'Basketball', 'Jose Gabriel T. Ignacio', 'Basketball', NULL, NULL, NULL, '2025-10-23 04:20:24'),
(12, 'Cheese', 'Jose Gabriel T. Ignacio', 'Cheese', NULL, NULL, NULL, '2025-10-24 05:55:40'),
(13, 'Cheeses', 'Jose Gabriel T. Ignacio', 'Cheeses', NULL, NULL, NULL, '2025-10-24 07:25:01'),
(14, 'Cheeserrrr', 'Jose Gabriel T. Ignacio', 'Cheeserrrrrrrrrrrrrrrrrr', NULL, NULL, NULL, '2025-10-24 07:34:54'),
(15, 'Cheeserrrrrrrrrrrrr', 'Jose Gabriel T. Ignacio', 'Cheeserrrrrrrrrrrrrrrrrrrrrrrrrrrrr', NULL, NULL, NULL, '2025-10-24 07:37:39');

-- --------------------------------------------------------

--
-- Table structure for table `club_announcements`
--

CREATE TABLE `club_announcements` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `club_announcements`
--

INSERT INTO `club_announcements` (`id`, `date`, `title`) VALUES
(3, '2025-12-12', 'Math Meeting');

-- --------------------------------------------------------

--
-- Table structure for table `club_members`
--

CREATE TABLE `club_members` (
  `id` int(11) NOT NULL,
  `club_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('member','president','vice_president','advisor') DEFAULT 'member',
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approved_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `club_members`
--

INSERT INTO `club_members` (`id`, `club_id`, `user_id`, `role`, `joined_at`, `approved_at`) VALUES
(8, 11, 11, 'member', '2025-10-23 10:47:51', '2025-10-24 08:21:34');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `date`, `title`) VALUES
(9, '2025-12-01', 'Intrams'),
(11, '2025-12-04', '1waedwqad');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `attempts` int(11) DEFAULT 0,
  `last_attempt` timestamp NOT NULL DEFAULT current_timestamp(),
  `locked_until` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `club_id` int(11) DEFAULT NULL,
  `message_type` enum('direct','club') DEFAULT 'direct',
  `sender_email` varchar(100) DEFAULT NULL,
  `receiver_email` varchar(100) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `club_id`, `message_type`, `sender_email`, `receiver_email`, `message`, `timestamp`) VALUES
(11, 1, 9, NULL, 'direct', NULL, NULL, 'DDDDD', '2025-10-24 15:23:56'),
(12, 9, NULL, 13, 'club', NULL, NULL, 'Welcome to Cheeses! This is the official group chat for club members and advisers.', '2025-10-24 15:25:01'),
(14, 1, NULL, 14, 'club', NULL, NULL, 'Welcome to Cheeserrrr! This is the official group chat for club members and advisers.', '2025-10-24 15:34:54'),
(15, 1, NULL, 15, 'club', NULL, NULL, 'Welcome to Cheeserrrrrrrrrrrrr! This is the official group chat for club members and advisers.', '2025-10-24 15:37:39'),
(16, 10, NULL, 11, 'club', NULL, NULL, 'Jose has joined the club!', '2025-10-24 16:21:34');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `lrn` varchar(50) NOT NULL,
  `role` enum('user','admin','teacher','studentcouncil','superadmin') DEFAULT 'user',
  `status` enum('pending','active','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `fullname`, `password`, `lrn`, `role`, `status`, `created_at`) VALUES
(1, 'gabignacio1@gmail.com', 'Jose Gabriel T. Ignacio', '$2y$10$bpkQh.9yFc7hCqZLuMNlFut5fjty/sYvfv06BzE0X.Qcn/WoZx2FS', '123132131313', 'admin', 'active', '2025-10-17 09:18:23'),
(2, 'gabignacio71@gmail.com', 'Jose Gabriel T. Ignacio', '$2y$10$W17AN.nPy8GnEvmtlwPseecGqRj43f6Aj1gej9K8F.KsteWDZj9EG', '31231231', 'superadmin', 'active', '2025-10-17 09:28:54'),
(3, 'gabignacio691@gmail.com', 'Jose Gabriel T.', '$2y$10$f8J5U1B3U93Ysh.m0KKZNOmeukW6sQA2ri.6boE8aFtLp2TVD1FXC', '123132131313', 'studentcouncil', 'active', '2025-10-17 09:37:56'),
(4, 'gabignacio12@gmail.com', 'Jose Gabriel', '$2y$10$q.WuJtEq.zY03RHYrCx7EOrBueyyZOmsXvepxQdpy3vPJ0VxL.9.S', '123132131313', 'studentcouncil', 'active', '2025-10-17 10:58:30'),
(6, 'gabignacio6122@gmail.com', 'Jose Gabriel T.', '$2y$10$vrn44juYtDuO/wpaRv.lYO5s9LEjw3euIPsATaLCQrTDv5SsM/QHW', '123132131313', 'user', 'active', '2025-10-18 11:17:45'),
(7, 'gabignacio6111@gmail.com', 'Jose Gabriel', '$2y$10$6deBolnnpr3bNmPRYAY5Pu7M3nz9T588puTJHKZXG4eKdB81hC9Z6', '12345', 'superadmin', 'active', '2025-10-21 07:20:41'),
(9, 'gabignacio1117@gmail.com', 'Jose Gabriel T.', '$2y$10$ReSGVIphNYSCpNqb3K87qeKk.x3txn861tdYd39hjHrKDqcn5HZni', '123132131313', 'admin', 'active', '2025-10-22 12:08:58'),
(10, 'gabignacio1111@gmail.com', 'Jose Gabriel T. Ignacio', '$2y$10$GNgPy3eRy6MUv5Lhy4eovO9FRIlx7JFmG6WLFhSRuDN87UTN2PhZ.', '123132131313', 'teacher', 'active', '2025-10-22 13:56:53'),
(11, 'gsbignacio0123@gmail.com', 'Jose', '$2y$10$YoRG66Plfz9kZ3NEcrjsl.6Db.3E8wr2bVHNQkghCqgASU9fCfMdW', '1111111', 'user', 'active', '2025-10-23 05:59:07');

-- --------------------------------------------------------

--
-- Table structure for table `user_votes`
--

CREATE TABLE `user_votes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `voted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `president` varchar(100) NOT NULL,
  `vice_president` varchar(100) NOT NULL,
  `secretary` varchar(100) NOT NULL,
  `list_members` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `votes`
--

INSERT INTO `votes` (`id`, `user_id`, `president`, `vice_president`, `secretary`, `list_members`, `created_at`) VALUES
(1, 6, 'Mr_Smith', 'Mr_Brown', 'Mr_Taylor', 'Mr_Clark', '2025-10-22 09:06:24'),
(2, 11, 'Mr_Smith', 'Mr_Brown', 'Ms_Williams', 'Mr_Clark', '2025-10-23 10:55:43');

-- --------------------------------------------------------

--
-- Table structure for table `vote_details`
--

CREATE TABLE `vote_details` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `position` varchar(50) NOT NULL,
  `voted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `voting_candidates`
--

CREATE TABLE `voting_candidates` (
  `id` int(11) NOT NULL,
  `candidate_name` varchar(100) NOT NULL,
  `position` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `votes` int(11) DEFAULT 0,
  `photo_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `voting_candidates`
--

INSERT INTO `voting_candidates` (`id`, `candidate_name`, `position`, `created_at`, `votes`, `photo_url`) VALUES
(2, 'Jerald', 'Vice President', '2025-10-23 11:35:17', 0, NULL),
(3, 'Gab', 'P.O', '2025-10-23 11:43:06', 0, NULL),
(4, 'Dormids', 'P.O', '2025-10-23 11:43:49', 0, NULL),
(6, 'Gabig', 'Vice President', '2025-10-23 12:41:58', 0, NULL),
(7, 'Gab', 'Grade 9', '2025-10-23 12:42:43', 0, NULL),
(8, 'Gab', 'President', '2025-10-23 12:42:59', 0, NULL),
(10, 'Gab', 'P.I.O', '2025-10-23 12:43:27', 0, NULL),
(11, 'dawd', 'Treasurer', '2025-10-23 12:43:36', 0, NULL),
(12, 'Gab', 'Secretary', '2025-10-23 12:43:46', 0, NULL),
(14, 'Dormids', 'Grade 12', '2025-10-23 12:57:47', 0, NULL),
(15, 'Gab', 'Grade 7', '2025-10-23 12:57:57', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `voting_settings`
--

CREATE TABLE `voting_settings` (
  `id` int(11) NOT NULL,
  `voting_active` tinyint(1) DEFAULT 0,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `voting_settings`
--

INSERT INTO `voting_settings` (`id`, `voting_active`, `last_updated`) VALUES
(1, 0, '2025-10-24 15:22:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clubs`
--
ALTER TABLE `clubs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `club_name` (`club_name`),
  ADD KEY `fk_club_president` (`president_id`),
  ADD KEY `fk_club_vice_president` (`vice_president_id`);

--
-- Indexes for table `club_announcements`
--
ALTER TABLE `club_announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `club_members`
--
ALTER TABLE `club_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `club_id` (`club_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sender` (`sender_id`),
  ADD KEY `idx_receiver` (`receiver_id`),
  ADD KEY `idx_club` (`club_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_votes`
--
ALTER TABLE `user_votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_vote` (`user_id`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `vote_details`
--
ALTER TABLE `vote_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_vote_details_user` (`user_id`),
  ADD KEY `idx_vote_details_candidate` (`candidate_id`);

--
-- Indexes for table `voting_candidates`
--
ALTER TABLE `voting_candidates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_voting_candidates_position` (`position`);

--
-- Indexes for table `voting_settings`
--
ALTER TABLE `voting_settings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clubs`
--
ALTER TABLE `clubs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `club_announcements`
--
ALTER TABLE `club_announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `club_members`
--
ALTER TABLE `club_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `user_votes`
--
ALTER TABLE `user_votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vote_details`
--
ALTER TABLE `vote_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `voting_candidates`
--
ALTER TABLE `voting_candidates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `voting_settings`
--
ALTER TABLE `voting_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `clubs`
--
ALTER TABLE `clubs`
  ADD CONSTRAINT `fk_club_president` FOREIGN KEY (`president_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_club_vice_president` FOREIGN KEY (`vice_president_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `club_members`
--
ALTER TABLE `club_members`
  ADD CONSTRAINT `club_members_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `club_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_votes`
--
ALTER TABLE `user_votes`
  ADD CONSTRAINT `user_votes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vote_details`
--
ALTER TABLE `vote_details`
  ADD CONSTRAINT `vote_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vote_details_ibfk_2` FOREIGN KEY (`candidate_id`) REFERENCES `voting_candidates` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
