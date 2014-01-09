SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
--
-- Database: `colorAdmin01`
--
-- --------------------------------------------------------
--
-- Table structure for table `color`
--
CREATE TABLE `color` (
  `color_id` int(11) NOT NULL AUTO_INCREMENT,
  `color` varchar(16) COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`color_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci ;

--
-- Dumping data for table `color`
--

INSERT INTO `color` VALUES(1, 'Red');
INSERT INTO `color` VALUES(2, 'Orange');
INSERT INTO `color` VALUES(3, 'Yellow');
INSERT INTO `color` VALUES(4, 'Green');
INSERT INTO `color` VALUES(5, 'Blue');
INSERT INTO `color` VALUES(6, 'Indigo');
INSERT INTO `color` VALUES(7, 'Violet');

-- --------------------------------------------------------
--
-- Table structure for table `votes`
--
CREATE TABLE `votes` (
  `vote_id` int(11) NOT NULL AUTO_INCREMENT,
  `city` varchar(32) COLLATE utf8_swedish_ci NOT NULL,
  `color_id` int(11) NOT NULL,
  `votes` int(11) NOT NULL,
  PRIMARY KEY (`vote_id`),
  KEY `idx_color_id` (`color_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci ;

--
-- Dumping data for table `votes`
--

INSERT INTO `votes` VALUES(1, 'Anchorage', 5, 10000);
INSERT INTO `votes` VALUES(2, 'Anchorage', 3, 15000);
INSERT INTO `votes` VALUES(3, 'Brooklyn', 1, 100000);
INSERT INTO `votes` VALUES(4, 'Brooklyn', 5, 250000);
INSERT INTO `votes` VALUES(5, 'Detroit', 1, 160000);
INSERT INTO `votes` VALUES(6, 'Selma', 3, 15000);
INSERT INTO `votes` VALUES(7, 'Selma', 7, 5000);

--
-- Constraints for table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`color_id`) REFERENCES `color` (`color_id`);
