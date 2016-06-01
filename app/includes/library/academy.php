<?

include("config/config.php");
include("config/html_header.php");

echo"
<body bgcolor=#EBEDEC leftmargin=0 topmargin=0>
";



// Специальность

echo"
<fieldset style='WIDTH: 98.6%'><legend>Получение специальности</legend>
<table width=100% cellspacing=0 cellpadding=5>
<tr>
<td align=center>

<b>В Академии Вы можете приобрести одну из нескольких проффесий.</b><br><br>

<table width=100% cellspacing=0 cellpadding=5 style='border-style: outset; border-width: 2' border=1>
<tr>

<td width=18 align=center><b>№</b></td>
<td><b>Наименование</b></td>
<td width=150 align=center><b>Уровень</b></td>
<td width=150 align=center><b>Срок обучения</b></td>
<td width=160 align=center><b>Стоимость обучения</b></td>


</tr>";


$ac=db::query("SELECT * FROM academy WHERE type=0 order by srok");


for ($i=0; $i<db::num_rows($ac); $i++) {
$acs=db::fetch($ac);

echo"
<tr>
<td align=center><b>".($i+1)."</b></td>
<td><b>$acs[title]</b></td>
<td align=center><b>$acs[level]</b></td>
<td align=center><b>".(round($acs[srok]/60,1))." мин.</b></td>
<td align=center><b>$acs[price] кр.</b></td>";




echo"</td></tr>";

}


echo"
</table>


</td>
</tr>
</table>

</fieldset><br><br><br>";

// Конец получения спец.


unset($ac, $acs);












// Владение

echo"
<fieldset style='WIDTH: 98.6%'><legend>Получение навыков владения оружием</legend>
<table width=100% cellspacing=0 cellpadding=5>
<tr>
<td align=center>

<b>Также в Академии Вы можете получить более высокий уровень мастерства владения тем или иным видом оружия:</b><br><br>

<table width=100% cellspacing=0 cellpadding=5 style='border-style: outset; border-width: 2' border=1>
<tr>

<td width=18 align=center><b>№</b></td>
<td><b>Наименование</b></td>
<td width=150 align=center><b>Срок обучения</b></td>
<td width=160 align=center><b>Стоимость обучения</b></td>


</tr>";


$ac=db::query("SELECT * FROM academy WHERE type=1 order by srok");


for ($i=0; $i<db::num_rows($ac); $i++) {
$acs=db::fetch($ac);

echo"
<tr>
<td align=center><b>".($i+1)."</b></td>
<td><b>$acs[title]</b></td>
<td align=center><b>".(round($acs[srok]/60,1))." мин.</b></td>
<td align=center><b>$acs[price] кр.</b></td>";




echo"</td></tr>";

}


echo"
</table>


</td>
</tr>
</table>

</fieldset>";

// Конец получения владений
