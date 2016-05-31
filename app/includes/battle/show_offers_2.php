<?php

/**
 * @var \App\Game\Controllers\MapController $this
 */

if (!isset($userOffer['BattleID']))
{
	// Форма подачи групповой заявки
	?>
	<form action="<?=$this->url->get('battle/') ?>?page=newbattle&battle_type=2" method=post>
		<table class="form">
			<tr>
				<td valign=top align=center>
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
							<td width=20% align=center><b>Комментарий:</b></td>
							<td width=29% align=center><input class=input name=comment style="WIDTH: 140px" value="" maxlength=15></td>
						</tr>
					</table>
					<hr color=CCCCCC>
					<table border=0 width=100% cellpadding=0 cellspacing=0>
						<tr>
							<td width=20% align=center><b>За меня:</b></td>
							<td width=29% align=center><select style="WIDTH: 140px" name=offer_level_1>
								<option value=1>Любого уровня
								<option value=2>Только моего уровня
								<option value=3>Моего уровня и ниже
								<option value=4>Только ниже уровнем</select>
							</td>
							<td width=2% align=center><b style="color: #CCCCCC">|</b></td>
							<td width=20% align=center><b>Против:</b></td>
							<td width=29% align=center><select style="WIDTH: 140px" name=offer_level_2>
								<option value=1>Любого уровня
								<option value=2>Только моего уровня
								<option value=3>Моего уровня и ниже
								<option value=4>Только ниже уровнем</select>
							</td>
						</tr>
					</table>
					<hr color=CCCCCC>
					<table border=0 width=100% cellpadding=0 cellspacing=0>
						<tr>
							<td width=20% align=center><b>Бойцов #1:</b></td>
							<td width=29% align=center>
								<input class=input name=size_left style="WIDTH: 140px; TEXT-ALIGN: Center" value="2" maxlength=2>
							</td>
							<td width=2% align=center><b style="color: #CCCCCC">|</b></td>
							<td width=20% align=center><b>Бойцов #2:</b></td>
							<td width=29% align=center>
								<input class=input name=size_right style="WIDTH: 140px; TEXT-ALIGN: Center" value="2" maxlength=2>
							</td>
						</tr>
					</table>
					<hr color=CCCCCC>
					<table border=0 width=100% cellpadding=0 cellspacing=0>
						<tr>
							<td width=49% align=center><b>Начало боя через:</b></td>
							<td width=2% align=center><b style="color: #CCCCCC">|</b></td>
							<td width=49% align=center><select style="WIDTH: 140px" name=time_battle_start>
								<option value=180>Через 3 минуты
								<option value=300>Через 5 минут
								<option value=600>Через 10 минут
							</td>
						</tr>
					</table>
					<hr color=CCCCCC>
					<center><input type=submit class=standbut value="Подать заявку" style="WIDTH: 187px"></center><br>
				</td>
			</tr>
		</table>
	</form>
	<?
	//
}
?>
<div id="battle_side" style='display: none'>
	<table width="300" bgcolor=e2e0e0>
		<tr>
			<td align=center style='BORDER-RIGHT: 0 solid' width=100%><b>Выбор группы</b></td>
			<td width=20 align=center style='BORDER-LEFT: 0 solid'><span style='CURSOR: Hand' onclick='$("#battle_side").hide();'><b>X</b></span></td></tr>
		<tr>
			<td colspan=2>
				<form action="<?=$this->url->get('battle/') ?>?page=take_it&battle_type=2" method="get">
					<input type="hidden" name="offer" value="">
					<input type="hidden" name="side" value="">
					<table width=100% cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td width=49% align=center>
								<input class=standbut value='Команда #1' type="submit" data-side="0">
							</td>
							<td width=2% align=center>|</td>
							<td width=49% align=center>
								<input class=standbut value='Команда #2' type="submit" data-side="1">
							</td>
						</tr>
					</table>
				</form>
			</td>
		</tr>
	</table>
</div>
<form action="<?=$this->url->get('battle/') ?>?page=take_it&battle_type=2" method="post">
	<table class='list'>
		<tr>
			<td width=20 align=center><b>#</b></td>
			<td width=45 align=center><b><img src='/images/images/clock.gif'></b></td>
			<td align=center><b>Команда #1</b></td>
			<td align=center><b>Команда #2</b></td>
		</tr>

