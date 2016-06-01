<?php
$menu->set_item_active('news');
$admin_mode = $tox1n_lenvaya_jopa and $tox1n_lenvaya_jopa->icon_id() >= 9;
if (!isset($lnk[1])) $lnk[1] = 1;
switch ($lnk[1]) {
	case 'view':
		if (!isset($lnk[2])) {
			include MITRASTROI_ROOT . "pages/404.php";
			exit();
		}

		$query = $db->execute("SELECT `news`.*, `user_info_cache`.`nickname`, `news_cats`.`name` FROM `news` LEFT JOIN `news_cats` ON `news_cats`.`id`=`news`.`cat` LEFT JOIN `user_info_cache` ON `user_info_cache`.`steamid`=`news`.`author` WHERE `news`.`id`='{$db->safe($lnk[2])}'");
		if ($db->num_rows($query) != 1) {
			include MITRASTROI_ROOT . "pages/404.php";
			exit();
		}
		$query = $db->fetch_array($query);

		Mitrastroi::TakeClass('comments');
		$page_fucking_title = $query['title'] . " - Новости Метростроя";
		include Mitrastroi::PathTPL("header");
		include Mitrastroi::PathTPL("left_side");

		include Mitrastroi::PathTPL("news/news_view");

		include Mitrastroi::PathTPL("right_side");
		include Mitrastroi::PathTPL("footer");
		break;
	case 'cat':
		$lnk_index = 3;
		$where = " WHERE `cat`='{$db->safe($lnk[2])}'";
		$url = "/news/cat/". htmlspecialchars((int)$lnk[2]) . '/';
	default:
		$num_by_page = 8;
		if (!isset($lnk_index)) $lnk_index = 1;
		if (!isset($where)) $where = '';
		if (!isset($url)) $url = '/news/';
		$page = (isset($lnk[$lnk_index]) and ((int) $lnk[$lnk_index]) > 0)? ((int) $lnk[$lnk_index]): 1;
		$first = ($page - 1) * $num_by_page;
		$news = $db->execute("SELECT `news`.*, `user_info_cache`.`nickname`, `news_cats`.`name` FROM `news` LEFT JOIN `news_cats` ON `news_cats`.`id`=`news`.`cat` LEFT JOIN `user_info_cache` ON `user_info_cache`.`steamid`=`news`.`author`$where ORDER BY `date` DESC LIMIT $first, $num_by_page");
		if (!$db->num_rows($news)) {
			include MITRASTROI_ROOT . "pages/404.php";
			exit();
		}

		$page_fucking_title = "Новости Метростроя - Страница " . $page;
		include Mitrastroi::PathTPL("header");
		include Mitrastroi::PathTPL("left_side");

		while ($query = $db->fetch_array($news)) {
			$query['text'] = str_replace(stristr($query['text'], '<cut>'), '', $query['text']);
			include Mitrastroi::PathTPL("news/news_preview");
		}

		$query = $db->execute("SELECT COUNT(*) FROM `news`$where");
		$query = $db->fetch_array($query);
		echo Mitrastroi::GeneratePagination($page, $num_by_page, $query[0], $url);

		include Mitrastroi::PathTPL("right_side");
		include Mitrastroi::PathTPL("footer");
		break;
}