<?php
if (!$logged_user or !$logged_user->take_group_info("tickets")) {
	include MITRASTROI_ROOT . "pages/403.php";
	exit();
}

$page_fucking_title = "Управление тикетами";
$menu->set_item_active('admin_tickets');
include Mitrastroi::PathTPL("header");
include Mitrastroi::PathTPL("left_side");

if (isset($lnk[1])) {
	$pl = new User($lnk[1], 'SID');
	if ($pl->uid() < 1) {
		include Mitrastroi::PathTPL("404");
		include Mitrastroi::PathTPL("footer");
		exit();
	}
	include Mitrastroi::PathTPL("tickets/adm_info");
	include Mitrastroi::PathTPL("tickets/adm_ticket_add");
	if (isset($_POST['message'])) {
		$db->execute("INSERT INTO `tickets` (`written`,`owner`,`text`,`date`,`type`,`viewed`) VALUES ('{$logged_user->steamid()}','{$pl->steamid()}','{$db->safe($_POST['message'])}',NOW(),4,0)");
	}

	if (isset($_POST['status']) and isset($_POST['id'])) {
		$db->execute("UPDATE `tickets` SET `type`='{$db->safe($_POST['status'])}' WHERE `tid`='{$db->safe($_POST['id'])}'");
	}

	if (isset($_POST['delete']) and isset($_POST['id'])) {
		$db->execute("DELETE FROM `tickets` WHERE  `tid`='{$db->safe($_POST['id'])}'");
	}

	$query = $db->execute("SELECT * FROM `tickets` LEFT JOIN `user_info_cache` ON `written`=`steamid` WHERE `owner`='{$pl->steamid()}' ORDER BY `date` DESC");
	while($ticket = $db->fetch_array($query)) {
		switch ($ticket['type']) {
			case 0: $status = "(Новый) "; break;
			case 1: $status = "(Просмотрен) "; break;
			case 2: $status = "(Закрыт) "; break;
			case 3: $status = "(Отклонен) "; break;
			case 4: $status = "<div class='label label-primary'>Сообщение администратора</div> "; break;
			default: $status = '';
		}
		include Mitrastroi::PathTPL("tickets/adm_ticket");
	}
} else {
	include Mitrastroi::PathTPL("tickets/adm_ticket_head");
	$query = $db->execute("SELECT * FROM `user_info_cache`, `tickets` WHERE `written`=`steamid` AND `tid`=(SELECT `tid` FROM `tickets` WHERE `written`=`steamid` AND `type`<2 ORDER BY `date` DESC LIMIT 1) ORDER BY (SELECT `date` FROM `tickets` WHERE `written`=`steamid` AND `type`<2 ORDER BY `date` DESC LIMIT 1) ASC") or die($db->error());
	while($ticket = $db->fetch_array($query)) {
		switch ($ticket['type']) {
			case 0: $status = "<div class='label label-success'><i class='fa fa-star'></i></div> "; break;
			case 1: $status = "<div class='label label-warning'><i class='fa fa-eye'></i></div> "; break;
			default: $status = '';
		}
		include Mitrastroi::PathTPL("tickets/adm_ticket_preview");
	}
}

include Mitrastroi::PathTPL("right_side");
include Mitrastroi::PathTPL("footer");