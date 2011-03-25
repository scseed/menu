-- -----------------------------------------------------
-- Table `menu`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `menu` (
  `id` TINYINT(3) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(256) NULL DEFAULT NULL COMMENT 'top menu level name' ,
  `title` VARCHAR(128) NULL DEFAULT NULL COMMENT 'title of a menu item' ,
  `route_name` VARCHAR(45) NOT NULL DEFAULT 'default' COMMENT 'route name' ,
  `directory` VARCHAR(128) NULL DEFAULT NULL COMMENT 'route directory' ,
  `controller` VARCHAR(128) NULL DEFAULT NULL COMMENT 'controller name' ,
  `action` VARCHAR(128) NULL DEFAULT NULL COMMENT 'action name' ,
  `params` VARCHAR(256) NULL DEFAULT NULL COMMENT 'request params (serialized)' ,
  `query` VARCHAR(256) NULL DEFAULT NULL COMMENT 'object id' ,
  `visible` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'menu item visibility' ,
  `class` VARCHAR(61) NULL DEFAULT NULL COMMENT 'menu item class' ,
  `lft` MEDIUMINT(9) NULL DEFAULT NULL ,
  `rgt` MEDIUMINT(9) NULL DEFAULT NULL ,
  `lvl` MEDIUMINT(9) NULL DEFAULT NULL ,
  `scope` SMALLINT(6) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'Menu tree';