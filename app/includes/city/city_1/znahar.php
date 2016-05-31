<?


$otdel = $_GET['otdel'];
$buy = $_GET['buy'];

if ($_REQUEST['movestat']) {
$movestat1 = $_POST['movestat1'];
$movestat2 = $_POST['movestat2'];
if ($movestat1<1 OR $movestat1>7 OR $movestat2<1 OR $movestat2>7) {$msg='Неверные параметры';}

if ($movestat1==1){$stat_m1=strength;}
elseif ($movestat1==2){$stat_m1=agility;}
elseif ($movestat1==3){$stat_m1=dex;}
elseif ($movestat1==4){$stat_m1=vitality;}
elseif ($movestat1==5){$stat_m1=power;}
elseif ($movestat1==6){$stat_m1=razum;}
elseif ($movestat1==7){$stat_m1=battery;}
elseif ($movestat1==8){$stat_m1=duh;}

if ($movestat2==1){$msprize=15; $stat_m2=strength;}
elseif ($movestat2==2){$msprize=15; $stat_m2=agility;}
elseif ($movestat2==3){$msprize=15; $stat_m2=dex;}
elseif ($movestat2==4){$msprize=15; $stat_m2=vitality;}
elseif ($movestat2==5){$msprize=15; $stat_m2=power;}
elseif ($movestat2==6){$msprize=15; $stat_m2=razum;}
elseif ($movestat2==7){$msprize=15; $stat_m2=battery;}
elseif ($movestat2==8){$msprize=15; $stat_m2=duh;}


if ($movestat1!=$movestat2) {
if ($stat[credits]>=$msprize) { // Хватает бабок
if ($stat[$stat_m1] > 1) {

mysql_query("UPDATE person set $stat_m1=$stat_m1-1, $stat_m2=$stat_m2+1, credits=credits-$msprize WHERE id='$stat[id]'");

$msg="Удачно переброшен стат. Вам это стоило $msprize кр.!";
$otdel  = 1;
} else $msg="Дальше уменьшить невозможно!";
} else $msg="Недостаточно кредитов!";
} else $msg="Неверные параметры";
}

if ($_POST['delklan']) {
if (!$stat['tribe']) $msg="Интересно из какого клана вы собрались выходить?";
elseif ($stat['b_tribe'] > 0) $msg="Глава клана не может покинуть его таким способом.";
elseif ($stat['ic'] < $now) $msg="Прежде надо пройти проверку у инквизиторов!";
elseif ($stat['credits'] < 50) $msg="Не хватает денег!";
else{

mysql_query("UPDATE `person` SET `tribe` = '', `tribe_rank` = '0', `sclon` = '0', `credits` = `credits` - 50 WHERE `user` = '".$stat['user']."'");

$msg="Вы успешно покинули клан ".$stat['tribe']."!";

$stat['tribe'] = '';
$stat['tribe_rank'] = 0;
$stat['credits'] -= 50;
}
}

if ($_POST['noinviz']) {
if ($stat['invisible'] < $now) $msg="Вы не тень!";
elseif ($stat['credits'] < 2) $msg="Не хватает денег!";
else{

mysql_query("UPDATE `person` SET `invisible` = '0', `credits` = `credits` - 1 WHERE `user` = '".$stat['user']."'");

$msg="Тень покинула вас!";

$stat['invisible'] = 0;
$stat['credits'] -= 1;
}
}

