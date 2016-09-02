<body bgcolor=FCFAF3>
<table align=center cellspacing=0 cellpadding=3 bordercolor=CCCCCC border=1 bgcolor=e2e2e2>
<tr bgcolor=F2F2F2>
     <td width=100><b>Место</b></td>
     <td width=300><b>Логин</b></td>
     <td width=100><b>Рейтинг</b></td>
</tr>

<?


foreach ($list as $n => $l)
{
	echo"<tr ".($n%2 == 1 ? "bgcolor=F2F2F2" : "").">
		 <td>".($n + 1)."</td>
		 <td>".$l['username']."</td>
		 <td>".$l['exp']."</td>
	</tr>";
}

?>
</table>