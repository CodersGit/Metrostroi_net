<?php
if (!$logged_user or !$logged_user->take_group_info("edit_tests")) {
	include MITRASTROI_ROOT . "pages/404.php";
	exit();
}

$pl = (isset($lnk[1]))? $lnk[1]: 'STEAM_0:0:185551566';

$query = $db->execute("SELECT *, (SELECT `nickname` FROM `user_info_cache` WHERE `steamid`=`student`) AS `student_nickname` FROM `tests_results` WHERE `reviewer`='{$db->safe($pl)}'");
$print = '';
while ($test = $db->fetch_array($query)) {
	$print .= "\n\n-----------------------------\n*Сдавал:* {$test['student_nickname']} Сдал? {$test['passed']} \n-----------------------------\n";
	$questions = json_decode($test['questions']);
	$answers = json_decode($test['answers']);
	foreach ($questions as $number => $question) {
		$print .= "\n*В*: " . $question . "\n";
		$print .= "*О*: " . htmlspecialchars(($answers)?(isset($answers->$number))?$answers->$number:$answers[$number]:'') . "\n";
	}
}
print nl2br($print);
