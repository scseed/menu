CREATE TABLE IF NOT EXISTS `menu` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) DEFAULT NULL COMMENT 'top menu level name',
  `title` varchar(128) DEFAULT NULL COMMENT 'title of a menu item',
  `route_name` varchar(45) NOT NULL DEFAULT 'default' COMMENT 'route name',
  `directory` varchar(128) DEFAULT NULL COMMENT 'route directory',
  `controller` varchar(128) DEFAULT NULL COMMENT 'controller name',
  `action` varchar(128) DEFAULT NULL COMMENT 'action name',
  `object_id` char(20) DEFAULT NULL COMMENT 'object id',
  `visible` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'menu item visibility',
  `class` varchar(61) DEFAULT NULL COMMENT 'menu item class',
  `lft` mediumint(9) NOT NULL,
  `rgt` mediumint(9) NOT NULL,
  `lvl` mediumint(9) NOT NULL,
  `scope` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Menu tree';