<?php

namespace Game\Controllers;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use Phalcon\Mvc\Controller;
use Phalcon\Tag;

/**
 * @RoutePrefix("/admin")
 * @Route("/")
 * @Route("/{action}/")
 * @Route("/{action}{params:(/.*)*}")
 * @Private
 */
class AdminController extends Controller
{
	private $modules = array();

	public function initialize ()
	{
		$this->tag->setDocType(Tag::HTML5);
		$this->tag->setTitle($this->config->app->name);
		$this->view->setMainView('admin');

		$result = $this->db->query("SELECT m.id, m.alias, m.name, r.right_id FROM game_cms_modules m LEFT JOIN game_cms_rights r ON r.module_id = m.id AND r.group_id = ".$this->user->group_id." AND right_id != '0' WHERE m.is_admin = '1' AND m.active = '1'");

		while ($r = $result->fetch())
		{
			$this->modules[$r['alias']] = array
			(
				'id' 	=> $r['id'],
				'alias'	=> $r['alias'],
				'name' 	=> $r['name'],
				'right' => $this->user->isAdmin() ? 2 : (!$r['right_id'] ? 0 : $r['right_id'])
			);
		}

		$menu = $this->getMenu(1, 2);

		foreach ($menu as $i => $item)
		{
			if (!isset($this->modules[$item['alias']]) || !$this->modules[$item['alias']]['right'])
				unset($menu[$i]);
		}

		$this->view->setVar('menu', $menu);
	}

	public function getMenu ($parent_id, $lvl = 1, $all = false)
	{
		$array = array();

		if ($parent_id >= 0 && $lvl > 0)
		{
			$childrens = $this->db->query("SELECT id, name, alias, icon, image, active FROM game_cms_menu WHERE parent_id = ".$parent_id." ".($all ? '' : "AND active = '1'")." ORDER BY priority ASC")->fetchAll();

			if (count($childrens) > 0)
			{
				foreach ($childrens as $children)
				{
					$array[] = array(
						'id' 		=> $children['id'],
						'alias' 	=> $children['alias'],
						'name' 		=> $children['name'],
						'children' 	=> ($lvl > 1) ? self::getMenu($children['id'], ($lvl - 1), $all) : array(),
						'active' 	=> $children['active'],
						'icon' 		=> $children['icon'],
						'image' 	=> $children['image']
					);
				}
			}
		}

		return $array;
	}

    public function indexAction()
    {

	}

	public function groupsAction()
	{
		$error = '';

		switch ($this->dispatcher->getParam('mode'))
		{
			case 'edit':

				$info = $this->db->query("SELECT * FROM game_users_groups WHERE id = ".$this->request->get('id', 'int', 0)."")->fetch();

				if (isset($info['id']))
				{
					if ($this->request->hasPost('save'))
					{
						if (!$this->request->getPost('name'))
							$error = 'Не указано имя пользователя';
						else
						{
							$this->db->updateAsDict(
								'game_users_groups',
								['name' => $this->request->getPost('name', null, '')],
								"id = ".$info['id']
							);

							if (is_array($this->request->getPost('module', 'int', '')))
							{
								$m = $this->request->getPost('module', 'int', '');

								foreach ($m as $moduleId => $rightId)
								{
									$check = $this->db->query("SELECT id FROM game_cms_modules WHERE active = '1' AND id = ".intval($moduleId)."")->fetch();

									if (isset($check['id']))
									{
										$rightId = min(2, max(0, $rightId));

										$f = $this->db->query("SELECT id FROM game_cms_rights WHERE group_id = '".$info['id']."' AND module_id = ".$check['id']."")->fetch();

										if (!isset($f['id']))
										{
											$this->db->insertAsDict(
												"game_cms_rights",
												Array
												(
													'group_id' 	=> $info['id'],
													'module_id' => $check['id'],
													'right_id' 	=> $rightId
												));
										}
										else
										{
											$this->db->updateAsDict(
												"game_cms_rights",
												Array
												(
													'group_id' 	=> $info['id'],
													'module_id' => $check['id'],
													'right_id' 	=> $rightId
												),
												"id = ".$f['id']
											);
										}
									}
								}
							}

							return $this->response->redirect('/admin/groups/action/edit/?id='.$info['id']);
						}
					}

					$modules = $this->db->query("SELECT * FROM game_cms_modules WHERE active = '1' ORDER BY id ASC")->fetchAll();

					$rights = array();

					$res = $this->db->query("SELECT * FROM game_cms_rights WHERE group_id = '".$info['id']."'");

					while ($r = $res->fetch())
						$rights[$r['module_id']] = $r;

					$this->view->setVar('rights', $rights);
					$this->view->setVar('modules', $modules);
					$this->view->setVar('info', $info);
				}
				else
					$error = 'Группа не найдена';

				$this->view->pick('admin/groups_edit');

				break;

			default:

				$list = $this->db->query("SELECT * FROM game_users_groups WHERE 1 ORDER BY id ASC")->fetchAll();

				$this->view->setVar('list', $list);

				break;
		}

		$this->view->setVar('error', $error);

		return true;
	}

