<?php
if (!$logged_user) {
	include MITRASTROI_ROOT . "pages/403.php";
	exit();
}

$statuses = array(
	"<span class='text-danger'>Не начат</span>",
	"<span class='text-primary'>Начат</span>",
	"<span class='text-info'>На проверке</span>",
	"<span class='text-green'>Проверен</span>",
);

if (!isset($lnk[1]) or $lnk[1]=='') {
	if(isset($_GET['take'])) {
		$query = $db->execute("SELECT * FROM `tests_results` WHERE `student`='{$logged_user->steamid()}' AND (`recived_date` > NOW() - INTERVAL 1 DAY OR `status`<2)");
		if(!$db->num_rows($query)) {
			$test = Mitrastroi::GenerateTest($_GET['take']);
			if ($test) {
				$db->execute("INSERT INTO `tests_results` (`status`, `student`, `questions`, `trname`, `recived_date`) "
					. "VALUES (0, '{$logged_user->steamid()}', '{$db->safe($test[0])}', '{$db->safe($test[1])}', NOW())") or die($db->error());
			} else $error = 'Не получилось сгенерировать тест';
		} else $error = 'Сегодня ты уже писал тест!';
	}
	$tests = $db->execute("SELECT * FROM `tests_results` WHERE `student`='{$logged_user->steamid()}' ORDER BY `recived_date` DESC ");
	$tests_take = $db->execute("SELECT * FROM `tests` ORDER BY `tpriority` DESC ");
} else {
	$test = $db->execute("SELECT *, (SELECT `nickname` FROM `user_info_cache` WHERE `steamid`=`reviewer`) AS `reviewer_nickname` FROM `tests_results` WHERE `student`='{$logged_user->steamid()}' AND `trid`='{$db->safe($lnk[1])}'");
	if (!$db->num_rows($test)) {
		include MITRASTROI_ROOT . "pages/404.php";
		exit();
	}
	$test = $db->fetch_array($test);
	$questions = json_decode($test['questions']);
	$answers = json_decode($test['answers']);
	if($test['status'] == 1 and isset($_POST['submit'])) {
		$tmp_answers = array();
		foreach ($questions as $number=>$question) {
			$tmp_answers[$number] = (isset($_POST['answer' . $number]))? $_POST['answer' . $number]: '';
		}
		$db->execute("UPDATE `tests_results` SET `status`=2, `completed_date`=NOW(), `answers`='{$db->safe(json_encode($tmp_answers))}' WHERE `trid`='{$db->safe($test['trid'])}'");
		header('Location: /tests');
	}
	if($test['status'] == 0)
		$db->execute("UPDATE `tests_results` SET `status`=1, `recived_date`=NOW() WHERE `trid`='{$db->safe($test['trid'])}'");
}

$page_fucking_title = "Мои тесты";
include Mitrastroi::PathTPL("header");
include Mitrastroi::PathTPL("left_side");

if (!isset($lnk[1]) or $lnk[1]=='')
	include Mitrastroi::PathTPL("tests/user/main");
else
	include Mitrastroi::PathTPL("tests/user/test");

include Mitrastroi::PathTPL("right_side");
include Mitrastroi::PathTPL("footer");
