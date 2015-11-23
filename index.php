<?php
require ("config.php");
require ("classes/db.class.php");
$db = new DB($config['db_base'],$config['db_host'],$config['db_user'], $config['db_pass'], $config['db_port']);
$db->connect();