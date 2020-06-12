# RestfulAPI

In this small project I have built a small RESTful API with authentication using ReactPHP. ReactPHP is a library that allows to do Asynchronous programming with PHP or non-blocking PHP.

[ReactPHP](https://reactphp.org/)

## What you will need
Create a MySQL database

```mysql
CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_uindex` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
```

Insert some users

On `index.php` update the MySQL connection credentials

Play with the Postman collection

## Usage

```bash
$ composer update
$ php index.php
```

## Requisites
* PHP 7
* MySQL
* Composer
