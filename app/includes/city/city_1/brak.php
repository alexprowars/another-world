<?php

$nwb = mysql_fetch_array(mysql_query("SELECT * FROM objects WHERE user='".$stat['user']."' AND inf LIKE 'medunica|%' AND tip='22'"));
$nwbinf=explode("|",$nwb['inf']);
if ($stat['room'] == 22) {
if (isset($_GET['pake1'])) {
if ($stat['kwest2'] != 3 && $stat['kwest2_s'] != 4 && $nwbinf['0']=='medunica') $msg="Ошибка, не пытайтесь взломать игру :)!";
else {
if ($nwbinf['0']=='medunica') { $msg="У Вас уже есть такое растение!";} else { 
$ItPake = "medunica";
$buyitem = mysql_fetch_array(mysql_query("SELECT * FROM items WHERE name='".$ItPake."'"));
if ($buyitem['tip'] == 1 && $buyitem['slot2'] == "w5") $secondary=1; else $secondary=0;
$inf="$buyitem[name]|$buyitem[title]|$buyitem[price]|0|$secondary|$buyitem[art]|0|$buyitem[iznos]";
$min="$buyitem[min_level]|$buyitem[min_str]|$buyitem[min_dex]|$buyitem[min_ag]|$buyitem[min_vit]|$buyitem[min_razum]|$buyitem[min_rase]|$buyitem[min_proff]";
mysql_query("INSERT INTO `objects` (`user`,`inf`,`min`,`tip`,`br1`,`br2`,`br3`,`br4`,`br5`,`min_d`,`max_d`,`hp`,`energy`,`strength`,`dex`,`agility`,`vitality`,`razum`,`krit`,`unkrit`,`uv`,`unuv`,`time`) VALUES ('".$stat['user']."','".$inf."','".$min."','".$buyitem['tip']."','".$buyitem['br1']."','".$buyitem['br2']."','".$buyitem['br3']."','".$buyitem['br4']."','".$buyitem['br5']."','".$buyitem['min']."','".$buyitem['max']."','".$buyitem['hp']."','".$buyitem['energy']."','".$buyitem['strength']."','".$buyitem['dex']."','".$buyitem['agility']."','".$buyitem['vitality']."','".$buyitem['razum']."','".$buyitem['krit']."','".$buyitem['unkrit']."','".$buyitem['uv']."','".$buyitem['unuv']."','".time()."')");
$msg="Вы получили <u>\"Медуница полевая\"</u><br>";
$nwbinf['0'] = "medunica";
}
}}
}

