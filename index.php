<?php

// корневая директория фреймворка
define('ROOT_DIR', getcwd());

// директория контроллеров
define('CONTROLLER_DIR', ROOT_DIR . '/controllers/');
// директория моделей
define('MODEL_DIR', ROOT_DIR . '/models/');
// директория представлений
define('VIEW_DIR', ROOT_DIR . '/views/');
// директория для скомпилированных файлов представления
define('VIEW_CACHE_DIR', ROOT_DIR . '/views/cache/');
// директория для служебных файлов
define('SYSTEM_DIR', ROOT_DIR . '/system/');

// файл с роутами
define('ROUTES', ROOT_DIR . '/routes.php');

/*
 * включаю служебные файлы, до того как будут вызываться контроллеры и все остальное
 * */
require_once(SYSTEM_DIR . 'Controller.php');
require_once(SYSTEM_DIR . 'Framework.php');


/*
 * конфигурирование подключения к бд
 * */
/*
 * http://idiorm.readthedocs.org/en/latest/
 * http://paris.readthedocs.org/en/latest/
 * */
require_once(SYSTEM_DIR . 'idiorm.php');
require_once(SYSTEM_DIR . 'paris.php');


/*
 * http://stackoverflow.com/a/599694
 *
 * включаю файлы моделей
 * */
foreach (glob(MODEL_DIR . "*.php") as $filename)
{
    include $filename;
}

define('MYSQL_HOST', 'localhost');
define('MYSQL_USER_NAME', 'root');
define('MYSQL_USER_PWD', 'root');
define('MYSQL_DB', 'some_db');

ORM::configure('mysql:host='. MYSQL_HOST .';dbname='. MYSQL_DB);
ORM::configure('username', MYSQL_USER_NAME);
ORM::configure('password', MYSQL_USER_PWD);


$framework = new Framework();

$framework->requestStart();

