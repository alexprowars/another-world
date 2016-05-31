<?
if ($_GET['sets'] == "buy"){
	if ($stat['credits'] >= 1){
	$nums = mysql_fetch_array(mysql_query("select MAX(number) as number from lotto"));
	if ($nums['number'] < 500) {
                	$nm = $nums['number'] + 1;
        		$buy = mysql_query("UPDATE person SET credits=credits-1 WHERE user='".$stat['user']."'");
        		$insert = mysql_query("INSERT INTO lotto(name, number) VALUES('".$stat['user']."', '$nm')");
        		$infond = mysql_query("UPDATE lotto_fond SET fond=fond+1");
        		echo "<center><font class=sysmessage>Вы купили билет под номером: <b>$nm</b></font></center>";
	}else echo "Все билеты проданы, попробуйте на следующей неделе.";
	exit();
}
	if ($credits < 1){
	echo "У вас недостаточно денег";
	exit();}

}
if ($_GET['sets'] == "play"){
	$nums1 = mysql_fetch_array(mysql_query("select MAX(number) as number from lotto"));
	$r_l = $nums1['number'];
	$num1 = rand(1, $r_l);
	$fondasd = mysql_query("SELECT * FROM lotto_fond");
	$resta = mysql_fetch_array($fondasd);
	$fond = $resta['fond'];
        	$date = date('d.m.Y H:i:s');
	$sqlwin = mysql_query("SELECT * FROM lotto WHERE number='$num1'");
	$reswinrow = mysql_num_rows($sqlwin);
		if($reswinrow == 0){
			$win = "нет";
                        $winperson = mysql_query("INSERT INTO lotto_winner(time, name, number, fond) VALUES('$date', '$win','$num1', $fond)");

			$sbrosl = mysql_query("TRUNCATE TABLE lotto");
		}else{
			while ($reswin = mysql_fetch_array($sqlwin)){
			$win = $reswin['name'];
			}
			$plus = mysql_query("UPDATE person SET credits=credits+'$fond' WHERE user='$win'");
			$sbrosf = mysql_query("UPDATE lotto_fond SET fond=0");
			$sbrosl = mysql_query("TRUNCATE TABLE lotto");
                     		$winperson = mysql_query("INSERT INTO lotto_winner(time, name, number, fond) VALUES('$date', '$win','$num1', '$fond')");
		}
	echo "<p><center><font class=sysmessage>Выиграл номер: <b>$num1</b>. Победитель розыгрыша: <b>$win</b>.</font></center><p>";
	exit();}
?>


<table cellSpacing=0 cellPadding=3 width="100%" border=0 align=center>
<tr>
<td width=45% valign=top>
<FIELDSET><LEGEND>Победители прошлых розыгрышей</LEGEND>
<?

$otchet=mysql_query("SELECT * FROM lotto_winner order by id desc");
	for ($i=0; $i<mysql_num_rows($otchet); $i++) {
		$otchets=mysql_fetch_array($otchet);
		echo"<u>$otchets[time]</u> | Победитель: <b>$otchets[name]</b> | Номер: $otchets[number] | Фонд: $otchets[fond]<br>";
	}
$nums1 = mysql_fetch_array(mysql_query("select MAX(number) as number  from lotto"));
$ost = 500 - $nums1['number'];
$sum=mysql_fetch_array(mysql_query("SELECT fond FROM lotto_fond"));

?>
</FIELDSET>
</td>
<td width=55% valign=top>
<FIELDSET><LEGEND>Правила игры</LEGEND>
Правила игры предельно просты, от Вас требуется только купить билет и запомнить число которое вам покажит компьютер.
<br>
Если ваще число совпадёт в конце недели с числом которое выдаст компьютер, то значит вы победитель!
<br>
Имена победителей розыгрышей публикуются на доске победителей(слева).
<br>
Сумма фонда лоттереи автоматически зачисливаеться на счёт выигрывшего игрока.<br>
Если победителей в розыгрыше нет, то сумма фонда остаёться до сл. розыгрыша.
</FIELDSET>
        <p>
        <FIELDSET><LEGEND>Новая игра</LEGEND>
&nbsp;&nbsp;<b>У Вас на счету:</b> <u><?=$stat['credits']?></u> <b>кр.<br>&nbsp;&nbsp;Стоимость билета <u>1</u> <b>кр.</b><br> &nbsp;&nbsp;Осталось билетов: <u><?=$ost?></u><br>&nbsp;&nbsp;Сумма выигрыша: <u><?=$sum['fond']?></u>
        <br><br>
        <center>          

<?
if ($stat['rank'] == 100) print "<input type=button class=input value='Провести лотто' onclick='window.location = \"/map/?gameroom=3&sets=play\"' class=search style='WIDTH: 100px'>
&nbsp;&nbsp;&nbsp;&nbsp; ";?>
<? print "<input type=button class=input value='Купить билет' onclick='window.location = \"/map/?gameroom=3&sets=buy\"' class=search style='WIDTH: 100px'>"; ?>
        </center>
        </FIELDSET>
        </form>
        </td>
        </tr>
        </table>

<?
if ($stat['kwest0'] == 17)
echo"<center><fieldset style='WIDTH: 70%'><font face=Verdana size=2><legend>Сообщение о Квесте</legend></font>
<div align=center><font face=Verdana size=2>
Вы случайно здесь увидели <b>\"Рубаху Война\"</b>!<br>
<input class=input type=button value='Подобрать' onclick='window.location.href=\"/map/?take1\"'>
</font></div></fieldset></center><br>";
?>
