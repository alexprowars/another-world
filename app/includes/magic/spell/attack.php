<?

if ($chl['user'] == $stat['user']) 		$nms = "Нападение на самого себя - это уже мазохизм...";
elseif ($stat['battle']) 			$nms = "В бою нападение невозможно!";
elseif (($chl['r_type'] != 0 && $chl['r_time'] > 0) || $chl['bog_time'] > 0) 	$nms = "Он занят!";
elseif ($chl['immun'] > $now) 			$nms = "На персонаже уже стоит защита от нападения!";
elseif ($chl['rank'] > 13) 			$nms = "Нападение на старших инквизиторов Запрещено!";
elseif ($chl['travma'] > $now) 			$nms = "Персонаж травмирован!";
elseif ($chl['room'] == 2) 			$nms = "И чё ты тут забыл?";
elseif ($chl['t_time'] > $now) 			$nms = "Персонаж в тюрьме!";
elseif ($stat['hp_now'] < (($stat['hp']+$stat['vitality']*5)*0.33)) $nms = "Вы слишком ослаблены для боя!";
elseif ($chl['hp_now'] <= 5) 			$nms = "Персонаж <u>$login</u> слишком слаб для поединка!";
elseif ($stat['level']-$chl['level'] >= 2) $nms="Вы не можете напасть на слабенького!";
else {

        	$levels = db::fetch(db::query("SELECT base FROM game_levels WHERE level=".$stat['level'].""));

        	if ($chl['battle']) {
		$t_b = db::fetch(db::query("select BattleType from game_battle WHERE BattleID = '".$chl['battle']."'"));
		if ($t_b['BattleType'] != 4){
			include("includes/magic/drop.php");
                		$prt = db::fetch(db::query("SELECT Team, BattleID FROM game_battle_users WHERE BattleID = ".$chl['battle']." AND FighterID = ".$chl['id'].""));

                		switch ($prt['Team']) {
                        			case 0: $side = 1; break;
                        			case 1: $side = 0; break;
                		}

				$t_x = rand(1, 3);
				$t_y = rand(1, 3);
                        	db::query("INSERT INTO game_battle_users (`BattleID`, `FighterID`, `Team`, `x`, `y`, `TotalExpa`) values ('".$prt['BattleID']."', '".$stat['id']."', ".$side.", '".$t_x."', '".$t_y."', '".$levels['base']."')");
                        	db::query("INSERT INTO game_battle_log (HitID, BattleID, HitTime, AttackerFighter, RedComment) values (0, ".$prt['BattleID'].", '".$now."', '".$stat['user']."', '74')");
                      	db::query("UPDATE game_users, game_battle SET game_users.battle=".$prt['BattleID'].", game_users.side=".$side.", game_battle.BattleType='2', game_battle.RaundTime='".$now."' WHERE game_users.id = ".$stat['id']." AND game_battle.BattleID = ".$prt['BattleID']."");

			$this->game->insertInChat("Разъярённый <b><u>".$stat['user']."</u></b> собрался с силами и напал на Вас!","","","1",$chl['user'],"",$chl['room']);

			echo"<script>parent.main.location=\"?tmp=\"+Math.random();\"\"</script>";
			die();

		} else $nms = "Вы не можете вмешиваться в бои света и тьмы!";
        	} else {
			include("includes/magic/drop.php");

			$max_offer = db::fetch(db::query("SELECT max(BattleID) as id FROM `game_battle`"));
                	$battime = $max_offer['id'] +1;

                	$chl_base = db::fetch(db::query("SELECT base FROM game_levels WHERE level=".$chl['level'].""));
                	$bdate=date("d.m.y H:i", $now);

				$this->db->query("INSERT INTO game_battle (BattleID, StartTime, BattleType, RaundTime, Timeout, Status) values ($battime, $now, '1', '".$now."', '60', 'InProcess')");
				$this->db->query("INSERT INTO game_battle_users (`BattleID`, `FighterID`, `Team`, `x`, `y`, `TotalExpa`) values ($battime, '$stat[id]', 0, 1, 1, '".$levels['base']."')");
				$this->db->query("INSERT INTO game_battle_users (`BattleID`, `FighterID`, `Team`, `x`, `y`, `TotalExpa`) values ($battime, '$chl[id]', 1, 2, 2, '".$chl_base['base']."')");
				$this->db->query("INSERT INTO game_battle_log (HitID, BattleID, HitTime, AttackerFighter, RedComment) values (0, ".$battime.", '".$now."', '".$stat['user']."', '71')");
				$this->db->query("update game_users set battle=$battime, side=0 WHERE id='$stat[id]'");
				$this->db->query("update game_users set battle=$battime, side=1 WHERE id='$chl[id]'");

				$this->game->insertInChat("Разъярённый <b><u>".$stat['user']."</u></b> собрался с силами и напал на Вас!","","","1",$chl['user'],"",$chl['room']);

			echo"<script>parent.main.location=\"?tmp=\"+Math.random();\"\"</script>";
			die();
        	}
}