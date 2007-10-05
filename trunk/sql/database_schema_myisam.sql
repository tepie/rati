-- phpMyAdmin SQL Dump
-- version 2.10.1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Oct 05, 2007 at 04:14 PM
-- Server version: 5.0.37
-- PHP Version: 5.2.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Database: `rati`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `attribute`
-- 

CREATE TABLE `attribute` (
  `ID` bigint(20) unsigned zerofill NOT NULL auto_increment COMMENT 'Attribute ID',
  `NAME` text character set latin1 collate latin1_general_cs COMMENT 'Object Name',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `UNIQUE Name` (`NAME`(512)),
  FULLTEXT KEY `NAME` (`NAME`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='Meta Attributes' AUTO_INCREMENT=109 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `object`
-- 

CREATE TABLE `object` (
  `ID` bigint(20) unsigned zerofill NOT NULL auto_increment COMMENT 'Object ID',
  `NAME` text character set latin1 collate latin1_general_cs COMMENT 'Object Name',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `UNIQUE Name` (`NAME`(512)),
  FULLTEXT KEY `NAME` (`NAME`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='Meta Objects' AUTO_INCREMENT=268560 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `relationship`
-- 

CREATE TABLE `relationship` (
  `ID` bigint(20) unsigned zerofill NOT NULL auto_increment COMMENT 'Relationship ID',
  `OBJECT_ID` bigint(20) unsigned zerofill default NULL COMMENT 'Object ID (FK)',
  `ATTRIBUTE_ID` bigint(20) unsigned zerofill default NULL COMMENT 'Attribute ID (FK)',
  `VALUE` longtext character set latin1 collate latin1_general_cs COMMENT 'Relationship Value',
  `REFERENCE` bigint(20) unsigned zerofill NOT NULL COMMENT 'Relationship Reference',
  PRIMARY KEY  (`ID`),
  KEY `REFERENCE` (`REFERENCE`),
  KEY `OBJECT_INDEX` (`OBJECT_ID`),
  KEY `ATTRIBUTE_INDEX` (`ATTRIBUTE_ID`),
  FULLTEXT KEY `VALUE` (`VALUE`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='Relationship Table' AUTO_INCREMENT=2413131 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `search_index`
-- 

CREATE TABLE `search_index` (
  `ID` bigint(20) unsigned zerofill NOT NULL auto_increment COMMENT 'Search Index ID',
  `OBJECT_NAME` text collate latin1_general_cs NOT NULL COMMENT 'Object Name',
  `COMBINED_ATTRIBUTES` longtext collate latin1_general_cs NOT NULL COMMENT 'Combined attributes of the object',
  `RANK` bigint(20) unsigned zerofill NOT NULL default '00000000000000000000' COMMENT 'Object Rank',
  `WEIGHT` bigint(20) unsigned zerofill NOT NULL default '00000000000000000000',
  `PERSPECTIVE` text collate latin1_general_cs COMMENT 'Perspective Name',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `OBJECT_NAME_UNIQUE` (`OBJECT_NAME`(512)),
  KEY `RANK` (`RANK`),
  KEY `WEIGHT` (`WEIGHT`),
  FULLTEXT KEY `COMBINED_ATTRIBUTES_TEXT` (`COMBINED_ATTRIBUTES`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs COMMENT='Search Ranking' AUTO_INCREMENT=252409 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `usage_assoc_ip_object`
-- 

CREATE TABLE `usage_assoc_ip_object` (
  `ID` bigint(20) unsigned zerofill NOT NULL auto_increment COMMENT 'Primary ID',
  `IP_ID` bigint(20) unsigned zerofill NOT NULL COMMENT 'IP ID',
  `OBJECT_ID` bigint(20) unsigned zerofill NOT NULL COMMENT 'Object ID',
  PRIMARY KEY  (`ID`),
  KEY `IP_ID` (`IP_ID`,`OBJECT_ID`),
  KEY `OBJECT_ID` (`OBJECT_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs COMMENT='Associate IP addresses with objects visited' AUTO_INCREMENT=14233 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `usage_assoc_ip_search`
-- 

CREATE TABLE `usage_assoc_ip_search` (
  `ID` bigint(20) unsigned zerofill NOT NULL auto_increment COMMENT 'Assocative ID',
  `IP_ID` bigint(20) unsigned zerofill NOT NULL COMMENT 'Usage ID Address Key',
  `SEARCH_ID` bigint(20) unsigned zerofill NOT NULL COMMENT 'Usage Search Text Key',
  PRIMARY KEY  (`ID`),
  KEY `INDEX_IP_ID` (`IP_ID`),
  KEY `INDEX_SEARCH_ID` (`SEARCH_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs COMMENT='Associate the IP addresses to search strings' AUTO_INCREMENT=1066 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `usage_ip_log`
-- 

CREATE TABLE `usage_ip_log` (
  `ID` bigint(20) unsigned zerofill NOT NULL auto_increment COMMENT 'Identifies a user',
  `IP_ADDRESS` text character set latin1 collate latin1_general_ci COMMENT 'User IP Address',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `IP_ADDRESS_UNIQUE` (`IP_ADDRESS`(1000))
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs COMMENT='A log of site usage based on a user''s IP Address' AUTO_INCREMENT=58 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `usage_search_log`
-- 

CREATE TABLE `usage_search_log` (
  `ID` bigint(20) unsigned zerofill NOT NULL auto_increment COMMENT 'Identified a search string',
  `SEARCH_TEXT` text character set latin1 collate latin1_general_ci NOT NULL COMMENT 'The search text used to search',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `SEARCH_TEXT_UNIQUE` (`SEARCH_TEXT`(1000))
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs COMMENT='Usage of searches being used' AUTO_INCREMENT=310 ;

DROP USER rati@localhost;
DROP USER ratiimport@localhost;
CREATE USER rati@localhost IDENTIFIED BY 'rati';
CREATE USER ratiimport@localhost IDENTIFIED BY 'ratiimport';
GRANT USAGE ON *.* TO rati@localhost IDENTIFIED BY 'rati';
GRANT USAGE ON *.* TO rati@localhost IDENTIFIED BY 'ratiimport';
GRANT SELECT ON `rati` . * TO rati@localhost;
GRANT SELECT , INSERT , UPDATE , DELETE ON `rati` . * TO ratiimport@localhost;
COMMIT;

COMMIT;
