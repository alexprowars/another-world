<table width=100% border=0 cellspacing=0 cellpadding=3>
<tr>

<table align=center cellspacing=0 cellpadding=3 bordercolor=CCCCCC border=1 bgcolor=e2e2e2>
<tr bgcolor=F2F2F2>
   <td width='5'>Место</td>
   <td align=center>Название клана</td>
   <td align=center>Число бойцов</td>
   <td align=center>Онлайн</td>
   <td align=center>Очки</td>
   <td align=center>Рейтинг</td>
</tr>

<?

$rt=db::query("SELECT * FROM game_tribes order by points desc");
$n=0;
while ($reit = db::fetch($rt))
{
	$n+=1;
	$result = db::first(db::query("SELECT COUNT(id) AS num FROM game_users WHERE tribe='".$reit['name']."'", true));

	$reiting = round($reit['points']/$result,2);

	echo"<tr ".($n%2 == 1 ? "bgcolor=F2F2F2" : "").">
	<TD align=\"center\">".$n."</TD>
	<TD><img src='img/klan/".$reit['name'].".gif' width=\"24\" height=\"14\">&nbsp;<A href=\"clan_inf.php?clan=".$reit['name']."\" target=\"_blank\"><B>".$reit['name']."</B></A></TD>
	<TD align=center>".$result."</TD>
	<TD align=\"center\">".db::first(db::query("SELECT COUNT(id) AS num FROM `game_users` WHERE `onlinetime` > '".(time()-180)."' and  rank!=60 AND `tribe`='".$reit['name']."'", true))."</TD>
	<TD align=\"center\">".$reit['points']."</TD>
	<TD align=\"center\">".$reiting."</TD>
	</TR>
	<TR>";
}

unset($rt,$reit,$n);
echo"
</TR>
</TABLE>";
?>