<?php
define('MITRASTROI_ROOT', dirname(__FILE__).'/');
require ("config.php");
date_default_timezone_set('Europe/Moscow');
$_STEAMAPI = $config['steam_api_key'];
require ("classes/base.class.php");
Mitrastroi::TakeClass('db');
Mitrastroi::TakeClass('user');
Mitrastroi::TakeClass('menu');
$main_page = 'home';
$show_login = false;
$db = new DB($config['db_base'],$config['db_host'],$config['db_user'], $config['db_pass'], $config['db_port']);
$db->connect();
Mitrastroi::TakeAuth();
if($tox1n_lenvaya_jopa and isset($_POST['cancel-mag-reports-alert'])) $db->execute("UPDATE `mag_reports` SET `mag_sender_heavy_read`=1 WHERE `mag_reporter`='{$tox1n_lenvaya_jopa->steamid()}'");
$menu = new Menu();
if (isset($_GET['page'])) $lnk = explode('/', $_GET['page']);
$mode = (isset($lnk[0]))? $lnk[0]: $main_page;
include (file_exists(MITRASTROI_ROOT . "pages/$mode.php"))? (MITRASTROI_ROOT . "pages/$mode.php"): (MITRASTROI_ROOT . "pages/404.php");