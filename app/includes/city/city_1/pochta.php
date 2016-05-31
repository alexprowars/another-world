<?

$act = $_GET['act'];

echo"<script language=JavaScript src='../img/js/show_inf.js'></script>
<script language=JavaScript src='../img/js/time.js'></script>";

echo"<body leftmargin=0 topmargin=0>
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
<td class='l_z_f'>Почтовое Отделение</td>
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
if ($stat['kwest2'] == 3 &&  $stat['kwest2_s'] == 4 && $nwbinf['0'] != 'ostrolistVisokij') echo"<img src='./img/items/ostrolistVisokij.gif' style='CURSOR: Hand' alt='Остролист высокий' onclick='window.location.href=\"/map/?pake1&tmp=\"+Math.random();\"\"'>";
echo"</td>
<td align=right valign=top>
<img src='/images/images/refresh.gif' style='CURSOR: Hand' alt='Обновить' onclick='window.location.href=\"/map/?act=".$_GET['act']."&tmp=\"+Math.random();\"\"'>
<img src='/images/images/back.gif' style='CURSOR: Hand' alt='Вернуться' onclick='window.location.href=\"/map/?refer=25&tmp=\"+Math.random();\"\"'>
</td>
</tr>
</table>";

echo"<table width=100% cellspacing=0 cellpadding=3 border=0>
<tr>
<td align=right>";

$unread = mysql_num_rows(db::query("SELECT `id` FROM `pochta` WHERE `whom` = '".$stat[user]."' AND `read` = '0' "));

echo"<DIV id=hint1></DIV>";

print"<table border='1' background='/images/inman_fon2.gif' cellpadding='0' cellspacing='0' style='border-collapse: collapse; border-style: solid; padding: 2' bordercolor='#D8C792' width='98%' height='150'>
<tr>
<td valign=top width=400>
<UL><b>Папки:</b><br><br>
<LI><a href=/map/?act=new>Написать </a>
<LI><a href=/map/?act=read>Входящие </a>"; if ($unread > 0) echo"<br>&nbsp;&nbsp;<small>(".$unread." новых)</small>"; echo"
<LI><a href='/map/?act=write'>Исходяшие</a>
</UL>
</td>

<td width=80% valign=top><center><b>Письма</b></center>";

if ($act == "read") {

	$poch = db::query("SELECT * FROM `pochta` WHERE `whom` = '".$stat[user]."' ORDER by ID DESC");

	echo "<table width=100% cellspacing=0 cellpadding=7 border=1 bordercolor=CCCCCC>
	<tr><td><b>№</td><td><b>Отправитель</td><td width=100%><b>Тема</td></tr>";

	while ($pochta = db::fetch($poch) ) {
		$i++;
		$user = $pochta["user"];
		$text = $pochta["subject"];
		$id = $pochta["id"];

		if ($pochta['read'] == 0) { $read="<b>"; }
		else { $read=""; }

		print "<tr style='CURSOR: Hand' onclick='window.location.href=\"/map/?act=let&id=$id\"'><td nowrap>$read$i</td><td nowrap>$read$user</td><td>$read$text</td></tr>";
	}
	echo "</table>";
}

if ($act == "let") {

	$id = intval($_GET['id']);

	$pochas = db::query("SELECT * FROM pochta WHERE id='".$id."' AND (`whom` = '".$stat['user']."' OR `user` = '".$stat['user']."') ORDER by ID DESC");
	if (mysql_num_rows($pochas) == 1){
		$let = db::fetch($pochas);

		if ($let['read'] == 0) {
			db::query("UPDATE `pochta` SET `read` = '1' WHERE `id` = '".$id."' ");
		}

		echo"<b>От:</b> ".$let['user']." <br>
		<b>Тема:</b> ".$let['subject']."<br>
		<b>Текст:</b><br>".$let['text']."<br>";
	}else{
		$msg = "Типа читернул";
	}

}

if ($act == "write") {

	$send = db::query("SELECT * FROM pochta WHERE user='".$stat[user]."' ORDER by ID DESC");

	echo "<table width=100% cellspacing=0 cellpadding=7 border=1 bordercolor=CCCCCC>
	<tr><td><b>№</td><td><b>Кому</td><td width=100%><b>Тема</td></tr>";

	while ($pochta = db::fetch($send) ) {
		$i++;
		$user=$pochta["whom"];
		$text=$pochta["subject"];
		$id=$pochta["id"];
		if ($pochta['read'] == 0) {$read1 = "<b>"; $read2 = "</b>";}
		else {$read1 = ""; $read2 = "";}
		print "<tr style='CURSOR: Hand' onclick='window.location.href=\"/map/?act=let&id=$id\"'><td>$read1$i$read2</td><td nowrap>$read1$user$read2</td><td>$read1$text$read2</td></tr>";
	}
	echo "</table>";
}


if ($act == "new") {
?>

<form name="add" action="/map/?act=new&do=3" method="POST">
<table>
<tr><td colspan=2><b><u>Написать письмо:</u></b></td><tr>
<tr>
<td><b>Тема:</b></td>
<td><input type=text name=subj class=new size=30></td>
</tr><tr>
<td><b>Кому:</b></td>
<td><input type=text name=target class=new size=30></td>
</tr><tr>
<td><b>Текст письма:</b></td>
<td><textarea name=text rows=7 cols=40></textarea></td>
</tr><tr align=center><td colspan=2>
<input type=submit value="Отправить письмо" class=input>
</td></tr></table>
</form>

<?
if ($_GET['do'] == "3") {

$text = HtmlSpecialChars($_POST['text']);
$target = addslashes($_POST['target']);
$subj = HtmlSpecialChars($_POST['subj']);

$infs = db::query("SELECT `id`, `user`, `room` FROM person WHERE `user` = '".$target."'");

if (mysql_num_rows($infs) != 1) { $msg = "Кому хотел послать?";
}elseif ($stat["credits"] < 1) { $msg = "У вас недостаточно денег";
}elseif ($subj == "") { $msg = "Введите тему сообщения.";
}elseif ($text == "") { $msg = "Введите текст сообщения.";
}else{
$info = db::fetch($infs);

		$cost = 1;
		db::query("INSERT INTO pochta(user, whom, text, subject, time) VALUES ('".$stat[user]."','".$target."','".$text."','".$subj."','".$now."')");
		db::query("UPDATE `person` SET `credits` = `credits` - '$cost' WHERE `user` = '".$stat['user']."' ");

		print"Письмо <b>".$subj."</b> успешно отправлено персонажу <b>".$info['user']."</b>.";

			$this->game->insertInChat("С вашего счета было снято <b>\"1 кр.\"</b>","","","1",$stat['user'],"",$stat['room']);
		$this->game->insertInChat("<b>Получено новое сообщение!</b>","","","1",$info['user'],"",$info['room']);

		print "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"2; URL=/map/?act=new\">";
}
}}

if (!empty($msg)) echo"<center><FONT COLOR=RED><b>$msg</b></font></center><BR>";

echo"</td></tr></table>

</td>
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
";

?>