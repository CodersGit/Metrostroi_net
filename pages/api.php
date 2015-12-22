<?php
if (!isset($lnk[1])) {
	include MITRASTROI_ROOT . "pages/404.php";
	exit;
}
switch ($lnk[1]) {
	case 'bad':
		if (!isset($lnk[2])) {
			include MITRASTROI_ROOT . "pages/404.php";
			exit;
		}
		$query = $db->execute("SELECT `reason` FROM `blacklist` WHERE `steam_id`='{$db->safe($lnk[2])}'");
		$query or die($db->error());
		if ($db->num_rows($query)) {
			$query = $db->fetch_array($query);
			exit($query['reason']);
		}
		break;
	default:
		include MITRASTROI_ROOT . "pages/404.php";
		exit;
		break;
}