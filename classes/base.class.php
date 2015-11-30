<?php
class Mitrastroi {
	public static function TakeClass ($class) {
		if (file_exists(MITRASTROI_ROOT . "classes/$class.class.php")) {
			include(MITRASTROI_ROOT . "classes/$class.class.php");
			return true;
		}
		return false;
	}
	public static function TakeTPL ($tpl) {
		if (file_exists(MITRASTROI_ROOT . "tpl/$tpl.html")) {
			include(MITRASTROI_ROOT . "tpl/$tpl.html");
			return true;
		}
		return false;
	}
	public static function PathTPL ($tpl) {
		if (file_exists(MITRASTROI_ROOT . "tpl/$tpl.html")) {
			return (MITRASTROI_ROOT . "tpl/$tpl.html");
		}
		return false;
	}
	public static function GeneratePagination($page, $amount_by_page, $total_amount) {

	}
}