//Обвенчать
if ($_POST['obvin']){
	if ($stat['admin']!=1 && $stat['id']!=1699638937)
    	$error = "У вас нет прав!";
    elseif (trim($_POST['muj'])=="" || trim($_POST['jena'])=="")
        $error = "Пустое поле.";
    elseif ($_POST['muj']==$_POST['jena'])
        $error = "Одинаковые Имена";
    else {
        $muj = mysql_fetch_array(mysql_query("select user, sex, semija, credits from person WHERE user='".addslashes($_POST['muj'])."'"));
        $jena = mysql_fetch_array(mysql_query("select user, sex, semija from person WHERE user='".addslashes($_POST['jena'])."'"));

        if (!$muj['user'])
           $error= 'Игрок "'.$_POST['muj'].'" не найден.';
        elseif (!$jena['user'])
           $error= 'Игрок "'.$_POST['jena'].'" не найден.';
        elseif ($muj['sex']!=1)
           $error = "У Мужа должен быть мужской пол";
        elseif ($jena['sex']!=2)
           $error = "У Жены должен быть женский пол";
        elseif ($muj['semija'])
           $error= 'Игрок "'.$_POST['muj'].'" уже женат';
        elseif ($jena['semija'])
           $error= 'Игрок "'.$_POST['jena'].'" уже замужем';
        elseif ($muj['f_credits']>350)
           $error = "Нахватает денег";
        else{
        	$mu = mysql_query("UPDATE `person` SET f_credits=f_credits-350,semija='".$jena['user']."' WHERE user='".$muj['user']."'");
		$jen = mysql_query("UPDATE `person` SET semija='".$muj['user']."' WHERE user='".$jena['user']."'");
		$ItPake = "weddingring";
		$buyitem = mysql_fetch_array(mysql_query("SELECT * FROM items WHERE name='".$ItPake."'"));
		if ($buyitem['tip'] == 1 && $buyitem['slot2'] == "w5") $secondary=1; else $secondary=0;
		$inf="$buyitem[name]|$buyitem[title]|$buyitem[price]|0|$secondary|$buyitem[art]|0|$buyitem[iznos]";
		$min="$buyitem[min_level]|$buyitem[min_str]|$buyitem[min_dex]|$buyitem[min_ag]|$buyitem[min_vit]|$buyitem[min_razum]|$buyitem[min_rase]|$buyitem[min_proff]";
		mysql_query("INSERT INTO `objects` (`user`,`inf`,`min`,`tip`,`br1`,`br2`,`br3`,`br4`,`br5`,`min_d`,`max_d`,`hp`,`energy`,`strength`,`dex`,`agility`,`vitality`,`razum`,`krit`,`unkrit`,`uv`,`unuv`,`time`) VALUES ('".$muj['user']."','".$inf."','".$min."','".$buyitem['tip']."','".$buyitem['br1']."','".$buyitem['br2']."','".$buyitem['br3']."','".$buyitem['br4']."','".$buyitem['br5']."','".$buyitem['min']."','".$buyitem['max']."','".$buyitem['hp']."','".$buyitem['energy']."','".$buyitem['strength']."','".$buyitem['dex']."','".$buyitem['agility']."','".$buyitem['vitality']."','".$buyitem['razum']."','".$buyitem['krit']."','".$buyitem['unkrit']."','".$buyitem['uv']."','".$buyitem['unuv']."','".time()."')");
		mysql_query("INSERT INTO `objects` (`user`,`inf`,`min`,`tip`,`br1`,`br2`,`br3`,`br4`,`br5`,`min_d`,`max_d`,`hp`,`energy`,`strength`,`dex`,`agility`,`vitality`,`razum`,`krit`,`unkrit`,`uv`,`unuv`,`time`) VALUES ('".$jena['user']."','".$inf."','".$min."','".$buyitem['tip']."','".$buyitem['br1']."','".$buyitem['br2']."','".$buyitem['br3']."','".$buyitem['br4']."','".$buyitem['br5']."','".$buyitem['min']."','".$buyitem['max']."','".$buyitem['hp']."','".$buyitem['energy']."','".$buyitem['strength']."','".$buyitem['dex']."','".$buyitem['agility']."','".$buyitem['vitality']."','".$buyitem['razum']."','".$buyitem['krit']."','".$buyitem['unkrit']."','".$buyitem['uv']."','".$buyitem['unuv']."','".time()."')");
				
if($mu && $jen){
			$msg = "Брак успешна состоялся.";
		}else{
			$error = "Брак не состоялся.";
		}
        }
    }

}
// END


//Развод
if ($_POST['razv']){
	if ($stat['admin']!=1 && $stat['id']!=1699638937)
    	$error = "У вас нет прав!";
    elseif (preg_match("/[^(\w)|(\x7F-\xFF)|(\s)|(\<>)|(\|(\<)|(\>)|(\%3B)|(\")|]/",$_POST['login']))
        $error = "Логин имеет запрещенные символы.";
    elseif (trim($_POST['login'])=="")
        $error = "Пустое поле.";
    else {
        $razv = mysql_fetch_array(mysql_query("select user, semija, ic, credits from person WHERE user='".addslashes($_POST['login'])."'"));

		if (!$razv['user'])
           $error= 'Игрок "'.$_POST['login'].'" не найден.';
        elseif(!$razv['semija'])
           $error= 'Игрок "'.$_POST['login'].'" не состоит в браке.';
        elseif ($razv['credits']<50)
           $error = "Нахватает денег";
	elseif ($razv['ic'] < $now)
           $error = "Персонаж либо слишком давно проходил проверку у Инквизиторов, либо не проходил её вовсе!";
		else{
        	$raz = mysql_query("UPDATE `person` SET credits=credits-50,semija='' WHERE user='".$razv['user']."'");
		$raz2 = mysql_query("UPDATE `person` SET semija='' WHERE user='".$razv['semija']."'");
		mysql_query("DELETE FROM objects WHERE inf LIKE 'weddingring|%' AND user='".$razv['user']."'");
		mysql_query("DELETE FROM objects WHERE inf LIKE 'weddingring|%' AND user='".$razv['semija']."'");		
			if($raz && $raz2){
				$msg = "Развод состоялся.";
			}else{
				$error = "Развод не состоялся.";
			}
        }
    }

}
// END


echo"<BODY leftmargin=0 topmargin=0>
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
<td class='l_z_f'>Храм</td>
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

        <table width=100% cellspacing=0 cellpadding=5 border=0>
        <tr><td>";
