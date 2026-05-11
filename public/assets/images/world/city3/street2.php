<?
define('INSIDE', true);
include("inc/db_connect.php");
$now=time();

$stat = mysql_fetch_array(mysql_query("SELECT *  FROM `players` WHERE `user` = '".$_COOKIE['user']."' AND `pass` = '".$_COOKIE['pass']."' LIMIT 1"));
  mysql_query("SET CHARSET cp1251");
if ($stat['bloked']) echo"<script>top.location='index.php?action=logout'</script>";

if ($stat['t_time']) { header("Location: prison.php"); exit; }
elseif ($stat['v_time']) { header("Location: ambulance.php"); exit; } // Редиректим в больницу
elseif ($stat['k_time']) { header("Location: academy.php"); exit; } // Редиректим в академию
elseif ($stat['w_time']) { header("Location: works.php"); exit; } // Редиректим в ворку
elseif ($stat['o_time']) { header("Location: repair.php"); exit; }
elseif ($stat['r_time']) { header("Location: vault.php"); exit; } 
else {

if (($stat['room']>200 && $stat['room']<=230) || ($stat['vault_time'] > $now && $stat['vault_move'] == 1)) {
        header("Location: vault.php");
        exit;
}

$night = date("H");

### Игорный дом
if ($room=="12") {
mysql_query("UPDATE players SET room=".$room.", lpv=".$now." WHERE user='".$stat['user']."'");
echo"
<SCRIPT LANGUAGE=\"JavaScript\">
<!--
top.frames['main'].location = \"gamblinghouse.php\";
top.frames['online'].location = top.frames['online'].location;
//-->
</SCRIPT>
";
exit();
}
###

### Шахта
if ($room=="400") {
if ($stat['proff'] != 5) {
echo"<script language=\"javascript\" type=\"text/javascript\">
alert('Вход только для шахтёров!');
top.frames['main'].location = \"street2.php\";</SCRIPT>";
exit();
}
mysql_query("UPDATE players SET room=".$room.", lpv=".$now." WHERE user='".$stat['user']."'");
echo"
<SCRIPT LANGUAGE=\"JavaScript\">
<!--
top.frames['main'].location = \"shahta.php\";
top.frames['online'].location = top.frames['online'].location;
//-->
</SCRIPT>
";
exit();
}
###

###Телеграф
if ($room=="25") {
mysql_query("UPDATE players SET room=".$room.", lpv=".$now." WHERE user='".$stat['user']."'");
echo"
<SCRIPT LANGUAGE=\"JavaScript\">
<!--
top.frames['main'].location = \"pochta.php\";
top.frames['online'].location = top.frames['online'].location;
//-->
</SCRIPT>
";
exit();
}
###

###Лавка мага
if ($room=="10") {
mysql_query("UPDATE players SET room=".$room.", lpv=".$now." WHERE user='".$stat['user']."'");
echo"
<SCRIPT LANGUAGE=\"JavaScript\">
<!--
top.frames['main'].location = \"mshop.php\";
top.frames['online'].location = top.frames['online'].location;
//-->
</SCRIPT>
";
exit();
}
###

###Кузница
if ($room=="11") {
mysql_query("UPDATE players SET room=".$room.", lpv=".$now." WHERE user='".$stat['user']."'");
echo"
<SCRIPT LANGUAGE=\"JavaScript\">
<!--
top.frames['main'].location = \"repair.php\";
top.frames['online'].location = top.frames['online'].location;
//-->
</SCRIPT>
";
exit();
}
###

###Больница
if ($room=="8") {
mysql_query("UPDATE players SET room=".$room.", lpv=".$now." WHERE user='".$stat['user']."'");
echo"
<SCRIPT LANGUAGE=\"JavaScript\">
<!--
top.frames['main'].location = \"ambulance.php\";
top.frames['online'].location = top.frames['online'].location;
//-->
</SCRIPT>
";
exit();
}
###

###Лес
if ($room=="26") {
mysql_query("UPDATE players SET room=".$room.", lpv=".$now." WHERE user='".$stat['user']."'");
echo"
<SCRIPT LANGUAGE=\"JavaScript\">
<!--
top.frames['main'].location = \"forest.php\";
top.frames['online'].location = top.frames['online'].location;
//-->
</SCRIPT>
";
exit();
}
###

###Запад
if ($room=="101") {
mysql_query("UPDATE players SET room=".$room.", lpv=".$now." WHERE user='".$stat['user']."'");
echo"
<SCRIPT LANGUAGE=\"JavaScript\">
<!--
top.frames['main'].location = \"street1.php\";
top.frames['online'].location = top.frames['online'].location;
//-->
</SCRIPT>
";
exit();
}
###

###Восток
if ($room=="104") {
mysql_query("UPDATE players SET room=".$room.", lpv=".$now." WHERE user='".$stat['user']."'");
echo"
<SCRIPT LANGUAGE=\"JavaScript\">
<!--
top.frames['main'].location = \"street3.php\";
top.frames['online'].location = top.frames['online'].location;
//-->
</SCRIPT>
";
exit();
}
###

###Глав. Площадь
if ($room == "103" || empty($room)) {
$room = 103;
mysql_query("UPDATE players SET room=".$room.", lpv=".$now." WHERE user='".$stat['user']."'");
echo"
<SCRIPT LANGUAGE=\"JavaScript\">
<!--
top.frames['online'].location = top.frames['online'].location;
//-->
</SCRIPT>
";
}
###

include('inc/html_header.php');

if ($night<19 & $night>12)
$city="$stat[img_path]/i/world/street2.jpg";
elseif ($night<22 & $night>6)
$city="$stat[img_path]/i/world/street2_v.jpg";
else $city="$stat[img_path]/i/world/street2_n.jpg";

echo"
<body bgcolor=#dedede leftmargin=0 topmargin=0>
<script language=\"javascript\" type=\"text/javascript\">
function imover(im)
{
  im.filters.Glow.Enabled=true;
}
function imout(im)
{
  im.filters.Glow.Enabled=false;
}
function m1()
{
  alert('Проход закрыт!');
}
function m2()
{
  alert('Рабочая неделя окончена! Пора и отдохнуть!');
}
</script>
<style type=\"text/css\">
img.aFilter {
  filter:Glow(color=#FFFFFF,Strength=4,Enabled=0);
  cursor:hand
}
hr {
  height: 1px;
                  }
</style>
<table align=left width=150 border=0 cellpadding=0 cellspacing=0>
&nbsp;&nbsp;&nbsp;<img src=i/ico/up.gif>&nbsp;&nbsp;&nbsp;<a href='?room=101'><small>Торговая площадь</small></a><br>
&nbsp;&nbsp;&nbsp;<img src=i/ico/up.gif>&nbsp;&nbsp;&nbsp;<a href='?room=12'><small>Игорный дом</small></a><br>
&nbsp;&nbsp;&nbsp;<img src=i/ico/up.gif>&nbsp;&nbsp;&nbsp;<a href='?room=400'><small>Шахта</small></a><br>
&nbsp;&nbsp;&nbsp;<img src=i/ico/up.gif>&nbsp;&nbsp;&nbsp;<a href='?room=26'><small>Дремучий лес</small></a><br>
&nbsp;&nbsp;&nbsp;<img src=i/ico/up.gif>&nbsp;&nbsp;&nbsp;<a href='?room=10'><small>Башня мага</small></a><br>
&nbsp;&nbsp;&nbsp;<img src=i/ico/up.gif>&nbsp;&nbsp;&nbsp;<a href='?room=25'><small>Почтовое отделение</small></a><br>
&nbsp;&nbsp;&nbsp;<img src=i/ico/up.gif>&nbsp;&nbsp;&nbsp;<a href='?room=8'><small>Больница</small></a><br>
&nbsp;&nbsp;&nbsp;<img src=i/ico/up.gif>&nbsp;&nbsp;&nbsp;<a href='?room=11'><small>Кузница</small></a><br>
&nbsp;&nbsp;&nbsp;<img src=i/ico/up.gif>&nbsp;&nbsp;&nbsp;<a href='?room=104'><small>Выход из города</small></a><br>
</table>
<table align=center width=700 height=320 border=0 cellpadding=0 cellspacing=0 BACKGROUND='$city'>

     <tr><td><div style=\"position:relative; id=\"ione\">";

     echo"<div style=\"position:absolute; left:110px; top:-90px;\"><img src=$stat[img_path]/i/world/0.gif width=\"50\" height=\"50\" alt='Шахта' class=aFilter onmouseover=\"imover(this);\" onmouseout=\"imout(this)\" onclick='top.frames[\"main\"].location = \"street2.php?room=400&tmp=\"+Math.random();\"\"'></div>
<div style=\"position:absolute; left:0px; top:100px;\"><img src=$stat[img_path]/i/world/0.gif width=\"130\" height=\"60\" alt='Торговая площадь' class=aFilter onmouseover=\"imover(this);\" onmouseout=\"imout(this)\" onclick='top.frames[\"main\"].location = \"street1.php?tmp=\"+Math.random();\"\"'></div>
<div style=\"position:absolute; left:50px; top:10px;\"><img src=$stat[img_path]/i/world/0.gif width=\"160\" height=\"80\" alt='Игорный дом' class=aFilter onmouseover=\"imover(this);\" onmouseout=\"imout(this)\" onclick='top.frames[\"main\"].location = \"street2.php?room=12&tmp=\"+Math.random();\"\"'></div>
<div style=\"position:absolute; left:325px; top:-130px;\"><img src=$stat[img_path]/i/world/0.gif width=\"35\" height=\"100\" alt='Башня мага' class=aFilter onmouseover=\"imover(this);\" onmouseout=\"imout(this)\" onclick='top.frames[\"main\"].location = \"street2.php?room=10&tmp=\"+Math.random();\"\"'></div>
<div style=\"position:absolute; left:580px; top:-70px;\"><img src=$stat[img_path]/i/world/0.gif width=\"90\" height=\"60\" alt='Кузница' class=aFilter onmouseover=\"imover(this);\" onmouseout=\"imout(this)\" onclick='top.frames[\"main\"].location = \"street2.php?room=11&tmp=\"+Math.random();\"\"'></div>
<div style=\"position:absolute; left:440px; top:-20px;\"><img src=$stat[img_path]/i/world/0.gif width=\"90\" height=\"60\" alt='Больница' class=aFilter onmouseover=\"imover(this);\" onmouseout=\"imout(this)\" onclick='top.frames[\"main\"].location = \"street2.php?room=8&tmp=\"+Math.random();\"\"'></div>
<div style=\"position:absolute; left:430px; top:-70px;\"><img src=$stat[img_path]/i/world/0.gif width=\"40\" height=\"40\" alt='Почтовое отделение' class=aFilter onmouseover=\"imover(this);\" onmouseout=\"imout(this)\" onclick='top.frames[\"main\"].location = \"street2.php?room=25&tmp=\"+Math.random();\"\"'></div>
<div style=\"position:absolute; left:500px; top:50px;\"><img src=$stat[img_path]/i/world/0.gif width=\"200\" height=\"110\" alt='Выход из города' class=aFilter onmouseover=\"imover(this);\" onmouseout=\"imout(this)\" onclick='top.frames[\"main\"].location = \"street3.php?tmp=\"+Math.random();\"\"'></div>
<div style=\"position:absolute; left:180px; top:-80px;\"><img src=$stat[img_path]/i/world/0.gif width=\"140\" height=\"40\" alt='Дремучий лес' class=aFilter onmouseover=\"imover(this);\" onmouseout=\"imout(this)\" onclick='top.frames[\"main\"].location = \"street2.php?room=26&tmp=\"+Math.random();\"\"'></div>
</div></tr></td>
</table>
</body>
";

}

?>

