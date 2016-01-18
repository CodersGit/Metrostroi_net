<?php
if (!$tox1n_lenvaya_jopa or !$tox1n_lenvaya_jopa->take_group_info("admin_panel")) {
	include MITRASTROI_ROOT . "pages/403.php";
	exit();
}
$page_fucking_title = "Одминка";
$menu->set_item_active('admin_data');

include Mitrastroi::PathTPL("header");
include Mitrastroi::PathTPL("left_side");

include Mitrastroi::PathTPL("admin/data_begin");

if (count($_POST)) {
	$check = true;
	foreach($_POST as $key=>$value) {
		$check = $check and Mitrastroi::SetData($key, $value);
	}
	echo ($check)?
		"<div class='alert alert-success'>Готово</div>":
		"<div class='alert alert-warning'>Чет пошло не так :(</div>";
}
$data = $db->execute("SELECT * FROM `data`");
while ($temp = $db->fetch_array($data))
	include Mitrastroi::PathTPL("admin/data_data");
include Mitrastroi::PathTPL("admin/data_end");

include Mitrastroi::PathTPL("right_side");
include Mitrastroi::PathTPL("footer");