if ($stat['kwest2'] == 3 &&  $stat['kwest2_s'] == 4 && $nwbinf['0'] != 'medunica') echo"<img src='./img/items/medunica.gif' style='CURSOR: Hand' alt='Медуница полевая' onclick='window.location.href=\"/map/?pake1&tmp=\"+Math.random();\"\"'>";
echo"</td>
        <td align=right valign=top>
        <img src='/images/images/refresh.gif' style='CURSOR: Hand' alt='Обновить' onclick='window.location.href=\"/map/?tmp=\"+Math.random();\"\"'>
        <img src='/images/images/back.gif' style='CURSOR: Hand' alt='Вернуться' onclick='window.location.href=\"/map/?refer=22&tmp=\"+Math.random();\"\"'>&nbsp;
        </td>
        </tr>
        </table>";

        echo"<table width=100% cellspacing=0 cellpadding=3 border=0>
        <tr>
        <td align=center style='padding-left: 20px'>";

        if (!empty($error)) echo"<center><FONT COLOR=RED><b>$error</b></font></center><BR>";
        if (!empty($msg)) echo"<center><font color=green><b>$msg</b></font></center><br>";

        if ($stat['admin']!=1 && $stat['id']!=1699638937)
            echo "<fieldset style='WIDTH: 100%; margin-right:20; float:left'><br><center><table width='100%' border='0'>
    <tr>
      <td colspan='2' valign='top'>
        <table border='0' width='100%'>
          <tr>
            	<td width='35%' align='center' valign='top'>
              <table width='100%' border='0'><tr><td align=center><img src='http://aw.ufanet.ru/img/gal_church.jpg' width='250' height='296' border='1'></td></tr></table>
		</td>
		<td width='65%' valign='top'>
	 Под сенью Храма соединяются два любящих сердца, дабы шагать бесконечными дорогами, которые ведут через Another World...
             Чарующая тишина, которую нарушает лишь величественный голос Священника, провозглашающего истину о том, что воин и воительница с этих пор - одно целое, как в поединке, так и в жизни.
             Мягкий говор гостей, прячущих подарки молодоженам... Боевые друзья и подруги, соклановцы - кого тут только нет! Однако для этой пары весь мир сейчас - это два золотых колечка, которые засияют у них на пальцах через мгновение.
	<table width='100%' style='border-collapse: collapse; border-style: solid; padding: 2' bordercolor='#D8C792' border=1>
                <tr>
                  <td colspan='4' align='center' bgcolor='#E0E0E0'><b>Последние 20 молодоженов</b></td>
                </tr>
                <tr align='center' bgcolor='#ECECEC'>
                  <td><b>Муж</b></td>
                  <td><b>Жена</b></td>
                  <td><b>Священник</b></td>
                  <td><b>Дата</b></td>
                </tr>
        </table>
            </td>
          </tr>
        </table>
      </td>
    </td>
  </table></center><br></fieldset><br><br>";
        else {
        			echo "<fieldset style='WIDTH: 45%; margin-right:20; float:center'><legend>Обвенчать</legend>
        			<table width=100% cellspacing=0 cellpadding=5>
			<tr>
			<td align=center>
        			<form method='POST' action='/map/' method=post style='margin:0; padding:0;'>
					<table cellspacing=0 cellpadding=5 style='border-collapse: collapse; border-style: solid; padding: 2' bordercolor='#D8C792' border=1>
					<tr>
					<td align='center'>Муж <input type='text' class=input name='muj' size='20'> Жена <input type='text' class=input name='jena' size='20'> <input type='submit' class=input value='Обвенчать' name='obvin'></td>
					</tr>
					<tr>
					<td align='center'>Стоимость брака <b>350 екр.</b> (Берутся с Мужа)</td>
					</tr>
					</table>
			</form>
			</td>
			</tr>
			</table>
        			</fieldset><br><br>";

        			echo "<fieldset style='WIDTH: 45%; margin-right:20; float:center'><legend>Развести</legend>
        			<table width=100% cellspacing=0 cellpadding=5>
			<tr>
			<td align=center>
        			<form method='POST' action='/map/' method=post style='margin:0; padding:0;'>
					<table cellspacing=0 cellpadding=5 style='border-collapse: collapse; border-style: solid; padding: 2' bordercolor='#D8C792' border=1>
					<tr>
					<td align='center'>Логин <input type='text' class=input name='login' size='20'> <input type='submit' class=input value='Развести' name='razv'></td>
					</tr>
					<tr>
					<td align='center'>Стоимость развода <b>50 кр.</b></td>
					</tr>
					</table>
			</form>
			</td>
			</tr>
			</table>
        			</fieldset>";
        }



        echo"
        </td>
        </tr>
        </table>";


?>

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