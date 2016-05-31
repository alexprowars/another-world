<?php

/**
 * @var \App\Game\Controllers\MapController $this
 */

// Если у вас подана заявка на одиночный поединок
if (isset($userOffer['BattleID']))
{
	if ($userOffer['Team'] && $userOffer['BattleType'] == 1)
	{
		?>
		<table class="form">
			<tr>
				<td align=center>
					<input type=button value='Отозвать вызов' onClick='load("<?=$this->url->get('battle/') ?>?page=take_back&battle_type=1")' class=standbut><br>
				</td>
			</tr>
		</table>
	<?
	}
	else
	{
		$opponent = $this->db->query("SELECT `u`.username, `u`.id, `u`.level, `u`.rank, `u`.tribe FROM `game_battle_users` f, `game_users` u WHERE f.BattleID = " . $userOffer['BattleID'] . " AND f.Team = '1' AND f.FighterID = `u`.id")->fetch();

		if ($opponent && $userOffer['BattleType'] == 1)
		{
			?>
			<table class="form">
				<tr>
					<td align=center>
						Игрок принял ваш вызов
						<span id='battle<?=$opponent['id'] ?>'></span>
						<script type="text/javascript">
							$('#battle<?=$opponent['id'] ?>').html(show_inf("<?=$opponent['username'] ?>","<?=$opponent['id'] ?>","<?=$opponent['level'] ?>","<?=$opponent['rank'] ?>","<?=$opponent['tribe'] ?>", 1));
						</script>
						<input type=button value="Отказаться" onClick='load("<?=$this->url->get('battle/') ?>?page=dismiss")' class="standbut">
						&nbsp;
						<input type=button value="Вперед!" onClick='load("<?=$this->url->get('battle/') ?>?page=start")' class="standbut"><br><br>
					</td>
				</tr>
			</table>
		<?
		}
		else if ($userOffer['BattleType'] == 1)
		{
			?>
			<table class="form">
				<tr>
					<td align=center>
						<br><input type=button value='Отозвать заявку' onclick='load("<?=$this->url->get('battle/') ?>?page=take_back&battle_type=1")' class=standbut><br><br>
					</td>
				</tr>
			</table>
		<?
		}
	}
}
else
{
	?>
	<form action="<?=$this->url->get('battle/') ?>?page=newbattle&battle_type=1" method=post>
		<table class="form">
			<tr>
				<td valign=top align=center>
					<table width=100%>
						<tr>
							<td align=center>Подать заявку</td>
						</tr>
					</table>
					<hr color=CCCCCC>
					<table width=100%>
						<tr>
							<td width=20% align=center><b>Таймаут:</b></td>
							<td width=29% align=center>
								<select style="WIDTH: 140px" name=timeout>
									<option value=1>1.5 мин.
									<option value=3>3 мин.
									<option value=5>5 мин.
									<option value=10>10 мин.
								</select>
							</td>
							<td width=2% align=center><b style="color: #CCCCCC">|<br>|</b></td>
							<td width=20% align=center><b>Комментарий:</b></td>
							<td width=29% align=center><input class=input name=comment style="WIDTH: 140px" value="" maxlength=15></td>
						</tr>
					</table>
					<hr color=CCCCCC>
					<table width=100%>
						<tr>
							<td width=20% align=center><b>Кровавый бой:</b></td>
							<td width=29% align=center>
								<select style="WIDTH: 140px" name=blood>
									<option value=0>Нет
									<option value=1>Да
								</select></td>
							<td width=2% align=center><b style="color: #CCCCCC">|<br>|</b></td>
							<td width=20% align=center><b>Рукопашный бой:</b></td>
							<td width=29% align=center>
								<select style="WIDTH: 140px" name=kulak>
									<option value=0>Нет
									<option value=1>Да
								</select></td>
						</tr>
					</table>
					<hr color=CCCCCC>
					<table width=100%>
						<tr>
							<td width=20% align=center><b>Ставка:</b></td>
							<td width=29% align=center>
								<select style="WIDTH: 140px" name=stavka>
									<option value=0>Нет
								</select>
							</td>
							<td width=2% align=center><b style="color: #CCCCCC">|<br>|</b></td>
							<td width=20% align=center><b>Действие:</b></td>
							<td width=29% align=center><input type=submit class=standbut value="Подать заявку" style="WIDTH: 140px"></td>
						</tr>
					</table>
					<hr color=CCCCCC>
					<small>
						<b>Кровавый бой</b> - противник получает травму<br><b>Рукопашный бой</b> - при начале боя с противника и подающего заявку автоматически снимаются вещи
					</small>
				</td>
			</tr>
		</table>
	</form>
	<?
}


