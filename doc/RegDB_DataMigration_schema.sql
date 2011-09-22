SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `REGDB` ;
USE `REGDB`;

-- -----------------------------------------------------
-- Table `REGDB`.`DATA_MIGRATION`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `REGDB`.`DATA_MIGRATION` ;

CREATE  TABLE IF NOT EXISTS `REGDB`.`DATA_MIGRATION` (
  `exper_id` INT NOT NULL ,
  `file` VARCHAR(255) NOT NULL ,
  `start_time` BIGINT UNSIGNED NOT NULL ,
  `stop_time` BIGINT UNSIGNED DEFAULT NULL ,
  PRIMARY KEY (`exper_id`,`file`) ,
  INDEX `EXPSWITCH_FK_1` (`exper_id` ASC) ,
  CONSTRAINT `DATA_MIGRATION_FK_1`
    FOREIGN KEY (`exper_id` )
    REFERENCES `REGDB`.`EXPERIMENT` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;