<?
include("config/config.php");


include("config/html_header.php");

echo"
<body bgcolor=#EBEDEC leftmargin=0 topmargin=0>
";










// Специальность

echo"
<center>
<b>Если вам нужны деньги, то вы можете заработать их в центре занятости. Когда вы работаете, то тратится показатель активности. Его можно восстановить в боях. За 1 бой дается 10 активности.</b><br><br>
<fieldset style='WIDTH: 98.6%'><legend>Легкая работа (требует 1 уровень и 25 активности за час)</legend>
<table width=100% cellspacing=0 cellpadding=5>
<tr>
<td align=center>


<table width=100% cellspacing=0 cellpadding=5 style='border-style: outset; border-width: 2' border=1>
<tr>

<td width=18 align=center><b>№</b></td>
<td><b>Наименование</b></td>
<td width=150 align=center><b>Срок работы</b></td>
<td width=160 align=center><b>Зарплата</b></td>


</tr>";


$ac=db::query("SELECT * FROM works WHERE type=0 order by srok");


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

</fieldset><br><br><br>";

// Конец получения спец.


unset($ac, $acs);












// Тяжелая работа

echo"
<fieldset style='WIDTH: 98.6%'><legend>Тяжелая работа (требует 2 уровень и 35 активности за час)</legend>
<table width=100% cellspacing=0 cellpadding=5>
<tr>
<td align=center>


<table width=100% cellspacing=0 cellpadding=5 style='border-style: outset; border-width: 2' border=1>
<tr>

<td width=18 align=center><b>№</b></td>
<td><b>Наименование</b></td>
<td width=150 align=center><b>Срок работы</b></td>
<td width=160 align=center><b>Зарплата за час работы</b></td>


</tr>";


$ac=db::query("SELECT * FROM works WHERE type=1 order by srok");


for ($i=0; $i<db::num_rows($ac); $i++) {
$acs=db::fetch($ac);

echo"
<tr>
<td align=center><b>".($i+1)."</b></td>
<td><b>$acs[title]</b></td>
<td align=center><b>Зависит от активности</b></td>
<td align=center><b>$acs[price] кр.</b></td>
";



echo"</td></tr>";

}


echo"
</table>


</td>
</tr>
</table>

</fieldset>";

// Конец получения владений





?>