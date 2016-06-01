<?

if ($_GET['buy'] != "") {

if (!preg_match("/^[0-9a-z]{1,10}$/", $_GET['buy'])) die("Ошибка");

$buyitem_res = mysql_query("SELECT * FROM `items` WHERE `name` = '".addslashes($_GET['buy'])."' AND `tip` = 18");
	if (mysql_num_rows($buyitem_res)) {
             $buyitem = mysql_fetch_array($buyitem_res); 
			 if ($buyitem['price'] <= $stat['credits']) {
 
                                        $result = mysql_query("UPDATE `person` SET person.credits = person.credits - ".$buyitem['price']." WHERE person.user = '".$stat['user']."' AND person.credits >= ".$buyitem['price']."");

                                        if ($result) {

                                                $inf = "".$buyitem['name']."|".$buyitem['title']."|".$buyitem['price']."|0|0|".$buyitem['art']."|0|".$buyitem['iznos']."";

                                                $min = "1|".$buyitem['min_str']."|".$buyitem['min_dex']."|".$buyitem['min_ag']."|".$buyitem['min_vit']."|".$buyitem['min_razum']."|".$buyitem['min_rase']."|".$buyitem['min_proff']."";

                                                $result2 = mysql_query("INSERT INTO `objects` (`user`,`inf`,`min`,`tip`,`br1`,`br2`,`br3`,`br4`,`br5`,`min_d`,`max_d`,`hp`,`energy`,`strength`,`dex`,`agility`,`vitality`,`razum`,`krit`,`unkrit`,`uv`,`unuv`,`time`) VALUES ('".$stat['user']."','".$inf."','".$min."','".$buyitem['tip']."','".$buyitem['br1']."','".$buyitem['br2']."','".$buyitem['br3']."','".$buyitem['br4']."','".$buyitem['br5']."','".$buyitem['min']."','".$buyitem['max']."','".$buyitem['hp']."','".$buyitem['energy']."','".$buyitem['strength']."','".$buyitem['dex']."','".$buyitem['agility']."','".$buyitem['vitality']."','".$buyitem['razum']."','".$buyitem['krit']."','".$buyitem['unkrit']."','".$buyitem['uv']."','".$buyitem['unuv']."','".time()."')");

                                                if ($result2) {
                                                        $msg="Вы купили предмет <u>".$buyitem['title']."</u> за <u>".$buyitem['price']."</u> кр.";
                                                }
                                        }
                        }
                        else
                                $msg="У Вас недостаточно денег для покупки предмета <u>".$buyitem['title']."</u>";
                }
                else                       $msg="Предмет не найден!";
}

if (!empty($_GET['sale']) && is_numeric($_GET['sale'])) {
	// Продаем
	$sale = addslashes($_GET['sale']);

	$is_ex=mysql_fetch_array(mysql_query("SELECT objects.`id`,objects.`inf`,objects.`tip` FROM objects, slots WHERE objects.user='".$stat['user']."' AND objects.present=0 AND objects.bank=0 AND objects.sclad=0 AND objects.komis=0 && objects.id=".addslashes($sale)." AND slots.id=".$stat['id']." && objects.id NOT IN (slots.1,slots.2,slots.3,slots.4,slots.5,slots.6,slots.7,slots.8,slots.9,slots.10,slots.11,slots.12,slots.13,slots.14,slots.15,slots.16,slots.17,slots.18,slots.19,slots.20)"));
	
	$is_ex_inf=explode("|",$is_ex['inf']);
	
	if (!empty($is_ex_inf['0'])) {
		if ($is_ex['tip'] != 12) {
			$price=round($is_ex_inf['2']);
			
			mysql_query("DELETE FROM objects WHERE id=".addslashes($sale)."");
			mysql_query("UPDATE person SET credits=credits+".$price." WHERE id=".$stat['id']."");
			
			$stat['credits']+=$price;
			
			$msg="Вы удачно продали предмет <u>".$is_ex_inf['1']."</u> за <u>".$price."</u> кр.";
			
			$is_ex_inf['0'] = "";
		}
		else $msg="Предмет <u>".$is_ex_inf['1']."</u> не подледжит продаже!";
	}
	else echo"Предмет не найден в Вашем рюкзаке!";
}

