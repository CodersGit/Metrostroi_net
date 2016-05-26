<?php
if (!$tox1n_lenvaya_jopa or $tox1n_lenvaya_jopa->icon_id() < 9) {
	include MITRASTROI_ROOT . "pages/403.php";
	exit();
}

$add = !(isset($lnk[1]) and (int) $lnk[1]);
$what = ($add)? 'Добавить':'Отредактировать';

if (!$add) {
	$query = $db->execute("SELECT * FROM `news` WHERE `id`='{$db->safe($lnk[1])}'");
	if ($db->num_rows($query) != 1) {
		include MITRASTROI_ROOT . "pages/404.php";
		exit();
	}
	$query = $db->fetch_array($query);
}

$title = (isset($_POST['title']))? $_POST['title'] : (($add)?'':$query['title']);
$text = (isset($_POST['text']))? $_POST['text'] : (($add)?'':$query['text']);
$cat = (isset($_POST['cat']))? (int) $_POST['cat'] : (($add)?0:(int)$query['cat']);

if (isset($_POST['submit']) and strlen($title) and strlen($text) and strlen($title) <= 250) {
	$update_time = (isset($_POST['renew_date']) and $_POST['renew_date'])? ', `date`=NOW()':'';
	$db->execute(
		($add)? "INSERT INTO `news` (`title`,`text`,`cat`,`date`,`author`) VALUES ('{$db->safe($title)}','{$db->safe($text)}','{$db->safe($cat)}',NOW(),'{$db->safe($tox1n_lenvaya_jopa->steamid())}')":
			"UPDATE `news` SET `title`='{$db->safe($title)}', `text`='{$db->safe($text)}', `cat`='{$db->safe($cat)}'$update_time WHERE `id`='{$db->safe($lnk[1])}'"
	);
	header ('Location: /news/view/' . (($add)?$db->insert_id():$lnk[1]));
} elseif (isset($_POST['submit']))
	$status = "<div class=\"alert alert-danger\">Заголовок должен быть заполнен и быть не длиннее 250 символов, текст новости тоже должен быть.</div>";

$query = $db->execute("SELECT * FROM `news_cats` ORDER BY `priority` DESC");
$cats_select = '';
while ($tmp_cat = $db->fetch_array($query)) {
	$cats_select .= "<option value=\"{$tmp_cat['id']}\"" . (($cat == $tmp_cat['id'])?'selected':'') . ">{$tmp_cat['name']}";
}

$page_fucking_title = $what . " новость";
$menu->set_item_active('news_add');
include Mitrastroi::PathTPL("header");
include Mitrastroi::PathTPL("left_side");

include Mitrastroi::PathTPL("news/news_add");

include Mitrastroi::PathTPL("right_side");
include Mitrastroi::PathTPL("footer");
