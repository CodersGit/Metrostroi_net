<?php
if (!$logged_user or !$logged_user->take_group_info("edit_tests")) {
	include MITRASTROI_ROOT . "pages/403.php";
	exit();
}

$menu->set_item_active('admin_questions');
$page_fucking_title = "Управление тестами";
include Mitrastroi::PathTPL("header");
include Mitrastroi::PathTPL("left_side");

if (!isset($lnk[1]) or $lnk[1] == '')
	$lnk[1] = 'main';

switch ($lnk[1]) {
	case 'main':
		if (isset($_GET['deltest']))
			$db->execute("DELETE FROM `tests` WHERE  `tid`='{$db->safe($_GET['deltest'])}'");
		$cats = $db->execute("SELECT * FROM `questions_cats`");
		$tests = $db->execute("SELECT * FROM `tests` ORDER BY `tpriority` DESC ");
		include Mitrastroi::PathTPL("tests/admin/qmain");
		break;
	case 'test':
		if (!isset($lnk[2])) {
			include Mitrastroi::PathTPL("404");
			include Mitrastroi::PathTPL("footer");
			exit();
		}
		if (isset($_POST['name']) and isset($_POST['priority']) and isset($_POST['qcats'])) {
			$qcats = json_encode(explode(';',$_POST['qcats']));
			$db->execute("UPDATE `tests` SET `tname`='{$db->safe($_POST['name'])}', "
										  . "`tpriority`='{$db->safe($_POST['priority'])}', "
										  . "`questions_cats`='{$db->safe($qcats)}' WHERE `tid`='{$db->safe($lnk[2])}'") or die($db->error());
		}
		$test = $db->execute("SELECT * FROM `tests` WHERE `tid`='{$db->safe($lnk[2])}'");
		$cats = $db->execute("SELECT * FROM `questions_cats`");
		if ($db->num_rows($test)!=1) {
			include Mitrastroi::PathTPL("404");
			include Mitrastroi::PathTPL("footer");
			exit();
		}
		if (!$db->num_rows($cats)) {
			include Mitrastroi::PathTPL("tests/admin/qnocats");
			include Mitrastroi::PathTPL("right_side");
			include Mitrastroi::PathTPL("footer");
			exit();
		}
		$test = $db->fetch_array($test);
		$testcats = '';
		foreach (json_decode($test['questions_cats']) as $cat)
			$testcats .= ';' . $cat;
		$testcats = mb_substr($testcats, 1);
		include Mitrastroi::PathTPL("tests/admin/qtest");
		break;
	case 'add_test':
		if (isset($_POST['name']) and isset($_POST['priority']) and isset($_POST['qcats'])) {
			$qcats = json_encode(explode(';',$_POST['qcats']));
			$db->execute("INSERT INTO `tests` (`tname`, `tpriority`, `questions_cats`)"
					   . "VALUES ('{$db->safe($_POST['name'])}','{$db->safe($_POST['priority'])}','{$db->safe($qcats)}')") or die($db->error());
//			header("Location /admin_questions/");
		}
		$cats = $db->execute("SELECT * FROM `questions_cats`");
		if (!$db->num_rows($cats)) {
			include Mitrastroi::PathTPL("tests/admin/qnocats");
			include Mitrastroi::PathTPL("right_side");
			include Mitrastroi::PathTPL("footer");
			exit();
		}
		include Mitrastroi::PathTPL("tests/admin/qaddtest");
		break;
	case 'add_cat':
		if (isset($_POST['name']))
			$db->execute("INSERT INTO `questions_cats` (`qcname`) VALUES ('{$db->safe($_POST['name'])}')") or die($db->error());
		include Mitrastroi::PathTPL("tests/admin/qaddcat");
		break;
	default:
		if (isset($_POST['qcname']))
			$db->execute("UPDATE `questions_cats` SET `qcname`='{$db->safe($_POST['qcname'])}' WHERE `qcid`='{$db->safe($lnk[1])}'");
		$cat = $db->execute("SELECT * FROM `questions_cats` WHERE `qcid`='{$db->safe($lnk[1])}'");
		if ($db->num_rows($cat)!=1) {
			include Mitrastroi::PathTPL("404");
			include Mitrastroi::PathTPL("footer");
			exit();
		}
		$cat = $db->fetch_array($cat);
		$c = 1;
		if (isset($_POST['add']) and isset($_POST['question']))
			$db->execute("INSERT INTO `questions` (`question`, `cat`) VALUES ('{$db->safe($_POST['question'])}','{$db->safe($lnk[1])}')");
		if (isset($_POST['edit']) and isset($_POST['qid']) and isset($_POST['question']))
			$db->execute("UPDATE `questions` SET `question`='{$db->safe($_POST['question'])}' WHERE `qid`='{$db->safe($_POST['qid'])}'");
		if (isset($_POST['delete']) and isset($_POST['qid']))
			$db->execute("DELETE FROM `questions` WHERE  `qid`='{$db->safe($_POST['qid'])}'");
		$questions = $db->execute("SELECT * FROM `questions` WHERE `cat`='{$db->safe($cat['qcid'])}'");
		include Mitrastroi::PathTPL("tests/admin/qcat");
		break;
}

include Mitrastroi::PathTPL("right_side");
include Mitrastroi::PathTPL("footer");
