<?php
if (!$tox1n_lenvaya_jopa or !$tox1n_lenvaya_jopa->take_group_info("change_group")) {
	include MITRASTROI_ROOT . "pages/403.php";
	exit();
}

$page_fucking_title = "Жалобы";
if (isset($lnk[1])) {
	$db->execute("DELETE FROM `reports` WHERE  `id`='{$db->safe($lnk[1])}'");
}

$menu->set_item_active('admin_reports');
include Mitrastroi::PathTPL("header");
include Mitrastroi::PathTPL("left_side");

$query = $db->execute("SELECT * FROM `reports` LEFT JOIN `user_info_cache` ON `target`=`steamid`");
while($pl_warn = $db->fetch_array($query)) {
	include Mitrastroi::PathTPL("admin/report");
}

include Mitrastroi::PathTPL("right_side");
include Mitrastroi::PathTPL("footer");