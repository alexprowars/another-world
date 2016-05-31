<?


echo"<body leftmargin=0 topmargin=0>
<table border='0' cellspacing='0' cellpadding='0' class='tmain'>
<tr><td valign='top' style='text-align:center;'>

<table border='0' cellspacing='0' cellpadding='0' align='center' class='ltable' style='width:98%;' background='../img/game/main/bodyfon.jpg' >
<tr>
<td colspan='3' valign='top' style='width:100%;'>
<table border='0' cellspacing='0' cellpadding='0'>
<tr>
<td><img src='../img/game/main/lp.jpg' width='40' height='28' border='0' alt='' /></td>
<td class='nmenus' style='white-space: nowrap; width:50%'>&nbsp;У вас с собой денег: <font color='#EDE2BE'>".$stat['credits']." кр.</font> | <font color='#EDE2BE'>".$stat['f_credits']." екр.</font></td>
<td align='center' valign='top' class='nmenus'>
<table border='0' cellspacing='0' cellpadding='0'>
<tr>
<td><img src='../img/game/main/lpl.jpg' width='40' height='28' alt='' border='0' /></td>
<td class='l_z_f'>Таверна</td>
<td><img src='../img/game/main/rpr.jpg' width='40' height='28' alt='' border='0' /></td>
</tr>
</table>
</td>
<td class='nmenus' style='white-space: nowrap; width:50%; text-align:right;'> </td>
<td><img src='../img/game/main/rp.jpg' width='40' height='28' border='0' alt='' /></td>			   
</tr>
</table>
<br>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;&nbsp;</td>
<td valign=top style='border:solid windowtext 1.5pt; border-color: e1d0b0;' bgcolor=efdcb8 class=textblock>";

print"<table width=100% cellspacing=0 cellpadding=5 border=0>
<tr><td align=right valign=top>
<img src='/images/images/refresh.gif' style='CURSOR: Hand' alt='Обновить' onclick='window.location.href=\"/map/?otdel=".$_GET['otdel']."&tmp=\"+Math.random();\"\"'>
<img src='/images/images/back.gif' style='CURSOR: Hand' alt='Вернуться' onclick='window.location.href=\"/map/?refer=35&tmp=\"+Math.random();\"\"'>
</td>
</tr>
</table>";

echo"
<table width=100% cellspacing=0 cellpadding=3 border=0>
<tr>
<td align=right>";