function show ($id) {
        global $stat;

switch ($id) {
        case 1:

echo"
<TABLE width=98% align=center>
<tr><TD>
<FORM METHOD=POST ACTION='/map/'>
<INPUT type=hidden name=sd4 value=403629609>
Вы можете стать иным - более ловким, сильным или мудрым... но лишь за счет других параметров<BR>
<small>(бесплатных перераспределений: 0)<BR></small><BR>
<table><tr><td>
Деньги: $stat[credits] кр.<br><br>
<u><i>Чистые параметры:</i></u><br>
Сила: $stat[strength]<BR>
Ловкость: $stat[agility]<BR>
Удача: $stat[dex]<BR>
Выносливость: $stat[vitality]<BR>
Энергия: $stat[power]<BR>
Разум: $stat[razum]<BR>
Активность: $stat[battery]<br>
Духовность: $stat[duh]<br>
</td>
<td>
Перенести&nbsp;
<SELECT name='movestat1'>
<OPTION value='1'>Сила</OPTION>
<OPTION value='2'>Ловкость</OPTION>
<OPTION value='3'>Удача</OPTION>
<OPTION value='4'>Выносливость</OPTION>
<OPTION value='5'>Энергия</OPTION>
<OPTION value='6'>Разум</OPTION>
<OPTION value='7'>Активность</OPTION>
<OPTION value='8'>Духовность</OPTION>

</SELECT>
&nbsp;в&nbsp;
<SELECT name='movestat2'>
<OPTION value='1'>Сила 15 кр.</OPTION>
<OPTION value='2'>Ловкость 15 кр.</OPTION>
<OPTION value='3'>Удача 15 кр.</OPTION>
<OPTION value='4'>Выносливость 15 кр.</OPTION>
<OPTION value='5'>Энергия 15 кр.</OPTION>
<OPTION value='6'>Разум 15 кр.</OPTION>
<OPTION value='7'>Активность 15 кр.</OPTION>
<OPTION value='8'>Духовность 15 кр.</OPTION>
</SELECT></td></tr><tr><td>
Роспись:
<INPUT type=submit name='movestat' class='input' value='Согласен' onclick=\"return confirm('Вы уверены в своем выборе?')\">
</FORM>
</td></tr></table>
<FORM METHOD=POST ACTION='/map/'>
Вы можете воспользоваться услугой \"Выход из клана\". Стоимость: 50 кр.
<INPUT type=submit name='delklan' class='input' value='Согласен' onclick=\"return confirm('Вы уверены в своем выборе?')\"><br><br>
Вы можете воспользоваться услугой \"Рассеять магию тени\". Стоимость: 1 кр.
<INPUT type=submit name='noinviz' class='input' value='Согласен' onclick=\"return confirm('Вы уверены, что хотите рассеять тень?')\">
</td></tr>
</TABLE>
";

 		break;	
        
		
}}


        if (!empty($buy)) include("city/shop/craft_znahar.php");


echo"
<body leftmargin=0 topmargin=0>
<<table border='0' cellspacing='0' cellpadding='0' class='tmain'>
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
<td class='l_z_f'>Домик Знахаря</td>
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

<SCRIPT src='../img/js/show_inf.js'></SCRIPT>


<table width=100% cellspacing=0 cellpadding=5 border=0>
<tr>

<td align=right valign=top>
<img src='/images/images/refresh.gif' style='CURSOR: Hand' alt='Обновить' onclick='window.location.href=\"/map/?otdel=".$_GET['otdel']."&tmp=\"+Math.random();\"\"'>
<img src='/images/images/back.gif' style='CURSOR: Hand' alt='Вернуться' onclick='window.location.href=\"/map/?refer=27&tmp=\"+Math.random();\"\"'>&nbsp;
</td>
</tr>

</table>

<table width=100% cellspacing=0 cellpadding=3 border=0>
<tr>
<td align=center>";


if ($msg!="") echo"<center><font color=red><b>$msg</b></font></center><br>";

echo"<div align='center'><table border='1' background='/images/inman_fon2.gif' cellpadding='0' cellspacing='0' style='border-collapse: collapse; border-style: solid; padding: 2' bordercolor='#D8C792' width='98%'>
<tr>

<td align=center width=33%><A"; if ($otdel == 1) echo" disabled><b>"; else echo" HREF='/map/?otdel=1'>"; echo"Знахарская</b></A></td>
<td align=center width=34%><A"; if ($otdel == 2) echo" disabled><b>"; else echo" HREF='/map/?otdel=2'>"; echo"Алхимка</b></A></td>
<td align=center width=33%><A"; if ($otdel == 3) echo" disabled><b>"; else echo" HREF='/map/?otdel=3'>"; echo"Травники</b></A></td>
</tr>";

if (!empty($_GET['otdel'])) {
        echo"<TR><TD COLSPAN=3 ALIGN=CENTER>";

        switch ($_GET['otdel']) {
                case 1: show(1); break;
                case 2: include('city/shop/_otdels_znahar.php'); break;
                case 3: include('city/znahar/1.php'); break;
                default: show(1); break;
        }

        echo"</TD></TR>";
}

echo"</table></div>";
if ($stat['kwest2'] == 1) {
echo"<br><br><input class=input type=button value='Отдать Водку знахарю' onclick='window.location.href=\"/map/?pake2\"'>"; }
?>
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
</center>
</body>
</html>