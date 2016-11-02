<?php
$page_fucking_title = "Список партнерских серверов";
$menu->set_item_active('servers');
include Mitrastroi::PathTPL("header");
include Mitrastroi::PathTPL("left_side");
$where = ($logged_user)?($logged_user->take_group_info("admin_panel"))?"":" WHERE `active`=1 OR `owner`='{$logged_user->steamid()}'":" WHERE `active`=1";
$query = $db->execute("SELECT * FROM `servers` LEFT JOIN `user_info_cache` ON `user_info_cache`.`steamid`=`servers`.`owner`$where ORDER BY `servers`.`id`") or die($db->error());
Mitrastroi::TakeTPL("servers/servers_head");
while($server = $db->fetch_array($query)) {
	include Mitrastroi::PathTPL("servers/server_row");
}
Mitrastroi::TakeTPL("servers/servers_foot");
include Mitrastroi::PathTPL("right_side");

include Mitrastroi::PathTPL("footer");