if ($stat['room'] == 35) {

if (isset($_GET['take1'])) {
if ($stat['kwest0'] != 0) $msg="Ошибка, не пытайтесь взломать игру :)!";
else {
mysql_query("UPDATE person SET kwest0=1 WHERE user='".$stat['user']."'");
$stat['kwest0']=1;
}
}

if (isset($_GET['take2'])) {
if ($stat['kwest0'] != 2) $msg="Ошибка, не пытайтесь взломать игру :)!";
else {
mysql_query("UPDATE person SET kwest0=3, credits=credits+5, kwest_k=kwest_k+1 WHERE user='".$stat['user']."'");
$stat['kwest0']=3;
$stat['credits']=$stat['credits']+5;
}
}

if (isset($_GET['take3'])) {
if ($stat['kwest0'] != 3) $msg="Ошибка, не пытайтесь взломать игру :)!";
else {
mysql_query("UPDATE person SET kwest0=4 WHERE user='".$stat['user']."'");
$stat['kwest0']=4;
}
}

if (isset($_GET['take4'])) {
if ($stat['kwest0'] != 5) $msg="Ошибка, не пытайтесь взломать игру :)!";
if ($stat['credits'] < 75) $msg="У вас нет 75 кр., чтобы закончить Квест №2!";
else {
mysql_query("UPDATE person SET kwest0=6, credits=credits-75, exp=exp+200, kwest_k=kwest_k+1 WHERE user='".$stat['user']."'");
$stat['kwest0']=6;
$stat['credits']=$stat['credits']-75;
$stat['exp']=$stat['exp']+200;
}
}


if (isset($_GET['take5'])) {
if ($stat['kwest0'] != 6) $msg="Ошибка, не пытайтесь взломать игру :)!";
else {
mysql_query("UPDATE person SET kwest0=7 WHERE user='".$stat['user']."'");
$stat['kwest0'] = 7;
$ItTake = "kwest0_old_ring";
$buyitem = mysql_fetch_array(mysql_query("SELECT * FROM items WHERE name='".$ItTake."'"));
if ($buyitem['tip'] == 1 && $buyitem['slot2'] == "w5") $secondary=1; else $secondary=0;
$inf="$buyitem[name]|$buyitem[title]|$buyitem[price]|0|$secondary|$buyitem[art]|0|$buyitem[iznos]";
$min="$buyitem[min_level]|$buyitem[min_str]|$buyitem[min_dex]|$buyitem[min_ag]|$buyitem[min_vit]|$buyitem[min_razum]|$buyitem[min_rase]|$buyitem[min_proff]";
mysql_query("INSERT INTO `objects` (`user`,`inf`,`min`,`tip`,`br1`,`br2`,`br3`,`br4`,`br5`,`min_d`,`max_d`,`hp`,`energy`,`strength`,`dex`,`agility`,`vitality`,`razum`,`krit`,`unkrit`,`uv`,`unuv`,`time`) VALUES ('".$stat['user']."','".$inf."','".$min."','".$buyitem['tip']."','".$buyitem['br1']."','".$buyitem['br2']."','".$buyitem['br3']."','".$buyitem['br4']."','".$buyitem['br5']."','".$buyitem['min']."','".$buyitem['max']."','".$buyitem['hp']."','".$buyitem['energy']."','".$buyitem['strength']."','".$buyitem['dex']."','".$buyitem['agility']."','".$buyitem['vitality']."','".$buyitem['razum']."','".$buyitem['krit']."','".$buyitem['unkrit']."','".$buyitem['uv']."','".$buyitem['unuv']."','".time()."')");
$msg="Вы получили <u>\"Испорченное Кольцо Мага\"</u><br>";
}
}

if (isset($_GET['take6'])) {
if ($stat['kwest0'] != 10) $msg="Ошибка, не пытайтесь взломать игру :)!";
else {
mysql_query("DELETE FROM objects WHERE tip='22' && user='".$stat['user']."'");
mysql_query("UPDATE person SET kwest0=11, kwest_k=kwest_k+1 WHERE user='".$stat['user']."'");
$stat['kwest0'] = 11;
$ItTake = "kwest0_new_ring";
$buyitem = mysql_fetch_array(mysql_query("SELECT * FROM items WHERE name='".$ItTake."'"));
if ($buyitem['tip'] == 1 && $buyitem['slot2'] == "w5") $secondary=1; else $secondary=0;
$inf="$buyitem[name]|$buyitem[title]|$buyitem[price]|0|$secondary|$buyitem[art]|0|$buyitem[iznos]";
$min="$buyitem[min_level]|$buyitem[min_str]|$buyitem[min_dex]|$buyitem[min_ag]|$buyitem[min_vit]|$buyitem[min_razum]|$buyitem[min_rase]|$buyitem[min_proff]";
mysql_query("INSERT INTO `objects` (`user`,`inf`,`min`,`tip`,`br1`,`br2`,`br3`,`br4`,`br5`,`min_d`,`max_d`,`hp`,`energy`,`strength`,`dex`,`agility`,`vitality`,`razum`,`krit`,`unkrit`,`uv`,`unuv`,`time`) VALUES ('".$stat['user']."','".$inf."','".$min."','".$buyitem['tip']."','".$buyitem['br1']."','".$buyitem['br2']."','".$buyitem['br3']."','".$buyitem['br4']."','".$buyitem['br5']."','".$buyitem['min']."','".$buyitem['max']."','".$buyitem['hp']."','".$buyitem['energy']."','".$buyitem['strength']."','".$buyitem['dex']."','".$buyitem['agility']."','".$buyitem['vitality']."','".$buyitem['razum']."','".$buyitem['krit']."','".$buyitem['unkrit']."','".$buyitem['uv']."','".$buyitem['unuv']."','".time()."')");
$msg="Вы получили <u>\"Кольцо Мага\"</u><br>";
}
}

if (isset($_GET['take7'])) {
if ($stat['kwest0'] != 11) $msg="Ошибка, не пытайтесь взломать игру :)!";
else {
mysql_query("UPDATE person SET kwest0=12 WHERE user='".$stat['user']."'");
$stat['kwest0']=12;
}
}

if (isset($_GET['take8'])) {
if ($stat['kwest0'] != 15) $msg="Ошибка, не пытайтесь взломать игру :)!";
else {
mysql_query("DELETE FROM objects WHERE tip='22' && user='".$stat['user']."'");
mysql_query("UPDATE person SET kwest0=16, kwest_k=kwest_k+1 WHERE user='".$stat['user']."'");
mysql_query("UPDATE person SET credits=credits+30 WHERE user='".$stat['user']."'");
mysql_query("UPDATE person SET o_updates=o_updates+1 WHERE user='".$stat['user']."'");
$stat['kwest0']=16;
$stat['credits']=$stat['credits']+30;
$stat['o_updates']=$stat['o_updates']+1;
}
}

if (isset($_GET['take9'])) {
if ($stat['kwest0'] != 16) $msg="Ошибка, не пытайтесь взломать игру :)!";
else {
mysql_query("UPDATE person SET kwest0=17 WHERE user='".$stat['user']."'");
$stat['kwest0']=17;
}
}

if (isset($_GET['take10'])) {
if ($stat['kwest0'] != 18) $msg="Ошибка, не пытайтесь взломать игру :)!";
else {
mysql_query("DELETE FROM objects WHERE tip='22' && user='".$stat['user']."'");
mysql_query("UPDATE person SET kwest0=19, kwest_k=kwest_k+1 WHERE user='".$stat['user']."'");
$stat['kwest0'] = 19;
$ItTake = "elstr8";
$buyitem = mysql_fetch_array(mysql_query("SELECT * FROM items WHERE name='".$ItTake."'"));
if ($buyitem['tip'] == 1 && $buyitem['slot2'] == "w5") $secondary=1; else $secondary=0;
$inf="$buyitem[name]|$buyitem[title]|$buyitem[price]|0|$secondary|$buyitem[art]|0|$buyitem[iznos]";
$min="$buyitem[min_level]|$buyitem[min_str]|$buyitem[min_dex]|$buyitem[min_ag]|$buyitem[min_vit]|$buyitem[min_razum]|$buyitem[min_rase]|$buyitem[min_proff]";
mysql_query("INSERT INTO `objects` (`user`,`inf`,`min`,`tip`,`br1`,`br2`,`br3`,`br4`,`br5`,`min_d`,`max_d`,`hp`,`energy`,`strength`,`dex`,`agility`,`vitality`,`razum`,`krit`,`unkrit`,`uv`,`unuv`,`time`) VALUES ('".$stat['user']."','".$inf."','".$min."','".$buyitem['tip']."','".$buyitem['br1']."','".$buyitem['br2']."','".$buyitem['br3']."','".$buyitem['br4']."','".$buyitem['br5']."','".$buyitem['min']."','".$buyitem['max']."','".$buyitem['hp']."','".$buyitem['energy']."','".$buyitem['strength']."','".$buyitem['dex']."','".$buyitem['agility']."','".$buyitem['vitality']."','".$buyitem['razum']."','".$buyitem['krit']."','".$buyitem['unkrit']."','".$buyitem['uv']."','".$buyitem['unuv']."','".time()."')");
$msg="Вы получили <u>\"Элексир\"</u><br>";
}
}

if (isset($_GET['take11'])) {
if ($stat['kwest0'] != 19) $msg="Ошибка, не пытайтесь взломать игру :)!";
else {
mysql_query("UPDATE person SET kwest0=20 WHERE user='".$stat['user']."'");
$stat['kwest0']=20;
}
}

if (isset($_GET['take12'])) {
if ($stat['kwest0'] != 24) $msg="Ошибка, не пытайтесь взломать игру :)!";
else {
mysql_query("DELETE FROM objects WHERE tip='22' && user='".$stat['user']."'");
mysql_query("UPDATE person SET kwest0=25, kwest_k=kwest_k+1 WHERE user='".$stat['user']."'");
mysql_query("UPDATE person SET credits=credits+25 WHERE user='".$stat['user']."'");
mysql_query("UPDATE person SET o_updates=o_updates+1 WHERE user='".$stat['user']."'");
mysql_query("UPDATE person SET s_updates=s_updates+1 WHERE user='".$stat['user']."'");
$stat['kwest0']=25;
$stat['credits']=$stat['credits']+25;
$stat['o_updates']=$stat['o_updates']+1;
$stat['s_updates']=$stat['s_updates']+1;
}
}


	if (isset($_GET['takes1'])) {
		if ($stat['q_id'] != 0) $msg = "Ошибка, не пытайтесь взломать игру :)!";
		elseif ($stat['q_time'] > time() - 43200) $msg = "Ваше время не пришло :)!";
		else {
	
			$sl = intval($_POST['sl']);
			if ($sl < 1 || $sl > 4) $sl = 3;
	
			$t_x = 0;
			$t_y = 0;
	
			if ($sl == 4)
				$radius = 75;
			elseif ($sl == 3)
				$radius = 30;
			elseif ($sl == 2)
				$radius = 20;
			else
				$radius = 10;
	
			mysql_query("INSERT INTO person_q (`q_id`, `type`, `sl`, `num_max`, `group`) VALUES (0, 1, ".$sl.", 10, 0)");
			$num = mysql_insert_id();
			mysql_query("UPDATE person SET q_id = ".$num.", q_time = ".time()." WHERE user = '".$stat['user']."'");
	
			$loc = mysql_query("SELECT * FROM map WHERE (tip > 47 AND tip < 1000) AND (x > ".(58 - $radius)." AND x < ".(58 + $radius).") AND (y > ".(44 - $radius)." AND y < ".(44 + $radius).") AND buildings = 0 AND bot = 0 ORDER BY RAND() LIMIT 10;");
	
			while ($locs = mysql_fetch_assoc($loc)) {
	
				mysql_query("INSERT INTO quests_temp VALUES (".$num.", ".$locs['x'].", ".$locs['y'].", 1, 0)");
			}
	
			$stat['q_id'] = $num;
			$msg = "Вы взяли квест \"В поисках клада\"";
		}
	}

	if ($stat['q_id'] != 0 && $_GET['otdel'] == 1)
		$quest = mysql_fetch_assoc(mysql_query("SELECT * FROM person_q WHERE q_id = ".$stat['q_id'].""));

	if (isset($_GET['takes1end'])) {
		if ($stat['q_id'] == 0) $msg = "Ошибка, не пытайтесь взломать игру :)!";
		elseif ($quest['num'] < 5) $msg = "Вы не собрали достаточного колличества клада!";
		else {
			
			if ($quest['group'] == 0) {
				mysql_query("DELETE FROM person_q WHERE q_id = ".$stat['q_id']."");
				mysql_query("DELETE FROM quests_temp WHERE q_id = ".$stat['q_id']."");
			}
			mysql_query("UPDATE person SET q_id = 0 WHERE user = '".$stat['user']."'");
			$stat['q_id'] = 0;

			$exp = $quest['sl'] * $quest['num'];

			$prize = mysql_query("SELECT * FROM prize WHERE p_id = ".$stat['id']." AND type = 1");

			if (mysql_num_rows($prize) == 0) {
				mysql_query("INSERT INTO prize VALUES (".$stat['id'].", 1, ".$exp.")");
			} else {
				mysql_query("UPDATE prize SET value = value + ".$exp." WHERE p_id = ".$stat['id']."");
			}
		}
	}


if (!empty($msg)) echo"<center><font color=red><b>$msg</b></font></center><br>";

function show ($id) {
        global $stat;

switch ($id) {
        case 1:

	global $quest;

echo"<fieldset style='WIDTH: 98.6%'><legend>Одиночные квесты</legend>
<table width=100% cellspacing=0 cellpadding=5>
<tr>
<td align=center width=50%>
<table width=100% cellspacing=0 cellpadding=5 style='border-collapse: collapse; border-style: solid; padding: 2' bordercolor='#D8C792' border=1>
<tr>
<td align=center>";

	if ($stat['q_id'] == 0) {

		if ($stat['q_time'] > time() - 43200) {

			echo"<center><b>Ждите до <u>".date("H:i:s", $stat['q_time']+43200)."</u> чтобы начать квест заново.</b></center>";

		} else {

			echo"<form action=/map/?takes1&otdel=1 method=post><table border=0><tr><td align=center><b>Взять квест:</b></td></tr>";

			echo"<tr><td align=center><select name=type><option value=1>Охота за сокровищами</select></td><tr><td align=center>Сложность:</tr></td></tr>";

			echo"<tr><td align=center><select name=sl><option value=1>Легко<option value=2>Нормально<option value=3>Сложно<option value=4>Нереально</select></td></tr>";

			echo"<tr><td align=center><input class=input type=submit value='Взять квест'></td></tr></table></form>";

		}

	} else {

		echo"<table border=0><tr><td align=center><b>Закончить квест:</b></td></tr>";

		if ($quest['type'] == 1)
			echo"<tr><td align=center>Собрано: <b>".$quest['num']."</b> из <b>".$quest['num_max']."</b></td></tr>";

		if ($quest['num'] >= 5)
			echo"<tr><td align=center><input class=input type=button value='Закончить' onclick='window.location.href=\"/map/?takes1end&otdel=1\"'></td></tr>";

		echo"</table>";
	}

echo"</td>
</tr>
</table>
</td>
<td align=center width=50%>
<table width=100% cellspacing=0 cellpadding=5 style='border-collapse: collapse; border-style: solid; padding: 2' bordercolor='#D8C792' border=1>
<tr>
<td align=left>";

	$pr = mysql_query("SELECT * FROM prize WHERE p_id = ".$stat['id']."");

	$i = 0;

	while ($prize = mysql_fetch_assoc($pr)) {
		$i++;
		switch ($prize['type']) {
			case 1: $type = "Кладоискатель";

		}
		if ($prize['value'] < 100)
			$lev = 1;
		elseif ($prize['value'] < 1000)
			$lev = 2;
		elseif ($prize['value'] < 10000)
			$lev = 3;
		elseif ($prize['value'] < 100000)
			$lev = 4;
		else
			$lev = 5;

		echo "<b>".$i.".</b> <img src='./img/prize/prize".$prize['type'].".gif' alt='".$type."'> - ".$type." <b>".$lev."</b> уровня. Очки кладоискателя: <b>".$prize['value']."/".(pow(10, $lev)*10)."</b><br>";
	}

	if ($i == 0)
		echo"<center>У вас нет наград.</center>";

echo"</td>
</tr>
</table>
</td>
</tr>
</table>
</fieldset>
<BR><BR>
<table><tr><td width=80%><small>
I. Типы квестов<br>
&nbsp;&nbsp;&nbsp;1. Охота за сокровищами - на карте хаотично распологаются 10 сундуков с сокровищами. Ваша задача - собрать как минимум 5 из 10 сундуков. За каждый собранный сундук начисляется золото и опыт, НО только при завершении квеста.<br>
II. Сложность<br>
&nbsp;&nbsp;&nbsp;Сложность влияет на разброс объектов на карте мира, чем выше сложность - тем больше радиус разбраса. На самой высокой сложности вам придёться обследовать весь мир.
</small></td></tr></table>
";

break;
        case 2:
echo"

<fieldset style='WIDTH: 98.6%'><legend>Получить Квест</legend>
<table width=100% cellspacing=0 cellpadding=5>
<tr>

<td align=center>
В этой <b>Хижине</b> вы сможете получать интересные/захватывающие квесты, следуйте правилам...<br><br>

<table width=100% cellspacing=0 cellpadding=5 style='border-collapse: collapse; border-style: solid; padding: 2' bordercolor='#D8C792' border=1>
<tr>
<td align=center>";

if ($stat['kwest0'] == 0) {
echo"<input class=input type=button value='Получить Квест №1!' onclick='window.location.href=\"/map/?take1&otdel=2\"'>"; }
elseif ($stat['kwest0'] == 1) {
echo"Вы получили <b>Квест №1</b>.<br>Для его выполнения вам предстоит добраться, через кровожадных монстров в подземелье, до <b>Зал Хаоса</b> и найти там <b>Подземный пояс</b>.<br>После этого придите сюда для получения бонуса."; }
elseif ($stat['kwest0'] == 2) {
echo"Поздравляю вы выполнили <b>Квест №1</b>, в честь этого вы получите бонус в размере <b>5 кр</b>.<br><input class=input type=button value='Получить бонус за Квест №1' onclick='window.location.href=\"/map/?take2&otdel=2\"'>"; }
elseif ($stat['kwest0'] == 3) {
echo"<center><b>Квест №1</b> выполнен.<br><input class=input type=button value='Получить Квест №2' onclick='window.location.href=\"/map/?take3&otdel=2\"'>"; }
elseif ($stat['kwest0'] == 4) {
echo"Вы получили <b>Квест №2</b>.<br>Для его выполнения вам предстоит добраться, до <b>Оружейного зала</b> в подземелье, и найти там сундук, в нем вы найдете <b>50 кр.</b>, но для окончания квеста вам нужно будет прибавить еще <b>25 кр.</b>, в итоге нужно <b>75 кр.</b>.<br>После этого прийде сюда для получения бонуса."; }
elseif ($stat['kwest0'] == 5) {
echo"Поздравляю вы выполнили <b>Квест №2</b>, в честь этого вы получите бонус в размере <b>200 опыта</b>.<br><input class=input type=button value='Получить бонус за Квест №2' onclick='window.location.href=\"/map/?take4&otdel=2\"'></center>"; }
elseif ($stat['kwest0'] == 6) {
echo"<b>Квест №1</b> выполнен.<br><b>Квест №2</b> выполнен.<br><input class=input type=button value='Получить Квест №3' onclick='window.location.href=\"/map/?take5&otdel=2\"'>"; }
elseif ($stat['kwest0'] == 7 || $stat['kwest0'] == 8 || $stat['kwest0'] == 9) {
echo"Это <b>\"Испорченное Кольцо Мага\"</b> оно потеряло свои свойства, для того чтобы его востановить в прежнее состояние, вам необходимо отыскать ингридиенты:<br> - <b>Рубин</b> (находится в <b>Секторе 12</b>, в Подземелье)<br> - <b>Йод</b> (находится в <b>Большом коридоре</b>, в Подземелье)<br> - <b>Змеиный Плод</b> (находится в <b>Секторе 43</b>, в Подземелье)<br> После того как вы все эти ингридиенты найдете, необходимо будет вернусть назад, для того чтобы наш алхимик зачаровал <b>\"Кольцо Мага\"</b>, это кольцо и будет вам бонусом за выполнение <b>Квеста №3</b>."; }
elseif ($stat['kwest0'] == 10) {
echo"Поздравляю вы выполнили <b>Квест №3</b>, в честь этого вы получите бонус <b>\"Кольцо Мага\"</b>.<br><input class=input type=button value='Получить бонус за Квест №3' onclick='window.location.href=\"/map/?take6&otdel=2\"'>"; }
elseif ($stat['kwest0'] == 11) {
echo"<b>Квест №1</b> выполнен.<br><b>Квест №2</b> выполнен.<br><b>Квест №3</b> выполнен.<br><input class=input type=button value='Получить Квест №4' onclick='window.location.href=\"/map/?take7&otdel=2\"'>"; }
elseif ($stat['kwest0'] == 12 || $stat['kwest0'] == 13 || $stat['kwest0'] == 14) {
echo"Вы получили <b>Квест №4</b>.<br>Ооо... Ты снова пришел ко мне, вот у меня для тебя новое задание:<br>Найди <b>3 части меча</b>:<br><b>Солнечный камень</b> что выпал из рукояди, саму <b>Рукоядь</b>, <b>Лезвие</b> от меча...<br>Ты наверное подумаешь, свехнулся старик, где же я их найду! А я тебе подскажу...<br><b>Солнечный камень</b> будет лежать в <b>Секторе 29</b>, <b>Рукоядь</b> в <b>Тупиковом тоннеле</b>, <b>Лезвие</b> будет торчать в <b>Секторе 60</b>.<br>Принеси мне эти части, я тебе отблагадарю сполна...<br>Удачи боец..."; }
elseif ($stat['kwest0'] == 15) {
echo"Поздравляю вы выполнили <b>Квест №4</b>, в честь этого вы получите бонус <b>30 кр, + 1 к особенностям</b>.<br><input class=input type=button value='Получить бонус за Квест №4' onclick='window.location.href=\"/map/?take8&otdel=2\"'>"; }
elseif ($stat['kwest0'] == 16) {
echo"<b>Квест №1</b> выполнен<br><b>Квест №2</b> выполнен<br><b>Квест №3</b> выполнен<br><b>Квест №4</b> выполнен<br><input class=input type=button value='Получить Квест №5' onclick='window.location.href=\"/map/?take9&otdel=2\"'>"; }
elseif ($stat['kwest0'] == 17) {
echo"Вы получили <b>Квест №5</b>.<br>Один из моих знакомых Войнов, когда шел ко мне в гости, потерял в одном из зданий свою <b>Рубаху</b>, которую он выйграл на арене, тебе задание:<br>Найди эту <b>Рубаху Война</b> и принеси мне..."; }
elseif ($stat['kwest0'] == 18) {
echo"Поздравляю вы выполнили <b>Квест №5</b>, в честь этого вы получите бонус <b>Элексир +8 к силе на час</b>.<br><input class=input type=button value='Получить бонус за Квест №5' onclick='window.location.href=\"/map/?take10&otdel=2\"'>"; }
elseif ($stat['kwest0'] == 19) {
echo"<b>Квест №1</b> выполнен<br><b>Квест №2</b> выполнен<br><b>Квест №3</b> выполнен<br><b>Квест №4</b> выполнен<br><b>Квест №5</b> выполнен<br><input class=input type=button value='Получить Квест №6' onclick='window.location.href=\"/map/?take11&otdel=2\"'>"; }
elseif ($stat['kwest0'] == 20 || $stat['kwest0'] == 21 || $stat['kwest0'] == 22 || $stat['kwest0'] == 23) {
echo"Вы получили <b>Квест №6</b>.<br>Привет еще раз, у меня беда! Я потерял своего любимого <b>котенка</b>, т.е. он убежал за ограду моего участка и не вернулся, я уже не знаю что и думать, может его украли, может надоело ему со мной жить, просто не знаю....<br><u>Если тебе не трудно отыщи моего любимого котенка, я тебя награжу сполна...</u>"; }
elseif ($stat['kwest0'] == 24) {
echo"Поздравляю вы выполнили <b>Квест №6</b>, в честь этого вы получите бонус <b>+1 к свободным характеристикам</b>, <b>+1 к свободным особеностям</b> и <b>+ 25 кр.</b>.<br><input class=input type=button value='Получить бонус за Квест №6' onclick='window.location.href=\"/map/?take12&otdel=2\"'>"; }
elseif ($stat['kwest0'] == 25) {
echo"<b>Квест №1</b> выполнен<br><b>Квест №2</b> выполнен<br><b>Квест №3</b> выполнен<br><b>Квест №4</b> выполнен<br><b>Квест №5</b> выполнен<br><b>Квест №6</b> выполнен<br><i>Продолжение следует...</i>"; }

echo"</td>
</tr>
</table>


</td>
</tr>
</table>
</fieldset>
<BR><BR>
";
break;
        case 3:
echo"На реконструкции";
break;
}}}

