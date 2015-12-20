<?php
$page_fucking_title = "Добавить пользователя...";
$menu->set_item_active('lists_add');
include Mitrastroi::PathTPL("header");
include Mitrastroi::PathTPL("left_side");

include Mitrastroi::PathTPL("user_add");

include Mitrastroi::PathTPL("right_side");
include Mitrastroi::PathTPL("footer");