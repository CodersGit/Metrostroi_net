<?php
if (!$logged_user or !$logged_user->take_group_info("up_down")) {
	include MITRASTROI_ROOT . "pages/403.php";
	exit();
}

if (!isset($lnk[1]) or $lnk[1]=='') {
	$tests = $db->execute("SELECT *, (SELECT `nickname` FROM `user_info_cache` WHERE `steamid`=`student`) AS `student_nickname` FROM `tests_results` WHERE `status`=2");
} else {
	$test = $db->execute("SELECT *, (SELECT `nickname` FROM `user_info_cache` WHERE `steamid`=`reviewer`) AS `reviewer_nickname`, (SELECT `nickname` FROM `user_info_cache` WHERE `steamid`=`student`) AS `student_nickname` FROM `tests_results` WHERE `trid`='{$db->safe($lnk[1])}'");
	if (!$db->num_rows($test)) {
		include MITRASTROI_ROOT . "pages/404.php";
		exit();
	}
	$test = $db->fetch_array($test);
	if ($test['status'] < 2) {
		include MITRASTROI_ROOT . "pages/404.php";
		exit();
	}
	$questions = json_decode($test['questions']);
	$answers = json_decode($test['answers']);
	$answers_marks = json_decode($test['answers_marks']);
	if($test['status'] >= 2 and isset($_POST['submit'])) {
		$result = (int) $_POST['submit'];
		$tmp_marks = array();
		foreach ($questions as $number=>$question) {
			$tmp_marks[$number] = (isset($_POST['mark' . $number]))? $_POST['mark' . $number]: '';
		}
		$db->execute("UPDATE `tests_results` SET `status`=3, `answers_marks`='{$db->safe(json_encode($tmp_marks))}', `reviewer`='{$logged_user->steamid()}', `review_date`=NOW(), `passed`='{$db->safe($result)}' WHERE `trid`='{$db->safe($test['trid'])}'");
		header('Location: /admin_tests');
	}
	if($test['status'] == 0)
		$db->execute("UPDATE `tests_results` SET `status`=1, `recived_date`=NOW() WHERE `trid`='{$db->safe($test['trid'])}'");
}

$menu->set_item_active('admin_tests');
$page_fucking_title = "Непроверенные тесты";
include Mitrastroi::PathTPL("header");
include Mitrastroi::PathTPL("left_side");

if (!isset($lnk[1]) or $lnk[1]=='')
	include Mitrastroi::PathTPL("tests/admin/main");
else
	include Mitrastroi::PathTPL("tests/admin/test");

include Mitrastroi::PathTPL("right_side");
include Mitrastroi::PathTPL("footer");
