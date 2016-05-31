<?
if (!empty($sale) && is_numeric($sale))
{
	// Продаем
	$sale = addslashes($sale);

	$now = time();
	$is_ex = db::fetch(db::query("SELECT objects.`id`,objects.`inf`,objects.`tip` FROM objects, slots WHERE objects.user='" . $stat['user'] . "' AND objects.present=0 && objects.id=" . addslashes($sale) . " AND slots.id=" . $stat['id'] . " && objects.id NOT IN (slots.1,slots.2,slots.3,slots.4,slots.5,slots.6,slots.7,slots.8,slots.9,slots.10,slots.11,slots.12,slots.13,slots.14,slots.15,slots.16,slots.17,slots.18,slots.19,slots.20,slots.21,slots.22)"));

	$is_ex_inf = explode("|", $is_ex['inf']);

	if (!empty($is_ex_inf['0']))
	{
		$price = round($is_ex_inf['2'] * 0.6, 2);

		$this->db->query("UPDATE objects SET sclad = '1' WHERE id=" . addslashes($sale) . "");
		$this->db->query("INSERT INTO sclad (`id`, `user`, `time`) values('$is_ex[id]', '$stat[user]', '$now')");
		$this->db->query("UPDATE person SET credits=credits+$price WHERE id=" . $stat[id] . "");
		$msg = "Предмет <u>" . $is_ex_inf['1'] . "</u> взят на хранение за " . $price . " кр.";

		$name2 = "" . $is_ex_inf['1'] . " (" . $price . " кр)";
		$chas = date("H");
		$d = date("d.m.Y");

		$time = date("H:i:s");

		$S = $this->db->query("INSERT INTO perevod(login,action,item,time,date,login2) VALUES('$stat[user]','сдал','$name2','$time','$d','ломбард')");

		$is_ex_inf['0'] = "";
	}
	else echo "Предмет не найден в Вашем рюкзаке!";
}
?>