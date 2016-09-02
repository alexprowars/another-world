<?php

/**
 * @var \Game\Controllers\MapController $this
 */

if (!isset($userOffer['BattleID']))
{
	// Форма подачи групповой заявки
	?>
	<form action="<?=$this->url->get('battle/') ?>?page=newbattle&battle_type=3" method=post>
		<table class="form">
			<tr>
				<td valign=top align=center><br>
					<table border=0 width=100% cellpadding=0 cellspacing=0>
						<tr>
							<td width=20% align=center><b>Таймаут:</b></td>
							<td width=29% align=center><select style="WIDTH: 140px" name=timeout>
								<option value=1>1.5 мин.
								<option value=3>3 мин.
								<option value=5>5 мин.
								<option value=10>10 мин.</select>
							</td>
							<td width=2% align=center><b style="color: #CCCCCC">|</b></td>
							<td width=20% align=center><b>Кровавый бой:</b></td>
							<td width=29% align=center><select style="WIDTH: 140px" name=blood>
								<option value=0>Нет
								<option value=1>Да</select>
							</td>
						</tr>
					</table>
					<hr color=CCCCCC>
					<table border=0 width=100% cellpadding=0 cellspacing=0>
						<tr>
							<td width=20% align=center><b>Бойцы :</b></td>
							<td width=29% align=center><select style="WIDTH: 140px" name=offer_level_1>
								<option value=1>Любого уровня
								<option value=2>Только моего уровня
								<option value=3>Моего уровня и ниже
								<option value=4>Только ниже уровнем</select>
							</td>
							<td width=2% align=center><b style="color: #CCCCCC">|</b></td>
							<td width=20% align=center><b>Начало боя через:</b></td>
							<td width=29% align=center><select style="WIDTH: 140px" name=time_battle_start>
								<option value=180>Через 3 минуты
								<option value=300>Через 5 минут
								<option value=600>Через 10 минут
							</td>
						</tr>
					</table>
					<hr color=CCCCCC>
					<table border=0 width=100% cellpadding=0 cellspacing=0>
						<tr>
							<td width=20% align=center><b>Алгоритм:</b></td>
							<td width=29% align=center><select style="WIDTH: 140px" name=alg>
								<option value=1>Равномерно по крутизне
								<option value=2>Сильные против слабых
								<option value=3>Случайно</select>
							</td>
							<td width=2% align=center><b style="color: #CCCCCC">|</b></td>
							<td width=20% align=center><b>Невидимый хаот:</b></td>
							<td width=29% align=center><select style="WIDTH: 140px" name=inv>
								<option value=0>Нет
								<option value=1>Да</select>
							</td>
						</tr>
					</table>
					<hr color=CCCCCC>
					<table border=0 width=100% cellpadding=0 cellspacing=0>
						<tr>
							<td width=100% align=center><input type=submit class=standbut value="Подать заявку" style="WIDTH: 140px"></td>
						</tr>
					</table>
					<hr color=CCCCCC>
					<small>
						<b>Хаотичный бой</b> - самый интересный тип боя, набирается команда игроков и система ХАОТИЧНО распределяет игроков по противоположным командам
					</small>
				</td>
			</tr>
		</table>
	</form>
	<?
	//
}

?>
<form action="<?=$this->url->get('battle/') ?>?page=take_it&battle_type=3" method="post">
	<table class="list">
		<tr>
			<td width=20 align=center><b>#</b></td>
			<td width=46 align=center><b><img src='/images/images/clock.gif'></b></td>
			<td align=left><b>Участники:</b></td>
		</tr>
<?

$cn = 0;

$offers = $this->db->query("SELECT * FROM `game_battle` WHERE `StartTime` > ".time()." AND `BattleType` = '3' AND Status = 'Zayavka' ORDER BY StartTime DESC");

while ($offer = $offers->fetch())
{
	$cn += 1;

	$participants = $this->db->query("SELECT u.username, u.id, u.level, u.rank, u.tribe, f.Team FROM game_battle_users f, game_users u WHERE u.id = f.FighterID and f.BattleID = ".$offer['BattleID']." ORDER BY f.Team");

	if ($offer['StartTime'] < time())
		break;

	if ($cn > 1)
		echo"<tr><td bgcolor=white colspan=3>&nbsp;</td></tr>";

	echo "<tr>";

	if (!$userOffer['BattleID'] && $userOffer['BattleID'] != $offer['BattleID'])
	{
		echo "<td align=center rowspan=3><img src='/images/images/join.gif' onclick='confirmDialog(\"Подтвердите действие\", \"Вы действительно хотите принять эту заявку?\", \"load(\\\"/battle/?page=take_it&battle_type=3&offer=".$offer['BattleID']."\\\")\")' style='CURSOR: Hand' alt='Принять вызов'></td>";
	}
	else
		echo"<td rowspan=3>&nbsp;</td>";

	if ($participants->numRows() > 0)
	{
		echo "<td align=center rowspan=3>".date("H:i", $offer['StartTime'])."</td><td align=left valign=top>";

		if ($offer['inv'] == 0)
		{
			while ($participant = $participants->fetch())
			{
				echo "<span id='person".$participant['id']."'></span>&nbsp;&nbsp;&nbsp;";
				echo "<script type='text/javascript'>$('#person".$participant['id']."').html(show_inf('".$participant['username']."', '".$participant['id']."', '".$participant['level']."', '".$participant['rank']."', '".$participant['tribe']."'));</script>";
			}
		}
		else
			echo"&nbsp;&nbsp;&nbsp;<i>неизвестно</i>";
	}

	echo "</td></tr><tr><td>

	<table cellspacing=0 cellpadding=0>
	<tr>
	<td valign=center><small>Уровень: <b>".$offer['minRedLevel']."-".$offer['maxRedLevel']."</b></small></td>
	<td valign=center><small><font color=CCCCCC>&nbsp;|&nbsp;</font> Алгоритм: <b>".$offer['alg']."</b></small></td>
	<td valign=center><small><font color=CCCCCC>&nbsp;|&nbsp;</font> Таймаут: <b>".($offer['Timeout'] / 60). " мин.</b></small></td>
	<td valign=center><small><font color=CCCCCC>&nbsp;|&nbsp;</font> Начало боя через:</small></td>

	<td valign=center><b><span id='battle_start_".$offer['BattleID']."'></span><script>ShowTime('battle_start_".$offer['BattleID']."', ".($offer['StartTime'] - time()).", 1, 3);</script></b></td>";

	if (!empty($offer['Comment']))
		echo"<td align=center><font color=CCCCCC>|</font><font color=blue> <small>[ <i><b>".$offer['comment']."</b></i> ]</font></small></td>";
	if (!empty($offer['isBlood']))
		echo"<td align=center><<font color=CCCCCC>|</font><img title=\"Кровавый бой\" src='/images/images/ckelet.gif'></td>";

	echo"</td></tr></table></td></tr>";
}

?>
	</table>
</form>