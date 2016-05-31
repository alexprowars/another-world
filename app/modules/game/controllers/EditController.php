<?php
namespace App\Game\Controllers;

use App\Models\Complects;

class EditController extends Application
{
	public function initialize ()
	{
		parent::initialize();
	}

    public function indexAction()
    {
		$message = '';

		$slot = $this->user->getSlot();

		if ($this->request->has('onset') && $this->request->get('onset', 'int') > 0)
		{
			$message = $slot->onsetObject($this->request->get('onset', 'int'));

			if ($message != '')
				$this->game->setStatus(2);
		}
		elseif ($this->request->has('unset') && $this->request->get('unset') != "all")
			$slot->unsetObject($this->request->get('unset', 'int'));
		elseif ($this->request->has('unset') && $this->request->get('unset') == "all")
			$slot->unsetObject();
		elseif ($this->request->has('drop') && $this->request->get('drop', 'int') > 0)
			$message = $this->drop($this->request->get('drop', 'int'));
		elseif ($this->request->get('do') == "wear")
		{
			if ($this->request->get('a') == "del")
			{
				$kit = Complects::findFirst('id = '.$this->request->get('id', 'int').' AND user_id = '.$this->user->getId().'');

				if ($kit !== false > 0)
					$kit->delete();

				$message = "Вы удалили комплект";
			}
			else
			{
				/**
				 * @var $kit \App\Models\Complects
				 */
				$kit = Complects::findFirst('id = '.$this->request->get('id', 'int').' AND user_id = '.$this->user->getId().'');

				if ($kit !== false && $kit->wear())
					$message = "Вы одели комплект";
				else
				{
					$this->game->setStatus(2);
					$message = "Такого комплекта не существует!";
				}
			}
		}
		elseif ($this->request->get('do') == "compl")
		{
			if (!preg_match("/^[А-Яа-яЁёa-zA-Z0-9_\-\!\~\.@ ]+$/", $this->request->getPost('name')))
			{
				$this->game->setStatus(2);
				$message = "Неверные параметры";
			}
			else
			{
				$kit = new Complects;
				$kit->user_id 	= $this->user->id;
				$kit->name 		= addslashes($this->request->getPost('name'));
				$kit->data 		= json_encode($slot->toArray());
				$kit->save();
			}
		}

		if ($this->request->has('item_type') && $this->request->get('item_type') != $this->user->item_type)
		{
			$item_type = $this->request->get('item_type', 'int');
			$item_type = ($item_type < 1 || $item_type > 9) ? 1 : $item_type;

			$this->db->query("UPDATE game_users SET item_type = " . $item_type . " WHERE id = '" . $this->user->getId() . "'");
			$this->user->item_type = $item_type;
		}

		$kit = ($this->user->item_type == 9);

		if (!$kit)
			$inventory = $slot->getInventoryObjects($this->user->item_type);
		else
			$inventory = Complects::find('user_id = '.$this->user->getId());

		$this->view->setVar('item_type', $this->user->item_type);
		$this->view->setVar('inventory', $inventory);
		$this->view->setVar('kompl', $kit);

		if ($this->request->isAjax())
			$this->game->setMessage($message);
		else
			$this->view->setVar('message', $message);
    }
	
	function drop ($itemId)
	{
		$result = '';
	
		$itemId = intval($itemId);
	
		$item = $this->db->query("SELECT `id`, `tip` FROM `game_objects` WHERE `id` = '" . $itemId . "' AND `user_id` = '" . $this->user->id . "'")->fetch();
	
		if (isset($item['id']))
		{
			if ($item['tip'] == 16 || $item['tip'] == 15)
			{
				$this->db->query("DELETE FROM game_objects WHERE id = '" . $item['id'] . "'");
	
				$this->db->query("INSERT INTO perevod (login,action,item,time,date,login2) VALUES ('".$this->user->username."','выбросил','".$item['id']."','".date("H:i:s")."','".date("d.m.Y")."','рюкзак')");
			}
			else
				$result = "Выбросить можно только подарки";
		}
	
		return $result;
	}
	
	function un_set ($itemId)
	{
		$itemId = intval(str_replace("w", "", $itemId));
	
		if ($itemId > 0)
		{
			$object = $this->db->query("SELECT s.`i" . $itemId . "` as id, o.hp, o.energy FROM game_slots s, game_objects o WHERE s.user_id = " . $this->user->id . " AND o.id = s.`" . addslashes($itemId) . "`")->fetch();
	
			if (isset($object['id']))
			{
				$this->db->query("UPDATE game_slots s, game_users u SET s.i" . $itemId . " = 0, u.hp_now = if (u.hp_now < " . $object['hp'] . ", 0, u.hp_now), u.energy_now = if (u.energy_now < " . $object['energy'] . ", 0, u.energy_now) WHERE s.user_id = u.id AND u.id = " . $this->user->id . "");
	
				return true;
			}
		}
	
		return false;
	}
}
 
?>