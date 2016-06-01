<?php

class Comments
{
	public static function ShowComments($type, $id, $hold_place = false)
	{
		global $db, $tox1n_lenvaya_jopa;
		$access = ($tox1n_lenvaya_jopa) ? ($tox1n_lenvaya_jopa->take_group_info("delete_comment")) ? 2 : 1 : 0;
		if ($access and isset($_POST['message']) and mb_strlen($message = $db->safe($_POST['message']), 'utf8') and mb_strlen($message, 'utf8') < 256) {
			$db->execute("INSERT INTO `comments` (`type`, `item_id`, `author`, `text`) VALUES ($type, '$id', '{$tox1n_lenvaya_jopa->steamid()}', '$message')") or die($db->error());
		}
		if ($access == 2 and isset($_POST['delete'])) {
			$db->execute("DELETE FROM `comments` WHERE  `cid`='{$db->safe($_POST['delete'])}'") or die($db->error());
		}
		$comments = $db->execute("SELECT `comments`.*, `user_info_cache`.*, `players`.`icon` FROM `comments` LEFT JOIN `user_info_cache` ON `comments`.`author`=`user_info_cache`.`steamid` LEFT JOIN `players` ON `comments`.`author`=`players`.`SID` WHERE `type`='{$db->safe($type)}' AND `item_id`='{$db->safe($id)}' ORDER BY `comments`.`date` DESC");
		if ($access > 0) include Mitrastroi::PathTPL("comments/comment_add");
		if ($db->num_rows($comments))
			while ($comment = $db->fetch_array($comments)) {
				include Mitrastroi::PathTPL("comments/comment");
			}
		elseif ($hold_place and $access == 0)
			Mitrastroi::TakeTPL('comments/no_comments');
	}
}