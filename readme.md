Project instructions
=============

1. Install apache/mysql/php 7.1
2. Install Nette Framework https://nette.org/cs/download
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

SANDBOX
----------

Sandbox is a pre-packaged and pre-configured Nette Framework application
that you can use as the skeleton for your new applications.

[Nette](http://nette.org) is a popular tool for PHP web development.
It is designed to be the most usable and friendliest as possible. It focuses
on security and performance and is definitely one of the safest PHP frameworks.


Installing
----------

The best way to install Sandbox is using Composer. If you don't have Composer yet, download
it following [the instructions](http://doc.nette.org/composer). Then use command:

		composer create-project nette/sandbox my-app
		cd my-app

Make directories `temp` and `log` writable. Navigate your browser
to the `www` directory and you will see a welcome page. PHP 5.4 allows
you run `php -S localhost:8888 -t www` to start the web server and
then visit `http://localhost:8888` in your browser.

It is CRITICAL that whole `app`, `log` and `temp` directories are NOT accessible
directly via a web browser! See [security warning](http://nette.org/security-warning).


License
-------
- Nette: New BSD License or GPL 2.0 or 3.0 (http://nette.org/license)
- Adminer: Apache License 2.0 or GPL 2 (http://www.adminer.org)