<?

$cn = 0;

$offers = $this->db->query("SELECT * FROM `game_battle` WHERE `StartTime` > ".time()." AND `BattleType` = '2' AND Status = 'Zayavka' ORDER BY StartTime DESC");

while ($offer = $offers->fetch())
{
	$cn += 1;

	$participants = $this->db->query("select u.username, u.id, u.level, u.rank, u.tribe, f.Team FROM game_battle_users f, game_users u WHERE u.id = f.FighterID AND f.BattleID = " . $offer['BattleID'] . " ORDER BY f.Team");

	if ($offer['StartTime'] < time())
		break;

	//if ($cn > 1)
	//	echo "<tr><td bgcolor=white colspan=4>&nbsp;</td></tr>";

	echo "<tr>";

	if (!$userOffer['BattleID'] && $userOffer['BattleID'] != $offer['BattleID'])
	{
		echo "<td align=center rowspan=3><img src='/images/images/join.gif' onclick='confirmDialog(\"Подтвердите действие\", \"Вы действительно хотите принять эту заявку?\", \"battle_side(\\\"" . $offer['BattleID'] . "\\\")\")' style='CURSOR: Hand' alt='Принять вызов'></td>";
	}
	else
		echo "<td rowspan=3>&nbsp;</td>";

	if ($participants->numRows() > 0)
	{
		echo "<td align=center rowspan=3>" . date("H:i", $offer['StartTime']) . "</td><td align=left valign=top width=206>";

		$opp = 0;

		while ($participant = $participants->fetch())
		{
			echo "<span id='person".$participant['id']."'></span>";

			if ($participant['Team'] == 0)
				echo "<script type='text/javascript'>$('#person".$participant['id']."').html(show_inf('" . $participant['username'] . "', '" . $participant['id'] . "', '" . $participant['level'] . "', '" . $participant['rank'] . "', '" . $participant['tribe'] . "'));</script>";
			elseif ($participant['Team'] == '1')
			{
				if ($opp == 0)
					echo "</td><td valign=top width=206>";

				echo "<script type='text/javascript'>$('#person".$participant['id']."').html(show_inf('" . $participant['username'] . "', '" . $participant['id'] . "', '" . $participant['level'] . "', '" . $participant['rank'] . "', '" . $participant['tribe'] . "'));</script>";

				$opp++;
			}
		}

		if ($opp == 0)
			echo "</td><td valign=top width=206><center><i>Группа пока не набрана</i></center>";
	}

	echo "</td></tr><tr>";

	echo "<td align=center>Бойцов: <b>" . $offer['RedTeamCapacity'] . "</b> | Уровень: [ <b>" . $offer['minRedLevel'] . "-" . $offer['maxRedLevel'] . "</b> ]</td>";

	echo "<td align=center>Бойцов: <b>" . $offer['BlueTeamCapacity'] . "</b> | Уровень: [ <b>" . $offer['minBlueLevel'] . "-" . $offer['maxBlueLevel'] . "</b> ]</td>";

	echo "</tr><tr><td colspan=2>
		<table cellspacing=0 cellpadding=0>
		<tr>
		<td valign=center><small>Таймаут: [ <b>" . ($offer['Timeout'] / 60) . " мин.</b> ]&nbsp;</small></td>
		<td valign=center><small><font color=CCCCCC>|</font> Начало боя через: [&nbsp;</small></td>

		<td valign=center><b>
		<div id='battle_start_".$offer['BattleID']."'></div>
		<script>ShowTime('battle_start_".$offer['BattleID']."', ".($offer['StartTime'] - time()).", 1, '2');</script>
		</b></td>

		<td valign=center><small>&nbsp;]</small>";

	if (!empty($offer['Comment']))
		echo "<td align=center><font color=blue> <small>[ <i><b>" . $offer['Comment'] . "</b></i> ]</font></small></td>";
	if (!empty($offer['isBlood']))
		echo " <img title=\"Кровавый бой\" src='/images/images/ckelet.gif'>";

	echo "</td></tr></table></td></tr>";
}

?>
	</table>
</form>