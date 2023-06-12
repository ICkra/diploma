<?php

defined(‘IYRYST’) or die(‘Access denied’);

// домен
define(‘PATH’, ‘http://www.vash-dogovir.com.ua//’);

// модель
define(‘MODEL’, ‘model/model.php’);

// контролер
define(‘CONTROLLER’, ‘controller/controller.php’);

// вид
define(‘VIEW’, ‘views/’);

// папка з активним шаблоном
define(‘TEMPLATE’, PATH.VIEW.’ivystavka/’);

// папка з картинками контента
define(‘DOCIMG’, PATH.’userfiles/doc_img/baseimg/’);

// папка з картинками галереї
define(‘GALLERYIMG’, PATH.’userfiles/doc_img/’);

// максимально допустима вага завантажуваних картинок - 1 Мб
define(‘SIZE’, 1048576);

// сервер БД
define(‘HOST’, ‘localhost’);

// користувач
define(‘USER’, ‘root’);

// пароль
define(‘PASS’, ‘‘);

// БД
define(‘DB’, ‘doc’);

// назва - title
define(‘TITLE’, ‘Система виставок’);

// email адміністратора
define(‘ADMIN_EMAIL’, ‘admin@doc.com’);

// кількість на сторінці
define(‘PERPAGE’, 9);

// папка шаблонов адміністративної частини
define(‘ADMIN_TEMPLATE’, ‘templates/’);

mysql_connect(HOST, USER, PASS) or die(‘No connect to Server’);
mysql_select_db(DB) or die(‘No connect to DB’);
mysql_query("SET NAMES ‘UTF8’") or die(‘Cant set charset’);
