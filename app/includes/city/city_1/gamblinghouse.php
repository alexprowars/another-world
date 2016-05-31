<?

if (isset($_GET['take1'])) {
if ($stat['kwest0'] != 17) $msg="Ошибка, не пытайтесь взломать игру :)!";
else {
mysql_query("UPDATE person SET kwest0=18 WHERE user='".$stat['user']."'");
$stat['kwest0'] = 18;
$ItTake = "kwest0_pybaxa_voina";
$buyitem = mysql_fetch_array(mysql_query("SELECT * FROM items WHERE name='".$ItTake."'"));
if ($buyitem['tip'] == 1 && $buyitem['slot2'] == "w5") $secondary=1; else $secondary=0;
$inf="$buyitem[name]|$buyitem[title]|$buyitem[price]|0|$secondary|$buyitem[art]|0|$buyitem[iznos]";
$min="$buyitem[min_level]|$buyitem[min_str]|$buyitem[min_dex]|$buyitem[min_ag]|$buyitem[min_vit]|$buyitem[min_razum]|$buyitem[min_rase]|$buyitem[min_proff]";
mysql_query("INSERT INTO `objects` (`user`,`inf`,`min`,`tip`,`br1`,`br2`,`br3`,`br4`,`br5`,`min_d`,`max_d`,`hp`,`energy`,`strength`,`dex`,`agility`,`vitality`,`razum`,`krit`,`unkrit`,`uv`,`unuv`,`time`) VALUES ('".$stat['user']."','".$inf."','".$min."','".$buyitem['tip']."','".$buyitem['br1']."','".$buyitem['br2']."','".$buyitem['br3']."','".$buyitem['br4']."','".$buyitem['br5']."','".$buyitem['min']."','".$buyitem['max']."','".$buyitem['hp']."','".$buyitem['energy']."','".$buyitem['strength']."','".$buyitem['dex']."','".$buyitem['agility']."','".$buyitem['vitality']."','".$buyitem['razum']."','".$buyitem['krit']."','".$buyitem['unkrit']."','".$buyitem['uv']."','".$buyitem['unuv']."','".time()."')");
$msg="Вы получили <u>\"Рубаху Война\"</u><br>";
}
}

echo"
<BODY leftmargin=0 topmargin=0>
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
<td class='l_z_f'>Игорный Дом</td>
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
<center><table border='0' cellspacing='0' cellpadding='0' class='tmain'><tr><td>
<table width=100% cellspacing=0 cellpadding=5 border=0>
<tr>
<td align=right valign=top>
 <img src='/images/images/refresh.gif' style='CURSOR: Hand' alt='Обновить' onclick='window.location.href=\"/map/?gameroom=".$_GET['gameroom']."&tmp=\"+Math.random();\"\"'>
<img src='/images/images/back.gif' style='CURSOR: Hand' alt='Вернуться' onclick='window.location.href=\"/map/?refer=12&tmp=\"+Math.random();\"\"'>  &nbsp;
</td></tr></table>
</td></tr><tr><td>
<table width=100% cellspacing=0 cellpadding=3 border=0><tr>
<td width='100%' height='100%'>
<table border='0' width='100%' height='100%' cellspacing='0' cellpadding='0' style='padding: 5'>
<tr>
<td width='80%' height='100%' align='center' valign='top'>
<table border='1' background='/images/inman_fon2.gif' cellpadding='0' cellspacing='0' style='border-collapse: collapse; border-style: solid; padding: 2' bordercolor='#D8C792' width='98%'>
<tr>
<td width='100%' align='center'><b><u>Выберите игровую комнату:</u></b></td></tr>
<tr>
<td width='100%' align='center'>
";

if (!empty($msg)) echo"<center><FONT COLOR=RED><b>$msg</b></font></center><BR>";

if (!empty($_GET['gameroom'])) {

        switch ($_GET['gameroom']) {
                case 1: include('games/1.php'); break;
                case 3: include('games/3.php'); break;
                default: echo"<B STYLE='COLOR: Red'>Что-то тут не так...</B>"; break;
        }
}

echo "
</td></tr></table>
</td><td width='20%' height='100%' valign='top'>
<div align='center'>
<table border='1' background='/images/inman_fon2.gif' cellpadding='0' cellspacing='0' style='border-collapse: collapse; border-style: solid; padding: 2' bordercolor='#D8C792' width='98%'>
<tr>
<td width='100%' colspan='4' align='center'><u>Доступные Игры:</u></td>
</tr><tr>
<td width='50%' align='center' colspan='4'><A"; if ($_GET['gameroom'] == 1) echo" disabled><b>"; else echo" HREF='/map/?gameroom=1'>"; echo"Кости</b></A></td></tr>
<td width='50%' align='center' colspan='4'><A"; if ($_GET['gameroom'] == 3) echo" disabled><b>"; else echo" HREF='/map/?gameroom=3'>"; echo"Лотерея</b></A></td></tr>
</tr></table></div> ";
echo "
</td></tr></table></td></tr></table>
</td></tr>
</table></td><td>&nbsp;&nbsp;&nbsp;</td>
</tr>
<tr><td><br></td></tr> 
</table>
</td></tr>
</table>
</center>
</body>
<BR>";

?>