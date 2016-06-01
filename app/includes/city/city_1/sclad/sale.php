<?

$shop_kol = 0;

$it_sost = db::query("SELECT objects.* FROM objects, slots WHERE objects.user='" . $stat['user'] . "' AND objects.present=0 AND objects.sclad=0 AND objects.komis=0 AND objects.bank=0  AND objects.bs=0 AND slots.id=" . $stat['id'] . " && objects.id NOT IN (slots.1,slots.2,slots.3,slots.4,slots.5,slots.6,slots.7,slots.8,slots.9,slots.10,slots.11,slots.12,slots.13,slots.14,slots.15,slots.16,slots.17,slots.18,slots.19,slots.20,slots.21,slots.22) ORDER BY time desc");

echo "<table width=100% border=1 cellspacing=0 cellpadding=5 style='border-collapse: collapse; border-style: solid; padding: 2' bordercolor='#D8C792'>";

include('includes/items/classes.php');

for ($i = 0; $i < db::num_rows($it_sost); $i++)
{

	$objects = db::fetch($it_sost);
	$shop_kol++;

	$obj_inf = explode("|", $objects['inf']);

	$sale_price = round($obj_inf['2'] * 0.6, 2);

	if ($shop_kol % 2 == 1)
		echo "<tr><td valign=\"top\" height=\"100%\" width=\"50%\">";
	else echo "<td valign=\"top\" height=\"100%\" width=\"50%\">";

	echo "<table border=1 height=\"100%\"  width=\"100%\"><tr><td width=50% align=center valign=center>
        	<a href='#'><b>" . $obj_inf['1'] . "</b></a><br><br>
        	<b>Гос. цена: " . $obj_inf['2'] . " кр.</b><br>
        	Долговечность предмета: " . $obj_inf['6'] . " [" . $obj_inf['7'] . "]<br>
        	Тип предмета: <i>" . $tip[$objects[tip]] . "</i><br>
        	</td>
        	<td width=50% align=center valign=center>
        	<img src='/img/items/" . $obj_inf['0'] . ".gif' alt='" . $obj_inf['1'] . "'><br>";

	echo "<form action='/map/?otdel=100&sale=" . $objects['id'] . "' method=\"POST\">
	<input type=submit name=send_credits value='Сдать на хранение' class=standbut>
	</form>";

	echo "</td></tr></table>";

	if ($shop_kol % 2 == 1)
		echo "</td>";
	else echo "</td></tr>";
}

if ($shop_kol == 0)
	echo "<tr><td align=center><b>У вас нет вещей в инвентаре</b></td></tr>";

echo "</table>";