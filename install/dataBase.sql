-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 10, 2016 at 04:07 AM
-- Server version: 5.7.16-0ubuntu0.16.04.1
-- PHP Version: 7.0.8-0ubuntu0.16.04.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


-- --------------------------------------------------------

--
-- Table structure for table `wa_admins`
--

CREATE TABLE `wa_admins` (
  `id` int(11) NOT NULL,
  `username` varchar(225) NOT NULL,
  `password` varchar(225) NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `wa_audios`
--

CREATE TABLE `wa_audios` (
  `id` int(11) NOT NULL,
  `audio_original_name` varchar(225) NOT NULL,
  `audio_new_name` varchar(225) NOT NULL,
  `audio_path` varchar(225) NOT NULL,
  `audio_hash` varchar(100) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `wa_backups`
--

CREATE TABLE `wa_backups` (
  `id` int(11) NOT NULL,
  `backup_original_name` varchar(225) NOT NULL,
  `backup_new_name` varchar(225) NOT NULL,
  `backup_path` varchar(225) NOT NULL,
  `backup_hash` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `wa_conversations`
--

CREATE TABLE `wa_conversations` (
  `id` int(11) NOT NULL,
  `sender` int(11) DEFAULT NULL,
  `recipient` int(11) DEFAULT NULL,
  `Date` text
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `wa_documents`
--

CREATE TABLE `wa_documents` (
  `id` int(11) NOT NULL,
  `document_original_name` varchar(225) NOT NULL,
  `document_new_name` varchar(225) NOT NULL,
  `document_path` varchar(225) NOT NULL,
  `document_hash` varchar(100) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `wa_groups`
--

CREATE TABLE `wa_groups` (
  `id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `date` text NOT NULL,
  `userID` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `wa_group_members`
--

CREATE TABLE `wa_group_members` (
  `id` int(10) NOT NULL,
  `groupID` int(10) NOT NULL,
  `userID` int(10) NOT NULL,
  `role` varchar(11) DEFAULT NULL,
  `isLeft` tinyint(1) NOT NULL DEFAULT '0',
  `Deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




-- --------------------------------------------------------

--
-- Table structure for table `wa_images`
--

CREATE TABLE `wa_images` (
  `id` int(11) NOT NULL,
  `image_original_name` varchar(225) NOT NULL,
  `image_new_name` varchar(225) NOT NULL,
  `image_type` int(11) NOT NULL,
  `image_path` varchar(225) NOT NULL,
  `image_hash` varchar(100) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `wa_messages`
--

CREATE TABLE `wa_messages` (
  `id` int(11) NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `image` varchar(250) DEFAULT NULL,
  `video` varchar(250) DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `audio` varchar(255) DEFAULT NULL,
  `document` varchar(255) DEFAULT NULL,
  `duration` longtext,
  `fileSize` longtext,
  `UserID` int(11) NOT NULL,
  `groupID` int(11) NOT NULL,
  `Date` text NOT NULL,
  `ConversationID` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;



-- --------------------------------------------------------

--
-- Table structure for table `wa_messages_groups_status`
--

CREATE TABLE `wa_messages_groups_status` (
  `id` int(11) NOT NULL,
  `messageId` int(11) DEFAULT NULL,
  `recipientId` int(11) DEFAULT NULL,
  `senderId` int(11) NOT NULL,
  `groupId` int(11) DEFAULT NULL,
  `status` int(1) DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `wa_settings`
--

CREATE TABLE `wa_settings` (
  `id` int(11) NOT NULL,
  `name` varchar(225) NOT NULL,
  `value` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Dumping data for table `wa_settings`
--

INSERT INTO `wa_settings` (`id`, `name`, `value`) VALUES
(1, 'privacy_policy', 'put your Privacy Policy here'),
(2, 'phone_number', 'put your SMS provider phone number here'),
(3, 'sms_authentication_key', 'put your SMS provider authentication key here'),
(4, 'account_sid', 'put your SMS provider account SID here '),
(5, 'base_url', 'put your base url here'),
(6, 'app_name', 'put your app name here'),
(7, 'banner_ads_unit_id', 'put your unit id of admob'),
(8, 'banner_ads_status', '0'),
(9, 'interstitial_ads_unit_id', 'put your Interstitial unit id of admob'),
(10, 'interstitial_ads_status', '0'),
(11, 'sms_verification', '0'),
(12, 'app_version', 'put your versionCode of your app Ex: 1'),
(13, 'app_key_secret', '7d3d3b6c2d3683bf25bbb51533sdc6daf'),
(14, 'debugging_mode', '0'),
(15, 'serverPort', '9001');

-- --------------------------------------------------------

--
-- Table structure for table `wa_sms_codes`
--

CREATE TABLE `wa_sms_codes` (
  `id` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `code` varchar(6) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;



-- --------------------------------------------------------

--
-- Table structure for table `wa_status`
--

CREATE TABLE `wa_status` (
  `id` int(11) NOT NULL,
  `status` varchar(225) NOT NULL,
  `userID` int(11) NOT NULL,
  `current` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;



-- --------------------------------------------------------

--
-- Table structure for table `wa_users`
--

CREATE TABLE `wa_users` (
  `id` int(10) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `phone` varchar(255) NOT NULL,
  `country` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `auth_token` varchar(32) NOT NULL,
  `status` varchar(255) DEFAULT NULL,
  `status_date` int(11) NOT NULL,
  `is_activated` int(1) NOT NULL DEFAULT '0',
  `has_backup` int(1) NOT NULL DEFAULT '0',
  `backup_hash` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `wa_videos`
--

CREATE TABLE `wa_videos` (
  `id` int(11) NOT NULL,
  `video_original_name` varchar(225) NOT NULL,
  `video_new_name` varchar(225) NOT NULL,
  `video_path` varchar(225) NOT NULL,
  `video_hash` varchar(100) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


--
-- Indexes for dumped tables
--

--
-- Indexes for table `wa_admins`
--
ALTER TABLE `wa_admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wa_audios`
--
ALTER TABLE `wa_audios`
  ADD PRIMARY KEY (`id`);


--
-- Indexes for table `wa_backups`
--
ALTER TABLE `wa_backups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wa_conversations`
--
ALTER TABLE `wa_conversations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wa_documents`
--
ALTER TABLE `wa_documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wa_groups`
--
ALTER TABLE `wa_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wa_group_members`
--
ALTER TABLE `wa_group_members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wa_images`
--
ALTER TABLE `wa_images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wa_messages`
--
ALTER TABLE `wa_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wa_messages_groups_status`
--
ALTER TABLE `wa_messages_groups_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wa_sms_codes`
--
ALTER TABLE `wa_sms_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `wa_status`
--
ALTER TABLE `wa_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wa_users`
--
ALTER TABLE `wa_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wa_videos`
--
ALTER TABLE `wa_videos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `wa_admins`
--
ALTER TABLE `wa_admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `wa_audios`
--
ALTER TABLE `wa_audios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=0;

-- AUTO_INCREMENT for table `wa_backups`
--
ALTER TABLE `wa_backups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `wa_conversations`
--
ALTER TABLE `wa_conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `wa_documents`
--
ALTER TABLE `wa_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `wa_groups`
--
ALTER TABLE `wa_groups`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `wa_group_members`
--
ALTER TABLE `wa_group_members`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `wa_images`
--
ALTER TABLE `wa_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `wa_messages`
--
ALTER TABLE `wa_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `wa_messages_groups_status`
--
ALTER TABLE `wa_messages_groups_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `wa_sms_codes`
--
ALTER TABLE `wa_sms_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `wa_status`
--
ALTER TABLE `wa_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `wa_users`
--
ALTER TABLE `wa_users`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=0;
--
-- AUTO_INCREMENT for table `wa_videos`
--
ALTER TABLE `wa_videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=0;
-- --------------------------------------------------------
