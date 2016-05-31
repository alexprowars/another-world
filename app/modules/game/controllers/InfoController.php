<?php
namespace App\Game\Controllers;

use App\Models\Users;

class InfoController extends Application
{
	public function initialize ()
	{
		parent::initialize();

		$js = $this->assets->collection('js');
		$js->addJs('js/inf.js');
	}

    public function indexAction()
    {
		$where = '';

		if ($this->request->hasQuery('login') && $this->request->getQuery('login') != '')
		{
			$where = " u.username ='" . addslashes(htmlspecialchars($this->request->getQuery('login'))) . "'";
		}
		elseif ($this->request->hasQuery('id') && $this->request->getQuery('id') != '')
		{
			$where = " u.id = " . intval($this->request->getQuery('id')) . "";
		}
		elseif (is_numeric($_SERVER['QUERY_STRING']))
		{
			$where = " u.id = " . intval($_SERVER['QUERY_STRING']) . "";
		}
		else
			$this->message('Персонаж с таким логином или ID не найден!', 'Ошибка');

		$parse = $this->db->query("SELECT u.*, i.* FROM game_users u, game_users_info i WHERE ".$where."  AND i.id = u.id")->fetch();

		$this->tag->prependTitle('Информация о персонаже - '.$parse['username']);

		$info = new Users();
		$info->onConstruct();
		$info->assign($parse);

		foreach ($parse as $key => $value)
			$parse['~'.$key] = $value;

		$parse += $info->getSlotsInfo();
		$info->checkEffects();

		$t = $info->toArray();

		foreach ($t as $k => $v)
		{
			$parse[$k] = $v;
		}

		if (!$info->obraz)
			$parse['obraz'] = "1/" . $info->sex;

		$parse['hp_max'] 		= $info->hp_max;
		$parse['energy_max'] 	= $info->energy_max;

		$parse['hp_now'] 		= round($info->hp_now);
		$parse['energy_now'] 	= round($info->energy_now);

		$parse['w_h'] = $info->getPercent($info->hp_now, $info->hp_max);
		$parse['w_e'] = $info->getPercent($info->energy_now, $info->energy_max);
		$parse['w_u'] = $info->getPercent($info->ustal_now, $info->ustal_max);

		if ($info->tribe > 0)
			$tribe = $this->db->query("SELECT * FROM game_tribes WHERE id = '".$info->tribe."'")->fetch();

		$parse['prizes'] = array();

		$prizes = $this->db->query("SELECT o.inf, u.username AS sender, p.text, p.who, p.tribe_id FROM game_objects o, game_users_prizes p LEFT JOIN game_users u ON u.id = p.sender_id WHERE p.user_id = '".$info->id."' AND o.id = p.object_id ORDER BY p.id DESC ".(!$this->request->hasQuery('prizes') ? 'LIMIT 17' : '')."");

		while ($prize = $prizes->fetch())
		{
			$prize['inf'] = explode("|", $prize['inf']);

			switch ($prize['who'])
			{
				case 'user':
					$poster = $prize['sender'];
					$who = $prize['sender'];
					break;
				case 'tribe':
					$poster = '</b>Клан <IMG SRC="/images/tribe/' . $prize['tribe_id'] . '.gif" WIDTH="24" HEIGHT="1"><B>' . $prize['tribe'] . '</B><B>';
					$who = $prize['tribe'];
					break;
				default:
					$poster = "<i>Аноним</i>";
					$who = "";
			}

			$parse['prizes'][] = array
			(
				'name' 	=> $prize['inf'][0],
				'title' => $prize['inf'][1],
				'text'	=> $prize['text'],
				'sender'=> $poster,
				'who'	=> $who
			);
		}

		$this->view->setVar('info', $parse);

		if (!$this->request->hasQuery('frame'))
			$this->view->setMainView('info');
	}
}

?>