echo"
<div align='center'><table border='1' background='/images/inman_fon2.gif' cellpadding='0' cellspacing='0' style='border-collapse: collapse; border-style: solid; padding: 2' bordercolor='#D8C792' width='98%'>
<tr>

<td align=center width=33%><A"; if ($_GET['otdel'] == 1) echo" disabled><b>"; else echo" HREF='/map/?otdel=1'>"; echo"Бар</b></A></td>
<td align=center width=34%><A"; if ($_GET['otdel'] == 2) echo" disabled><b>"; else echo" HREF='/map/?otdel=2'>"; echo"Комната старца</b></A></td>
<td align=center width=33%><A"; if ($_GET['otdel'] == 3) echo" disabled><b>"; else echo" HREF='/map/?otdel=3'>"; echo"Комната отдыха</b></A></td>

</tr>";
if (!empty($_GET['otdel'])) {
        echo"<TR><TD COLSPAN=3 ALIGN=CENTER>";

        switch ($_GET['otdel']) {
                case 1: show(1); break;
                case 2: show(2); break;
                case 3: if ($stat['user'] == admin) { include('city/bar/1.php'); }else{ echo"The Beer for Real Men";} break;
                default: echo"<B STYLE='COLOR: Red'>Что-то тут не так...</B>"; break;
        }

        echo"</TD></TR>";
}


echo"
</table>
</div>
</td>
</tr></table>
</td><td>&nbsp;&nbsp;&nbsp;</td>
</tr>
</table></td></tr></table>
</td></tr>
</table></td>
</tr>
<tr><td><br></td></tr> 
</table>
</td></tr>
</table>
</center>
</body>
</html>
";
?>