?>
<form action="<?=$this->url->get('battle/') ?>?page=take_it" method="post">
	<table class='list'>
		<tr>
			<th width=20 align=center><b>#</b></th>
			<th width=46 align=center><b><img src='/images/images/clock.gif'></b></th>
			<th>&nbsp;</th>
		</tr>
<?

$offers = $this->db->query("SELECT * FROM `game_battle` WHERE `StartTime` > " . time() . " AND `BattleType` = '".$battleType."' AND `Status` = 'Zayavka' ORDER BY StartTime DESC");

while ($offer = $offers->fetch())
{
	$participants = $this->db->query("SELECT `u`.username, `u`.id, `u`.level, `u`.rank, `u`.tribe, f.Team FROM `game_battle_users` f, `game_users` u WHERE u.id = f.FighterID AND f.BattleID = " . $offer['BattleID'] . " ORDER BY f.Team");

	if ($participants->numRows() > 1 && $userOffer['BattleID'] != $offer['BattleID'])
		continue;

	echo "<tr>";

	if (!$userOffer['StartTime'] && $userOffer['BattleID'] != $offer['BattleID'])
	{
		echo "<td align=center><a href='javascript:;' onclick='confirmDialog(\"Подтвердите действие\", \"Вы действительно хотите принять эту заявку?\", \"load(\\\"/battle/?page=take_it&battle_type=".$battleType."&offer=" . $offer['BattleID'] . "\\\")\")'><img src='/images/images/join.gif' alt='Принять вызов'></a></td>";
	}
	else
		echo "<td>&nbsp;</td>";

	if ($participants->numRows() > 0)
	{
		echo "<td align=\"center\">" . date("H:i ", $offer['StartTime'] - 600) . "</td><td align=\"left\">";

		while ($participant = $participants->fetch())
		{
			echo "<span id='person".$participant['id']."'></span>";

			if ($participant['Team'] == 0)
			{
				echo "<script type='text/javascript'>$('#person".$participant['id']."').html(show_inf('" . $participant['username'] . "', '" . $participant['id'] . "', '" . $participant['level'] . "', '" . $participant['rank'] . "', '" . $participant['tribe'] . "'));</script>";
			}
			elseif ($participant['Team'] == 1)
			{
				echo " <i>против</i> ";
				echo "<script type='text/javascript'>$('#person".$participant['id']."').html(show_inf('" . $participant['username'] . "', '" . $participant['id'] . "', '" . $participant['level'] . "', '" . $participant['rank'] . "', '" . $participant['tribe'] . "'));</script>";
			}
		}
	}
	else
		continue;

	if (!empty($offer['Timeout']))
		echo " <small>[ <b>" . ($offer['Timeout'] / 60) . " мин.</b> ]</small>";
	if (!empty($offer['Comment']))
		echo " <font color=blue> <small>[ <i><b>" . $offer['Comment'] . "</b></i> ]</font></small>";
	if (!empty($offer['IsBlood']))
		echo " <img title=\"Кровавый бой\" src='/images/images/ckelet.gif'>";
	if (!empty($offer['WeaponUsing']))
		echo " <img title=\"Кулачный бой\" src='/images/images/kulak.gif'>";

	echo "</td></tr>";
}

?>
</table></form>