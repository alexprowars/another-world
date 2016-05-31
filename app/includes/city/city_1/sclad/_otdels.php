<?

if (!empty($otdel))
{

	$shop_kol = 0;

	$shop = db::query("SELECT s.*, o.* FROM game_sclad s, game_objects o WHERE s.object_id = o.id AND o.sclad = '1' AND s.user_id = '" . app::$user->data['id'] . "' ORDER BY s.time");

	echo "<table width=100% border=1 cellspacing=0 cellpadding=5 style='border-collapse: collapse; border-style: solid; padding: 2px' bordercolor='#D8C792'>";

	for ($i = 0; $i < db::num_rows($shop); $i++)
	{
		$shop_kol++;

		$objects = db::fetch($shop);

		$obj_inf = explode("|", $objects['inf']);

		if ($shop_kol % 2 == 1)
			echo "<tr><td valign=\"top\" height=\"100%\" width=\"50%\">";
		else echo "<td valign=\"top\" height=\"100%\" width=\"50%\">";

		echo "<table border=1 height=\"100%\" width=\"100%\"><tr><td width=50% align=center valign=center>
                        <a href='#'><b>" . $obj_inf['1'] . "</b></a><br><br>
                        <b>Гос. цена: " . $obj_inf['2'] . " кр.</b><br>
                        Долговечность предмета: " . $obj_inf['6'] . " [" . $obj_inf['7'] . "]<br>";

		if (app::$user->data['id'] == $objects['user_id'])
		{
			echo "<span onclick=\"if (confirm('Снять с хранения предмет &quot;" . $obj_inf['1'] . "&quot;?')) window.location='/map/?otdel=" . $otdel . "&buy=" . $objects['id'] . "'\" style='CURSOR: Hand'><b>Снять с хранения</b></a></td>";
		}
		else
		{
			echo "Владелец: <b>" . $objects['user'] . "</b><br></td>";
		}

		echo "<td width=50% align=center><img src='/img/items/" . $obj_inf['0'] . ".gif' alt='" . $obj_inf['1'] . "'><br></td></tr></table>";

		if ($shop_kol % 2 == 1)
			echo "</td>";
		else echo "</td></tr>";
	}

	if ($shop_kol == 0)
		echo "<tr><td align=center><b>На складе нет ваших вещей</b></td></tr>";

	echo "</table>";
}

?>