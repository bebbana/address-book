1. Install apache/mysql/php 7.1
2. Install Nette Framework https://nette.org/cs/download
3.
3. Unzip or clone project to local php scripts folder and open in IDE as nette project
4. Create new mysql database `address-book` and new table users:

CREATE DATABASE address-book;

CREATE TABLE `address-book`.`users` 
( `id` INT NOT NULL AUTO_INCREMENT , 
`fullname` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL , 
`email` VARCHAR(50) NOT NULL , 
`telnumber` VARCHAR(13) NULL ,
`url` VARCHAR(50) NOT NULL , 
`counter` INT(5) NULL , 
`memo` TEXT NULL , PRIMARY KEY (`id`)) 
ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_czech_ci;