	public function treeAction()
	{
		if ($this->dispatcher->getParam('mode') == 'node')
		{
			$this->response->setContentType('application/json', 'utf-8');

			$result = array();

			$parent = (int) $this->request->get('parent', 'int', 0);

			$nodes = $this->getMenu($parent, 2);

			foreach ($nodes as $node)
			{
				$result[] = array
				(
					'id' 		=> $node['id'],
					'text' 		=> $node['name'],
					'type'		=> (count($node['children']) > 0) ? 'folder' : 'file',
					'children' 	=> (count($node['children']) > 0),
					'state'		=> array('opened' => false)
				);
			}

			$this->response->setJsonContent($result);
			$this->response->send();
			$this->view->disable();
		}
	}

	public function modulesAction()
	{
		$error = '';

		switch ($this->dispatcher->getParam('mode'))
		{
			case 'edit':

				$this->view->pick('admin/modules_edit');

				$info =$this->db->query("SELECT * FROM game_cms_modules WHERE id = ".$this->request->get('id', 'int', 0)."")->fetch();

				if (isset($info['id']))
				{
					if ($this->request->hasPost('save'))
					{
						if (!$this->request->getPost('alias', null, ''))
							$error = 'Не указан алиас модуля';
						elseif (!$this->request->getPost('name', null, ''))
							$error = 'Не указано название модуля';
						else
						{
							$active = $this->request->getPost('active', null, '') != '' ? 1 : 0;
							$private = $this->request->getPost('private', null, '') != '' ? 1 : 0;

							$this->db->updateAsDict(
								"game_cms_modules",
								Array
								(
									'active' 	=> $active,
									'private' 	=> $private,
									'alias' 	=> $this->request->getPost('alias', null, ''),
									'name' 		=> $this->request->getPost('name', null, '')
								),
								"id = ".$info['id']
							);

							$this->response->redirect('/admin/modules/action/edit/?id='.$info['id'].'');
						}
					}

					$this->view->setVar('info', $info);
				}

				break;

			default:

				$list = $this->db->query("SELECT * FROM game_cms_modules WHERE 1 ORDER BY is_admin DESC, alias ASC")->fetchAll();

				$this->view->setVar('list', $list);

				break;
		}

		$this->view->setVar('error', $error);

		return true;
	}

	public function settingsAction ()
	{
		if (isset($_POST['save']))
		{
			foreach ($_POST['setting'] as $key => $value)
			{
				core::updateConfig($key, addslashes($value));
			}

			$this->message('Настройки игры успешно сохранены!', 'Выполнено');
		}
		else
		{
			$parse = array();
			$parse['settings'] = array();

			$settings = $this->db->query("SELECT * FROM game_config ORDER BY `key`");

			while ($setting = $settings->fetch())
			{
				$parse['settings'][] = $setting;
			}

			$this->view->pick('admin/options');
			$this->view->setVar('parse', $parse);
		}
	}
}