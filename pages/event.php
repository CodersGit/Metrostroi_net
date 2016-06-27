<?php

if (!isset($lnk[1])) {
	include Mitrastroi::PathTPL("event_index");
	exit();
}
switch ($lnk[1]) {
	case '':
		include Mitrastroi::PathTPL("event_index");
		break;
	case 'day1':
		include Mitrastroi::PathTPL("event_dayOne");
		break;
	default:
		include MITRASTROI_ROOT . "pages/404.php";
		break;
}