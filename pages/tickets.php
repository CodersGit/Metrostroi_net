<?php
if (!$logged_user) {
	include MITRASTROI_ROOT . "pages/403.php";
	exit();
}

$page_fucking_title = "Тикеты";
include Mitrastroi::PathTPL("header");
include Mitrastroi::PathTPL("left_side");

include Mitrastroi::PathTPL("tickets/ticket_add");

if (isset($_POST['message'])) {
	$db->execute("INSERT INTO `tickets` (`written`,`owner`,`text`,`date`,`type`,`viewed`) VALUES ('{$logged_user->steamid()}','{$logged_user->steamid()}','{$db->safe($_POST['message'])}',NOW(),0,1)");
}

$query = $db->execute("SELECT * FROM `tickets` LEFT JOIN `user_info_cache` ON `written`=`steamid` WHERE `owner`='{$logged_user->steamid()}' ORDER BY `date` DESC");
while($ticket = $db->fetch_array($query)) {
	switch ($ticket['type']) {
		case 0: $status = "(Новый) "; break;
		case 1: $status = "(Просмотрен) "; break;
		case 2: $status = "(Закрыт) "; break;
		case 3: $status = "(Отклонен) "; break;
		case 4: $status = "<div class='label label-primary'>Сообщение администратора</div> "; break;
		default: $status = '';
	}
	include Mitrastroi::PathTPL("tickets/ticket");
}
$db->execute("UPDATE `tickets` SET `viewed`=1 WHERE `owner`='{$logged_user->steamid()}'");

include Mitrastroi::PathTPL("right_side");
include Mitrastroi::PathTPL("footer");