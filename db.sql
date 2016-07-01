

CREATE TABLE IF NOT EXISTS `Mybid` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `ListingId` bigint(20) NOT NULL,
  `CreationDate` datetime NOT NULL,
  `UserId` int(11) DEFAULT NULL,
  `Amount` int(11) NOT NULL,
  `Rate` int(11) NOT NULL,
  `CreditRank` varchar(2) NOT NULL,
  `RuleId` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `UserId` (`UserId`),
  KEY `CreditRank` (`CreditRank`),
  KEY `RuleId` (`RuleId`),
  KEY `ListingId` (`ListingId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32312 ;


CREATE TABLE IF NOT EXISTS `User` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `CreationDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Token` text NOT NULL,
  `OpenID` text NOT NULL,
  `Code` text NOT NULL,
  `RefreshToken` text NOT NULL,
  `ExpiresIn` datetime NOT NULL,
  `IsAutoBid` tinyint(1) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=50 ;


CREATE TABLE IF NOT EXISTS `Rules` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(30) NOT NULL,
  `Description` text NOT NULL,
  `CreationDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;
