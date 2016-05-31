<?
if (!empty($otdel)) {

        $shop=mysql_query("SELECT * FROM items WHERE craft=2 ORDER BY price");
                include('includes/items/classes.php');
        echo"<table width=100% border=1 cellspacing=0 cellpadding=5 bordercolor=A5A5A5>";

        for($i = 0; $i < mysql_num_rows($shop); $i++) {
                $iteminfo=mysql_fetch_array($shop);

                include('includes/items/items.php');


		$item_name = $iteminfo["name"];
                        echo"
                        <tr><td width=33% align=center valign=center>
                        <a href='' target=_blank><b>".$iteminfo['title']."</b></a><br><br>
                        <b>Гос. цена: ".$iteminfo['price']." кр.</b><br>
                        Долговечность предмета: 0 [".$iteminfo['iznos']."]<br>
                        Тип предмета: <i>".$tip[$iteminfo['tip']]."</i><br>
                        <br>
                        </td>
                        <td align=center width=34% nowrap>
                        <img src='../img/items/".$iteminfo['name'].".gif' alt='".$iteminfo['title']."'>
                        <br>";
                         if($stat[proff]==7){echo"<span onclick=\"if (confirm('Сварить зелье &quot;".$iteminfo['title']."&quot;?')) window.location='/map/?otdel=".$otdel."&buy=".$iteminfo['name']."'\" style='CURSOR: Hand'><b>Сварить зелье</b></a>";
		}else{
		echo"<font color=red>Сварить зелье может только Алхимик</font>";}
                        echo"</td>
                        <td width=100% valign=top>&nbsp;";
		if ($min_level || $min_str || $min_dex || $min_ag || $min_vit || $min_razum || $min_proff)
                        echo"<b>Минимальные требования:</b><br>
                        $min_level$min_str$min_dex$min_ag$min_vit$min_razum$min_proff<br>";
                        if ($hp || $energy || $min || $max || $strength || $dex || $agility || $vitality || $razum || $br1 || $br2 || $br5 || $br3 || $br4 || $krit || $unkrit || $uv || $unuv) echo"<b>Действие предмета:</b><br>
                        $hp$energy$min$max$strength$dex$agility$vitality$razum$br1$br2$br5$br3$br4$krit$unkrit$uv$unuv<br>";
		
		#echo "$item_name";
		$S = mysql_query("SELECT * FROM `craft` WHERE `item_id` LIKE '$item_name' ");
		if (mysql_num_rows($S) > 0) {
		$craft = mysql_fetch_array($S);
			echo "<b>Требуемые ингридиенты:</b>";

			$SC1 = mysql_query("SELECT * FROM `objects` WHERE `user` =  '$stat[user]' AND inf LIKE '%$craft[c1]%' ");
			if (mysql_num_rows($SC1) > 0) {$color="000000";}
			else {$color = "color=red";}

			$SC2 = mysql_query("SELECT * FROM `objects` WHERE `user` =  '$stat[user]' AND inf LIKE '%$craft[c2]%' ");
			if (mysql_num_rows($SC2) > 0) {$color2="000000";}
			else {$color2 = "color=red";}

			$SC3 = mysql_query("SELECT * FROM `objects` WHERE `user` =  '$stat[user]' AND inf LIKE '%$craft[c3]%' ");
			if (mysql_num_rows($SC3) > 0) {$color3="000000";}
			else {$color3 = "color=red";}
			
                        $SC4 = mysql_query("SELECT * FROM `objects` WHERE `user` =  '$stat[user]' AND inf LIKE '%$craft[c4]%' ");
			if (mysql_num_rows($SC3) > 0) {$color4="000000";}
			else {$color4 = "color=red";}
			
			$SC5 = mysql_query("SELECT * FROM `objects` WHERE `user` =  '$stat[user]' AND inf LIKE '%$craft[c5]%' ");
			if (mysql_num_rows($SC3) > 0) {$color5="000000";}
			else {$color5 = "color=red";}

			echo "<br><b>&#9674;</b> <font $color>$craft[c1]</font>";
			if ($craft[c2] !="") {
			  echo "<br><b>&#9674;</b> <font $color2>$craft[c2]</font>";
			  if ($craft[c3] !="") {
			    echo "<br><b>&#9674;</b> <font $color3>$craft[c3]</font>";
  			         if ($craft[c4] !="") {
			            echo "<br><b>&#9674;</b> <font $color4>$craft[c4]</font>";
	            		         if ($craft[c5] !="") {
			                    echo "<br><b>&#9674;</b> <font $color5>$craft[c5]</font>";
			  }
			  }
			  }
			}
		}

                        if (!empty($iteminfo['about']))
                                echo"<br><br><b>Дополнительная информация:</b><br>".$iteminfo['about'];

                        echo"
                        </td></tr>";
                
        }
        echo"</table>";
}

?>