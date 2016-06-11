<?php

if (!isset($lnk[1])) {
	include Mitrastroi::PathTPL("event_index");
	exit();
}
$day = 1;
switch ($lnk[1]) {
	case '':
		include Mitrastroi::PathTPL("event_index");
		break;
	case 'day1':
		include Mitrastroi::PathTPL("event_dayOne");
		break;
	case 'day2':
		include ($day >= 2)? Mitrastroi::PathTPL("event_dayTwo"): (MITRASTROI_ROOT . "pages/404.php");
		break;
	case 'day3':
		include ($day >= 3)? Mitrastroi::PathTPL("event_dayThree"): (MITRASTROI_ROOT . "pages/404.php");
		break;
	default:
		include MITRASTROI_ROOT . "pages/404.php";
		break;
}