function show ($id) {
        global $stat;

switch ($id) {

        case 1:
	echo"<table border=0 width=100%>";
$it_sost=mysql_query("SELECT * FROM `objects` WHERE `user` = '".$stat['user']."' AND (`tip` >= 19 AND `tip` <= 20) AND present=0 AND bank=0 AND sclad=0 AND komis=0");

if (mysql_num_rows($it_sost)) {
	
        echo"<table width=100% cellspacing=0 cellpadding=5 style='border-style: outset; border-width: 2' border=1>";

                include('includes/items/classes.php');

        for($i=0; $i<mysql_num_rows($it_sost); $i++) {

                $objects=mysql_fetch_array($it_sost);

                $obj_inf=explode("|",$objects['inf']);
                $obj_min=explode("|",$objects['min']);

                include('includes/items/min_tr.php');
                include('includes/items/add.php');
				
	$sale_price=round($obj_inf['2']); 

                echo"<tr><td width=33% align=center valign=center>
                <a href='' target=_blank><b>".$obj_inf['1']."</b></a><br>
                <b>Гос. цена: ".$obj_inf['2']." кр.</b><br>
                
                Тип предмета: <i>".$tip[$objects['tip']]."</i><br>
                </td>
                <td width=34% align=center>
                <img src='../img/items/".$obj_inf['0'].".gif' alt='".$obj_inf['1']."'>
                <br>
				<b>
                <span onclick=\"if (confirm('Продать предмет &quot;".$obj_inf['1']."&quot; за &quot;".$sale_price."&quot; кр.?')) window.location='/map/?otdel=1&sale=".$objects['id']."'\" style='CURSOR: Hand'>Продать</a>
				</b>
                </td>
                <td width=33% valign=top>&nbsp;
                ";
                

                if ($objects['about']) echo"<b><i>Дополнительная информация:</i></b><br><br>".$objects[about]."";

                echo"</td></tr>";
	
			 
		}
	


} else
	 echo"У Вас нет предметов, подлежащих продаже.";
echo"</table>";



 		break;	
        
		
		case 2:
echo"<table border=0 width=100%>";

$shop=mysql_query("SELECT * FROM `items` WHERE `tip` = 18");
if (mysql_num_rows($shop)) {
        echo"<table width=100% border=1 cellspacing=0 cellpadding=5 bordercolor=A5A5A5>";

        for($i=0; $i<mysql_num_rows($shop); $i++) {
                $iteminfo=mysql_fetch_array($shop);

                include('includes/items/classes.php');
                include('includes/items/items.php');

                
                        echo"
                        <tr><td width=33% align=center valign=center>
                        <a href='' target=_blank><b>".$iteminfo['title']."</b></a><br><br>
                        <b>Гос. цена: ".$iteminfo['price']." кр.</b><br>
                        Долговечность предмета: 0 [".$iteminfo['iznos']."]<br>
                        </td>
                        <td width=34% align=center>
                        <img src='../img/items/".$iteminfo['name'].".gif' alt='".$iteminfo['title']."'>
                        <br>
                        <span onclick=\"if (confirm('Купить предмет &quot;".$iteminfo['title']."&quot;?')) window.location='/map/?otdel=2&buy=".$iteminfo['name']."'\" style='CURSOR: Hand'><b>Купить</b></a>
                        </td>
                        <td width=33% valign=top>&nbsp;";
		if ($min_rase || $min_level || $min_str || $min_dex || $min_ag || $min_vit || $min_razum || $min_proff)
                        echo"<b><i>Минимальные требования:</i></b><br>
                        $min_rase$min_level$min_str$min_dex$min_ag$min_vit$min_razum$min_proff<br>";

                        if ($hp || $energy || $min || $max || $strength || $dex || $agility || $vitality || $razum || $br1 || $br2 || $br5 || $br3 || $br4 || $krit || $unkrit || $uv || $unuv || $mblock || $pbr || $kbr || $metk) echo"<b><i>Действие предмета:</i></b><br>
                        $hp$energy$min$max$strength$dex$agility$vitality$razum$br1$br2$br5$br3$br4$krit$mkrit$unkrit$uv$unuv$pblock$mblock$pbr$kbr$metk<br>";

                        if (!empty($iteminfo['about']))
                                echo"<b><i>Дополнительная информация:</i></b><br>".$iteminfo['about'];

                        echo"
                        </td></tr>";
                
        }
} else
	 echo"У Вас нет предметов, подлежащих починке.";
        echo"</table>";
 		break;

		
	


}}





echo"<body bgcolor=#dedede leftmargin=0 topmargin=0>
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
<td class='l_z_f'>Сбор Ресурсов</td>
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
<td valign=top style='border:solid windowtext 1.5pt; border-color: e1d0b0;' bgcolor=efdcb8 class=textblock>
<DIV ID=hint1></DIV>

<SCRIPT src='../img/js/show_inf.js'></SCRIPT>";


print"<table width=100% cellspacing=0 cellpadding=5 border=0>
<tr>
<td align=right valign=top>

<img src='/images/images/refresh.gif' style='CURSOR: Hand' alt='Обновить' onclick='window.location.href=\"/map/?otdel=".$_GET['otdel']."&tmp=\"+Math.random();\"\"'>

<img src='/images/images/back.gif' style='CURSOR: Hand' alt='Вернуться' onclick='window.location.href=\"/map/?refer=19&tmp=\"+Math.random();\"\"'>&nbsp;

</td>
</tr>

</table>";



echo"
<table width=100% cellspacing=0 cellpadding=3 border=0>
<tr>
<td align=center>
";


if ($msg!="") echo"<center><font color=red><b>$msg</b></font></center><br>";

echo"
<FIELDSET style='WIDTH: 98.6%'><legend>Отделы</legend>
<table width=100% cellspacing=0 cellpadding=5>
<tr>

<td align=center width=32%><A"; if ($_GET['otdel'] == 1) echo" disabled><b>"; else echo" HREF='/map/?otdel=1'>"; echo"Сдача ресурсов</b></A></td><td width=1% align=center><b>|</b></td>
<td align=center width=34%><A"; if ($_GET['otdel'] == 2) echo" disabled><b>"; else echo" HREF='/map/?otdel=2'>"; echo"Инструменты</b></A></td><td width=1% align=center></td>


</tr>";

if (!empty($_GET['otdel'])) {
        echo"<TR><TD COLSPAN=5 ALIGN=CENTER><HR COLOR='#CCCCCC'>";

        switch ($_GET['otdel']) {
                case 1: show(1); break;
                case 2: show(2); break;
                
                default: echo"<B STYLE='COLOR: Red'>Что-то тут не так...</B>"; break;
        }

        echo"</TD></TR>";
}


echo"
</table>
</FIELDSET>
</td>
</tr>
</table>
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
</center>";