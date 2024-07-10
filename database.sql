-- MySQL Script generated by MySQL Workbench
-- Fri Jul  5 16:59:19 2024
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema midiATech
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `midiATech` ;

-- -----------------------------------------------------
-- Schema midiATech
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `midiATech` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci ;
USE `midiATech` ;

-- -----------------------------------------------------
-- Table `midiATech`.`users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `midiATech`.`users` ;

CREATE TABLE IF NOT EXISTS `midiATech`.`users` (
                                                   `id` INT NOT NULL AUTO_INCREMENT,
                                                   `firstname` VARCHAR(80) NOT NULL,
                                                   `lastname` VARCHAR(80) NOT NULL,
                                                   `birthday` DATE NOT NULL,
                                                   `email` VARCHAR(255) NOT NULL,
                                                   `address` VARCHAR(400) NOT NULL,
                                                   `password` VARCHAR(255) NOT NULL,
                                                   `temporary_password` VARCHAR(255) NULL,
                                                   PRIMARY KEY (`id`))
    ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `midiATech`.`categories`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `midiATech`.`categories` ;

CREATE TABLE IF NOT EXISTS `midiATech`.`categories` (
                                                        `id` INT NOT NULL AUTO_INCREMENT,
                                                        `name` VARCHAR(100) NOT NULL,
                                                        PRIMARY KEY (`id`))
    ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `midiATech`.`books`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `midiATech`.`books` ;

CREATE TABLE IF NOT EXISTS `midiATech`.`books` (
                                                   `id` INT NOT NULL AUTO_INCREMENT,
                                                   `title` VARCHAR(255) NOT NULL,
                                                   `picture` VARCHAR(500) NULL,
                                                   `description` LONGTEXT NULL,
                                                   `author` VARCHAR(255) NOT NULL,
                                                   `date` DATE NOT NULL,
                                                   `pages` INT NOT NULL,
                                                   `quantities` INT NOT NULL,
                                                   `id_category` INT NOT NULL,
                                                   PRIMARY KEY (`id`),
                                                   INDEX `fk_books_categories1_idx` (`id_category` ASC) VISIBLE,
                                                   CONSTRAINT `fk_books_categories`
                                                       FOREIGN KEY (`id_category`)
                                                           REFERENCES `midiATech`.`categories` (`id`)
                                                           ON DELETE NO ACTION
                                                           ON UPDATE NO ACTION)
    ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `midiATech`.`types`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `midiATech`.`types` ;

CREATE TABLE IF NOT EXISTS `midiATech`.`types` (
                                                   `id` INT NOT NULL AUTO_INCREMENT,
                                                   `name` VARCHAR(100) NOT NULL,
                                                   PRIMARY KEY (`id`))
    ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `midiATech`.`videos`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `midiATech`.`videos` ;

CREATE TABLE IF NOT EXISTS `midiATech`.`videos` (
                                                    `id` INT NOT NULL AUTO_INCREMENT,
                                                    `title` VARCHAR(255) NOT NULL,
                                                    `picture` VARCHAR(500) NULL,
                                                    `description` LONGTEXT NULL,
                                                    `director` VARCHAR(255) NOT NULL,
                                                    `date` DATE NOT NULL,
                                                    `duration` INT NOT NULL,
                                                    `quantities` INT NOT NULL,
                                                    `id_category` INT NOT NULL,
                                                    `id_types` INT NOT NULL,
                                                    PRIMARY KEY (`id`),
                                                    INDEX `fk_videos_categories1_idx` (`id_category` ASC) VISIBLE,
                                                    INDEX `fk_videos_types1_idx` (`id_types` ASC) VISIBLE,
                                                    CONSTRAINT `fk_videos_categories`
                                                        FOREIGN KEY (`id_category`)
                                                            REFERENCES `midiATech`.`categories` (`id`)
                                                            ON DELETE NO ACTION
                                                            ON UPDATE NO ACTION,
                                                    CONSTRAINT `fk_videos_types`
                                                        FOREIGN KEY (`id_types`)
                                                            REFERENCES `midiATech`.`types` (`id`)
                                                            ON DELETE NO ACTION
                                                            ON UPDATE NO ACTION)
    ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `midiATech`.`musics`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `midiATech`.`musics` ;

CREATE TABLE IF NOT EXISTS `midiATech`.`musics` (
                                                    `id` INT NOT NULL AUTO_INCREMENT,
                                                    `title` VARCHAR(255) NOT NULL,
                                                    `picture` VARCHAR(500) NULL,
                                                    `singer` VARCHAR(255) NOT NULL,
                                                    `date` DATE NOT NULL,
                                                    `duration` INT NOT NULL,
                                                    `quantities` INT NOT NULL,
                                                    `id_category` INT NOT NULL,
                                                    PRIMARY KEY (`id`),
                                                    INDEX `fk_musics_categories1_idx` (`id_category` ASC) VISIBLE,
                                                    CONSTRAINT `fk_musics_categories`
                                                        FOREIGN KEY (`id_category`)
                                                            REFERENCES `midiATech`.`categories` (`id`)
                                                            ON DELETE NO ACTION
                                                            ON UPDATE NO ACTION)
    ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `midiATech`.`borrowing`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `midiATech`.`borrowing` ;

CREATE TABLE IF NOT EXISTS `midiATech`.`borrowing` (
                                                       `id` INT NOT NULL AUTO_INCREMENT,
                                                       `id_users` INT NOT NULL,
                                                       `id_media` INT NOT NULL,
                                                       `media_type` ENUM('book', 'music', 'video') NOT NULL,
                                                       `date` DATE NOT NULL,
                                                       PRIMARY KEY (`id`),
                                                       INDEX `fk_users_idx` (`id_users` ASC) VISIBLE,
                                                       CONSTRAINT `fk_borrowing_users`
                                                           FOREIGN KEY (`id_users`)
                                                               REFERENCES `midiATech`.`users` (`id`)
                                                               ON DELETE NO ACTION
                                                               ON UPDATE NO ACTION
) ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

INSERT INTO `categories` (`name`) VALUES 
('Roman'),
('Policier'),
('Science-fiction'),
('Fantastique'),
('Histoire'),
('Essai');

INSERT INTO `books` (`title`, `picture`, `description`, `author`, `date`, `pages`, `quantities`, `id_category`) VALUES 
('L\'Étranger', 'letranger.jpg', 'Un roman de l\'absurde écrit par Albert Camus.', 'Albert Camus', '1942-05-01', 123, 10, 1),
('Le Petit Prince', 'le_petit_prince.jpg', 'Un conte poétique et philosophique écrit par Antoine de Saint-Exupéry.', 'Antoine de Saint-Exupéry', '1943-04-06', 96, 15, 1),
('Millénium : Les hommes qui n\'aimaient pas les femmes', 'millenium.jpg', 'Un roman policier suédois écrit par Stieg Larsson.', 'Stieg Larsson', '2005-08-09', 465, 20, 2),
('Dune', 'dune.jpg', 'Un roman de science-fiction écrit par Frank Herbert.', 'Frank Herbert', '1965-08-01', 412, 8, 3),
('Harry Potter à l \'école des sorciers', 'harry_potter.jpg', 'Un roman fantastique écrit par J.K. Rowling.', 'J.K. Rowling', '1997-06-26', 309, 12, 4 ),
('Sapiens : Une brève histoire de l\'humanité', 'sapiens.jpg', 'Un essai explorant l\'histoire de l\'espèce humaine.', 'Yuval Noah Harari', '2011-06-04', 498, 10, 6),
('Les Misérables', 'les_miserables.jpg', 'Un roman historique écrit par Victor Hugo.', 'Victor Hugo', '1862-01-14', 1488, 11, 5),
('1984', '1984.jpg', 'Un roman de science-fiction dystopique écrit par George Orwell.', 'George Orwell', '1949-06-08', 328, 9, 3),
('Le Nom de la rose', 'le_nom_de_la_rose.jpg', 'Un roman policier historique écrit par Umberto Eco.', 'Umberto Eco', '1980-04-01', 500, 14, 2);





