-- phpMyAdmin SQL Dump
-- version 4.2.12
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 25, 2014 at 08:11 PM
-- Server version: 5.5.37-0ubuntu0.13.10.1
-- PHP Version: 5.5.3-1ubuntu2.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `test_active_record_class`
--

-- --------------------------------------------------------

--
-- Table structure for table `ar_test_members`
--

CREATE TABLE IF NOT EXISTS `ar_test_members` (
`id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `city_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=792 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ar_test_members`
--

INSERT INTO `ar_test_members` (`id`, `name`, `age`, `city_id`) VALUES
(783, 'Sibel', 18, 2),
(784, 'Selim Emre', 21, 3),
(785, 'Emre', 21, 1),
(786, 'Ali', 25, 2),
(787, 'Emel', 24, 3),
(788, 'Nihal', 27, 1),
(789, 'Burcu', 15, 2),
(790, 'Sinan', 26, 3),
(791, 'Sinan', 22, 2);

-- --------------------------------------------------------

--
-- Table structure for table `ar_test_member_city`
--

CREATE TABLE IF NOT EXISTS `ar_test_member_city` (
`id` int(11) NOT NULL,
  `city` varchar(100) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ar_test_member_city`
--

INSERT INTO `ar_test_member_city` (`id`, `city`) VALUES
(1, 'Istanbul'),
(2, 'Izmir'),
(3, 'Ankara');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ar_test_members`
--
ALTER TABLE `ar_test_members`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ar_test_member_city`
--
ALTER TABLE `ar_test_member_city`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ar_test_members`
--
ALTER TABLE `ar_test_members`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=792;
--
-- AUTO_INCREMENT for table `ar_test_member_city`
--
ALTER TABLE `ar_test_member_city`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
