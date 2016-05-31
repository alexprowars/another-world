<?
if(!defined("INSIDE")){ die("attemp hacking");}
if (isset($_GET['buy'])) {

        $shop_sost_res = mysql_query("SELECT * FROM `shop` WHERE `name` = '".addslashes($_GET['buy'])."'");

        if (mysql_num_rows($shop_sost_res)) {

                $buyitem_res = mysql_query("SELECT * FROM `items` WHERE `name` = '".addslashes($_GET['buy'])."'");

                if (mysql_num_rows($buyitem_res)) {

                        $buyitem = mysql_fetch_array($buyitem_res);
                        $shop_sost = mysql_fetch_array($shop_sost_res);



$done = 0;
			$item_name = $buyitem["title"];
			$S = mysql_query("SELECT * FROM `craft` WHERE `item_id` LIKE '".addslashes($_GET['buy'])."' ");
			if (mysql_num_rows($S) > 0) {
			   $craft = mysql_fetch_array($S);
				$c1 = $craft["c1"];
				$c2 = $craft["c2"];
				$c3 = $craft["c3"];
			        $c4 = $craft["c4"];
    				$c5 = $craft["c5"];
			   $SC1 = mysql_query("SELECT * FROM `objects` WHERE `user` =  '$stat[user]' AND inf LIKE '%$c1%' ");
			   $SC2 = mysql_query("SELECT * FROM `objects` WHERE `user` =  '$stat[user]' AND inf LIKE '%$c2%' ");
			   $SC3 = mysql_query("SELECT * FROM `objects` WHERE `user` =  '$stat[user]' AND inf LIKE '%$c3%' ");
			   $SC4 = mysql_query("SELECT * FROM `objects` WHERE `user` =  '$stat[user]' AND inf LIKE '%$c4%' ");
			   $SC5 = mysql_query("SELECT * FROM `objects` WHERE `user` =  '$stat[user]' AND inf LIKE '%$c5%' ");
			   $n_c1 = mysql_num_rows($SC1);
			   $n_c2 = mysql_num_rows($SC2);
			   $n_c3 = mysql_num_rows($SC3);
			   $n_c4 = mysql_num_rows($SC4);
			   $n_c5 = mysql_num_rows($SC5);
			   if ($c1 == "") {$n_c1 = 1;}
			   if ($c2 == "") {$n_c2 = 1;}
                           if ($c3 == "") {$n_c3 = 1;}
                           if ($c4 == "") {$n_c4 = 1;}
                           if ($c5 == "") {$n_c5 = 1;}
			   if ($n_c1 > 0 AND $n_c2 > 0 AND $n_c3 > 0 AND $n_c4 > 0 AND $n_c5 > 0) {

				if ($c1 != ""){$MSC1 = mysql_fetch_array($SC1); $id_c1 = $MSC1[id];mysql_query("DELETE FROM objects WHERE `user` =  '$stat[user]' AND id = $id_c1 LIMIT 1 ");}
				if ($c2 != ""){$MSC2 = mysql_fetch_array($SC2); $id_c2 = $MSC2[id];mysql_query("DELETE FROM objects WHERE `user` =  '$stat[user]' AND id = $id_c2 LIMIT 1 ");}
				if ($c3 != ""){$MSC3 = mysql_fetch_array($SC3); $id_c3 = $MSC3[id];mysql_query("DELETE FROM objects WHERE `user` =  '$stat[user]' AND id = $id_c3 LIMIT 1 ");}
				if ($c4 != ""){$MSC4 = mysql_fetch_array($SC4); $id_c4 = $MSC4[id];mysql_query("DELETE FROM objects WHERE `user` =  '$stat[user]' AND id = $id_c4 LIMIT 1 ");}
				if ($c5 != ""){$MSC5 = mysql_fetch_array($SC5); $id_c5 = $MSC5[id];mysql_query("DELETE FROM objects WHERE `user` =  '$stat[user]' AND id = $id_c5 LIMIT 1 ");}

				$done = 1;
			    }
	                    else {$done = 0;}

if ($done == 0){$msg="У вас не хватает компонентов для создания предмета <u>".$buyitem['title']."</u>";}
			}

			else {$done = 1;}
                                        if ($buyitem['tip'] == 1 && $buyitem['slot2'] == "w5")
                                                $secondary=1;
                                        else
                                                $secondary=0;

			if ($done == 1) {



				$inf = "".$buyitem['name']."|".$buyitem['title']."|".$buyitem['price']."|0|".$secondary."|".$buyitem['art']."|0|".$buyitem['iznos']."";
                                                $min = "".$buyitem['min_level']."|".$buyitem['min_str']."|".$buyitem['min_dex']."|".$buyitem['min_ag']."|".$buyitem['min_vit']."|".$buyitem['min_razum']."|".$buyitem['min_rase']."|".$buyitem['min_proff']."|".$buyitem['min_user']."";

                                                $result2 = mysql_query("INSERT INTO `objects` (`user`,`inf`,`min`,`tip`,`br1`,`br2`,`br3`,`br4`,`br5`,`min_d`,`max_d`,`hp`,`energy`,`strength`,`dex`,`agility`,`vitality`,`razum`,`krit`,`unkrit`,`uv`,`unuv`,`time`) VALUES ('".$stat['user']."','".$inf."','".$min."','".$buyitem['tip']."','".$buyitem['br1']."','".$buyitem['br2']."','".$buyitem['br3']."','".$buyitem['br4']."','".$buyitem['br5']."','".$buyitem['min']."','".$buyitem['max']."','".$buyitem['hp']."','".$buyitem['energy']."','".$buyitem['strength']."','".$buyitem['dex']."','".$buyitem['agility']."','".$buyitem['vitality']."','".$buyitem['razum']."','".$buyitem['krit']."','".$buyitem['unkrit']."','".$buyitem['uv']."','".$buyitem['unuv']."','".time()."')");

                                                if ($result2) {
                                                        $msg="Вы сделали предмет <u>".$buyitem['title']."</u>";
                                                }}
}
        }
        else
                $msg="Нет информации о предмете!";
}

?>