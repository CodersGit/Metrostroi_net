<?php
class Menu {
	private $menu;
	public function Menu() {
		$this->menu = array(
			'admin' => array(
				'url' => '/admin_constants',
				'title' => 'Одменка',
				'active' => false,
				'place' => 0,
				'parent' => false,
				'right' => 'tickets',
			),
			'admin_data' => array(
				'url' => '/admin_constants',
				'title' => 'Константы',
				'active' => false,
				'place' => 0,
				'parent' => 'admin',
				'right' => 'admin_panel',
			),
			'admin_MAG' => array(
				'url' => '/admin_mag',
				'title' => 'MAG: Жалобы',
				'active' => false,
				'place' => 0,
				'parent' => 'admin',
				'right' => 'mag_reports',
			),
			'admin_tickets' => array(
				'url' => '/admin_tickets',
				'title' => 'Тикеты',
				'active' => false,
				'place' => 0,
				'parent' => 'admin',
				'right' => 'tickets',
			),
			'admin_tests' => array(
				'url' => '/admin_tests',
				'title' => 'Тесты',
				'active' => false,
				'place' => 0,
				'parent' => 'admin',
				'right' => 'tickets',
			),
			'admin_questions' => array(
				'url' => '/admin_questions',
				'title' => 'Вопросы к тестам',
				'active' => false,
				'place' => 0,
				'parent' => 'admin',
				'right' => 'tickets',
			),
			'news' => array(
				'url'=>'/news',
				'title'=>'Новости',
				'active'=>false,
				'place' => 0,
				'parent' => false,
			),
			'lists' => array(
				'url'=>'/players',
				'title'=>'Списки',
				'active'=>false,
				'place' => 0,
				'parent' => false,
			),
			'players' => array(
				'url'=>'/players',
				'title'=>'Список званий',
				'active'=>false,
				'place' => 0,
				'parent' => 'lists',
			),
			'MAG_list' => array(
				'url'=>'/mag/list',
				'title'=>'Список последних MAG-банов',
				'active'=>false,
				'place' => 0,
				'parent' => 'lists',
			),
			'servers' => array(
				'url'=>'/servers',
				'title'=>'Список серверов',
				'active'=>false,
				'place' => 0,
				'parent' => 'lists',
			),
			'lists_add' => array(
				'url'=>'/user_add',
				'title'=>'Добавить',
				'active'=>false,
				'place' => 0,
				'parent' => 'lists',
				'right' => 'change_group',
				'icon' => 6,
			),
			'player_add' => array(
				'url'=>'/player_add',
				'title'=>'В список званий',
				'active'=>false,
				'place' => 0,
				'parent' => 'lists_add',
				'right' => 'change_group',
			),
			'black_add' => array(
				'url'=>'/blacklist_add',
				'title'=>'В список плохишей',
				'active'=>false,
				'place' => 0,
				'parent' => 'lists_add',
				'right' => 'blacklist_edit',
			),
			'server_add' => array(
				'url'=>'/server_add',
				'title'=>'Сервер',
				'active'=>false,
				'place' => 0,
				'parent' => 'lists_add',
				'icon' => 6,
			),
			'news_add' => array(
				'url'=>'/news_add',
				'title'=>'Новость',
				'active'=>false,
				'place' => 0,
				'parent' => 'lists_add',
				'icon' => 9,
			),
			'info' => array(
				'url'=>'#',
				'title'=>'Инфо',
				'active'=>false,
				'place' => 1,
				'parent' => false,
			),
			'guide' => array(
				'url'=>'/guide',
				'title'=>'Руководство',
				'active'=>false,
				'place' => 1,
				'parent' => 'info',
			),
			'MAG' => array(
				'url'=>'/mag',
				'title'=>'MAG',
				'active'=>false,
				'place' => 1,
				'parent' => 'info',
			),
			'wiki' => array(
				'url'=>'http://wiki.metrostroi.net" target="_blank',
				'title'=>'WIKI',
				'active'=>false,
				'place' => 1,
				'parent' => 'info',
			),
		);
	}
	public function set_item_active ($id) {
		$this->menu[$id]['active'] = true;
		if ($this->menu[$id]['parent']) $this->set_item_active($this->menu[$id]['parent']);
	}
	private function show_item ($id, &$item, $kid = false) {
		global $logged_user;
		$c = 0; $sub = '';
		if (isset($item['right']) and !($logged_user and $logged_user->take_group_info($item['right']))) {
			if (isset($item['icon']) and !($logged_user and $logged_user->icon_id() >= $item['icon']))
				return '';
			elseif (!isset($item['icon']))
				return '';
		}
		foreach ($this->menu as $tmp_id => $tmp_item)
			if ($tmp_item['parent'] and $tmp_item['parent'] == $id) {
				$sub .= $this->show_item($tmp_id, $tmp_item, true);
				if ($tmp_item['active'])
					$item ['active'] = true;
				$c++;
			}
		ob_start();
		include Mitrastroi::PathTPL(($c)?($kid)?'menu/item_subdropdown':'menu/item_dropdown':'menu/item');
		return ob_get_clean();
	}
	public function show($tpl, $error = false) {
		global $logged_user;
		$menu = array('','');
		foreach ($this->menu as $id => $item)
			if (!$item['parent'])
				$menu[$item['place']] .= $this->show_item($id, $item);
		ob_start();
		include Mitrastroi::PathTPL('menu/'. $tpl);
		return ob_get_clean();
	}
}