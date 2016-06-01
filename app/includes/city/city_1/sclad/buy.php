<?

if (isset($_GET['buy']))
{

	$shop_sost_res = db::query("SELECT * FROM `sclad` WHERE `id` = '" . addslashes($_GET['buy']) . "'");

	if (db::num_rows($shop_sost_res))
	{

		$buyitem_res = db::query("SELECT * FROM `objects` WHERE `id` = '" . addslashes($_GET['buy']) . "'");

		if (db::num_rows($buyitem_res))
		{

			$buyitem = db::fetch($buyitem_res);
			$shop_sost = db::fetch($shop_sost_res);
			$obj_inf = explode("|", $buyitem['inf']);
			$prise = round($obj_inf[2] * 0.7, 2);
			if ($prise <= $stat['credits'])
			{

				$this->db->query("UPDATE person SET person.credits = person.credits - " . $prise . " WHERE person.user = '" . $stat['user'] . "' AND person.credits>=" . $prise . "");
				$this->db->query("UPDATE objects SET user='" . $stat['user'] . "', sclad = '0' WHERE objects.id = '" . $shop_sost['id'] . "'");
				$this->db->query("DELETE FROM sclad WHERE id='" . $shop_sost['id'] . "'");

				$name2 = "" . $obj_inf['1'] . " (" . $prise . " кр)";
				$chas = date("H");
				$d = date("d.m.Y");

				$time = date("H:i:s");

				$S = $this->db->query("INSERT INTO perevod(login,action,item,time,date,login2) VALUES('$stat[user]','выкупил','$name2','$time','$d','ломбард')");

				$msg = "Вы выкупили предмет за <u>" . $prise . "</u> кр.";
			}
			else
				$msg = "У Вас недостаточно денег для выпокупки предмета <u>" . $obj_inf['1'] . "</u>";
		}
		else
			$msg = "Предмет не найден!";
	}
	else
		$msg = "Предмет не найден в ломбарде!";
}