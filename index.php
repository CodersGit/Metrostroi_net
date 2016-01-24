<?php
define('MITRASTROI_ROOT', dirname(__FILE__).'/');
require ("config.php");
date_default_timezone_set('Europe/Moscow');
$_STEAMAPI = $config['steam_api_key'];
require ("classes/base.class.php");
echo Mitrastroi::PathTPL("pagination/pagin_item_active.html");
echo Mitrastroi::PathTPL("pagination/pagin_item_inactive.html");
Mitrastroi::TakeClass('db');
Mitrastroi::TakeClass('user');
Mitrastroi::TakeClass('menu');
$main_page = 'home';
$db = new DB($config['db_base'],$config['db_host'],$config['db_user'], $config['db_pass'], $config['db_port']);
$db->connect();
Mitrastroi::TakeAuth();
$menu = new Menu();
if (isset($_GET['page'])) $lnk = explode('/', $_GET['page']);
$mode = (isset($lnk[0]))? $lnk[0]: $main_page;
include (file_exists(MITRASTROI_ROOT . "pages/$mode.php"))? (MITRASTROI_ROOT . "pages/$mode.php"): (MITRASTROI_ROOT . "pages/404.php");