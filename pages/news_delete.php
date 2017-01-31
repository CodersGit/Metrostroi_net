<?php
if (!$logged_user or $logged_user->max_icon_id() < 9) {
	include MITRASTROI_ROOT . "pages/403.php";
	exit();
}
$menu->set_item_active('news');
$admin_mode = false;

if (!isset($lnk[1])) {
	include MITRASTROI_ROOT . "pages/404.php";
	exit();
}

$query = $db->execute("SELECT `news`.*, `user_info_cache`.`nickname`, `news_cats`.`name` FROM `news` LEFT JOIN `news_cats` ON `news_cats`.`id`=`news`.`cat` LEFT JOIN `user_info_cache` ON `user_info_cache`.`steamid`=`news`.`author` WHERE `news`.`id`='{$db->safe($lnk[1])}'");
if ($db->num_rows($query) != 1) {
	include MITRASTROI_ROOT . "pages/404.php";
	exit();
}

if(isset($_POST['submit'])) {
	$db->execute("DELETE FROM `news` WHERE  `id`='{$db->safe($lnk[1])}'");
	header('Location: /news');
}
$query = $db->fetch_array($query);

$page_fucking_title = $query['title'] . " - Новости Метростроя";
include Mitrastroi::PathTPL("header");
include Mitrastroi::PathTPL("left_side");

include Mitrastroi::PathTPL("news/news_delete");
include Mitrastroi::PathTPL("news/news_view");

include Mitrastroi::PathTPL("right_side");
include Mitrastroi::PathTPL("footer");
