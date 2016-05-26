<?php
class Menu {
	private $menu;
	public function Menu() {
		$this->menu = array(
			'admin' => array(
				'url' => '/admin_constants',
				'title' => 'Одминка',
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
			'admin_reports' => array(
				'url' => '/admin_reports',
				'title' => 'Жалобы',
				'active' => false,
				'place' => 0,
				'parent' => 'admin',
				'right' => 'blacklist_edit',
			),
			'admin_tickets' => array(
				'url' => '/admin_tickets',
				'title' => 'Тикеты',
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
			'black' => array(
				'url'=>'/blacklist',
				'title'=>'Список плохих игроков',
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
			'logout' => array(
				'url'=>'/logout',
				'title'=>'Выход',
				'active'=>false,
				'place' => 1,
				'parent' => false,
				'right' => 'name'
			),
		);
	}
	public function set_item_active ($id) {
		$this->menu[$id]['active'] = true;
		if ($this->menu[$id]['parent']) $this->set_item_active($this->menu[$id]['parent']);
	}
	private function show_item ($id, &$item, $kid = false) {
		global $tox1n_lenvaya_jopa;
		$c = 0; $sub = '';
		if (isset($item['right']) and !($tox1n_lenvaya_jopa and $tox1n_lenvaya_jopa->take_group_info($item['right']))) {
			if (isset($item['icon']) and !($tox1n_lenvaya_jopa and $tox1n_lenvaya_jopa->icon_id() >= $item['icon']))
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
	public function show($tpl = 'menu') {
		global $tox1n_lenvaya_jopa;
		$menu = array('','');
		foreach ($this->menu as $id => $item)
			if (!$item['parent'])
				$menu[$item['place']] .= $this->show_item($id, $item);
		ob_start();
		include Mitrastroi::PathTPL('menu/'. $tpl);
		return ob_get_clean();
	}
}