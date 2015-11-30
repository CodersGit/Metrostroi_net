<?php
require ("config.php");
require ("classes/base.class.php");
Mitrastroi::TakeClass('db');
define('MITRASTROI_ROOT', dirname(__FILE__).'/');
$main_page = 'home';
$db = new DB($config['db_base'],$config['db_host'],$config['db_user'], $config['db_pass'], $config['db_port']);
$db->connect();
include (file_exists(MITRASTROI_ROOT . "pages/{$_GET['page']}.php"))? (MITRASTROI_ROOT . "pages/{$_GET['page']}.php"): (MITRASTROI_ROOT . "pages/404.php");