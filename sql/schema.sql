-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema ppg
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `ppg` ;

-- -----------------------------------------------------
-- Schema ppg
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `ppg` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
SHOW WARNINGS;
USE `ppg` ;

-- -----------------------------------------------------
-- Table `ppg`.`organisations`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ppg`.`organisations` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `ppg`.`organisations` (
  `id` BIGINT NOT NULL AUTO_INCREMENT COMMENT 'This table holds the high level organisation record. This table will need to be held in one DB and generate a unique sequence for all organisations. There is a constraint of ',
  `name` VARCHAR(45) NULL,
  `address1` VARCHAR(45) NULL,
  `address2` VARCHAR(45) NULL,
  `city` VARCHAR(45) NULL,
  `state` VARCHAR(45) NULL,
  `postcode` VARCHAR(45) NULL,
  `last_project_id` INT NULL DEFAULT 0,
  `user_created_id` INT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `ppg`.`user_profiles`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ppg`.`user_profiles` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `ppg`.`user_profiles` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Holds the profile of a logged in user vs. a guest user, vs.a hacker.',
  `organisations_id` BIGINT NOT NULL,
  `name` VARCHAR(45) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_user_profiles_organisations1_idx` (`organisations_id` ASC),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC),
  CONSTRAINT `fk_user_profiles_organisations1`
    FOREIGN KEY (`organisations_id`)
    REFERENCES `ppg`.`organisations` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `ppg`.`roles`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ppg`.`roles` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `ppg`.`roles` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT 'This holds the application access roles, for change manager, program manager, project manager and project user, timesheet user etc ...',
  `organisations_id` BIGINT NOT NULL,
  `name` VARCHAR(45) NULL,
  PRIMARY KEY (`id`, `organisations_id`),
  INDEX `fk_roles_organisations1_idx` (`organisations_id` ASC),
  CONSTRAINT `fk_roles_organisations1`
    FOREIGN KEY (`organisations_id`)
    REFERENCES `ppg`.`organisations` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `ppg`.`calendars`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ppg`.`calendars` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `ppg`.`calendars` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `organisations_id` BIGINT NOT NULL,
  `name` VARCHAR(45) NULL,
  PRIMARY KEY (`id`, `organisations_id`),
  INDEX `fk_calendars_organisations1_idx` (`organisations_id` ASC),
  CONSTRAINT `fk_calendars_organisations1`
    FOREIGN KEY (`organisations_id`)
    REFERENCES `ppg`.`organisations` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `ppg`.`resources`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ppg`.`resources` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `ppg`.`resources` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `organisations_id` BIGINT NOT NULL,
  `name` VARCHAR(45) NULL,
  PRIMARY KEY (`id`, `organisations_id`),
  INDEX `fk_resources_organisations1_idx` (`organisations_id` ASC),
  CONSTRAINT `fk_resources_organisations1`
    FOREIGN KEY (`organisations_id`)
    REFERENCES `ppg`.`organisations` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `ppg`.`users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ppg`.`users` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `ppg`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT 'User details',
  `organisations_id` BIGINT NOT NULL,
  `user_profiles_id` INT NOT NULL,
  `roles_id` INT NOT NULL,
  `calendars_id` INT NOT NULL,
  `resources_id` INT NOT NULL,
  `handle` VARCHAR(45) NULL,
  `email` VARCHAR(45) NULL,
  `password` VARCHAR(255) NULL,
  `verifylink` VARCHAR(45) NULL,
  PRIMARY KEY (`id`, `organisations_id`, `user_profiles_id`),
  INDEX `fk_users_organisations1_idx` (`organisations_id` ASC),
  INDEX `fk_users_user_profiles1_idx` (`user_profiles_id` ASC),
  INDEX `fk_users_roles1_idx` (`roles_id` ASC),
  INDEX `fk_users_calendars1_idx` (`calendars_id` ASC),
  INDEX `fk_users_resources1_idx` (`resources_id` ASC),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC),
  INDEX `verify_long` (`verifylink`(40) ASC),
  CONSTRAINT `fk_users_organisations1`
    FOREIGN KEY (`organisations_id`)
    REFERENCES `ppg`.`organisations` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_user_profiles1`
    FOREIGN KEY (`user_profiles_id`)
    REFERENCES `ppg`.`user_profiles` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_roles1`
    FOREIGN KEY (`roles_id`)
    REFERENCES `ppg`.`roles` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_calendars1`
    FOREIGN KEY (`calendars_id`)
    REFERENCES `ppg`.`calendars` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_resources1`
    FOREIGN KEY (`resources_id`)
    REFERENCES `ppg`.`resources` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `ppg`.`changes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ppg`.`changes` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `ppg`.`changes` (
  `id` INT NOT NULL,
  `organisations_id` BIGINT NOT NULL,
  PRIMARY KEY (`id`, `organisations_id`),
  INDEX `fk_changes_organisations1_idx` (`organisations_id` ASC),
  CONSTRAINT `fk_changes_organisations1`
    FOREIGN KEY (`organisations_id`)
    REFERENCES `ppg`.`organisations` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `ppg`.`programs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ppg`.`programs` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `ppg`.`programs` (
  `id` INT NOT NULL,
  `changes_id` INT NOT NULL,
  `organisations_id` BIGINT NOT NULL,
  PRIMARY KEY (`id`, `organisations_id`),
  INDEX `fk_programs_changes1_idx` (`changes_id` ASC),
  INDEX `fk_programs_organisations1_idx` (`organisations_id` ASC),
  CONSTRAINT `fk_programs_changes1`
    FOREIGN KEY (`changes_id`)
    REFERENCES `ppg`.`changes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_programs_organisations1`
    FOREIGN KEY (`organisations_id`)
    REFERENCES `ppg`.`organisations` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `ppg`.`projects`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ppg`.`projects` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `ppg`.`projects` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `programs_id` INT NULL,
  `organisations_id` BIGINT NOT NULL,
  `name` VARCHAR(45) NOT NULL,
  `start_date` DATETIME NULL,
  `last_date` DATETIME NULL,
  `proj_days` INT NULL,
  PRIMARY KEY (`id`, `organisations_id`),
  INDEX `fk_projects_programs1_idx` (`programs_id` ASC),
  INDEX `fk_projects_organisations1_idx` (`organisations_id` ASC),
  CONSTRAINT `fk_projects_programs1`
    FOREIGN KEY (`programs_id`)
    REFERENCES `ppg`.`programs` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_projects_organisations1`
    FOREIGN KEY (`organisations_id`)
    REFERENCES `ppg`.`organisations` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `ppg`.`objectives`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ppg`.`objectives` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `ppg`.`objectives` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `projects_id` BIGINT NOT NULL,
  `seq_id` INT NOT NULL,
  `description` VARCHAR(200) NULL,
  `duration` DECIMAL(3) NULL DEFAULT 0.000,
  `duration_format` VARCHAR(45) NULL DEFAULT 'd',
  `start_date` DATETIME NULL,
  `end_date` DATETIME NULL,
  `completion` INT NULL DEFAULT 0,
  `act_date` DATETIME NULL,
  PRIMARY KEY (`id`, `projects_id`, `seq_id`),
  INDEX `fk_objectives_projects1_idx` (`projects_id` ASC),
  INDEX `fk_objectives_projects2_idx` (`id` ASC, `projects_id` ASC),
  CONSTRAINT `fk_objectives_projects1`
    FOREIGN KEY (`projects_id`)
    REFERENCES `ppg`.`projects` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `ppg`.`divisions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ppg`.`divisions` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `ppg`.`divisions` (
  `id` INT NOT NULL COMMENT 'Holds the divisions within an organisation -> a default 0 record will be created, if customers want more it can be used.',
  `organisations_id` BIGINT NOT NULL,
  PRIMARY KEY (`id`, `organisations_id`),
  INDEX `fk_divisions_organisations_idx` (`organisations_id` ASC),
  CONSTRAINT `fk_divisions_organisations`
    FOREIGN KEY (`organisations_id`)
    REFERENCES `ppg`.`organisations` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `ppg`.`rates`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ppg`.`rates` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `ppg`.`rates` (
  `id` INT NOT NULL,
  `organisations_id` BIGINT NOT NULL,
  PRIMARY KEY (`id`, `organisations_id`),
  INDEX `fk_rates_organisations1_idx` (`organisations_id` ASC),
  CONSTRAINT `fk_rates_organisations1`
    FOREIGN KEY (`organisations_id`)
    REFERENCES `ppg`.`organisations` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `ppg`.`sessions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ppg`.`sessions` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `ppg`.`sessions` (
  `session_id` VARCHAR(45) NOT NULL COMMENT 'logged in session user',
  `users_id` INT NOT NULL,
  `users_organisations_id` BIGINT NOT NULL,
  `users_user_profiles_id` INT NOT NULL,
  `remote_address` VARCHAR(45) NULL,
  `referer_address` VARCHAR(45) NULL,
  `user_agent` VARCHAR(255) NULL,
  `creation_date` DATETIME NULL,
  `last_accessed` DATETIME NULL,
  `pageviews` BIGINT NULL,
  `curr_proj_name` VARCHAR(45) NULL,
  `curr_proj_id` BIGINT NULL,
  PRIMARY KEY (`session_id`, `users_id`, `users_organisations_id`, `users_user_profiles_id`),
  INDEX `fk_sessions_users1_idx` (`users_id` ASC, `users_organisations_id` ASC, `users_user_profiles_id` ASC),
  INDEX `session_id` (`session_id`(45) ASC),
  CONSTRAINT `fk_sessions_users1`
    FOREIGN KEY (`users_id` , `users_organisations_id` , `users_user_profiles_id`)
    REFERENCES `ppg`.`users` (`id` , `organisations_id` , `user_profiles_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `ppg`.`baselines`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ppg`.`baselines` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `ppg`.`baselines` (
  `id` INT NOT NULL,
  `objectives_id` BIGINT NOT NULL,
  `objectives_projects_id` BIGINT NOT NULL,
  PRIMARY KEY (`id`, `objectives_id`, `objectives_projects_id`),
  INDEX `fk_baselines_objectives1_idx` (`objectives_id` ASC, `objectives_projects_id` ASC),
  CONSTRAINT `fk_baselines_objectives1`
    FOREIGN KEY (`objectives_id` , `objectives_projects_id`)
    REFERENCES `ppg`.`objectives` (`id` , `projects_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `ppg`.`budgets`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ppg`.`budgets` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `ppg`.`budgets` (
  `id` INT NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `ppg`.`allocations`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ppg`.`allocations` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `ppg`.`allocations` (
  `id` INT NOT NULL,
  `resources_id` INT NOT NULL,
  `resources_organisations_id` BIGINT NOT NULL,
  `objectives_id` BIGINT NOT NULL,
  `budgets_id` INT NOT NULL,
  PRIMARY KEY (`id`, `resources_id`, `resources_organisations_id`, `budgets_id`),
  INDEX `fk_allocations_resources1_idx` (`resources_id` ASC, `resources_organisations_id` ASC),
  INDEX `fk_allocations_objectives1_idx` (`objectives_id` ASC),
  INDEX `fk_allocations_budgets1_idx` (`budgets_id` ASC),
  CONSTRAINT `fk_allocations_resources1`
    FOREIGN KEY (`resources_id` , `resources_organisations_id`)
    REFERENCES `ppg`.`resources` (`id` , `organisations_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_allocations_objectives1`
    FOREIGN KEY (`objectives_id`)
    REFERENCES `ppg`.`objectives` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_allocations_budgets1`
    FOREIGN KEY (`budgets_id`)
    REFERENCES `ppg`.`budgets` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `ppg`.`timesheets`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ppg`.`timesheets` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `ppg`.`timesheets` (
  `id` INT NOT NULL,
  `allocations_id` INT NOT NULL,
  `allocations_resources_id` INT NOT NULL,
  `allocations_resources_organisations_id` BIGINT NOT NULL,
  `allocations_budgets_id` INT NOT NULL,
  PRIMARY KEY (`id`, `allocations_id`, `allocations_resources_id`, `allocations_resources_organisations_id`, `allocations_budgets_id`),
  INDEX `fk_timesheets_allocations1_idx` (`allocations_id` ASC, `allocations_resources_id` ASC, `allocations_resources_organisations_id` ASC, `allocations_budgets_id` ASC),
  CONSTRAINT `fk_timesheets_allocations1`
    FOREIGN KEY (`allocations_id` , `allocations_resources_id` , `allocations_resources_organisations_id` , `allocations_budgets_id`)
    REFERENCES `ppg`.`allocations` (`id` , `resources_id` , `resources_organisations_id` , `budgets_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `ppg`.`spends`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ppg`.`spends` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `ppg`.`spends` (
  `id` INT NOT NULL,
  `budgets_id` INT NOT NULL,
  `objectives_id` BIGINT NOT NULL,
  PRIMARY KEY (`id`, `budgets_id`),
  INDEX `fk_spends_budgets1_idx` (`budgets_id` ASC),
  INDEX `fk_spends_objectives1_idx` (`objectives_id` ASC),
  CONSTRAINT `fk_spends_budgets1`
    FOREIGN KEY (`budgets_id`)
    REFERENCES `ppg`.`budgets` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_spends_objectives1`
    FOREIGN KEY (`objectives_id`)
    REFERENCES `ppg`.`objectives` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `ppg`.`licenses`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ppg`.`licenses` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `ppg`.`licenses` (
  `id` INT NOT NULL COMMENT 'Table of general licenses',
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `ppg`.`authorised_licenses`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ppg`.`authorised_licenses` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `ppg`.`authorised_licenses` (
  `id` INT NOT NULL COMMENT 'table of authorised (paid for) licenses. Used for application functionality',
  `licenses_id` INT NOT NULL,
  `organisations_id` BIGINT NOT NULL,
  PRIMARY KEY (`id`, `licenses_id`, `organisations_id`),
  INDEX `fk_authorised_licenses_licenses1_idx` (`licenses_id` ASC),
  INDEX `fk_authorised_licenses_organisations1_idx` (`organisations_id` ASC),
  CONSTRAINT `fk_authorised_licenses_licenses1`
    FOREIGN KEY (`licenses_id`)
    REFERENCES `ppg`.`licenses` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_authorised_licenses_organisations1`
    FOREIGN KEY (`organisations_id`)
    REFERENCES `ppg`.`organisations` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `ppg`.`journals`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ppg`.`journals` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `ppg`.`journals` (
  `id` INT NOT NULL,
  `budgets_id` INT NOT NULL,
  `budgets_id1` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_journals_budgets1_idx` (`budgets_id` ASC),
  INDEX `fk_journals_budgets2_idx` (`budgets_id1` ASC),
  CONSTRAINT `fk_journals_budgets1`
    FOREIGN KEY (`budgets_id`)
    REFERENCES `ppg`.`budgets` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_journals_budgets2`
    FOREIGN KEY (`budgets_id1`)
    REFERENCES `ppg`.`budgets` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `ppg`.`dependencies`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ppg`.`dependencies` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `ppg`.`dependencies` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `type` VARCHAR(45) NULL,
  `lead_lag` DECIMAL(3) NULL,
  `organisations_id` BIGINT NOT NULL,
  `projects_id` BIGINT NOT NULL,
  `projects_organisations_id` BIGINT NOT NULL,
  `objectives_id` BIGINT NOT NULL,
  `objectives_projects_id` BIGINT NOT NULL,
  `from_objectives_id` BIGINT NULL,
  `description` TEXT(255) NULL,
  PRIMARY KEY (`id`, `projects_organisations_id`, `objectives_id`, `objectives_projects_id`, `projects_id`, `organisations_id`),
  INDEX `fk_dependencies_objectives2_idx` (`id` ASC),
  INDEX `fk_dependencies_organisations1_idx` (`organisations_id` ASC),
  INDEX `fk_dependencies_projects1_idx` (`projects_id` ASC, `projects_organisations_id` ASC),
  INDEX `fk_dependencies_objectives1_idx` (`objectives_id` ASC, `objectives_projects_id` ASC),
  UNIQUE INDEX `from_objectives_id_UNIQUE` (`from_objectives_id` ASC, `objectives_id` ASC),
  CONSTRAINT `fk_dependencies_organisations1`
    FOREIGN KEY (`organisations_id`)
    REFERENCES `ppg`.`organisations` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_dependencies_projects1`
    FOREIGN KEY (`projects_id` , `projects_organisations_id`)
    REFERENCES `ppg`.`projects` (`id` , `organisations_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_dependencies_objectives1`
    FOREIGN KEY (`objectives_id` , `objectives_projects_id`)
    REFERENCES `ppg`.`objectives` (`id` , `projects_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `ppg`.`domains`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ppg`.`domains` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `ppg`.`domains` (
  `id` INT NOT NULL DEFAULT 0 COMMENT 'Holds the domains, i.e. Technical / Functional that a change / program / project can belong to.',
  `organisations_id` BIGINT NOT NULL,
  PRIMARY KEY (`id`, `organisations_id`),
  INDEX `fk_domains_organisations1_idx` (`organisations_id` ASC),
  CONSTRAINT `fk_domains_organisations1`
    FOREIGN KEY (`organisations_id`)
    REFERENCES `ppg`.`organisations` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
